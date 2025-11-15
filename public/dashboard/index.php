<?php


require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../src/Models/Task.php';
require_once __DIR__ . '/../../src/Models/Category.php';
require_once __DIR__ . '/../../src/Helpers/Session.php';
require_once __DIR__ . '/../../src/Middleware/AuthMiddleware.php';

// Kiểm tra đăng nhập
AuthMiddleware::check();

$userId = Session::get('user_id');
$username = Session::get('username');

// Xử lý filter và sort
$statusFilter = $_GET['status'] ?? '';
$categoryFilter = $_GET['category'] ?? '';
$priorityFilter = $_GET['priority'] ?? '';
$sortBy = $_GET['sort'] ?? 'due_date';
$sortOrder = $_GET['order'] ?? 'ASC';

// Lấy danh sách tasks
$taskModel = new Task();
$tasks = $taskModel->getAllByUserId($userId, $statusFilter ?: null, $categoryFilter ?: null, $priorityFilter ?: null, $sortBy, $sortOrder);

// Lấy tags cho mỗi task
foreach ($tasks as &$task) {
    $task['tags'] = $taskModel->getTaskTags($task['id']);
}

// Lấy danh sách categories và tags
$categoryModel = new Category();
$categories = $categoryModel->getAllByUserId($userId);

// Đếm số lượng tasks theo status
$totalTasks = count($tasks);
$pendingCount = $taskModel->countByStatus($userId, 'pending');
$inProgressCount = $taskModel->countByStatus($userId, 'in_progress');
$completedCount = $taskModel->countByStatus($userId, 'completed');
$cancelledCount = $taskModel->countByStatus($userId, 'cancelled');
$overdueCount = $taskModel->countOverdue($userId);

