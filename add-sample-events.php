<?php
/**
 * Add Sample Events Script
 * Adds exciting sample events to the database
 */

echo "<h1>🎪 Add Sample Events</h1>";
echo "<p>Adding exciting events to your Event Booking System...</p>";

// Include database configuration
require_once 'config/database.php';

try {
    $pdo = getDBConnection();
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
    echo "✅ <strong>Database connection successful!</strong>";
    echo "</div>";
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
    echo "❌ <strong>Database connection failed:</strong><br>";
    echo $e->getMessage();
    echo "</div>";
    exit;
}

echo "<h2>🎫 Adding Exciting Events</h2>";

// Sample events data
$sampleEvents = [
    [
        'name' => 'Afrobeats Music Festival 2025',
        'description' => 'The biggest Afrobeats festival in Central Africa! Featuring top artists from Nigeria, Ghana, Cameroon and beyond. Experience the best of African music with live performances, food vendors, and cultural displays.',
        'date' => '2025-08-15',
        'time' => '18:00:00',
        'venue' => 'Palais des Sports',
        'location' => 'Yaoundé, Cameroon',
        'organizer' => 'Afrobeats Central Africa',
        'organizer_contact' => 'info@afrobeatsfest.cm',
        'price' => 15000,
        'total_tickets' => 5000,
        'available_tickets' => 5000,
        'image' => 'events/afrobeats-festival.jpg'
    ],
    [
        'name' => 'Boxing Championship: Lions vs Eagles',
        'description' => 'Professional boxing championship featuring the best fighters from Cameroon and Nigeria. Witness explosive action in the ring with multiple title fights throughout the evening.',
        'date' => '2025-07-20',
        'time' => '20:00:00',
        'venue' => 'Omnisport Stadium',
        'location' => 'Douala, Cameroon',
        'organizer' => 'Central Africa Boxing Federation',
        'organizer_contact' => 'boxing@cafed.cm',
        'price' => 25000,
        'total_tickets' => 3000,
        'available_tickets' => 3000,
        'image' => 'events/boxing-championship.jpg'
    ],
    [
        'name' => 'Tech Innovation Summit 2025',
        'description' => 'Join leading tech entrepreneurs, developers, and innovators for a day of inspiring talks, networking, and showcasing cutting-edge technology solutions from across Africa.',
        'date' => '2025-09-10',
        'time' => '09:00:00',
        'venue' => 'Hilton Hotel Conference Center',
        'location' => 'Yaoundé, Cameroon',
        'organizer' => 'TechHub Cameroon',
        'organizer_contact' => 'events@techhub.cm',
        'price' => 35000,
        'total_tickets' => 500,
        'available_tickets' => 500,
        'image' => 'events/tech-summit.jpg'
    ],
    [
        'name' => 'Comedy Night with African Stars',
        'description' => 'Laugh until your sides hurt with the funniest comedians from across Africa! An evening of non-stop entertainment featuring stand-up comedy, skits, and surprise guest appearances.',
        'date' => '2025-07-05',
        'time' => '19:30:00',
        'venue' => 'Cultural Center Auditorium',
        'location' => 'Yaoundé, Cameroon',
        'organizer' => 'Laugh Factory Africa',
        'organizer_contact' => 'bookings@laughfactory.cm',
        'price' => 8000,
        'total_tickets' => 800,
        'available_tickets' => 800,
        'image' => 'events/comedy-night.jpg'
    ],
    [
        'name' => 'Fashion Week Cameroon 2025',
        'description' => 'Discover the latest trends in African fashion! Top designers showcase their collections in this glamorous event featuring runway shows, fashion exhibitions, and networking opportunities.',
        'date' => '2025-10-15',
        'time' => '17:00:00',
        'venue' => 'Palais des Congrès',
        'location' => 'Yaoundé, Cameroon',
        'organizer' => 'Cameroon Fashion Council',
        'organizer_contact' => 'info@fashionweekcm.com',
        'price' => 20000,
        'total_tickets' => 1200,
        'available_tickets' => 1200,
        'image' => 'events/fashion-week.jpg'
    ],
    [
        'name' => 'Football Derby: Lions vs Stallions',
        'description' => 'The most anticipated football match of the year! Watch as the Indomitable Lions face off against the Stallions in this thrilling championship match.',
        'date' => '2025-08-30',
        'time' => '16:00:00',
        'venue' => 'Ahmadou Ahidjo Stadium',
        'location' => 'Yaoundé, Cameroon',
        'organizer' => 'Cameroon Football Federation',
        'organizer_contact' => 'tickets@fecafoot.cm',
        'price' => 5000,
        'total_tickets' => 40000,
        'available_tickets' => 40000,
        'image' => 'events/football-derby.jpg'
    ],
    [
        'name' => 'Jazz & Blues Night',
        'description' => 'An intimate evening of smooth jazz and soulful blues featuring local and international artists. Perfect for music lovers seeking a sophisticated night out.',
        'date' => '2025-07-12',
        'time' => '20:30:00',
        'venue' => 'Jazz Café Yaoundé',
        'location' => 'Yaoundé, Cameroon',
        'organizer' => 'Jazz Society Cameroon',
        'organizer_contact' => 'events@jazzcafe.cm',
        'price' => 12000,
        'total_tickets' => 200,
        'available_tickets' => 200,
        'image' => 'events/jazz-night.jpg'
    ],
    [
        'name' => 'Food & Wine Festival',
        'description' => 'Celebrate the rich culinary heritage of Cameroon and Africa! Taste exotic dishes, premium wines, and learn from renowned chefs in cooking demonstrations.',
        'date' => '2025-09-25',
        'time' => '12:00:00',
        'venue' => 'Botanical Garden',
        'location' => 'Limbe, Cameroon',
        'organizer' => 'Culinary Arts Cameroon',
        'organizer_contact' => 'info@foodfest.cm',
        'price' => 18000,
        'total_tickets' => 1000,
        'available_tickets' => 1000,
        'image' => 'events/food-festival.jpg'
    ],
    [
        'name' => 'Gaming Tournament: Esports Championship',
        'description' => 'The ultimate gaming competition! Watch top gamers compete in popular games like FIFA, Call of Duty, and League of Legends for cash prizes and glory.',
        'date' => '2025-08-05',
        'time' => '10:00:00',
        'venue' => 'Gaming Arena Complex',
        'location' => 'Douala, Cameroon',
        'organizer' => 'Esports Cameroon',
        'organizer_contact' => 'tournaments@esports.cm',
        'price' => 3000,
        'total_tickets' => 600,
        'available_tickets' => 600,
        'image' => 'events/gaming-tournament.jpg'
    ],
    [
        'name' => 'Cultural Heritage Festival',
        'description' => 'Celebrate the diverse cultures of Cameroon! Traditional dances, music, crafts, and storytelling from all 10 regions. A family-friendly event showcasing our rich heritage.',
        'date' => '2025-12-20',
        'time' => '14:00:00',
        'venue' => 'National Museum Grounds',
        'location' => 'Yaoundé, Cameroon',
        'organizer' => 'Ministry of Arts and Culture',
        'organizer_contact' => 'culture@minac.gov.cm',
        'price' => 2000,
        'total_tickets' => 2000,
        'available_tickets' => 2000,
        'image' => 'events/cultural-festival.jpg'
    ]
];

