<?php
session_start();
date_default_timezone_set('Africa/Kampala');

if (!defined('APP_NAME')) {
    define('APP_NAME', 'Thrift with Ramo');
}

if (!defined('WHATSAPP_NUMBER')) {
    define('WHATSAPP_NUMBER', '0761480601');
}

if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}

if (!defined('DB_NAME')) {
    define('DB_NAME', 'thrift');
}

if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}

if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/thrift');
}

if (!defined('UPLOAD_DIR')) {
    define('UPLOAD_DIR', __DIR__ . '/uploads/');
}

if (!defined('UPLOAD_URL')) {
    define('UPLOAD_URL', BASE_URL . '/uploads/');
}

if (!defined('DEFAULT_ADMIN_USERNAME')) {
    define('DEFAULT_ADMIN_USERNAME', 'admin');
}
