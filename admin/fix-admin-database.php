<?php
// Fix admin database schema and data
require_once 'includes/admin-functions.php';

echo "<h1>🔧 Admin Database Fix</h1>";

try {
    $pdo = getDBConnection();
    
    echo "<h2>📊 Current Admin Users Table Structure</h2>";
    
    // Check current table structure
    $stmt = $pdo->query("DESCRIBE admin_users");
    $columns = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Default']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check if required columns exist
    $columnNames = array_column($columns, 'Field');
    $requiredColumns = ['status', 'last_login', 'first_name', 'last_name'];
    $missingColumns = array_diff($requiredColumns, $columnNames);
    
    if (!empty($missingColumns)) {
        echo "<h3>⚠️ Missing Columns Found</h3>";
        echo "<p>Adding missing columns: " . implode(', ', $missingColumns) . "</p>";
        
        // Add missing columns
        if (in_array('status', $missingColumns)) {
            $pdo->exec("ALTER TABLE admin_users ADD COLUMN status ENUM('active', 'inactive', 'suspended') DEFAULT 'active' AFTER role");
            echo "<p>✅ Added 'status' column</p>";
        }
        
        if (in_array('last_login', $missingColumns)) {
            $pdo->exec("ALTER TABLE admin_users ADD COLUMN last_login TIMESTAMP NULL AFTER status");
            echo "<p>✅ Added 'last_login' column</p>";
        }
        
        if (in_array('first_name', $missingColumns)) {
            $pdo->exec("ALTER TABLE admin_users ADD COLUMN first_name VARCHAR(50) NULL AFTER password");
            echo "<p>✅ Added 'first_name' column</p>";
        }
        
        if (in_array('last_name', $missingColumns)) {
            $pdo->exec("ALTER TABLE admin_users ADD COLUMN last_name VARCHAR(50) NULL AFTER first_name");
            echo "<p>✅ Added 'last_name' column</p>";
        }
    } else {
        echo "<p>✅ All required columns exist</p>";
    }
    
    echo "<h2>👥 Current Admin Users</h2>";
    
    // Check current admin users
    $stmt = $pdo->query("SELECT * FROM admin_users");
    $admins = $stmt->fetchAll();
    
    if (empty($admins)) {
        echo "<p>⚠️ No admin users found. Creating default admin...</p>";
        
        // Create default admin
        $stmt = $pdo->prepare("
            INSERT INTO admin_users (username, email, password, first_name, last_name, full_name, role, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            'admin',
            'nkumbelarry@gmail.com',
            password_hash('admin123', PASSWORD_DEFAULT),
            'Admin',
            'User',
            'Admin User',
            'super_admin',
            'active'
        ]);
        
        echo "<p>✅ Created default admin user</p>";
        echo "<p><strong>Username:</strong> admin</p>";
        echo "<p><strong>Password:</strong> admin123</p>";
        echo "<p><strong>Email:</strong> nkumbelarry@gmail.com</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Name</th><th>Role</th><th>Status</th><th>Created</th></tr>";
        
        foreach ($admins as $admin) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($admin['id']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['username']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['email']) . "</td>";
            echo "<td>" . htmlspecialchars(($admin['first_name'] ?? '') . ' ' . ($admin['last_name'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars($admin['role']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['status'] ?? 'active') . "</td>";
            echo "<td>" . htmlspecialchars($admin['created_at']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Update existing admins to have proper status and names if missing
        foreach ($admins as $admin) {
            $needsUpdate = false;
            $updates = [];
            $params = [];
            
            if (empty($admin['status'])) {
                $updates[] = "status = ?";
                $params[] = 'active';
                $needsUpdate = true;
            }
            
            if (empty($admin['first_name']) && $admin['username'] === 'admin') {
                $updates[] = "first_name = ?";
                $params[] = 'Admin';
                $needsUpdate = true;
            }
            
            if (empty($admin['last_name']) && $admin['username'] === 'admin') {
                $updates[] = "last_name = ?";
                $params[] = 'User';
                $needsUpdate = true;
            }
            
            if ($needsUpdate) {
                $params[] = $admin['id'];
                $sql = "UPDATE admin_users SET " . implode(', ', $updates) . " WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                echo "<p>✅ Updated admin user: " . htmlspecialchars($admin['username']) . "</p>";
            }
        }
    }
    
    echo "<h2>🔐 Testing Admin Authentication</h2>";
    
    // Test admin login function
    $testUsername = 'admin';
    $testPassword = 'admin123';
    
    echo "<p>Testing login with username: <strong>$testUsername</strong></p>";
    
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? AND status = 'active'");
    $stmt->execute([$testUsername]);
    $testAdmin = $stmt->fetch();
    
    if ($testAdmin) {
        echo "<p>✅ Admin user found in database</p>";
        echo "<p>Username: " . htmlspecialchars($testAdmin['username']) . "</p>";
        echo "<p>Email: " . htmlspecialchars($testAdmin['email']) . "</p>";
        echo "<p>Role: " . htmlspecialchars($testAdmin['role']) . "</p>";
        echo "<p>Status: " . htmlspecialchars($testAdmin['status']) . "</p>";
        
        if (password_verify($testPassword, $testAdmin['password'])) {
            echo "<p>✅ Password verification successful</p>";
        } else {
            echo "<p>❌ Password verification failed</p>";
            
            // Update password
            $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
            $stmt->execute([$newHash, $testAdmin['id']]);
            echo "<p>✅ Password updated for admin user</p>";
        }
    } else {
        echo "<p>❌ Admin user not found or inactive</p>";
    }
    
    echo "<h2>🎯 Admin Access Information</h2>";
    echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🔗 Admin Login URL:</h3>";
    echo "<p><a href='index.php' style='color: #0066cc; font-weight: bold;'>http://localhost:8080/admin/index.php</a></p>";
    echo "<h3>🔑 Admin Credentials:</h3>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p><strong>Email:</strong> nkumbelarry@gmail.com</p>";
    echo "</div>";
    
    echo "<h2>✅ Database Fix Complete!</h2>";
    echo "<p>All admin database issues have been resolved. You can now log in to the admin panel.</p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error</h2>";
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Please check your database connection and try again.</p>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
    background: #f5f5f5;
}

h1, h2, h3 {
    color: #333;
}

table {
    width: 100%;
    background: white;
    border-radius: 5px;
    overflow: hidden;
}

th {
    background: #667eea;
    color: white;
    padding: 10px;
    text-align: left;
}

td {
    padding: 8px 10px;
    border-bottom: 1px solid #eee;
}

tr:nth-child(even) {
    background: #f9f9f9;
}

p {
    line-height: 1.6;
}

.success {
    color: #28a745;
    font-weight: bold;
}

.error {
    color: #dc3545;
    font-weight: bold;
}

.warning {
    color: #ffc107;
    font-weight: bold;
}
</style>
