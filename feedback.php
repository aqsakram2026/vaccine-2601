<?php
session_start();

function feedback_redirect($variant, $message)
{
    $_SESSION['feedback_flash'] = ['variant' => $variant, 'message' => $message];
    header('Location: index.php#reviews');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Method not allowed.');
}

$csrf = '';
if (isset($_POST['csrf']) && is_string($_POST['csrf'])) {
    $csrf = $_POST['csrf'];
}
if (empty($_SESSION['feedback_csrf']) || $csrf === '' || !hash_equals($_SESSION['feedback_csrf'], $csrf)) {
    feedback_redirect('danger', 'Your form session expired. Please reopen the review form and try again.');
}

if (!empty($_POST['website'])) {
    http_response_code(400);
    exit('Invalid form submission.');
}

$displayName = '';
if (isset($_POST['display_name']) && is_string($_POST['display_name'])) {
    $displayName = trim($_POST['display_name']);
}
$role = '';
if (isset($_POST['role']) && is_string($_POST['role'])) {
    $role = $_POST['role'];
}
$review = '';
if (isset($_POST['review']) && is_string($_POST['review'])) {
    $review = trim($_POST['review']);
}
$rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
$publicConsent = isset($_POST['public_consent']) && $_POST['public_consent'] === '1';
$roles = ['Parent', 'Hospital', 'Other'];
$displayNameLength = strlen($displayName);
$reviewLength = strlen($review);
if (function_exists('mb_strlen')) {
    $displayNameLength = mb_strlen($displayName, 'UTF-8');
    $reviewLength = mb_strlen($review, 'UTF-8');
}

if ($displayNameLength > 60 || !in_array($role, $roles, true)
    || $rating === false || $rating === null || $rating < 1 || $rating > 5
    || $reviewLength < 5 || $reviewLength > 700 || !$publicConsent) {
    feedback_redirect('danger', 'Please check the name, role, rating, review length and publication consent.');
}

mysqli_report(MYSQLI_REPORT_OFF);
require __DIR__ . '/config.php';
if (!mysqli_set_charset($connect, 'utf8mb4')) {
    error_log('E-Vaccine could not set the feedback database character set: ' . mysqli_error($connect));
    feedback_redirect('danger', 'Your feedback could not be saved right now. Please try again later.');
}

$statement = mysqli_prepare(
    $connect,
    'INSERT INTO customer_feedback (display_name, role, rating, review, public_consent, is_approved)
     VALUES (NULLIF(?, \'\'), ?, ?, ?, 1, 0)'
);
if (!$statement) {
    error_log('E-Vaccine could not prepare the feedback insert: ' . mysqli_error($connect));
    feedback_redirect('danger', 'Your feedback could not be saved. Please contact the team and try again later.');
}

mysqli_stmt_bind_param($statement, 'ssis', $displayName, $role, $rating, $review);
if (!mysqli_stmt_execute($statement)) {
    error_log('E-Vaccine feedback insert failed: ' . mysqli_stmt_error($statement));
    mysqli_stmt_close($statement);
    feedback_redirect('danger', 'Your feedback could not be saved. Please contact the team and try again later.');
}

mysqli_stmt_close($statement);
$_SESSION['feedback_csrf'] = bin2hex(random_bytes(32));
feedback_redirect('success', 'Thank you for sharing feedback. It will be reviewed before it is published.');
