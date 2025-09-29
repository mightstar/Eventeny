<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../models/Cart.php';
require_once '../models/Ticket.php';
$cart = new Cart();
$ticket = new Ticket();
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// Get or create session ID
$sessionId = session_id();

switch ($method) {
    case 'GET':
        $items = $cart->getCartItems($sessionId);
        $total = $cart->getCartTotal($sessionId);
        $itemCount = $cart->getCartItemCount($sessionId);
        
        echo json_encode([
            'success' => true, 
            'data' => [
                'items' => $items,
                'total' => $total,
                'itemCount' => $itemCount
            ]
        ]);
        break;
        
    case 'POST':
        if (!isset($input['ticket_id']) || !isset($input['quantity'])) {
            echo json_encode(['success' => false, 'error' => 'Ticket ID and quantity required']);
            break;
        }
        
        // Get current cart quantity for this ticket
        $existingItem = $cart->getItem($sessionId, $input['ticket_id']);
        $currentCartQuantity = $existingItem ? $existingItem['quantity'] : 0;
        $requestedQuantity = $input['quantity'];
        $totalQuantity = $currentCartQuantity + $requestedQuantity;
        
        // Check if ticket is available for the total quantity
        if (!$ticket->isAvailable($input['ticket_id'], $totalQuantity)) {
            $availableQuantity = $ticket->getAvailableQuantity($input['ticket_id']);
            $canAddQuantity = max(0, $availableQuantity - $currentCartQuantity);
            
            if ($canAddQuantity == 0) {
                echo json_encode(['success' => false, 'error' => 'No more tickets available']);
            } else {
                echo json_encode(['success' => false, 'error' => "Only {$canAddQuantity} more tickets available"]);
            }
            break;
        }
        
        $success = $cart->addItem($sessionId, $input['ticket_id'], $requestedQuantity);
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Item added to cart']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to add item to cart']);
        }
        break;
        
    case 'PUT':
        if (!isset($input['ticket_id']) || !isset($input['quantity'])) {
            echo json_encode(['success' => false, 'error' => 'Ticket ID and quantity required']);
            break;
        }
        
        $success = $cart->updateQuantity($sessionId, $input['ticket_id'], $input['quantity']);
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Cart updated']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update cart']);
        }
        break;
        
    case 'DELETE':
        if (!isset($_GET['ticket_id'])) {
            echo json_encode(['success' => false, 'error' => 'Ticket ID required']);
            break;
        }
        
        $success = $cart->removeItem($sessionId, $_GET['ticket_id']);
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to remove item from cart']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        break;
}
?>


