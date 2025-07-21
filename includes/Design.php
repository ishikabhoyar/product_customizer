<?php
class Design {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function saveDesign($userId, $type, $name, $designPath, $color) {
        $query = "INSERT INTO designs (user_id, type, name, design_path, color)
                 VALUES (:user_id, :type, :name, :design_path, :color)
                 RETURNING id";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':user_id' => $userId,
                ':type' => $type,
                ':name' => $name,
                ':design_path' => $designPath,
                ':color' => $color
            ]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Failed to save design: " . $e->getMessage());
        }
    }

    public function getUserDesigns($userId, $type = null) {
        $query = "SELECT * FROM designs WHERE user_id = :user_id";
        if ($type) {
            $query .= " AND type = :type";
        }
        $query .= " ORDER BY created_at DESC";

        try {
            $stmt = $this->db->prepare($query);
            $params = [':user_id' => $userId];
            if ($type) {
                $params[':type'] = $type;
            }
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Failed to fetch designs: " . $e->getMessage());
        }
    }

    // Count total designs for a user
    public function countUserDesigns($userId) {
        $query = "SELECT COUNT(*) FROM designs WHERE user_id = :user_id";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Failed to count designs: " . $e->getMessage());
        }
    }

    // Count designs by type for a user
    public function countUserDesignsByType($userId, $type) {
        $query = "SELECT COUNT(*) FROM designs WHERE user_id = :user_id AND type = :type";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':user_id' => $userId,
                ':type' => $type
            ]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Failed to count designs by type: " . $e->getMessage());
        }
    }

    // Get recent designs with more details
    public function getRecentDesigns($userId, $limit = 5) {
        $query = "SELECT d.id, d.name, d.type, d.design_path, d.color, d.created_at,
                 CASE WHEN c.design_id IS NOT NULL THEN 'In Cart' ELSE 'Published' END as status
                 FROM designs d
                 LEFT JOIN cart_items c ON d.id = c.design_id AND c.user_id = d.user_id
                 WHERE d.user_id = :user_id
                 ORDER BY d.created_at DESC
                 LIMIT :limit";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Failed to fetch recent designs: " . $e->getMessage());
        }
    }

    // Get design statistics
    public function getDesignStats($userId) {
        // Get counts for each type
        $tshirtCount = $this->countUserDesignsByType($userId, 'tshirt');
        $watchCount = $this->countUserDesignsByType($userId, 'watch');
        $phoneCount = $this->countUserDesignsByType($userId, 'phone');
        $totalCount = $this->countUserDesigns($userId);

        // Calculate percentages for growth indicators (mock data for now)
        // In a real app, you might compare with previous period counts
        return [
            'total' => [
                'count' => $totalCount,
                'growth' => 12 // percentage
            ],
            'tshirt' => [
                'count' => $tshirtCount,
                'growth' => 8
            ],
            'watch' => [
                'count' => $watchCount,
                'growth' => 15
            ],
            'phone' => [
                'count' => $phoneCount,
                'growth' => 5
            ]
        ];
    }

    // Get a design by ID
    public function getDesignById($designId, $userId) {
        $query = "SELECT * FROM designs WHERE id = :id AND user_id = :user_id";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':id' => $designId,
                ':user_id' => $userId
            ]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception("Failed to fetch design: " . $e->getMessage());
        }
    }

    // Update a design
    public function updateDesign($designId, $userId, $data) {
        $query = "UPDATE designs SET
                 name = :name,
                 type = :type,
                 color = :color
                 WHERE id = :id AND user_id = :user_id";

        try {
            $stmt = $this->db->prepare($query);
            $params = [
                ':id' => $designId,
                ':user_id' => $userId,
                ':name' => $data['name'],
                ':type' => $data['type'],
                ':color' => $data['color']
            ];
            $stmt->execute($params);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Failed to update design: " . $e->getMessage());
        }
    }

    // Delete a design
    public function deleteDesign($designId, $userId) {
        $query = "DELETE FROM designs WHERE id = :id AND user_id = :user_id";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':id' => $designId,
                ':user_id' => $userId
            ]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Failed to delete design: " . $e->getMessage());
        }
    }
}