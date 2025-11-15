<?php


require_once __DIR__ . '/../../config/database.php';

class Tag {
    private $pdo;

    public function __construct() {
        $this->pdo = getDB();
    }

    /**
     * Tạo tag mới
     */
    public function create($userId, $name, $color = '#6B7280') {
        $stmt = $this->pdo->prepare("
            INSERT INTO tags (user_id, name, color, created_at) 
            VALUES (?, ?, ?, NOW())
        ");

        return $stmt->execute([$userId, $name, $color]);
    }

    /**
     * Lấy tất cả tags của user
     */
    public function getAllByUserId($userId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM tags 
            WHERE user_id = ? 
            ORDER BY name ASC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy tag theo ID và user_id
     */
    public function findByIdAndUserId($tagId, $userId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM tags WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$tagId, $userId]);
        return $stmt->fetch();
    }

    /**
     * Cập nhật tag
     */
    public function update($tagId, $userId, $name, $color = null) {
        $stmt = $this->pdo->prepare("
            UPDATE tags 
            SET name = ?, color = ?
            WHERE id = ? AND user_id = ?
        ");

        return $stmt->execute([$name, $color, $tagId, $userId]);
    }

    /**
     * Xóa tag
     */
    public function delete($tagId, $userId) {
        $stmt = $this->pdo->prepare("
            DELETE FROM tags WHERE id = ? AND user_id = ?
        ");

        return $stmt->execute([$tagId, $userId]);
    }

    /**
     * Kiểm tra tag name đã tồn tại chưa (cho user)
     */
    public function nameExists($userId, $name, $excludeId = null) {
        $query = "SELECT COUNT(*) FROM tags WHERE user_id = ? AND name = ?";
        $params = [$userId, $name];
        
        if ($excludeId) {
            $query .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
}

