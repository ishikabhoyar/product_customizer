<?php
class Cart {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function addItem($userId, $designId, $quantity = 1) {
        $query = "INSERT INTO cart_items (user_id, design_id, quantity) 
                 VALUES (:user_id, :design_id, :quantity)";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':user_id' => $userId,
                ':design_id' => $designId,
                ':quantity' => $quantity
            ]);
            return true;
        } catch (PDOException $e) {
            throw new Exception("Failed to add item to cart: " . $e->getMessage());
        }
    }

    public function getCartItems($userId) {
        $query = "SELECT ci.*, d.name, d.design_path, d.color 
                 FROM cart_items ci 
                 JOIN designs d ON ci.design_id = d.id 
                 WHERE ci.user_id = :user_id";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Failed to fetch cart items: " . $e->getMessage());
        }
    }
}