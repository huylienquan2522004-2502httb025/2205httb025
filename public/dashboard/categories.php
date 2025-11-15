<?php


require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../src/Models/Category.php';
require_once __DIR__ . '/../../src/Helpers/Session.php';
require_once __DIR__ . '/../../src/Middleware/AuthMiddleware.php';

// Kiểm tra đăng nhập
AuthMiddleware::check();

$userId = Session::get('user_id');
$username = Session::get('username');

$categoryModel = new Category();
$errors = [];

// Xử lý thêm category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create') {
        $name = trim($_POST['name'] ?? '');
        $color = $_POST['color'] ?? '#3B82F6';
        $icon = trim($_POST['icon'] ?? '');

        if (empty($name)) {
            $errors['name'] = 'Vui lòng nhập tên danh mục';
        } elseif ($categoryModel->nameExists($userId, $name)) {
            $errors['name'] = 'Tên danh mục đã tồn tại';
        }

        if (empty($errors)) {
            if ($categoryModel->create($userId, $name, $color, $icon ?: null)) {
                Session::setFlash('success', 'Thêm danh mục thành công!');
                header('Location: categories.php');
                exit;
            }
        }
    } elseif ($_POST['action'] === 'delete') {
        $categoryId = $_POST['category_id'] ?? null;
        if ($categoryId && $categoryModel->delete($categoryId, $userId)) {
            Session::setFlash('success', 'Xóa danh mục thành công!');
        } else {
            Session::setFlash('error', 'Không thể xóa danh mục.');
        }
        header('Location: categories.php');
        exit;
    }
}

// Lấy danh sách categories
$categories = $categoryModel->getAllByUserId($userId);

$successMessage = Session::getFlash('success');
$errorMessage = Session::getFlash('error');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý danh mục - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Header -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="text-gray-600 hover:text-gray-900">← Dashboard</a>
                    <h1 class="text-2xl font-bold text-gray-900">Quản lý danh mục</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="tags.php" class="text-blue-600 hover:text-blue-800">Quản lý Tags</a>
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

        <!-- Form thêm category -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">Thêm danh mục mới</h2>
            <form method="POST" action="" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="hidden" name="action" value="create">
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên danh mục *</label>
                    <input type="text" id="name" name="name" required
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500 <?php echo isset($errors['name']) ? 'border-red-500' : ''; ?>"
                           placeholder="Ví dụ: Công việc, Cá nhân...">
                    <?php if (isset($errors['name'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['name']); ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Màu sắc</label>
                    <input type="color" id="color" name="color" value="#3B82F6"
                           class="w-full h-10 border border-gray-300 rounded-md cursor-pointer">
                </div>

                <div>
                    <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">Icon (emoji)</label>
                    <select id="icon" name="icon" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Chọn icon --</option>
                        <option value="💼">💼 Cặp (Công việc)</option>
                        <option value="👤">👤 Người (Cá nhân)</option>
                        <option value="📚">📚 Sách (Học tập)</option>
                        <option value="❤️">❤️ Tim (Sức khỏe)</option>
                        <option value="🛒">🛒 Giỏ hàng (Mua sắm)</option>
                        <option value="👨‍👩‍👧">👨‍👩‍👧 Gia đình</option>
                        <option value="🎯">🎯 Mục tiêu</option>
                        <option value="💻">💻 Máy tính</option>
                        <option value="📱">📱 Điện thoại</option>
                        <option value="🏠">🏠 Nhà</option>
                        <option value="✈️">✈️ Máy bay (Du lịch)</option>
                        <option value="🎨">🎨 Nghệ thuật</option>
                        <option value="⚽">⚽ Thể thao</option>
                        <option value="🍔">🍔 Đồ ăn</option>
                        <option value="🎵">🎵 Âm nhạc</option>
                        <option value="🎬">🎬 Phim</option>
                        <option value="📷">📷 Ảnh</option>
                        <option value="🚗">🚗 Xe</option>
                        <option value="💰">💰 Tiền</option>
                        <option value="⭐">⭐ Sao</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                        Thêm danh mục
                    </button>
                </div>
            </form>
        </div>

        <!-- Danh sách categories -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-bold">Danh sách danh mục (<?php echo count($categories); ?>)</h2>
            </div>
            
            <?php if (empty($categories)): ?>
                <div class="p-8 text-center">
                    <p class="text-gray-500">Chưa có danh mục nào. Hãy thêm danh mục đầu tiên!</p>
                </div>
            <?php else: ?>
                <div class="divide-y">
                    <?php foreach ($categories as $category): ?>
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center text-2xl" style="background-color: <?php echo htmlspecialchars($category['color']); ?>">
                                    <?php
                                    // Mapping text cũ sang emoji
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
                                    
                                    $displayIcon = $category['icon'];
                                    if ($displayIcon && isset($iconMap[$displayIcon])) {
                                        $displayIcon = $iconMap[$displayIcon];
                                    }
                                    
                                    echo $displayIcon ? htmlspecialchars($displayIcon) : '📁';
                                    ?>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900"><?php echo htmlspecialchars($category['name']); ?></h3>
                                    <p class="text-sm text-gray-500">Danh mục</p>
                                </div>
                            </div>
                            <form method="POST" action="" onsubmit="return confirm('Bạn chắc chắn muốn xóa danh mục này? Các tasks trong danh mục sẽ không bị xóa.')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                    🗑️ Xóa
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Hướng dẫn nhanh -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="font-semibold text-blue-900 mb-2">💡 Gợi ý danh mục nhanh:</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm text-blue-800">
                <div>💼 Công việc</div>
                <div>👤 Cá nhân</div>
                <div>📚 Học tập</div>
                <div>❤️ Sức khỏe</div>
                <div>🛒 Mua sắm</div>
                <div>👨‍👩‍👧 Gia đình</div>
                <div>🎯 Dự án</div>
                <div>📁 Khác</div>
            </div>
            <p class="text-xs text-blue-700 mt-2">💡 Mẹo: Chọn icon emoji phù hợp để dễ nhận diện danh mục!</p>
        </div>
    </div>
</body>
</html>

