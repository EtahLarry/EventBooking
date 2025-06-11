<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// User authentication functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function login($username, $password) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        return true;
    }
    return false;
}

function register($data) {
    $pdo = getDBConnection();
    
    // Check if username or email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$data['username'], $data['email']]);
    if ($stmt->fetch()) {
        return false;
    }
    
    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, first_name, last_name, phone, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([
        $data['username'],
        $data['email'],
        password_hash($data['password'], PASSWORD_DEFAULT),
        $data['first_name'],
        $data['last_name'],
        $data['phone'] ?? '',
        $data['address'] ?? ''
    ]);
}

function logout() {
    session_destroy();
    header('Location: index.php');
    exit();
}

// Event functions
function getAllEvents($search = '', $location = '', $date = '') {
    $pdo = getDBConnection();
    $sql = "SELECT * FROM events WHERE status = 'active'";
    $params = [];
    
    if (!empty($search)) {
        $sql .= " AND (name LIKE ? OR description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if (!empty($location)) {
        $sql .= " AND location LIKE ?";
        $params[] = "%$location%";
    }
    
    if (!empty($date)) {
        $sql .= " AND date = ?";
        $params[] = $date;
    }
    
    $sql .= " ORDER BY date ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getEventById($id) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND status = 'active'");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Cart functions
function addToCart($userId, $eventId, $quantity = 1) {
    $pdo = getDBConnection();
    
    // Check if item already in cart
    $stmt = $pdo->prepare("SELECT * FROM cart_items WHERE user_id = ? AND event_id = ?");
    $stmt->execute([$userId, $eventId]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Update quantity
        $stmt = $pdo->prepare("UPDATE cart_items SET quantity = quantity + ? WHERE user_id = ? AND event_id = ?");
        return $stmt->execute([$quantity, $userId, $eventId]);
    } else {
        // Insert new item
        $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, event_id, quantity) VALUES (?, ?, ?)");
        return $stmt->execute([$userId, $eventId, $quantity]);
    }
}

function getCartItems($userId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT c.*, e.name, e.price, e.date, e.time, e.venue, e.image 
        FROM cart_items c 
        JOIN events e ON c.event_id = e.id 
        WHERE c.user_id = ? AND e.status = 'active'
        ORDER BY c.added_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function removeFromCart($userId, $eventId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ? AND event_id = ?");
    return $stmt->execute([$userId, $eventId]);
}

function updateCartQuantity($userId, $eventId, $quantity) {
    if ($quantity <= 0) {
        return removeFromCart($userId, $eventId);
    }
    
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND event_id = ?");
    return $stmt->execute([$quantity, $userId, $eventId]);
}

function getCartTotal($userId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT SUM(c.quantity * e.price) as total 
        FROM cart_items c 
        JOIN events e ON c.event_id = e.id 
        WHERE c.user_id = ? AND e.status = 'active'
    ");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

// Booking functions
function createBooking($userId, $cartItems, $attendeeInfo) {
    $pdo = getDBConnection();
    
    try {
        $pdo->beginTransaction();
        
        foreach ($cartItems as $item) {
            // Check ticket availability
            $event = getEventById($item['event_id']);
            if ($event['available_tickets'] < $item['quantity']) {
                throw new Exception("Not enough tickets available for " . $event['name']);
            }
            
            // Generate booking reference
            $bookingRef = 'BK' . date('Ymd') . rand(1000, 9999);
            
            // Create booking
            $stmt = $pdo->prepare("
                INSERT INTO bookings (user_id, event_id, quantity, total_amount, booking_reference, 
                                    attendee_name, attendee_email, attendee_phone, status, payment_status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'confirmed', 'completed')
            ");
            $stmt->execute([
                $userId,
                $item['event_id'],
                $item['quantity'],
                $item['quantity'] * $item['price'],
                $bookingRef,
                $attendeeInfo['name'],
                $attendeeInfo['email'],
                $attendeeInfo['phone']
            ]);
            
            // Update available tickets
            $stmt = $pdo->prepare("UPDATE events SET available_tickets = available_tickets - ? WHERE id = ?");
            $stmt->execute([$item['quantity'], $item['event_id']]);
        }
        
        // Clear cart
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
        $stmt->execute([$userId]);
        
        $pdo->commit();
        return true;
        
    } catch (Exception $e) {
        $pdo->rollback();
        throw $e;
    }
}

function getUserBookings($userId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT b.*, e.name, e.date, e.time, e.venue, e.location, e.image 
        FROM bookings b 
        JOIN events e ON b.event_id = e.id 
        WHERE b.user_id = ? 
        ORDER BY b.booking_date DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

// Utility functions
function formatPrice($price) {
    return number_format($price, 0) . ' CFA';
}

function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

function formatTime($time) {
    return date('g:i A', strtotime($time));
}

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function generateQRCode($text) {
    // Simple QR code generation (in real implementation, use a QR library)
    return "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($text);
}
?>
