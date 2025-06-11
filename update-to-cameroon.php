<?php
// Update Event Booking System to Cameroon Context
require_once 'config/database.php';

echo "<h1>🇨🇲 Updating Event Booking System to Cameroon Context</h1>";

try {
    $pdo = getDBConnection();
    
    echo "<h2>📍 Updating Events to Cameroon Locations</h2>";
    
    // Clear existing events
    $pdo->exec("DELETE FROM events");
    echo "<p>✅ Cleared existing events</p>";
    
    // Insert Cameroon-focused events with CFA Franc pricing
    $events = [
        [
            'name' => 'Cameroon Tech Summit 2025',
            'description' => 'Annual technology conference featuring latest innovations and networking opportunities across Central Africa.',
            'date' => '2025-03-15',
            'time' => '09:00:00',
            'venue' => 'Palais des Congrès',
            'location' => 'Yaoundé, Cameroon',
            'organizer' => 'CamerTech Events',
            'organizer_contact' => 'contact@camertech.cm',
            'image' => 'tech-conference.jpg',
            'price' => 75000,
            'available_tickets' => 500,
            'total_tickets' => 500
        ],
        [
            'name' => 'Makossa Music Festival',
            'description' => 'Three-day music festival celebrating Cameroonian music and featuring top artists from across Africa.',
            'date' => '2025-06-20',
            'time' => '14:00:00',
            'venue' => 'Ahmadou Ahidjo Stadium',
            'location' => 'Yaoundé, Cameroon',
            'organizer' => 'Makossa Productions',
            'organizer_contact' => 'info@makossafest.cm',
            'image' => 'music-festival.jpg',
            'price' => 25000,
            'available_tickets' => 2000,
            'total_tickets' => 2000
        ],
        [
            'name' => 'Entrepreneurship Workshop Cameroon',
            'description' => 'Professional development workshop for entrepreneurs and business leaders in Central Africa.',
            'date' => '2025-04-10',
            'time' => '10:00:00',
            'venue' => 'Hilton Yaoundé',
            'location' => 'Yaoundé, Cameroon',
            'organizer' => 'Business Academy Cameroon',
            'organizer_contact' => 'workshops@bizacademy.cm',
            'image' => 'business-workshop.jpg',
            'price' => 50000,
            'available_tickets' => 100,
            'total_tickets' => 100
        ],
        [
            'name' => 'African Art Exhibition',
            'description' => 'Contemporary art exhibition showcasing local Cameroonian and international African artists.',
            'date' => '2025-05-05',
            'time' => '11:00:00',
            'venue' => 'Musée National',
            'location' => 'Yaoundé, Cameroon',
            'organizer' => 'Art Collective Cameroon',
            'organizer_contact' => 'gallery@artcollective.cm',
            'image' => 'art-exhibition.jpg',
            'price' => 5000,
            'available_tickets' => 300,
            'total_tickets' => 300
        ],
        [
            'name' => 'Cameroon Food & Culture Festival',
            'description' => 'Culinary experience featuring renowned Cameroonian chefs and traditional cuisine.',
            'date' => '2025-07-12',
            'time' => '17:00:00',
            'venue' => 'Boulevard du 20 Mai',
            'location' => 'Yaoundé, Cameroon',
            'organizer' => 'Culinary Events Cameroon',
            'organizer_contact' => 'events@culinary.cm',
            'image' => 'food-wine.jpg',
            'price' => 15000,
            'available_tickets' => 800,
            'total_tickets' => 800
        ],
        [
            'name' => 'Douala Business Expo',
            'description' => 'Major business exhibition showcasing opportunities in Cameroon\'s economic capital.',
            'date' => '2025-08-15',
            'time' => '08:00:00',
            'venue' => 'Douala Congress Hall',
            'location' => 'Douala, Cameroon',
            'organizer' => 'Expo Cameroon',
            'organizer_contact' => 'info@expocameroon.cm',
            'image' => 'business-workshop.jpg',
            'price' => 30000,
            'available_tickets' => 600,
            'total_tickets' => 600
        ],
        [
            'name' => 'Mount Cameroon Adventure Festival',
            'description' => 'Outdoor adventure festival at the foot of West Africa\'s highest peak.',
            'date' => '2025-09-10',
            'time' => '06:00:00',
            'venue' => 'Buea Mountain Resort',
            'location' => 'Buea, Cameroon',
            'organizer' => 'Adventure Cameroon',
            'organizer_contact' => 'adventure@cameroon.cm',
            'image' => 'music-festival.jpg',
            'price' => 20000,
            'available_tickets' => 400,
            'total_tickets' => 400
        ],
        [
            'name' => 'Bamenda Cultural Heritage Festival',
            'description' => 'Celebrating the rich cultural heritage of the Northwest Region of Cameroon.',
            'date' => '2025-10-05',
            'time' => '10:00:00',
            'venue' => 'Bamenda Municipal Stadium',
            'location' => 'Bamenda, Cameroon',
            'organizer' => 'Northwest Cultural Association',
            'organizer_contact' => 'culture@northwest.cm',
            'image' => 'art-exhibition.jpg',
            'price' => 8000,
            'available_tickets' => 1000,
            'total_tickets' => 1000
        ],
        [
            'name' => 'Garoua Trade Fair',
            'description' => 'Annual trade fair showcasing products and services from Northern Cameroon.',
            'date' => '2025-11-20',
            'time' => '09:00:00',
            'venue' => 'Garoua Exhibition Center',
            'location' => 'Garoua, Cameroon',
            'organizer' => 'North Cameroon Chamber of Commerce',
            'organizer_contact' => 'trade@garoua.cm',
            'image' => 'business-workshop.jpg',
            'price' => 12000,
            'available_tickets' => 800,
            'total_tickets' => 800
        ],
        [
            'name' => 'Kribi Beach Music Festival',
            'description' => 'Beachside music festival featuring local and international artists by the Atlantic Ocean.',
            'date' => '2025-12-15',
            'time' => '16:00:00',
            'venue' => 'Kribi Beach Resort',
            'location' => 'Kribi, Cameroon',
            'organizer' => 'Coastal Events Cameroon',
            'organizer_contact' => 'beach@kribi.cm',
            'image' => 'music-festival.jpg',
            'price' => 18000,
            'available_tickets' => 1500,
            'total_tickets' => 1500
        ]
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO events (name, description, date, time, venue, location, organizer, organizer_contact, image, price, available_tickets, total_tickets) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($events as $event) {
        $stmt->execute([
            $event['name'],
            $event['description'],
            $event['date'],
            $event['time'],
            $event['venue'],
            $event['location'],
            $event['organizer'],
            $event['organizer_contact'],
            $event['image'],
            $event['price'],
            $event['available_tickets'],
            $event['total_tickets']
        ]);
        echo "<p>✅ Added: " . htmlspecialchars($event['name']) . " - " . number_format($event['price']) . " CFA</p>";
    }
    
    echo "<h2>📊 Summary of Changes</h2>";
    
    // Count events by location
    $stmt = $pdo->query("SELECT location, COUNT(*) as count FROM events GROUP BY location ORDER BY count DESC");
    $locations = $stmt->fetchAll();
    
    echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3>🏙️ Events by City:</h3>";
    foreach ($locations as $location) {
        echo "<p><strong>" . htmlspecialchars($location['location']) . ":</strong> " . $location['count'] . " events</p>";
    }
    echo "</div>";
    
    // Price range
    $stmt = $pdo->query("SELECT MIN(price) as min_price, MAX(price) as max_price, AVG(price) as avg_price FROM events");
    $priceStats = $stmt->fetch();
    
    echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3>💰 Pricing in CFA Francs:</h3>";
    echo "<p><strong>Lowest Price:</strong> " . number_format($priceStats['min_price']) . " CFA</p>";
    echo "<p><strong>Highest Price:</strong> " . number_format($priceStats['max_price']) . " CFA</p>";
    echo "<p><strong>Average Price:</strong> " . number_format($priceStats['avg_price']) . " CFA</p>";
    echo "</div>";
    
    echo "<h2>🎯 Cameroon Context Features Added</h2>";
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3>✅ Updates Completed:</h3>";
    echo "<ul>";
    echo "<li>🏢 <strong>Contact Address:</strong> Avenue Kennedy, Plateau District, Yaoundé</li>";
    echo "<li>📞 <strong>Phone Format:</strong> +237 652 731 798 (Cameroon format)</li>";
    echo "<li>🗺️ <strong>Map Location:</strong> Updated to Yaoundé, Cameroon</li>";
    echo "<li>💰 <strong>Currency:</strong> Changed from USD to CFA Francs</li>";
    echo "<li>🎪 <strong>Events:</strong> " . count($events) . " Cameroon-focused events added</li>";
    echo "<li>🌍 <strong>Locations:</strong> Yaoundé, Douala, Buea, Bamenda, Garoua, Kribi</li>";
    echo "<li>🚌 <strong>Transport:</strong> Updated to bus stations (more relevant for Cameroon)</li>";
    echo "<li>⏰ <strong>Time Zone:</strong> WAT (West Africa Time) references</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<h2>🌟 Your Cameroon Event Booking System</h2>";
    echo "<div style='background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 30px; border-radius: 15px; margin: 20px 0;'>";
    echo "<h3>🇨🇲 Now Featuring:</h3>";
    echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;'>";
    
    $features = [
        ['🏛️', 'Yaoundé Events', 'Capital city conferences and exhibitions'],
        ['🏢', 'Douala Business', 'Economic capital trade and business events'],
        ['🏔️', 'Buea Adventures', 'Mountain and outdoor activities'],
        ['🎭', 'Cultural Festivals', 'Traditional Cameroonian celebrations'],
        ['🏖️', 'Coastal Events', 'Beach festivals and coastal activities'],
        ['💰', 'CFA Pricing', 'Local currency for easy understanding']
    ];
    
    foreach ($features as $feature) {
        echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px; text-align: center;'>";
        echo "<div style='font-size: 2em; margin-bottom: 10px;'>{$feature[0]}</div>";
        echo "<h5>{$feature[1]}</h5>";
        echo "<p style='font-size: 0.9em; opacity: 0.9;'>{$feature[2]}</p>";
        echo "</div>";
    }
    echo "</div>";
    echo "</div>";
    
    echo "<h2>🔗 Test Your Updated System</h2>";
    echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3>🧪 Pages to Test:</h3>";
    echo "<ul>";
    echo "<li><a href='index.php' target='_blank'>🏠 Homepage</a> - See Cameroon events</li>";
    echo "<li><a href='events.php' target='_blank'>🎪 Events Page</a> - Browse all Cameroon events</li>";
    echo "<li><a href='about.php' target='_blank'>❤️ About Us</a> - Updated Cameroon context</li>";
    echo "<li><a href='contact.php' target='_blank'>📞 Contact Us</a> - Yaoundé map and details</li>";
    echo "<li><a href='admin/index.php' target='_blank'>🛡️ Admin Panel</a> - Manage Cameroon events</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='background: #d1ecf1; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
    echo "<h3>🎉 Cameroon Event Booking System Ready!</h3>";
    echo "<p>Your platform now showcases the best of Cameroon's event scene with local pricing, locations, and cultural context.</p>";
    echo "<p><strong>Perfect for promoting events across all 10 regions of Cameroon!</strong></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3>❌ Error Updating System</h3>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}
?>

<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
    background: #f8f9fa;
}

h1 {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    margin-bottom: 30px;
}

h2 {
    color: #333;
    border-bottom: 2px solid #667eea;
    padding-bottom: 10px;
    margin-top: 30px;
}

a {
    color: #667eea;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
</style>
