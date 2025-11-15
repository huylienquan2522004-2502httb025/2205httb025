<?php

require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../src/Models/Task.php';
require_once __DIR__ . '/../../../src/Models/Category.php';
require_once __DIR__ . '/../../../src/Models/Tag.php';
require_once __DIR__ . '/../../../src/Helpers/Session.php';
require_once __DIR__ . '/../../../src/Middleware/AuthMiddleware.php';

// Kiểm tra đăng nhập
AuthMiddleware::check();

$userId = Session::get('user_id');
$username = Session::get('username');

// Lấy danh sách categories và tags
$categoryModel = new Category();
$categories = $categoryModel->getAllByUserId($userId);

$tagModel = new Tag();
$tags = $tagModel->getAllByUserId($userId);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $dueDate = $_POST['due_date'] ?? null;
    $dueTime = $_POST['due_time'] ?? null;
    $status = $_POST['status'] ?? 'pending';
    $categoryId = $_POST['category_id'] ?? null;
    $priority = $_POST['priority'] ?? 'medium';
    $isImportant = isset($_POST['is_important']) ? 1 : 0;
    $tagIds = $_POST['tag_ids'] ?? [];

    // Validation
    if (empty($title)) {
        $errors['title'] = 'Vui lòng nhập tiêu đề công việc';
    }

    // Validate status (không cho tạo task cancelled, phải hủy sau)
    if (!in_array($status, ['pending', 'in_progress', 'completed'])) {
        $status = 'pending';
    }

    // Validate priority
    if (!in_array($priority, ['low', 'medium', 'high', 'urgent'])) {
        $priority = 'medium';
    }

    // Validate date
    if ($dueDate && !strtotime($dueDate)) {
        $errors['due_date'] = 'Ngày không hợp lệ';
    }

    // Tạo task
    if (empty($errors)) {
        $taskModel = new Task();
        if ($taskModel->create($userId, $title, $description ?: null, $dueDate ?: null, $dueTime ?: null, $status, $categoryId ?: null, $priority, $isImportant)) {
            // Lấy task ID vừa tạo
            require_once __DIR__ . '/../../../config/database.php';
            $newTaskId = getDB()->lastInsertId();
            
            // Gán tags
            if (!empty($tagIds)) {
                $taskModel->attachTags($newTaskId, $tagIds);
            }
            
            Session::setFlash('success', 'Thêm công việc thành công!');
            header('Location: ../index.php');
            exit;
        } else {
            $errors['general'] = 'Có lỗi xảy ra. Vui lòng thử lại.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm công việc - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Header -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="../index.php" class="text-gray-600 hover:text-gray-900 mr-4">← Quay lại</a>
                    <h1 class="text-2xl font-bold text-gray-900">Thêm công việc mới</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Xin chào, <strong><?php echo htmlspecialchars($username); ?></strong></span>
                    <a href="../../logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Đăng xuất
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow p-8">
            <?php if (isset($errors['general'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($errors['general']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Tiêu đề <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" required
                           class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500 <?php echo isset($errors['title']) ? 'border-red-500' : ''; ?>"
                           value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                           placeholder="Nhập tiêu đề công việc">
                    <?php if (isset($errors['title'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['title']); ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Mô tả</label>
                    <textarea id="description" name="description" rows="4"
                              class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Nhập mô tả chi tiết (tùy chọn)"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700">Ngày hết hạn</label>
                        <input type="date" id="due_date" name="due_date"
                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500 <?php echo isset($errors['due_date']) ? 'border-red-500' : ''; ?>"
                               value="<?php echo htmlspecialchars($_POST['due_date'] ?? ''); ?>">
                        <?php if (isset($errors['due_date'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['due_date']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="due_time" class="block text-sm font-medium text-gray-700">Giờ hết hạn</label>
                        <input type="time" id="due_time" name="due_time"
                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               value="<?php echo htmlspecialchars($_POST['due_time'] ?? ''); ?>">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Danh mục</label>
                        <select id="category_id" name="category_id"
                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700">Độ ưu tiên</label>
                        <select id="priority" name="priority"
                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="low" <?php echo ($_POST['priority'] ?? 'medium') === 'low' ? 'selected' : ''; ?>>Thấp</option>
                            <option value="medium" <?php echo ($_POST['priority'] ?? 'medium') === 'medium' ? 'selected' : ''; ?>>Trung bình</option>
                            <option value="high" <?php echo ($_POST['priority'] ?? 'medium') === 'high' ? 'selected' : ''; ?>>Cao</option>
                            <option value="urgent" <?php echo ($_POST['priority'] ?? 'medium') === 'urgent' ? 'selected' : ''; ?>>Khẩn cấp</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái</label>
                    <select id="status" name="status"
                            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="pending" <?php echo ($_POST['status'] ?? 'pending') === 'pending' ? 'selected' : ''; ?>>Chờ xử lý</option>
                        <option value="in_progress" <?php echo ($_POST['status'] ?? '') === 'in_progress' ? 'selected' : ''; ?>>Đang làm</option>
                        <option value="completed" <?php echo ($_POST['status'] ?? '') === 'completed' ? 'selected' : ''; ?>>Hoàn thành</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        <?php foreach ($tags as $tag): ?>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="tag_ids[]" value="<?php echo $tag['id']; ?>"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       <?php echo (isset($_POST['tag_ids']) && in_array($tag['id'], $_POST['tag_ids'])) ? 'checked' : ''; ?>>
                                <span class="text-sm text-gray-700"><?php echo htmlspecialchars($tag['name']); ?></span>
                            </label>
                        <?php endforeach; ?>
                        <?php if (empty($tags)): ?>
                            <p class="text-sm text-gray-500">Chưa có tags. Tạo tags mới trong quản lý tags.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="is_important" value="1"
                               class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500"
                               <?php echo isset($_POST['is_important']) ? 'checked' : ''; ?>>
                        <span class="text-sm font-medium text-gray-700">⭐ Đánh dấu quan trọng</span>
                    </label>
                </div>

                <div class="flex space-x-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium">
                        Thêm công việc
                    </button>
                    <a href="../index.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-md font-medium">
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

