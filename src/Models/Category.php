<?php

require_once __DIR__ . '/../../config/database.php';

class Category {
    private $pdo;

    public function __construct() {
        $this->pdo = getDB();
    }

    /**
     * Tạo category mới
     */
    public function create($userId, $name, $color = '#3B82F6', $icon = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO categories (user_id, name, color, icon, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");

        return $stmt->execute([$userId, $name, $color, $icon]);
    }

    /**
     * Lấy tất cả categories của user
     */
    public function getAllByUserId($userId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM categories 
            WHERE user_id = ? 
            ORDER BY display_order ASC, name ASC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy category theo ID và user_id
     */
    public function findByIdAndUserId($categoryId, $userId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM categories WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$categoryId, $userId]);
        return $stmt->fetch();
    }

    /**
     * Cập nhật category
     */
    public function update($categoryId, $userId, $name, $color = null, $icon = null) {
        $stmt = $this->pdo->prepare("
            UPDATE categories 
            SET name = ?, color = ?, icon = ?, updated_at = NOW()
            WHERE id = ? AND user_id = ?
        ");

        return $stmt->execute([$name, $color, $icon, $categoryId, $userId]);
    }

    /**
     * Xóa category
     */
    public function delete($categoryId, $userId) {
        $stmt = $this->pdo->prepare("
            DELETE FROM categories WHERE id = ? AND user_id = ?
        ");

        return $stmt->execute([$categoryId, $userId]);
    }

    /**
     * Kiểm tra category name đã tồn tại chưa (cho user)
     */
    public function nameExists($userId, $name, $excludeId = null) {
        $query = "SELECT COUNT(*) FROM categories WHERE user_id = ? AND name = ?";
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

