<?php
require_once '../includes/functions.php';

// Set JSON header
header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please log in to continue.']);
    exit();
}

$userId = $_SESSION['user_id'];
$action = $_REQUEST['action'] ?? '';

try {
    switch ($action) {
        case 'add':
            $eventId = $_POST['event_id'] ?? 0;
            $quantity = $_POST['quantity'] ?? 1;
            
            if (!$eventId || $quantity < 1) {
                echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
                exit();
            }
            
            // Check if event exists and has available tickets
            $event = getEventById($eventId);
            if (!$event) {
                echo json_encode(['success' => false, 'message' => 'Event not found.']);
                exit();
            }
            
            if ($event['available_tickets'] < $quantity) {
                echo json_encode(['success' => false, 'message' => 'Not enough tickets available.']);
                exit();
            }
            
            // Add to cart
            if (addToCart($userId, $eventId, $quantity)) {
                echo json_encode(['success' => true, 'message' => 'Item added to cart successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add item to cart.']);
            }
            break;
            
        case 'update':
            $eventId = $_POST['event_id'] ?? 0;
            $quantity = $_POST['quantity'] ?? 1;
            
            if (!$eventId || $quantity < 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
                exit();
            }
            
            // Check if event exists
            $event = getEventById($eventId);
            if (!$event) {
                echo json_encode(['success' => false, 'message' => 'Event not found.']);
                exit();
            }
            
            if ($quantity > 0 && $event['available_tickets'] < $quantity) {
                echo json_encode(['success' => false, 'message' => 'Not enough tickets available.']);
                exit();
            }
            
            // Update cart
            if (updateCartQuantity($userId, $eventId, $quantity)) {
                echo json_encode(['success' => true, 'message' => 'Cart updated successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update cart.']);
            }
            break;
            
        case 'remove':
            $eventId = $_POST['event_id'] ?? 0;
            
            if (!$eventId) {
                echo json_encode(['success' => false, 'message' => 'Invalid event ID.']);
                exit();
            }
            
            // Remove from cart
            if (removeFromCart($userId, $eventId)) {
                echo json_encode(['success' => true, 'message' => 'Item removed from cart.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to remove item from cart.']);
            }
            break;
            
        case 'clear':
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
            
            if ($stmt->execute([$userId])) {
                echo json_encode(['success' => true, 'message' => 'Cart cleared successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to clear cart.']);
            }
            break;
            
        case 'count':
            $cartItems = getCartItems($userId);
            $count = 0;
            foreach ($cartItems as $item) {
                $count += $item['quantity'];
            }
            echo json_encode(['success' => true, 'count' => $count]);
            break;
            
        case 'total':
            $total = getCartTotal($userId);
            echo json_encode(['success' => true, 'total' => $total]);
            break;
            
        case 'items':
            $cartItems = getCartItems($userId);
            echo json_encode(['success' => true, 'items' => $cartItems]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action.']);
            break;
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
