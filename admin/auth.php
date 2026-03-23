<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';

if (!session_id()) session_start();

// ── Basic session checks ──────────────────────────────────────

function isLoggedIn(): bool {
    return isset($_SESSION['admin_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

/** Require a minimum role level. Redirects with 403 if insufficient. */
function requireRole(string ...$roles): void {
    requireLogin();
    if (!in_array($_SESSION['admin_role'] ?? '', $roles, true)) {
        http_response_code(403);
        die('<h2 style="font-family:sans-serif;padding:2rem;color:#721c24">Accès refusé — vous n\'avez pas les droits nécessaires.</h2>');
    }
}

// ── Role helpers ──────────────────────────────────────────────

function getRole(): string {
    return $_SESSION['admin_role'] ?? '';
}

function isSuperAdmin(): bool {
    return getRole() === 'superadmin';
}

function isAdmin(): bool {
    return in_array(getRole(), ['superadmin', 'admin'], true);
}

function isEditor(): bool {
    return getRole() === 'editor';
}

/**
 * Permission matrix:
 *   superadmin → everything
 *   admin      → content + user management (editors only), NOT settings system-level, NOT delete superadmin/admin
 *   editor     → activities, blog, formations only (create/edit, NOT delete)
 */
function can(string $action): bool {
    $role = getRole();
    $matrix = [
        // Dashboard
        'view_dashboard'      => ['superadmin','admin','editor'],
        // Content
        'manage_hero'         => ['superadmin','admin'],
        'manage_activities'   => ['superadmin','admin','editor'],
        'manage_blog'         => ['superadmin','admin','editor'],
        'manage_formations'   => ['superadmin','admin','editor'],
        'delete_content'      => ['superadmin','admin'],
        'manage_feedbacks'    => ['superadmin','admin'],
        // Organisation
        'manage_members'      => ['superadmin','admin'],
        'manage_partners'     => ['superadmin','admin'],
        'manage_stats'        => ['superadmin','admin'],
        // System
        'view_messages'       => ['superadmin','admin'],
        'view_donations'      => ['superadmin','admin'],
        'manage_settings'     => ['superadmin'],
        // User management
        'manage_users'        => ['superadmin','admin'],
        'create_editor'       => ['superadmin','admin'],
        'reset_editor_pass'   => ['superadmin','admin'],
        'create_admin'        => ['superadmin'],
        'reset_admin_pass'    => ['superadmin'],
        'delete_user'         => ['superadmin'],
    ];
    return in_array($role, $matrix[$action] ?? [], true);
}

// ── Login / Logout ────────────────────────────────────────────

function adminLogin(string $username, string $password): bool {
    $user = fetchOne("SELECT * FROM admin_users WHERE username=? AND is_active=1", [$username]);
    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id']   = $user['id'];
        $_SESSION['admin_name'] = $user['full_name'];
        $_SESSION['admin_role'] = $user['role'];
        query("UPDATE admin_users SET last_login=NOW() WHERE id=?", [$user['id']]);
        return true;
    }
    return false;
}

function adminLogout(): void {
    session_destroy();
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}

// ── Password utilities ────────────────────────────────────────

/** Generate a strong random password */
function generatePassword(int $length = 12): string {
    $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789!@#$%';
    $pass = '';
    for ($i = 0; $i < $length; $i++) {
        $pass .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $pass;
}

/** Hash a password */
function hashPassword(string $plain): string {
    return password_hash($plain, PASSWORD_BCRYPT);
}
