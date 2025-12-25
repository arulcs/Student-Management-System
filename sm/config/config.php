<?php
// Global configuration
const DB_HOST = '127.0.0.1';
const DB_NAME = 'sms_db';
const DB_USER = 'root';
const DB_PASS = '';

// App settings
const APP_NAME = 'Student Management System';
const APP_BASE_URL = '/sm'; // change if different folder
const APP_DEBUG = true; // set false in production

if (APP_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

session_start();

// Simple CSRF helper
function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_check($token) {
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token ?? '');
}
