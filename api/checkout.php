<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../models/Cart.php';
require_once '../models/Order.php';
require_once '../models/Ticket.php';
require_once '../config/database.php';

// Create single database connection
$database = new Database();
$db = $database->getConnection();

$cart = new Cart();
$order = new Order($db);
$ticket = new Ticket($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sessionId = session_id();
    $cartItems = $cart->getCartItems($sessionId);
    
    if (empty($cartItems)) {
        echo json_encode(['success' => false, 'error' => 'Cart is empty']);
        exit;
    }
    
    // Validate stock availability before processing order
    $stockErrors = [];
    foreach ($cartItems as $item) {
        if (!$ticket->isAvailable($item['ticket_id'], $item['quantity'])) {
            $availableQuantity = $ticket->getAvailableQuantity($item['ticket_id']);
            $stockErrors[] = "{$item['title']}: Only {$availableQuantity} tickets available (requested: {$item['quantity']})";
        }
    }
    
    if (!empty($stockErrors)) {
        echo json_encode([
            'success' => false, 
            'error' => 'Stock validation failed',
            'details' => $stockErrors
        ]);
        exit;
    }
    
        try {
            // Start transaction
            $db->beginTransaction();
            
            // Create order (without internal transaction)
            $orderId = $order->createFromCart($sessionId, $cartItems);
            
            // Reduce ticket quantities
            foreach ($cartItems as $item) {
                if (!$ticket->reduceQuantity($item['ticket_id'], $item['quantity'])) {
                    throw new Exception("Failed to reduce quantity for ticket: {$item['title']}");
                }
            }
            
            // Commit transaction
            $db->commit();
            
            // Clear cart
            $cart->clearCart($sessionId);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Order completed successfully',
                'orderId' => $orderId
            ]);
        } catch (Exception $e) {
            // Rollback transaction
            $order->getConnection()->rollBack();
            echo json_encode(['success' => false, 'error' => 'Failed to process order: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
?>


