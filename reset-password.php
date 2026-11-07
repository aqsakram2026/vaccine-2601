<?php
session_start();
require __DIR__ . '/config.php';

$accountType = isset($_REQUEST['type']) && is_string($_REQUEST['type']) ? $_REQUEST['type'] : '';
$token = isset($_REQUEST['token']) && is_string($_REQUEST['token']) ? $_REQUEST['token'] : '';
$accounts = [
    'parent' => ['label' => 'Parent', 'table' => 'parents', 'password_column' => 'Hashed_password', 'login' => 'parents/parent-login.php'],
    'hospital' => ['label' => 'Hospital', 'table' => 'hospitals', 'password_column' => 'password', 'login' => 'hospital/hospital_login.php']
];
$errors = [];
$passwordUpdated = false;
$resetAccountName = '';
$resetAccountId = 0;
$csrfToken = $_SESSION['reset_password_csrf'] ?? bin2hex(random_bytes(32));
$_SESSION['reset_password_csrf'] = $csrfToken;

if (!isset($accounts[$accountType]) || !preg_match('/^[a-f0-9]{64}$/', $token)) {
    $tokenIsValid = false;
} else {
    $tokenHash = hash('sha256', $token);
    $lookup = mysqli_prepare(
        $connect,
        'SELECT account_id FROM password_reset_tokens
         WHERE account_type = ? AND token_hash = ? AND used_at IS NULL
           AND expires_at > CURRENT_TIMESTAMP
         LIMIT 1'
    );
    if (!$lookup) {
        error_log('E-Vaccine reset token lookup could not be prepared: ' . mysqli_error($connect));
        $tokenIsValid = false;
    } else {
        mysqli_stmt_bind_param($lookup, 'ss', $accountType, $tokenHash);
        if (!mysqli_stmt_execute($lookup)) {
            error_log('E-Vaccine reset token lookup failed: ' . mysqli_stmt_error($lookup));
            $tokenIsValid = false;
        } else {
            mysqli_stmt_store_result($lookup);
            $tokenIsValid = mysqli_stmt_num_rows($lookup) === 1;
            if ($tokenIsValid) {
                mysqli_stmt_bind_result($lookup, $resetAccountId);
                $tokenIsValid = mysqli_stmt_fetch($lookup) === true;
                $resetAccountId = (int)$resetAccountId;
            }
        }
        mysqli_stmt_close($lookup);
    }
}

