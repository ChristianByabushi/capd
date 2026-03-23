<?php
/**
 * config.php — loaded from environment or .env file
 * NEVER commit real credentials. Copy config.example.php → config.php and fill in values.
 */

// ── Load .env file if present (for local dev without server env vars) ──
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($val);
    }
}

function env(string $key, string $default = ''): string {
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

// ── Database ──────────────────────────────────────────────────
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', '')); #just in local and might change either!!!! 
define('DB_NAME', env('DB_NAME', 'capd_db')); # just in local

// ── App ───────────────────────────────────────────────────────
define('BASE_URL',    rtrim(env('BASE_URL', 'http://localhost/capd'), '/'));
define('UPLOAD_DIR',  __DIR__ . '/uploads/');
define('UPLOAD_URL',  BASE_URL . '/uploads/');
define('DEFAULT_LANG', env('DEFAULT_LANG', 'fr'));
define('APP_ENV',     env('APP_ENV', 'development'));

// ── Languages ─────────────────────────────────────────────────
define('LANGUAGES', ['fr' => 'Français', 'en' => 'English', 'sw' => 'Kiswahili']);

// ── Timezone ──────────────────────────────────────────────────
date_default_timezone_set('Africa/Kinshasa');

// ── Error reporting ───────────────────────────────────────────
if (APP_ENV === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// ── Security ──────────────────────────────────────────────────
define('CSRF_SECRET', env('CSRF_SECRET', 'change_me_in_env'));
