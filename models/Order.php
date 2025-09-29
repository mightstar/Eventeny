<?php
require_once '../config/database.php';

class Order {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    public function getConnection() {
        return $this->db;
    }
    
    // Create order from cart
    public function createFromCart($sessionId, $cartItems) {
        // Calculate total
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        // Create order
        $sql1 = "INSERT INTO orders (session_id, total_amount, status) VALUES (?, ?, 'completed')";
        $stmt1 = $this->db->prepare($sql1);
        $stmt1->execute([$sessionId, $total]);
        $orderId = $this->db->lastInsertId();
        
        // Create order items
        foreach ($cartItems as $item) {
            $sql2 = "INSERT INTO order_items (order_id, ticket_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute([$orderId, $item['ticket_id'], $item['quantity'], $item['price']]);
        }

        return $orderId;
    }
    
    // Get order by ID
    public function getById($orderId) {
        $sql = "SELECT * FROM orders WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetch();
    }
    
    // Get order items with ticket details
    public function getOrderItems($orderId) {
        $sql = "SELECT oi.*, t.title, t.image_url 
                FROM order_items oi 
                JOIN tickets t ON oi.ticket_id = t.id 
                WHERE oi.order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
    
    // Get orders by session
    public function getBySession($sessionId) {
        $sql = "SELECT * FROM orders WHERE session_id = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sessionId]);
        return $stmt->fetchAll();
    }
}
?>


