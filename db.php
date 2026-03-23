<?php
require_once __DIR__ . '/config.php';

function db(): mysqli {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            _dbError('Database connection failed');
        }
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}

/** Internal: show or log DB errors safely */
function _dbError(string $msg, string $detail = ''): void {
    if (APP_ENV !== 'production') {
        die(htmlspecialchars($msg . ($detail ? ': ' . $detail : ''), ENT_QUOTES));
    }
    error_log('[CAPD DB] ' . $msg . ($detail ? ': ' . $detail : ''));
    die('Une erreur est survenue. Veuillez réessayer plus tard.');
}

/**
 * Auto-derive mysqli type string from a params array.
 * int/bool → 'i', float → 'd', everything else → 's'
 */
function _buildTypes(array $params): string {
    $types = '';
    foreach ($params as $v) {
        if (is_int($v) || is_bool($v))   $types .= 'i';
        elseif (is_float($v))            $types .= 'd';
        else                             $types .= 's'; // string, null, etc.
    }
    return $types;
}

/**
 * Execute a prepared query.
 * Types are now auto-derived — just pass the params array.
 * Usage: query("SELECT * FROM t WHERE id=?", [42])
 *        query("SELECT * FROM t")
 * Legacy 3-arg form still accepted: query($sql, 'i', [42])
 */
function query(string $sql, $typesOrParams = '', array $params = []) {
    $db = db();

    // Normalise: accept both query($sql, $params) and legacy query($sql, 'i', $params)
    if (is_array($typesOrParams)) {
        $params = $typesOrParams;
        $types  = _buildTypes($params);
    } else {
        $types = $typesOrParams; // legacy explicit string (ignored if empty)
        if ($types === '' && count($params) > 0) {
            $types = _buildTypes($params);
        }
    }

    if ($types !== '' && count($params) > 0) {
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            _dbError('Prepare failed', $db->error);
        }
        $stmt->bind_param($types, ...$params);
        if (!$stmt->execute()) {
            _dbError('Execute failed', $stmt->error);
        }
        $result = $stmt->get_result();
        return $result !== false ? $result : true;
    }

    $result = $db->query($sql);
    if ($result === false) {
        _dbError('Query failed', $db->error);
    }
    return $result;
}

/** Fetch all rows as associative array */
function fetchAll(string $sql, $typesOrParams = '', array $params = []): array {
    $result = query($sql, $typesOrParams, $params);
    if ($result instanceof mysqli_result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

/** Fetch single row */
function fetchOne(string $sql, $typesOrParams = '', array $params = []): ?array {
    $result = query($sql, $typesOrParams, $params);
    if ($result instanceof mysqli_result) {
        return $result->fetch_assoc() ?: null;
    }
    return null;
}

/** Last inserted ID */
function lastInsertId(): int {
    return (int) db()->insert_id;
}

/** Get a setting value by key */
function getSetting(string $key): string {
    $row = fetchOne("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
    return $row['setting_value'] ?? '';
}
