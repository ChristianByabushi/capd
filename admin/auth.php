<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';

if (!session_id()) session_start();

function isLoggedIn(): bool {
    return isset($_SESSION['admin_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

function adminLogin(string $username, string $password): bool {
    $user = fetchOne("SELECT * FROM admin_users WHERE username=?", [$username]);
    if ($user && password_verify($password, $user['password'])) {
        // Regenerate session ID on login to prevent session fixation
        session_regenerate_id(true);
        $_SESSION['admin_id']   = $user['id'];
        $_SESSION['admin_name'] = $user['full_name'];
        $_SESSION['admin_role'] = $user['role'];
        return true;
    }
    return false;
}

function adminLogout(): void {
    session_destroy();
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}

function isSuperAdmin(): bool {
    return ($_SESSION['admin_role'] ?? '') === 'superadmin';
}
