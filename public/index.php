<?php


require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../src/Helpers/Session.php';
require_once __DIR__ . '/../src/Middleware/AuthMiddleware.php';

Session::start();

// Nếu đã đăng nhập thì redirect về dashboard
if (Session::has('user_id')) {
    header('Location: dashboard/index.php');
    exit;
} else {
    // Nếu chưa đăng nhập thì redirect về login
    header('Location: login.php');
    exit;
}

