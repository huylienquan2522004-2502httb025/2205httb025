<?php


require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../src/Models/Task.php';
require_once __DIR__ . '/../../../src/Helpers/Session.php';
require_once __DIR__ . '/../../../src/Middleware/AuthMiddleware.php';

// Kiểm tra đăng nhập
AuthMiddleware::check();

$userId = Session::get('user_id');

// Lấy task ID
$taskId = $_GET['id'] ?? null;

if (!$taskId) {
    Session::setFlash('error', 'Không tìm thấy công việc.');
    header('Location: ../index.php');
    exit;
}

$taskModel = new Task();

// Kiểm tra task có tồn tại và thuộc về user không
$task = $taskModel->findByIdAndUserId($taskId, $userId);

if (!$task) {
    Session::setFlash('error', 'Không tìm thấy công việc hoặc bạn không có quyền thao tác.');
    header('Location: ../index.php');
    exit;
}

// Đổi status thành cancelled
$pdo = getDB();
$stmt = $pdo->prepare("
    UPDATE tasks 
    SET status = 'cancelled', updated_at = NOW()
    WHERE id = ? AND user_id = ?
");

if ($stmt->execute([$taskId, $userId])) {
    Session::setFlash('success', 'Đã hủy công việc!');
} else {
    Session::setFlash('error', 'Có lỗi xảy ra.');
}

header('Location: ../index.php');
exit;

