-- Online Event Booking System Database
-- Create database
CREATE DATABASE IF NOT EXISTS event_booking_system;
USE event_booking_system;

-- Users table for authentication and profiles
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Events table
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    date DATE NOT NULL,
    time TIME NOT NULL,
    venue VARCHAR(200) NOT NULL,
    location VARCHAR(200) NOT NULL,
    organizer VARCHAR(100) NOT NULL,
    organizer_contact VARCHAR(100),
    image VARCHAR(255),
    price DECIMAL(10,2) NOT NULL,
    available_tickets INT NOT NULL,
    total_tickets INT NOT NULL,
    status ENUM('active', 'inactive', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bookings table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    quantity INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    booking_reference VARCHAR(50) UNIQUE,
    attendee_name VARCHAR(100),
    attendee_email VARCHAR(100),
    attendee_phone VARCHAR(20),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Shopping cart table
CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_event (user_id, event_id)
);

-- Admin users table
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'super_admin') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample admin user (password: admin123)
INSERT INTO admin_users (username, email, password, full_name, role) VALUES
('admin', 'nkumbelarry@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'super_admin');

-- Insert sample events - Cameroon locations with CFA Franc pricing
INSERT INTO events (name, description, date, time, venue, location, organizer, organizer_contact, image, price, available_tickets, total_tickets) VALUES
('Cameroon Tech Summit 2025', 'Annual technology conference featuring latest innovations and networking opportunities across Central Africa.', '2025-03-15', '09:00:00', 'Palais des Congrès', 'Yaoundé, Cameroon', 'CamerTech Events', 'contact@camertech.cm', 'tech-conference.jpg', 75000, 500, 500),
('Makossa Music Festival', 'Three-day music festival celebrating Cameroonian music and featuring top artists from across Africa.', '2025-06-20', '14:00:00', 'Ahmadou Ahidjo Stadium', 'Yaoundé, Cameroon', 'Makossa Productions', 'info@makossafest.cm', 'music-festival.jpg', 25000, 2000, 2000),
('Entrepreneurship Workshop Cameroon', 'Professional development workshop for entrepreneurs and business leaders in Central Africa.', '2025-04-10', '10:00:00', 'Hilton Yaoundé', 'Yaoundé, Cameroon', 'Business Academy Cameroon', 'workshops@bizacademy.cm', 'business-workshop.jpg', 50000, 100, 100),
('African Art Exhibition', 'Contemporary art exhibition showcasing local Cameroonian and international African artists.', '2025-05-05', '11:00:00', 'Musée National', 'Yaoundé, Cameroon', 'Art Collective Cameroon', 'gallery@artcollective.cm', 'art-exhibition.jpg', 5000, 300, 300),
('Cameroon Food & Culture Festival', 'Culinary experience featuring renowned Cameroonian chefs and traditional cuisine.', '2025-07-12', '17:00:00', 'Boulevard du 20 Mai', 'Yaoundé, Cameroon', 'Culinary Events Cameroon', 'events@culinary.cm', 'food-wine.jpg', 15000, 800, 800),
('Douala Business Expo', 'Major business exhibition showcasing opportunities in Cameroon\'s economic capital.', '2025-08-15', '08:00:00', 'Douala Congress Hall', 'Douala, Cameroon', 'Expo Cameroon', 'info@expocameroon.cm', 'business-workshop.jpg', 30000, 600, 600),
('Mount Cameroon Adventure Festival', 'Outdoor adventure festival at the foot of West Africa\'s highest peak.', '2025-09-10', '06:00:00', 'Buea Mountain Resort', 'Buea, Cameroon', 'Adventure Cameroon', 'adventure@cameroon.cm', 'music-festival.jpg', 20000, 400, 400);

-- Insert sample users (password: password123)
INSERT INTO users (username, email, password, first_name, last_name, phone, address) VALUES
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Doe', '555-0123', '123 Main St, New York, NY'),
('jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane', 'Smith', '555-0456', '456 Oak Ave, Los Angeles, CA');