if ($tokenIsValid && $accountType === 'parent') {
    $parentNameLookup = mysqli_prepare($connect, 'SELECT name FROM parents WHERE id = ? LIMIT 1');
    if ($parentNameLookup) {
        mysqli_stmt_bind_param($parentNameLookup, 'i', $resetAccountId);
        if (mysqli_stmt_execute($parentNameLookup)) {
            mysqli_stmt_bind_result($parentNameLookup, $resetAccountName);
            mysqli_stmt_fetch($parentNameLookup);
        } else {
            error_log('E-Vaccine reset page parent name lookup failed: ' . mysqli_stmt_error($parentNameLookup));
        }
        mysqli_stmt_close($parentNameLookup);
    } else {
        error_log('E-Vaccine reset page parent name lookup could not be prepared: ' . mysqli_error($connect));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = isset($_POST['csrf']) && is_string($_POST['csrf']) ? $_POST['csrf'] : '';
    $newPassword = isset($_POST['new_password']) && is_string($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) && is_string($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    if (!hash_equals($csrfToken, $csrf)) {
        $errors[] = 'Your form session expired. Refresh the reset link and try again.';
    } elseif (!$tokenIsValid) {
        $errors[] = 'This reset link is invalid or has expired. Request a new link.';
    } elseif (strlen($newPassword) < 6) {
        $errors[] = 'Choose a password with at least 6 characters.';
    } elseif (!hash_equals($newPassword, $confirmPassword)) {
        $errors[] = 'The password confirmation does not match.';
    } else {
        $passwordHash = password_hash($newPassword, PASSWORD_ARGON2ID);
        if (!is_string($passwordHash)) {
            error_log('E-Vaccine could not hash a password reset password.');
            $errors[] = 'Your password could not be updated. Please try again.';
        } elseif (!mysqli_begin_transaction($connect)) {
            error_log('E-Vaccine password reset transaction could not start: ' . mysqli_error($connect));
            $errors[] = 'Your password could not be updated. Please try again.';
        } else {
            $lockedToken = mysqli_prepare(
                $connect,
                'SELECT account_id FROM password_reset_tokens
                 WHERE account_type = ? AND token_hash = ? AND used_at IS NULL
                   AND expires_at > CURRENT_TIMESTAMP
                 LIMIT 1 FOR UPDATE'
            );
            $accountId = 0;
            $locked = false;
            if ($lockedToken) {
                mysqli_stmt_bind_param($lockedToken, 'ss', $accountType, $tokenHash);
                if (mysqli_stmt_execute($lockedToken)) {
                    mysqli_stmt_bind_result($lockedToken, $accountId);
                    $locked = mysqli_stmt_fetch($lockedToken) === true;
                } else {
                    error_log('E-Vaccine reset token lock failed: ' . mysqli_stmt_error($lockedToken));
                }
                mysqli_stmt_close($lockedToken);
            } else {
                error_log('E-Vaccine reset token lock could not be prepared: ' . mysqli_error($connect));
            }

            $updated = false;
            if ($locked) {
                $account = $accounts[$accountType];
                $updateAccount = mysqli_prepare(
                    $connect,
                    'UPDATE `' . $account['table'] . '` SET `' . $account['password_column'] . '` = ? WHERE id = ?'
                );
                if ($updateAccount) {
                    mysqli_stmt_bind_param($updateAccount, 'si', $passwordHash, $accountId);
                    $updated = mysqli_stmt_execute($updateAccount) && mysqli_stmt_affected_rows($updateAccount) === 1;
                    if (!$updated) {
                        error_log('E-Vaccine account password reset failed: ' . mysqli_stmt_error($updateAccount));
                    }
                    mysqli_stmt_close($updateAccount);
                } else {
                    error_log('E-Vaccine account password reset could not be prepared: ' . mysqli_error($connect));
                }
            }

            if ($updated) {
                $consume = mysqli_prepare(
                    $connect,
                    'UPDATE password_reset_tokens SET used_at = CURRENT_TIMESTAMP
                     WHERE account_type = ? AND account_id = ? AND used_at IS NULL'
                );
                if ($consume) {
                    mysqli_stmt_bind_param($consume, 'si', $accountType, $accountId);
                    $updated = mysqli_stmt_execute($consume);
                    if (!$updated) {
                        error_log('E-Vaccine reset tokens could not be consumed: ' . mysqli_stmt_error($consume));
                    }
                    mysqli_stmt_close($consume);
                } else {
                    error_log('E-Vaccine reset tokens could not be prepared for consumption: ' . mysqli_error($connect));
                    $updated = false;
                }
            }

            if ($updated && mysqli_commit($connect)) {
                $passwordUpdated = true;
                $tokenIsValid = false;
                if ($accountType === 'parent') {
                    require_once __DIR__ . '/includes/parent-reset-requests.php';
                    if (evax_ensure_parent_reset_requests_table($connect)) {
                        $completeRequest = mysqli_prepare(
                            $connect,
                            "UPDATE parent_password_reset_requests
                             SET status = 'Completed'
                             WHERE reset_token_hash = ? AND status IN ('Link Generated', 'Sent')"
                        );
                        if ($completeRequest) {
                            mysqli_stmt_bind_param($completeRequest, 's', $tokenHash);
                            if (!mysqli_stmt_execute($completeRequest)) {
                                error_log('E-Vaccine completed reset request status update failed: ' . mysqli_stmt_error($completeRequest));
                            }
                            mysqli_stmt_close($completeRequest);
                        } else {
                            error_log('E-Vaccine completed reset request update could not be prepared: ' . mysqli_error($connect));
                        }
                    }
                }
            } else {
                mysqli_rollback($connect);
                $errors[] = 'Your password could not be updated. Please try again.';
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
    <title>Reset Password | E-Vaccine</title>
    <?php include __DIR__ . '/includes/portal-head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="includes/theme.js?v=6"></script>
    <link href="includes/theme.css?v=5" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light min-vh-100 d-flex align-items-center justify-content-center p-3">
    <main class="col-12 col-md-7 col-lg-5 bg-white p-4 p-md-5 rounded-4 shadow border border-light-subtle">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mb-3" style="width:60px;height:60px" aria-hidden="true">
                <i class="bi bi-shield-lock fs-3"></i>
            </div>
            <h1 class="h3 fw-bold text-primary"><?= $passwordUpdated ? 'Password updated' : 'Choose a new password' ?></h1>
            <p class="text-secondary mb-0">
                <?= $passwordUpdated
                    ? 'Your password has been changed. Sign in with your new password.'
                    : ($accountType === 'parent' && $resetAccountName !== ''
                        ? 'Hello ' . e($resetAccountName) . ', set a new password for your parent account.'
                        : 'Set a new password for your ' . e($accounts[$accountType]['label'] ?? 'account') . ' account.') ?>
            </p>
        </div>
        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
        <?php endforeach; ?>
        <?php if ($passwordUpdated): ?>
            <a class="btn btn-primary btn-lg w-100" href="<?= e($accounts[$accountType]['login']) ?>">Continue to login</a>
        <?php elseif ($tokenIsValid && isset($accounts[$accountType])): ?>
            <form method="post">
                <input type="hidden" name="type" value="<?= e($accountType) ?>">
                <input type="hidden" name="token" value="<?= e($token) ?>">
                <input type="hidden" name="csrf" value="<?= e($csrfToken) ?>">
                <label class="form-label fw-semibold" for="newPassword">New password</label>
                <input class="form-control form-control-lg mb-3" id="newPassword" name="new_password" type="password" minlength="6" autocomplete="new-password" required>
                <label class="form-label fw-semibold" for="confirmPassword">Confirm new password</label>
                <input class="form-control form-control-lg mb-3" id="confirmPassword" name="confirm_password" type="password" minlength="6" autocomplete="new-password" required>
                <div class="form-text mb-3">Use at least 6 characters.</div>
                <button class="btn btn-primary btn-lg w-100" type="submit">Update password</button>
            </form>
        <?php else: ?>
            <?php if (!$errors): ?>
                <div class="alert alert-warning" role="alert">This reset link is invalid or has expired. Request a new link.</div>
            <?php endif; ?>
            <a class="btn btn-outline-primary w-100" href="<?= isset($accounts[$accountType]) && $accountType === 'hospital' ? 'forgot-password2.php?type=hospital' : 'forgot-password.php' ?>">Request a new reset link</a>
        <?php endif; ?>
    </main>
    <?php if ($passwordUpdated || $errors || !$tokenIsValid): ?>
        <script>
            Swal.fire({
                icon: <?= json_encode($passwordUpdated ? 'success' : 'error') ?>,
                title: <?= json_encode($passwordUpdated ? 'Password updated' : 'Unable to reset password') ?>,
                text: <?= json_encode($passwordUpdated
                    ? 'Your password has been changed. Sign in with your new password.'
                    : ($errors ? implode(' ', $errors) : 'This reset link is invalid or has expired.'),
                    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
                confirmButtonColor: '#2563eb'
            });
        </script>
    <?php endif; ?>
</body>
</html>