// Flash messages
$successMessage = Session::getFlash('success');
$errorMessage = Session::getFlash('error');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Header -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900"><?php echo APP_NAME; ?></h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="categories.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">📁 Danh mục</a>
                    <a href="tags.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">🏷️ Tags</a>
                    <span class="text-gray-700">Xin chào, <strong><?php echo htmlspecialchars($username); ?></strong></span>
                    <a href="../logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Đăng xuất
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Messages -->
        <?php if ($successMessage): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-xs font-medium text-gray-500">Tổng số</div>
                <div class="mt-1 text-2xl font-bold text-gray-900"><?php echo $totalTasks; ?></div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-xs font-medium text-yellow-600">Chờ xử lý</div>
                <div class="mt-1 text-2xl font-bold text-yellow-600"><?php echo $pendingCount; ?></div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-xs font-medium text-blue-600">Đang làm</div>
                <div class="mt-1 text-2xl font-bold text-blue-600"><?php echo $inProgressCount; ?></div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-xs font-medium text-green-600">Hoàn thành</div>
                <div class="mt-1 text-2xl font-bold text-green-600"><?php echo $completedCount; ?></div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-xs font-medium text-gray-500">Đã hủy</div>
                <div class="mt-1 text-2xl font-bold text-gray-600"><?php echo $cancelledCount; ?></div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-xs font-medium text-red-600">Quá hạn</div>
                <div class="mt-1 text-2xl font-bold text-red-600"><?php echo $overdueCount; ?></div>
            </div>
        </div>

        <!-- Actions and Filters -->
        <div class="bg-white rounded-lg shadow mb-6 p-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <a href="tasks/create.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium inline-block text-center">
                    ➕ Thêm công việc mới
                </a>

                <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                    <!-- Filter by Status -->
                    <form method="GET" action="" class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700">Trạng thái:</label>
                        <select name="status" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="">Tất cả</option>
                            <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Chờ xử lý</option>
                            <option value="in_progress" <?php echo $statusFilter === 'in_progress' ? 'selected' : ''; ?>>Đang làm</option>
                            <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>Hoàn thành</option>
                            <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                        </select>
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($categoryFilter); ?>">
                        <input type="hidden" name="priority" value="<?php echo htmlspecialchars($priorityFilter); ?>">
                        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sortBy); ?>">
                        <input type="hidden" name="order" value="<?php echo htmlspecialchars($sortOrder); ?>">
                    </form>

                    <!-- Filter by Category -->
                    <?php if (!empty($categories)): ?>
                    <form method="GET" action="" class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700">Danh mục:</label>
                        <select name="category" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="">Tất cả</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $categoryFilter == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="status" value="<?php echo htmlspecialchars($statusFilter); ?>">
                        <input type="hidden" name="priority" value="<?php echo htmlspecialchars($priorityFilter); ?>">
                        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sortBy); ?>">
                        <input type="hidden" name="order" value="<?php echo htmlspecialchars($sortOrder); ?>">
                    </form>
                    <?php endif; ?>

                    <!-- Filter by Priority -->
                    <form method="GET" action="" class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700">Độ ưu tiên:</label>
                        <select name="priority" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="">Tất cả</option>
                            <option value="urgent" <?php echo $priorityFilter === 'urgent' ? 'selected' : ''; ?>>Khẩn cấp</option>
                            <option value="high" <?php echo $priorityFilter === 'high' ? 'selected' : ''; ?>>Cao</option>
                            <option value="medium" <?php echo $priorityFilter === 'medium' ? 'selected' : ''; ?>>Trung bình</option>
                            <option value="low" <?php echo $priorityFilter === 'low' ? 'selected' : ''; ?>>Thấp</option>
                        </select>
                        <input type="hidden" name="status" value="<?php echo htmlspecialchars($statusFilter); ?>">
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($categoryFilter); ?>">
                        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sortBy); ?>">
                        <input type="hidden" name="order" value="<?php echo htmlspecialchars($sortOrder); ?>">
                    </form>

                    <!-- Sort -->
                    <form method="GET" action="" class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700">Sắp xếp:</label>
                        <select name="sort" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="due_date" <?php echo $sortBy === 'due_date' ? 'selected' : ''; ?>>Ngày hết hạn</option>
                            <option value="priority" <?php echo $sortBy === 'priority' ? 'selected' : ''; ?>>Độ ưu tiên</option>
                            <option value="created_at" <?php echo $sortBy === 'created_at' ? 'selected' : ''; ?>>Ngày tạo</option>
                            <option value="title" <?php echo $sortBy === 'title' ? 'selected' : ''; ?>>Tiêu đề</option>
                            <option value="status" <?php echo $sortBy === 'status' ? 'selected' : ''; ?>>Trạng thái</option>
                        </select>
                        <select name="order" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="ASC" <?php echo $sortOrder === 'ASC' ? 'selected' : ''; ?>>Tăng dần</option>
                            <option value="DESC" <?php echo $sortOrder === 'DESC' ? 'selected' : ''; ?>>Giảm dần</option>
                        </select>
                        <input type="hidden" name="status" value="<?php echo htmlspecialchars($statusFilter); ?>">
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($categoryFilter); ?>">
                        <input type="hidden" name="priority" value="<?php echo htmlspecialchars($priorityFilter); ?>">
                    </form>
                </div>
            </div>
        </div>

        <!-- Tasks List -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <?php if (empty($tasks)): ?>
                <div class="p-8 text-center">
                    <p class="text-gray-500 text-lg">Chưa có công việc nào. Hãy thêm công việc mới!</p>
                    <a href="tasks/create.php" class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                        Thêm công việc đầu tiên
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
                        <?php foreach ($tasks as $task): ?>
                            <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow <?php echo $task['is_important'] ? 'border-yellow-400 bg-yellow-50' : 'border-gray-200 bg-white'; ?>">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 flex items-center">
                                            <?php if ($task['is_important']): ?>
                                                <span class="text-yellow-500 mr-1">⭐</span>
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($task['title']); ?>
                                        </h3>
                                    </div>
                                </div>
                                
                                <?php if ($task['description']): ?>
                                    <p class="text-sm text-gray-600 mb-3 line-clamp-2"><?php echo htmlspecialchars($task['description']); ?></p>
                                <?php endif; ?>

                                <div class="space-y-2 mb-3">
                                    <!-- Category -->
                                    <?php if ($task['category_name']): ?>
                                        <div class="flex items-center space-x-2">
                                            <div class="inline-flex items-center justify-center w-6 h-6 rounded text-xs" style="background-color: <?php echo htmlspecialchars($task['category_color'] ?? '#3B82F6'); ?>">
                                                <?php
                                                // Lấy icon từ category
                                                $categoryIcon = null;
                                                foreach ($categories as $cat) {
                                                    if ($cat['id'] == $task['category_id']) {
                                                        $categoryIcon = $cat['icon'];
                                                        break;
                                                    }
                                                }
                                                
                                                // Mapping text cũ sang emoji (backward compatibility)
                                                $iconMap = [
                                                    'briefcase' => '💼',
                                                    'user' => '👤',
                                                    'book' => '📚',
                                                    'heart' => '❤️',
                                                    'shopping-cart' => '🛒',
                                                    'users' => '👨‍👩‍👧',
                                                    'target' => '🎯',
                                                    'laptop' => '💻',
                                                    'smartphone' => '📱',
                                                    'home' => '🏠'
                                                ];
                                                
                                                // Nếu icon là text cũ, convert sang emoji
                                                if ($categoryIcon && isset($iconMap[$categoryIcon])) {
                                                    $categoryIcon = $iconMap[$categoryIcon];
                                                }
                                                
                                                echo $categoryIcon ? htmlspecialchars($categoryIcon) : '📁';
                                                ?>
                                            </div>
                                            <span class="text-xs text-gray-600"><?php echo htmlspecialchars($task['category_name']); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Priority -->
                                    <?php
                                    $priorityColors = [
                                        'urgent' => 'bg-red-100 text-red-800',
                                        'high' => 'bg-orange-100 text-orange-800',
                                        'medium' => 'bg-blue-100 text-blue-800',
                                        'low' => 'bg-gray-100 text-gray-800'
                                    ];
                                    $priorityLabels = [
                                        'urgent' => 'Khẩn cấp',
                                        'high' => 'Cao',
                                        'medium' => 'Trung bình',
                                        'low' => 'Thấp'
                                    ];
                                    $priority = $task['priority'] ?? 'medium';
                                    ?>
                                    <div>
                                        <span class="px-2 py-1 text-xs font-semibold rounded <?php echo $priorityColors[$priority] ?? 'bg-gray-100 text-gray-800'; ?>">
                                            <?php echo $priorityLabels[$priority] ?? $priority; ?>
                                        </span>
                                    </div>

                                    <!-- Tags -->
                                    <?php if (!empty($task['tags'])): ?>
                                        <div class="flex flex-wrap gap-1">
                                            <?php foreach ($task['tags'] as $tag): ?>
                                                <span class="px-2 py-1 text-xs rounded text-white" style="background-color: <?php echo htmlspecialchars($tag['color'] ?? '#6B7280'); ?>">
                                                    <?php echo htmlspecialchars($tag['name']); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Due Date -->
                                    <?php if ($task['due_date']): ?>
                                        <?php
                                        $dueDate = strtotime($task['due_date']);
                                        $today = strtotime('today');
                                        $isOverdue = $dueDate < $today && $task['status'] !== 'completed' && $task['status'] !== 'cancelled';
                                        ?>
                                        <div class="text-xs <?php echo $isOverdue ? 'text-red-600 font-semibold' : 'text-gray-600'; ?>">
                                            📅 <?php echo date('d/m/Y', $dueDate); ?>
                                            <?php if ($isOverdue): ?>
                                                <span class="ml-1">(Quá hạn)</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Status -->
                                <?php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'in_progress' => 'bg-blue-100 text-blue-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-gray-100 text-gray-800'
                                ];
                                $statusLabels = [
                                    'pending' => 'Chờ xử lý',
                                    'in_progress' => 'Đang làm',
                                    'completed' => 'Hoàn thành',
                                    'cancelled' => 'Đã hủy'
                                ];
                                $status = $task['status'];
                                ?>
                                <div class="mb-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $statusColors[$status] ?? 'bg-gray-100 text-gray-800'; ?>">
                                        <?php echo $statusLabels[$status] ?? $status; ?>
                                    </span>
                                </div>

                                <!-- Actions -->
                                <div class="flex flex-wrap gap-2 pt-2 border-t">
                                    <a href="tasks/edit.php?id=<?php echo $task['id']; ?>" class="text-xs text-blue-600 hover:text-blue-900">✏️ Sửa</a>
                                    
                                    <?php if ($task['status'] !== 'completed' && $task['status'] !== 'cancelled'): ?>
                                        <a href="tasks/complete.php?id=<?php echo $task['id']; ?>" class="text-xs text-green-600 hover:text-green-900" onclick="return confirm('Đánh dấu hoàn thành?')">✅ Hoàn thành</a>
                                        <a href="tasks/cancel.php?id=<?php echo $task['id']; ?>" class="text-xs text-orange-600 hover:text-orange-900" onclick="return confirm('Hủy công việc này?')">🚫 Hủy</a>
                                    <?php endif; ?>
                                    
                                    <a href="tasks/toggle-important.php?id=<?php echo $task['id']; ?>" class="text-xs text-yellow-600 hover:text-yellow-900">
                                        <?php echo $task['is_important'] ? '⭐ Bỏ đánh dấu' : '⭐ Đánh dấu'; ?>
                                    </a>
                                    
                                    <a href="tasks/delete.php?id=<?php echo $task['id']; ?>" class="text-xs text-red-600 hover:text-red-900" onclick="return confirm('Bạn chắc chắn muốn XÓA VĨNH VIỄN task này?')">🗑️ Xóa</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