$addedCount = 0;
$skippedCount = 0;

foreach ($sampleEvents as $event) {
    try {
        // Check if event already exists
        $stmt = $pdo->prepare("SELECT id FROM events WHERE name = ?");
        $stmt->execute([$event['name']]);
        
        if ($stmt->fetch()) {
            echo "<p>⚠️ Event already exists: <strong>{$event['name']}</strong></p>";
            $skippedCount++;
            continue;
        }
        
        // Insert new event
        $stmt = $pdo->prepare("
            INSERT INTO events (name, description, date, time, venue, location, organizer, organizer_contact, price, total_tickets, available_tickets, image, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')
        ");
        
        $stmt->execute([
            $event['name'],
            $event['description'],
            $event['date'],
            $event['time'],
            $event['venue'],
            $event['location'],
            $event['organizer'],
            $event['organizer_contact'],
            $event['price'],
            $event['total_tickets'],
            $event['available_tickets'],
            $event['image']
        ]);
        
        echo "<p>✅ Added event: <strong>{$event['name']}</strong></p>";
        $addedCount++;
        
    } catch (Exception $e) {
        echo "<p>❌ Failed to add event <strong>{$event['name']}</strong>: " . $e->getMessage() . "</p>";
    }
}

echo "<h2>🎉 Events Added Successfully!</h2>";
echo "<div style='background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h3>✅ Event Addition Complete!</h3>";
echo "<p><strong>Summary:</strong></p>";
echo "<ul>";
echo "<li>✅ <strong>$addedCount</strong> new events added</li>";
echo "<li>⚠️ <strong>$skippedCount</strong> events already existed</li>";
echo "<li>🎪 <strong>Total variety:</strong> Music, Sports, Tech, Comedy, Fashion, Food, Gaming, Culture</li>";
echo "</ul>";
echo "<p><strong>Your Event Booking System now has exciting events for everyone!</strong></p>";
echo "</div>";

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='index.php' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 10px;'>🏠 View Homepage</a>";
echo "<a href='events.php' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 10px;'>🎫 Browse All Events</a>";
echo "<a href='admin' style='background: #6f42c1; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 10px;'>🛡️ Admin Panel</a>";
echo "</div>";
?>
