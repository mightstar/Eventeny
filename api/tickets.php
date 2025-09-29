<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../models/Ticket.php';

$ticket = new Ticket();
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $result = $ticket->getById($_GET['id']);
        } else {
            $result = $ticket->getAll();
        }
        echo json_encode(['success' => true, 'data' => $result]);
        break;
        
    case 'POST':
        $required = ['title', 'price', 'quantity', 'sale_start_date', 'sale_end_date', 'visibility'];
        $missing = array_diff($required, array_keys($input));
        
        if (!empty($missing)) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields: ' . implode(', ', $missing)]);
            break;
        }
        
        $success = $ticket->create($input);
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Ticket created successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to create ticket']);
        }
        break;
        
    case 'PUT':
        if (!isset($_GET['id'])) {
            echo json_encode(['success' => false, 'error' => 'Ticket ID required']);
            break;
        }
        
        $success = $ticket->update($_GET['id'], $input);
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Ticket updated successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update ticket']);
        }
        break;
        
    case 'DELETE':
        if (!isset($_GET['id'])) {
            echo json_encode(['success' => false, 'error' => 'Ticket ID required']);
            break;
        }
        
        $success = $ticket->delete($_GET['id']);
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Ticket deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete ticket']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        break;
}
?>


