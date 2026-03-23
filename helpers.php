<?php
require_once __DIR__ . '/config.php';

// Language handling
function initLang(): string {
    if (!session_id()) session_start();
    $supported = array_keys(LANGUAGES);
    if (isset($_GET['lang']) && in_array($_GET['lang'], $supported)) {
        $_SESSION['lang'] = $_GET['lang'];
    }
    if (!isset($_SESSION['lang'])) {
        $_SESSION['lang'] = DEFAULT_LANG;
    }
    return $_SESSION['lang'];
}

function getLang(): string {
    if (!session_id()) session_start();
    return $_SESSION['lang'] ?? DEFAULT_LANG;
}

// Load translations
function t(string $key): string {
    static $strings = null;
    if ($strings === null) {
        $lang = getLang();
        $file = __DIR__ . "/lang/{$lang}.php";
        $strings = file_exists($file) ? require $file : require __DIR__ . '/lang/fr.php';
    }
    return $strings[$key] ?? $key;
}

// Get localized field from DB row (e.g. title_fr / title_en / title_sw)
function loc(array $row, string $field): string {
    $lang = getLang();
    return $row["{$field}_{$lang}"] ?? $row["{$field}_fr"] ?? '';
}

// Sanitize output
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Generate slug (no intl extension required)
function slugify(string $text): string {
    $text = mb_strtolower($text, 'UTF-8');
    $map = [
        'à'=>'a','á'=>'a','â'=>'a','ã'=>'a','ä'=>'a','å'=>'a','æ'=>'ae',
        'ç'=>'c','è'=>'e','é'=>'e','ê'=>'e','ë'=>'e','ì'=>'i','í'=>'i',
        'î'=>'i','ï'=>'i','ð'=>'d','ñ'=>'n','ò'=>'o','ó'=>'o','ô'=>'o',
        'õ'=>'o','ö'=>'o','ø'=>'o','ù'=>'u','ú'=>'u','û'=>'u','ü'=>'u',
        'ý'=>'y','þ'=>'th','ÿ'=>'y','œ'=>'oe','ß'=>'ss',
    ];
    $text = strtr($text, $map);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

// Upload file helper — validates extension AND MIME type
function uploadFile(string $inputName, string $subDir = ''): ?string {
    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) return null;

    $allowedExts  = ['jpg','jpeg','png','gif','webp','pdf','mp4'];
    $allowedMimes = [
        'image/jpeg','image/png','image/gif','image/webp',
        'application/pdf','video/mp4',
    ];

    $ext  = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
    $mime = mime_content_type($_FILES[$inputName]['tmp_name']);

    if (!in_array($ext, $allowedExts, true) || !in_array($mime, $allowedMimes, true)) return null;

    // Extra: block PHP/script content disguised as images
    if (str_starts_with($mime, 'image/')) {
        $imgInfo = @getimagesize($_FILES[$inputName]['tmp_name']);
        if ($imgInfo === false) return null; // not a real image
    }

    $dir = UPLOAD_DIR . ($subDir ? $subDir . '/' : '');
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $filename = bin2hex(random_bytes(8)) . '.' . $ext;
    move_uploaded_file($_FILES[$inputName]['tmp_name'], $dir . $filename);
    return ($subDir ? $subDir . '/' : '') . $filename;
}

// Redirect
function redirect(string $url): void {
    header("Location: $url");
    exit;
}

// ── CSRF protection ───────────────────────────────────────────

/** Generate (or reuse) a CSRF token for the current session */
function csrfToken(): string {
    if (!session_id()) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Render a hidden CSRF input — use inside every POST form */
function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}

/** Verify CSRF token; dies with 403 on failure */
function csrfVerify(): void {
    if (!session_id()) session_start();
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Requête invalide (CSRF). Veuillez recharger la page et réessayer.');
    }
}

// ── Input sanitization helpers ────────────────────────────────

/** Cast to int, optionally clamp to min/max */
function intInput(string $key, int $default = 0, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX): int {
    $v = (int)($_POST[$key] ?? $default);
    return max($min, min($max, $v));
}

/** Trim a POST string, optionally strip tags */
function strInput(string $key, string $default = '', bool $stripTags = true): string {
    $v = trim($_POST[$key] ?? $default);
    return $stripTags ? strip_tags($v) : $v;
}

// Active nav link
function activeClass(string $page): string {
    $current = basename($_SERVER['PHP_SELF'], '.php');
    return $current === $page ? 'active' : '';
}

// Format date localized
function formatDate(string $date): string {
    $lang = getLang();
    $ts = strtotime($date);
    $locales = ['fr' => 'fr_FR', 'en' => 'en_US', 'sw' => 'sw_KE'];
    return date('d/m/Y', $ts);
}

// Truncate text
function truncate(string $text, int $length = 150): string {
    $text = strip_tags($text);
    return mb_strlen($text) > $length ? mb_substr($text, 0, $length) . '...' : $text;
}

// Convert any YouTube URL to embed HTML
// Accepts: youtu.be/ID, youtube.com/watch?v=ID, youtube.com/embed/ID, or plain ID
function youtubeEmbed(string $url, string $title = '', int $width = 0): string {
    if (empty(trim($url))) return '';
    // Extract video ID
    $id = '';
    if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([a-zA-Z0-9_-]{11})/', $url, $m)) {
        $id = $m[1];
    } elseif (preg_match('/^[a-zA-Z0-9_-]{11}$/', trim($url))) {
        $id = trim($url);
    }
    if (!$id) return '';
    $titleAttr = $title ? htmlspecialchars($title, ENT_QUOTES) : 'Vidéo YouTube';
    $style = $width ? "max-width:{$width}px;" : '';
    return '<div class="yt-embed" style="'.$style.'">
      <iframe src="https://www.youtube.com/embed/'.htmlspecialchars($id).'" 
        title="'.$titleAttr.'" frameborder="0" allowfullscreen
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
      </iframe>
    </div>';
}
