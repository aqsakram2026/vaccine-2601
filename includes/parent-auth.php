<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id']['id'])) {
    header('Location: parent-login.php');
    exit;
}

$parentId = (int)$_SESSION['user_id']['id'];
$parentResult = mysqli_query(
    $connect,
    "SELECT id, name, email, profile_image FROM parents WHERE id = $parentId LIMIT 1"
);
if (!$parentResult) {
    error_log('E-Vaccine parent session lookup failed: ' . mysqli_error($connect));
    http_response_code(500);
    exit('Your account is temporarily unavailable.');
}

$parentProfile = mysqli_fetch_assoc($parentResult);
mysqli_free_result($parentResult);
if (!$parentProfile) {
    unset($_SESSION['user_id']);
    header('Location: parent-login.php');
    exit;
}

$_SESSION['user_id'] = [
    'id' => (int)$parentProfile['id'],
    'name' => (string)$parentProfile['name'],
    'email' => (string)$parentProfile['email'],
    'profile_image' => (string)($parentProfile['profile_image'] ?? '')
];
$parentName = (string)$parentProfile['name'];
$parentProfileImage = (string)($parentProfile['profile_image'] ?? '');
