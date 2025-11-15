<?php


require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Helpers/Session.php';
require_once __DIR__ . '/../src/Middleware/AuthMiddleware.php';

// Kiểm tra nếu đã đăng nhập thì redirect về dashboard
AuthMiddleware::guest();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation
    if (empty($usernameOrEmail)) {
        $errors['username'] = 'Vui lòng nhập tên đăng nhập hoặc email';
    }

    if (empty($password)) {
        $errors['password'] = 'Vui lòng nhập mật khẩu';
    }

    // Xác thực user
    if (empty($errors)) {
        $userModel = new User();
        $user = $userModel->findByUsernameOrEmail($usernameOrEmail);

        if ($user && $userModel->verifyPassword($password, $user['password'])) {
            // Đăng nhập thành công - Lưu session
            Session::start();
            Session::set('user_id', $user['id']);
            Session::set('username', $user['username']);
            Session::set('email', $user['email']);

            // Redirect về dashboard
            header('Location: dashboard/index.php');
            exit;
        } else {
            $errors['general'] = 'Tên đăng nhập/email hoặc mật khẩu không đúng';
        }
    }
}

// Hiển thị flash message nếu có
$successMessage = Session::getFlash('success');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Đăng nhập vào tài khoản
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Hoặc
                <a href="register.php" class="font-medium text-blue-600 hover:text-blue-500">
                    đăng ký tài khoản mới
                </a>
            </p>
        </div>
        <form class="mt-8 space-y-6 bg-white p-8 rounded-lg shadow-md" method="POST" action="">
            <?php if (isset($errors['general'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <?php echo htmlspecialchars($errors['general']); ?>
                </div>
            <?php endif; ?>

            <?php if ($successMessage): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <?php echo htmlspecialchars($successMessage); ?>
                </div>
            <?php endif; ?>

            <div class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Tên đăng nhập hoặc Email</label>
                    <input id="username" name="username" type="text" required autofocus
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm <?php echo isset($errors['username']) ? 'border-red-500' : ''; ?>"
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                           placeholder="Nhập tên đăng nhập hoặc email">
                    <?php if (isset($errors['username'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['username']); ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Mật khẩu</label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm <?php echo isset($errors['password']) ? 'border-red-500' : ''; ?>"
                           placeholder="Nhập mật khẩu">
                    <?php if (isset($errors['password'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['password']); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Đăng nhập
                </button>
            </div>
        </form>
    </div>
</body>
</html>

