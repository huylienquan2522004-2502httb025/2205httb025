<?php

require_once __DIR__ . '/../../config/database.php';

class User {
    private $pdo;

    public function __construct() {
        $this->pdo = getDB();
    }

    /**
     * Tạo user mới
     */
    public function create($username, $email, $password, $fullName = null) {
        // Băm mật khẩu
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare("
            INSERT INTO users (username, email, password, full_name, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");

        return $stmt->execute([$username, $email, $hashedPassword, $fullName]);
    }

    /**
     * Tìm user theo username
     */
    public function findByUsername($username) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM users WHERE username = ?
        ");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    /**
     * Tìm user theo email
     */
    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM users WHERE email = ?
        ");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Tìm user theo username hoặc email
     */
    public function findByUsernameOrEmail($usernameOrEmail) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM users WHERE username = ? OR email = ?
        ");
        $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
        return $stmt->fetch();
    }

    /**
     * Kiểm tra username đã tồn tại chưa
     */
    public function usernameExists($username) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM users WHERE username = ?
        ");
        $stmt->execute([$username]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Kiểm tra email đã tồn tại chưa
     */
    public function emailExists($email) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM users WHERE email = ?
        ");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Xác thực mật khẩu
     */
    public function verifyPassword($password, $hashedPassword) {
        return password_verify($password, $hashedPassword);
    }

    /**
     * Lấy user theo ID
     */
    public function findById($id) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM users WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}

