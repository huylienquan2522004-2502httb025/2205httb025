<?php


require_once __DIR__ . '/../../config/database.php';

class Task {
    private $pdo;

    public function __construct() {
        $this->pdo = getDB();
    }

    /**
     * Tạo task mới
     */
    public function create($userId, $title, $description = null, $dueDate = null, $dueTime = null, $status = 'pending', $categoryId = null, $priority = 'medium', $isImportant = 0) {
        $stmt = $this->pdo->prepare("
            INSERT INTO tasks (user_id, category_id, title, description, due_date, due_time, priority, status, is_important, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        return $stmt->execute([$userId, $categoryId, $title, $description, $dueDate, $dueTime, $priority, $status, $isImportant]);
    }

    /**
     * Lấy tất cả tasks của user với thông tin category
     */
    public function getAllByUserId($userId, $status = null, $categoryId = null, $priority = null, $sortBy = 'due_date', $sortOrder = 'ASC') {
        $query = "SELECT t.*, c.name AS category_name, c.color AS category_color 
                  FROM tasks t 
                  LEFT JOIN categories c ON t.category_id = c.id 
                  WHERE t.user_id = ?";
        $params = [$userId];

        // Filter theo status
        if ($status && in_array($status, ['pending', 'in_progress', 'completed', 'cancelled'])) {
            $query .= " AND t.status = ?";
            $params[] = $status;
        }

        // Filter theo category
        if ($categoryId) {
            $query .= " AND t.category_id = ?";
            $params[] = $categoryId;
        }

        // Filter theo priority
        if ($priority && in_array($priority, ['low', 'medium', 'high', 'urgent'])) {
            $query .= " AND t.priority = ?";
            $params[] = $priority;
        }

        // Sort
        $allowedSorts = ['due_date', 'created_at', 'title', 'status', 'priority', 'is_important'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'due_date';
        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

        // Sort by multiple fields for better UX
        if ($sortBy === 'priority') {
            $query .= " ORDER BY FIELD(t.priority, 'urgent', 'high', 'medium', 'low') $sortOrder, t.due_date ASC";
        } else {
            $query .= " ORDER BY t.is_important DESC, t.$sortBy $sortOrder";
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy task theo ID và user_id (đảm bảo user chỉ xem task của mình)
     */
    public function findByIdAndUserId($taskId, $userId) {
        $stmt = $this->pdo->prepare("
            SELECT t.*, c.name AS category_name, c.color AS category_color 
            FROM tasks t 
            LEFT JOIN categories c ON t.category_id = c.id 
            WHERE t.id = ? AND t.user_id = ?
        ");
        $stmt->execute([$taskId, $userId]);
        return $stmt->fetch();
    }

    /**
     * Lấy tags của task
     */
    public function getTaskTags($taskId) {
        $stmt = $this->pdo->prepare("
            SELECT tg.* FROM tags tg
            INNER JOIN task_tags tt ON tg.id = tt.tag_id
            WHERE tt.task_id = ?
        ");
        $stmt->execute([$taskId]);
        return $stmt->fetchAll();
    }

    /**
     * Gán tags cho task
     */
    public function attachTags($taskId, $tagIds) {
        // Xóa tags cũ
        $stmt = $this->pdo->prepare("DELETE FROM task_tags WHERE task_id = ?");
        $stmt->execute([$taskId]);

        // Thêm tags mới
        if (!empty($tagIds)) {
            $stmt = $this->pdo->prepare("INSERT INTO task_tags (task_id, tag_id) VALUES (?, ?)");
            foreach ($tagIds as $tagId) {
                $stmt->execute([$taskId, $tagId]);
            }
        }
        return true;
    }

    /**
     * Cập nhật task
     */
    public function update($taskId, $userId, $title, $description = null, $dueDate = null, $dueTime = null, $status = 'pending', $categoryId = null, $priority = 'medium', $isImportant = 0) {
        $stmt = $this->pdo->prepare("
            UPDATE tasks 
            SET title = ?, description = ?, due_date = ?, due_time = ?, status = ?, 
                category_id = ?, priority = ?, is_important = ?, updated_at = NOW()
            WHERE id = ? AND user_id = ?
        ");

        return $stmt->execute([$title, $description, $dueDate, $dueTime, $status, $categoryId, $priority, $isImportant, $taskId, $userId]);
    }

    /**
     * Xóa task
     */
    public function delete($taskId, $userId) {
        $stmt = $this->pdo->prepare("
            DELETE FROM tasks WHERE id = ? AND user_id = ?
        ");

        return $stmt->execute([$taskId, $userId]);
    }

    /**
     * Đánh dấu task hoàn thành
     */
    public function markAsCompleted($taskId, $userId) {
        $stmt = $this->pdo->prepare("
            UPDATE tasks 
            SET status = 'completed', completed_at = NOW(), updated_at = NOW()
            WHERE id = ? AND user_id = ?
        ");

        return $stmt->execute([$taskId, $userId]);
    }

    /**
     * Toggle is_important
     */
    public function toggleImportant($taskId, $userId) {
        // Lấy giá trị hiện tại
        $task = $this->findByIdAndUserId($taskId, $userId);
        if (!$task) return false;

        $newValue = $task['is_important'] ? 0 : 1;
        $stmt = $this->pdo->prepare("
            UPDATE tasks 
            SET is_important = ?, updated_at = NOW()
            WHERE id = ? AND user_id = ?
        ");

        return $stmt->execute([$newValue, $taskId, $userId]);
    }

    /**
     * Đếm số lượng tasks theo status
     */
    public function countByStatus($userId, $status) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM tasks WHERE user_id = ? AND status = ?
        ");
        $stmt->execute([$userId, $status]);
        return $stmt->fetchColumn();
    }

    /**
     * Đếm tasks quá hạn
     */
    public function countOverdue($userId) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM tasks 
            WHERE user_id = ? 
            AND due_date < CURDATE() 
            AND status != 'completed' 
            AND status != 'cancelled'
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }
}

