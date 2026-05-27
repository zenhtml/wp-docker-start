<?php
define('DB_NAME',     getenv_docker('DB_NAME', 'wordpress'));
define('DB_USER',     getenv_docker('DB_USER', 'root'));
define('DB_PASSWORD', getenv_docker('DB_PASSWORD', ''));
define('DB_HOST',     getenv_docker('DB_HOST', 'mysql'));
define('DB_CHARSET',  'utf8mb4');
define('DB_COLLATE',  '');

$table_prefix = getenv_docker('WP_TABLE_PREFIX', 'wp_');

define('WP_DEBUG',         getenv_docker('WP_DEBUG', 'false') === 'true');
define('WP_DEBUG_LOG',     getenv_docker('WP_DEBUG_LOG', 'false') === 'true');
define('WP_DEBUG_DISPLAY', getenv_docker('WP_DEBUG_DISPLAY', 'false') === 'true');

define('WP_MEMORY_LIMIT',     '256M');
define('WP_MAX_MEMORY_LIMIT', '512M');

define('DISALLOW_FILE_EDIT', true);
define('AUTOMATIC_UPDATER_DISABLED', true);
define('WP_AUTO_UPDATE_CORE', false);

define('FORCE_SSL_ADMIN', false);

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

function getenv_docker(string $key, string $default = ''): string {
    $value = getenv($key);
    if ($value === false || $value === '') {
        return $default;
    }
    return $value;
}

require_once ABSPATH . 'wp-settings.php';
