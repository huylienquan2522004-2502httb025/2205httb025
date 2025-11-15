<?php


require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../src/Models/Tag.php';
require_once __DIR__ . '/../../src/Helpers/Session.php';
require_once __DIR__ . '/../../src/Middleware/AuthMiddleware.php';

// Kiểm tra đăng nhập
AuthMiddleware::check();

$userId = Session::get('user_id');
$username = Session::get('username');

$tagModel = new Tag();
$errors = [];

// Xử lý thêm tag
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create') {
        $name = trim($_POST['name'] ?? '');
        $color = $_POST['color'] ?? '#6B7280';

        if (empty($name)) {
            $errors['name'] = 'Vui lòng nhập tên tag';
        } elseif ($tagModel->nameExists($userId, $name)) {
            $errors['name'] = 'Tên tag đã tồn tại';
        }

        if (empty($errors)) {
            if ($tagModel->create($userId, $name, $color)) {
                Session::setFlash('success', 'Thêm tag thành công!');
                header('Location: tags.php');
                exit;
            }
        }
    } elseif ($_POST['action'] === 'delete') {
        $tagId = $_POST['tag_id'] ?? null;
        if ($tagId && $tagModel->delete($tagId, $userId)) {
            Session::setFlash('success', 'Xóa tag thành công!');
        } else {
            Session::setFlash('error', 'Không thể xóa tag.');
        }
        header('Location: tags.php');
        exit;
    }
}

// Lấy danh sách tags
$tags = $tagModel->getAllByUserId($userId);

$successMessage = Session::getFlash('success');
$errorMessage = Session::getFlash('error');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Tags - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Header -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="text-gray-600 hover:text-gray-900">← Dashboard</a>
                    <h1 class="text-2xl font-bold text-gray-900">Quản lý Tags</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="categories.php" class="text-blue-600 hover:text-blue-800">Quản lý Danh mục</a>
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

        <!-- Form thêm tag -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">Thêm tag mới</h2>
            <form method="POST" action="" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="hidden" name="action" value="create">
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên tag *</label>
                    <input type="text" id="name" name="name" required
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500 <?php echo isset($errors['name']) ? 'border-red-500' : ''; ?>"
                           placeholder="Ví dụ: Khẩn cấp, Quan trọng...">
                    <?php if (isset($errors['name'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['name']); ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Màu sắc</label>
                    <input type="color" id="color" name="color" value="#6B7280"
                           class="w-full h-10 border border-gray-300 rounded-md cursor-pointer">
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                        Thêm tag
                    </button>
                </div>
            </form>
        </div>

        <!-- Danh sách tags -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-bold">Danh sách tags (<?php echo count($tags); ?>)</h2>
            </div>
            
            <?php if (empty($tags)): ?>
                <div class="p-8 text-center">
                    <p class="text-gray-500">Chưa có tag nào. Hãy thêm tag đầu tiên!</p>
                </div>
            <?php else: ?>
                <div class="p-6">
                    <div class="flex flex-wrap gap-3">
                        <?php foreach ($tags as $tag): ?>
                            <div class="inline-flex items-center space-x-2 px-4 py-2 rounded-full text-white" style="background-color: <?php echo htmlspecialchars($tag['color']); ?>">
                                <span class="font-medium"><?php echo htmlspecialchars($tag['name']); ?></span>
                                <form method="POST" action="" class="inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa tag này?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="tag_id" value="<?php echo $tag['id']; ?>">
                                    <button type="submit" class="text-white hover:text-red-200 text-lg font-bold">
                                        ×
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Hướng dẫn nhanh -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="font-semibold text-blue-900 mb-2">💡 Gợi ý tags:</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm text-blue-800">
                <div>• Khẩn cấp</div>
                <div>• Quan trọng</div>
                <div>• Dự án</div>
                <div>• Họp</div>
                <div>• Viết báo cáo</div>
                <div>• Review</div>
                <div>• Bug</div>
                <div>• Feature</div>
            </div>
        </div>
    </div>
</body>
</html>

