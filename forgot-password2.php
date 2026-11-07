<?php
session_start();
require __DIR__ . '/config.php';

$accountType = isset($_REQUEST['type']) && is_string($_REQUEST['type']) ? $_REQUEST['type'] : '';
$accounts = [
    'parent' => ['label' => 'parent', 'table' => 'parents'],
    'hospital' => ['label' => 'hospital', 'table' => 'hospitals']
];
$message = '';
$messageType = 'info';
$csrfToken = $_SESSION['forgot_password_csrf'] ?? bin2hex(random_bytes(32));
$_SESSION['forgot_password_csrf'] = $csrfToken;

if (!isset($accounts[$accountType])) {
    http_response_code(400);
    $message = 'Choose password recovery from the parent or hospital login page.';
    $messageType = 'warning';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) && is_string($_POST['email']) ? trim($_POST['email']) : '';
    $csrf = isset($_POST['csrf']) && is_string($_POST['csrf']) ? $_POST['csrf'] : '';
    if (!hash_equals($csrfToken, $csrf)) {
        $message = 'Your form session expired. Refresh the page and try again.';
        $messageType = 'danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
        $message = 'Enter a valid email address.';
        $messageType = 'warning';
    } else {
        $message = 'If an account matches that email, a password reset link will be sent.';
        $fromAddress = $password_reset_from_email;
        if (!is_string($fromAddress) || !filter_var($fromAddress, FILTER_VALIDATE_EMAIL)) {
            error_log('E-Vaccine password reset unavailable: configure EVAX_MAIL_FROM with a verified sender address.');
            $message = 'Password recovery email is not configured yet. Please contact the site administrator.';
            $messageType = 'warning';
        } else {
            $table = $accounts[$accountType]['table'];
            $lookup = mysqli_prepare($connect, "SELECT id FROM `$table` WHERE email = ? LIMIT 1");
            if (!$lookup) {
                error_log('E-Vaccine password reset account lookup could not be prepared: ' . mysqli_error($connect));
                $message = 'Password recovery is temporarily unavailable. Please try again later.';
                $messageType = 'danger';
            } else {
                mysqli_stmt_bind_param($lookup, 's', $email);
                if (!mysqli_stmt_execute($lookup)) {
                    error_log('E-Vaccine password reset account lookup failed: ' . mysqli_stmt_error($lookup));
                    mysqli_stmt_close($lookup);
                    $message = 'Password recovery is temporarily unavailable. Please try again later.';
                    $messageType = 'danger';
                } else {
                    mysqli_stmt_bind_result($lookup, $accountId);
                    $accountFound = mysqli_stmt_fetch($lookup);
                    mysqli_stmt_close($lookup);

                    if ($accountFound) {
                        $cooldown = mysqli_prepare(
                            $connect,
                            'SELECT id FROM password_reset_tokens
                             WHERE account_type = ? AND account_id = ?
                               AND created_at > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 MINUTE)
                             LIMIT 1'
                        );
                        $recentRequest = false;
                        if ($cooldown) {
                            mysqli_stmt_bind_param($cooldown, 'si', $accountType, $accountId);
                            if (mysqli_stmt_execute($cooldown)) {
                                mysqli_stmt_store_result($cooldown);
                                $recentRequest = mysqli_stmt_num_rows($cooldown) > 0;
                            } else {
                                error_log('E-Vaccine password reset cooldown query failed: ' . mysqli_stmt_error($cooldown));
                            }
                            mysqli_stmt_close($cooldown);
                        } else {
                            error_log('E-Vaccine password reset cooldown query could not be prepared: ' . mysqli_error($connect));
                        }

                        if (!$recentRequest) {
                            $rawToken = bin2hex(random_bytes(32));
                            $tokenHash = hash('sha256', $rawToken);
                            $invalidate = mysqli_prepare(
                                $connect,
                                'UPDATE password_reset_tokens SET used_at = CURRENT_TIMESTAMP
                                 WHERE account_type = ? AND account_id = ? AND used_at IS NULL'
                            );
                            $insert = mysqli_prepare(
                                $connect,
                                'INSERT INTO password_reset_tokens (account_type, account_id, token_hash, expires_at)
                                 VALUES (?, ?, ?, DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 1 HOUR))'
                            );
                            if (!$invalidate || !$insert) {
                                error_log('E-Vaccine password reset token statements could not be prepared: ' . mysqli_error($connect));
                                if ($invalidate) {
                                    mysqli_stmt_close($invalidate);
                                }
                                if ($insert) {
                                    mysqli_stmt_close($insert);
                                }
                                $message = 'Password recovery is temporarily unavailable. Please try again later.';
                                $messageType = 'danger';
                            } else {
                                mysqli_stmt_bind_param($invalidate, 'si', $accountType, $accountId);
                                $invalidated = mysqli_stmt_execute($invalidate);
                                mysqli_stmt_close($invalidate);
                                mysqli_stmt_bind_param($insert, 'sis', $accountType, $accountId, $tokenHash);
                                $inserted = $invalidated && mysqli_stmt_execute($insert);
                                if (!$invalidated || !$inserted) {
                                    error_log('E-Vaccine password reset token could not be stored: ' . mysqli_stmt_error($insert));
                                    $message = 'Password recovery is temporarily unavailable. Please try again later.';
                                    $messageType = 'danger';
                                }
                                mysqli_stmt_close($insert);

                                if ($inserted) {
                                    $host = (string)($_SERVER['SERVER_NAME'] ?? '');
                                    $hostIsValid = preg_match('/^[A-Za-z0-9.-]+$/', $host) === 1;
                                    $https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== '' && strtolower((string)$_SERVER['HTTPS']) !== 'off';
                                    $scheme = $https ? 'https' : 'http';
                                    $port = (int)($_SERVER['SERVER_PORT'] ?? ($https ? 443 : 80));
                                    $portSuffix = ($https && $port === 443) || (!$https && $port === 80) ? '' : ':' . $port;
                                    $directory = str_replace('\\', '/', dirname((string)($_SERVER['SCRIPT_NAME'] ?? '/forgot-password.php')));
                                    $path = trim($directory, '/');
                                    $encodedPath = $path === ''
                                        ? ''
                                        : '/' . implode('/', array_map('rawurlencode', explode('/', $path)));

                                    if (!$hostIsValid) {
                                        error_log('E-Vaccine password reset link was not sent: invalid SERVER_NAME.');
                                        $message = 'Password recovery is temporarily unavailable. Please try again later.';
                                        $messageType = 'danger';
                                    } else {
                                        $resetUrl = $scheme . '://' . $host . $portSuffix . $encodedPath
                                            . '/reset-password.php?type=' . rawurlencode($accountType)
                                            . '&token=' . rawurlencode($rawToken);
                                        $subject = 'E-Vaccine password reset';
                                        $body = "A password reset was requested for your E-Vaccine account.\n\n"
                                            . "Use this link within one hour to choose a new password:\n"
                                            . $resetUrl . "\n\n"
                                            . "If you did not request this, you can ignore this email.";
                                        $headers = "From: E-Vaccine <" . $fromAddress . ">\r\n"
                                            . "Content-Type: text/plain; charset=UTF-8\r\n"
                                            . "X-Mailer: PHP/" . PHP_VERSION;
                                        if (!mail($email, $subject, $body, $headers)) {
                                            error_log('E-Vaccine password reset email could not be sent.');
                                            $consume = mysqli_prepare(
                                                $connect,
                                                'UPDATE password_reset_tokens SET used_at = CURRENT_TIMESTAMP WHERE token_hash = ?'
                                            );
                                            if ($consume) {
                                                mysqli_stmt_bind_param($consume, 's', $tokenHash);
                                                mysqli_stmt_execute($consume);
                                                mysqli_stmt_close($consume);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password | E-Vaccine</title>
    <?php include __DIR__ . '/includes/portal-head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="includes/theme.js?v=6"></script>
    <link href="includes/theme.css?v=5" rel="stylesheet">
</head>
<body class="bg-light min-vh-100 d-flex align-items-center justify-content-center p-3">
    <main class="col-12 col-md-7 col-lg-5 bg-white p-4 p-md-5 rounded-4 shadow border border-light-subtle">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mb-3" style="width:60px;height:60px" aria-hidden="true">
                <i class="bi bi-key fs-3"></i>
            </div>
            <h1 class="h3 fw-bold text-primary">Forgot password?</h1>
            <p class="text-secondary mb-0">Enter the email address for your <?= isset($accounts[$accountType]) ? e($accounts[$accountType]['label']) : '' ?> account.</p>
        </div>
        <?php if ($message !== ''): ?>
            <div class="alert alert-<?= e($messageType) ?>" role="status"><?= e($message) ?></div>
        <?php endif; ?>
        <?php if (isset($accounts[$accountType])): ?>
            <form method="post">
                <input type="hidden" name="type" value="<?= e($accountType) ?>">
                <input type="hidden" name="csrf" value="<?= e($csrfToken) ?>">
                <label class="form-label fw-semibold" for="email">Email address</label>
                <input class="form-control form-control-lg mb-3" id="email" name="email" type="email" maxlength="100" autocomplete="email" required placeholder="you@example.com">
                <button class="btn btn-primary btn-lg w-100" type="submit">Send reset link</button>
            </form>
        <?php endif; ?>
        <p class="text-center small mt-4 mb-0">
            <a href="<?= isset($accounts[$accountType]) && $accountType === 'hospital' ? 'hospital/hospital_login.php' : 'parents/parent-login.php' ?>">Back to login</a>
        </p>
    </main>
</body>
</html>
