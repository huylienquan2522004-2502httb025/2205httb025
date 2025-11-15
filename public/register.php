<?php


require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Helpers/Session.php';
require_once __DIR__ . '/../src/Middleware/AuthMiddleware.php';

// Kiểm tra nếu đã đăng nhập thì redirect về dashboard
AuthMiddleware::guest();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($username)) {
        $errors['username'] = 'Vui lòng nhập tên đăng nhập';
    } elseif (strlen($username) < 3) {
        $errors['username'] = 'Tên đăng nhập phải có ít nhất 3 ký tự';
    }

    if (empty($email)) {
        $errors['email'] = 'Vui lòng nhập email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email không hợp lệ';
    } else {
        // Email là bắt buộc trong schema mới
    }

    if (empty($password)) {
        $errors['password'] = 'Vui lòng nhập mật khẩu';
    } elseif (strlen($password) < PASSWORD_MIN_LENGTH) {
        $errors['password'] = 'Mật khẩu phải có ít nhất ' . PASSWORD_MIN_LENGTH . ' ký tự';
    }

    if ($password !== $confirmPassword) {
        $errors['confirm_password'] = 'Mật khẩu xác nhận không khớp';
    }

    // Kiểm tra username và email đã tồn tại chưa
    if (empty($errors)) {
        $userModel = new User();
        
        if ($userModel->usernameExists($username)) {
            $errors['username'] = 'Tên đăng nhập đã tồn tại';
        }
        
        if ($userModel->emailExists($email)) {
            $errors['email'] = 'Email đã tồn tại';
        }

        // Tạo user mới
        if (empty($errors)) {
            if ($userModel->create($username, $email, $password)) {
                Session::setFlash('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
                header('Location: login.php');
                exit;
            } else {
                $errors['general'] = 'Có lỗi xảy ra. Vui lòng thử lại.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Đăng ký tài khoản
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Hoặc
                <a href="login.php" class="font-medium text-blue-600 hover:text-blue-500">
                    đăng nhập nếu đã có tài khoản
                </a>
            </p>
        </div>
        <form class="mt-8 space-y-6 bg-white p-8 rounded-lg shadow-md" method="POST" action="">
            <?php if (isset($errors['general'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <?php echo htmlspecialchars($errors['general']); ?>
                </div>
            <?php endif; ?>

            <div class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Tên đăng nhập</label>
                    <input id="username" name="username" type="text" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm <?php echo isset($errors['username']) ? 'border-red-500' : ''; ?>"
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                           placeholder="Nhập tên đăng nhập">
                    <?php if (isset($errors['username'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['username']); ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" name="email" type="email" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm <?php echo isset($errors['email']) ? 'border-red-500' : ''; ?>"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           placeholder="Nhập email">
                    <?php if (isset($errors['email'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['email']); ?></p>
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

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Xác nhận mật khẩu</label>
                    <input id="confirm_password" name="confirm_password" type="password" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm <?php echo isset($errors['confirm_password']) ? 'border-red-500' : ''; ?>"
                           placeholder="Nhập lại mật khẩu">
                    <?php if (isset($errors['confirm_password'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['confirm_password']); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Đăng ký
                </button>
            </div>
        </form>
    </div>
</body>
</html>

