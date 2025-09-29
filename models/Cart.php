<?php
require_once '../config/database.php';

class Cart {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Add item to cart
    public function addItem($sessionId, $ticketId, $quantity) {
        // Check if item already exists in cart
        $existing = $this->getItem($sessionId, $ticketId);
        
        if ($existing) {
            // Update quantity
            $newQuantity = $existing['quantity'] + $quantity;
            return $this->updateQuantity($sessionId, $ticketId, $newQuantity);
        } else {
            // Add new item
            $sql = "INSERT INTO cart_items (session_id, ticket_id, quantity) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$sessionId, $ticketId, $quantity]);
        }
    }
    
    // Get cart items with ticket details
    public function getCartItems($sessionId) {
        $sql = "SELECT ci.*, t.title, t.price, t.image_url, t.description 
                FROM cart_items ci 
                JOIN tickets t ON ci.ticket_id = t.id 
                WHERE ci.session_id = ? 
                ORDER BY ci.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sessionId]);
        return $stmt->fetchAll();
    }
    
    // Get specific cart item
    public function getItem($sessionId, $ticketId) {
        $sql = "SELECT * FROM cart_items WHERE session_id = ? AND ticket_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sessionId, $ticketId]);
        return $stmt->fetch();
    }
    
    // Update item quantity
    public function updateQuantity($sessionId, $ticketId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeItem($sessionId, $ticketId);
        }
        
        $sql = "UPDATE cart_items SET quantity = ? WHERE session_id = ? AND ticket_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$quantity, $sessionId, $ticketId]);
    }
    
    // Remove item from cart
    public function removeItem($sessionId, $ticketId) {
        $sql = "DELETE FROM cart_items WHERE session_id = ? AND ticket_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$sessionId, $ticketId]);
    }
    
    // Clear entire cart
    public function clearCart($sessionId) {
        $sql = "DELETE FROM cart_items WHERE session_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$sessionId]);
    }
    
    // Get cart total
    public function getCartTotal($sessionId) {
        $items = $this->getCartItems($sessionId);
        $total = 0;
        
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return $total;
    }
    
    // Get cart item count
    public function getCartItemCount($sessionId) {
        $sql = "SELECT SUM(quantity) as total FROM cart_items WHERE session_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sessionId]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}
?>


