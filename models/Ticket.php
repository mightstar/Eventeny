<?php
require_once '../config/database.php';

class Ticket {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Create a new ticket
    public function create($data) {
        $sql = "INSERT INTO tickets (title, description, price, quantity, sale_start_date, sale_end_date, visibility, image_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['title'],
            $data['description'],
            $data['price'],
            $data['quantity'],
            $data['sale_start_date'],
            $data['sale_end_date'],
            $data['visibility'],
            $data['image_url'] ?? null
        ];
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    // Get all tickets (for organizer view)
    public function getAll() {
        $sql = "SELECT * FROM tickets ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Get public tickets (for customer view)
    public function getPublic() {
        $sql = "SELECT * FROM tickets WHERE visibility = 'public' AND sale_start_date <= NOW() AND sale_end_date >= NOW() ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Get ticket by ID
    public function getById($id) {
        $sql = "SELECT * FROM tickets WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    // Update ticket
    public function update($id, $data) {
        $sql = "UPDATE tickets SET title = ?, description = ?, price = ?, quantity = ?, 
                sale_start_date = ?, sale_end_date = ?, visibility = ?, image_url = ? 
                WHERE id = ?";
        
        $params = [
            $data['title'],
            $data['description'],
            $data['price'],
            $data['quantity'],
            $data['sale_start_date'],
            $data['sale_end_date'],
            $data['visibility'],
            $data['image_url'] ?? null,
            $id
        ];
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    // Delete ticket
    public function delete($id) {
        $sql = "DELETE FROM tickets WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    // Check if ticket is available for purchase
    public function isAvailable($id, $quantity = 1) {
        $ticket = $this->getById($id);
        if (!$ticket) return false;
        
        $now = date('Y-m-d H:i:s');
        return $ticket['visibility'] === 'public' && 
               $ticket['sale_start_date'] <= $now && 
               $ticket['sale_end_date'] >= $now &&
               $ticket['quantity'] >= $quantity;
    }
    
    // Get available quantity for a ticket
    public function getAvailableQuantity($id) {
        $ticket = $this->getById($id);
        if (!$ticket) return 0;
        
        $now = date('Y-m-d H:i:s');
        if ($ticket['visibility'] !== 'public' || 
            $ticket['sale_start_date'] > $now || 
            $ticket['sale_end_date'] < $now) {
            return 0;
        }
        
        return $ticket['quantity'];
    }
    
    // Reduce ticket quantity after successful order
    public function reduceQuantity($id, $quantity) {
        $sql = "UPDATE tickets SET quantity = quantity - ? WHERE id = ? AND quantity >= ?";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([$quantity, $id, $quantity]);
        
        // Check if any rows were affected
        if ($stmt->rowCount() === 0) {
            return false;
        }
        
        return $result;
    }
}
?>


