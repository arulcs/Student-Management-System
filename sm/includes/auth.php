<?php
require_once __DIR__ . '/../config/config.php';

function current_user() {
    return $_SESSION['user'] ?? null;
}

function require_login() {
    if (!current_user()) {
        header('Location: ' . APP_BASE_URL . '/public/');
        exit;
    }
}

function require_role($role) {
    require_login();
    if ((current_user()['role'] ?? '') !== $role) {
        http_response_code(403);
        echo 'Access denied';
        exit;
    }
}
