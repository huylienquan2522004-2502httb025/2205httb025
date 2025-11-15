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
    Session::setFlash('error', 'Không tìm thấy công việc hoặc bạn không có quyền xóa.');
    header('Location: ../index.php');
    exit;
}

// Xóa task
if ($taskModel->delete($taskId, $userId)) {
    Session::setFlash('success', 'Xóa công việc thành công!');
} else {
    Session::setFlash('error', 'Có lỗi xảy ra khi xóa công việc.');
}

header('Location: ../index.php');
exit;

