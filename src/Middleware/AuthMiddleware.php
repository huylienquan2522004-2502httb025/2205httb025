<?php


require_once __DIR__ . '/../Helpers/Session.php';

class AuthMiddleware {
    public static function check() {
        Session::start();
        
        if (!Session::has('user_id')) {
            header('Location: ' . BASE_URL . '/login.php');
            exit;
        }
    }

    public static function guest() {
        Session::start();
        
        if (Session::has('user_id')) {
            header('Location: ' . BASE_URL . '/dashboard/index.php');
            exit;
        }
    }
}
