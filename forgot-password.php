<?php
session_start();
require __DIR__ . '/config.php';
require_once __DIR__ . '/includes/parent-reset-requests.php';

if (isset($_GET['type']) && $_GET['type'] === 'hospital') {
    header('Location: forgot-password2.php?type=hospital');
    exit;
}

$message = '';
$messageType = 'danger';
$statusUrl = '';
$csrfToken = $_SESSION['parent_reset_request_csrf'] ?? bin2hex(random_bytes(32));
$_SESSION['parent_reset_request_csrf'] = $csrfToken;

if (!evax_ensure_parent_reset_requests_table($connect)) {
    http_response_code(500);
    $message = 'Password reset requests are temporarily unavailable.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $message === '') {
    $name = isset($_POST['name']) && is_string($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) && is_string($_POST['email']) ? trim($_POST['email']) : '';
    $csrf = isset($_POST['csrf']) && is_string($_POST['csrf']) ? $_POST['csrf'] : '';

    if (!hash_equals($csrfToken, $csrf)) {
        $message = 'Your form session expired. Refresh the page and try again.';
        $messageType = 'warning';
    } elseif ($name === '' || strlen($name) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
        $message = 'Enter your registered name and a valid email address.';
        $messageType = 'warning';
    } else {
        $parentId = null;
        $lookup = mysqli_prepare($connect, 'SELECT id FROM parents WHERE LOWER(name) = LOWER(?) AND LOWER(email) = LOWER(?) LIMIT 1');
        if (!$lookup) {
            error_log('E-Vaccine parent reset request lookup could not be prepared: ' . mysqli_error($connect));
            $message = 'Your request could not be submitted. Please try again later.';
        } else {
            mysqli_stmt_bind_param($lookup, 'ss', $name, $email);
            if (!mysqli_stmt_execute($lookup)) {
                error_log('E-Vaccine parent reset request lookup failed: ' . mysqli_stmt_error($lookup));
                $message = 'Your request could not be submitted. Please try again later.';
            } else {
                mysqli_stmt_bind_result($lookup, $matchedParentId);
                if (mysqli_stmt_fetch($lookup)) {
                    $parentId = (int)$matchedParentId;
                }
            }
            mysqli_stmt_close($lookup);
        }

        if ($message === '') {
            $nameSql = mysqli_real_escape_string($connect, $name);
            $emailSql = mysqli_real_escape_string($connect, $email);
            $statusKey = bin2hex(random_bytes(32));
            $statusHash = hash('sha256', $statusKey);
            $statusHashSql = mysqli_real_escape_string($connect, $statusHash);
            if ($parentId === null) {
                $inserted = mysqli_query(
                    $connect,
                    "INSERT INTO parent_password_reset_requests
                        (parent_name, parent_email, status_token_hash)
                     VALUES ('$nameSql', '$emailSql', '$statusHashSql')"
                );
            } else {
                $inserted = mysqli_query(
                    $connect,
                    "INSERT INTO parent_password_reset_requests
                        (parent_id, parent_name, parent_email, status_token_hash)
                     VALUES ($parentId, '$nameSql', '$emailSql', '$statusHashSql')"
                );
            }

            if (!$inserted) {
                error_log('E-Vaccine parent reset request could not be saved: ' . mysqli_error($connect));
                $message = 'Your request could not be submitted. Please try again later.';
            } else {
                $requestId = (int)mysqli_insert_id($connect);
                $message = 'Your request has been submitted. Save the private status link below to check its progress.';
                $messageType = 'success';
                $statusUrl = 'parents/reset-request-status.php?id=' . $requestId . '&key=' . rawurlencode($statusKey);
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
    <title>Request Password Reset | E-Vaccine</title>
    <?php include __DIR__ . '/includes/portal-head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="includes/theme.js?v=6"></script>
    <link href="includes/theme.css?v=5" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light min-vh-100 d-flex align-items-center justify-content-center p-3">
    <main class="col-12 col-md-8 col-lg-6 bg-white p-4 p-md-5 rounded-4 shadow border border-light-subtle">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mb-3" style="width:60px;height:60px" aria-hidden="true">
                <i class="bi bi-key fs-3"></i>
            </div>
            <h1 class="h3 fw-bold text-primary">Forgot password?</h1>
            <p class="text-secondary mb-0">Submit your registered name and email. An administrator will review your request.</p>
        </div>
        <?php if ($message !== ''): ?>
            <div class="alert alert-<?= e($messageType) ?>" role="status"><?= e($message) ?></div>
        <?php endif; ?>
        <?php if ($statusUrl !== ''): ?>
            <div class="alert alert-info">
                <label class="form-label fw-semibold" for="statusLink">Private request status link</label>
                <div class="input-group">
                    <input class="form-control" id="statusLink" type="text" readonly value="<?= e($statusUrl) ?>">
                    <button class="btn btn-outline-primary" type="button" id="copyStatusLink">Copy</button>
                </div>
                <small class="d-block mt-2">Keep this link private. It grants access to your request status.</small>
                <a class="btn btn-sm btn-outline-primary mt-2" href="<?= e($statusUrl) ?>">Check request status</a>
            </div>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="csrf" value="<?= e($csrfToken) ?>">
            <label class="form-label fw-semibold" for="name">Registered name</label>
            <input class="form-control form-control-lg mb-3" id="name" name="name" type="text" maxlength="100" autocomplete="name" required>
            <label class="form-label fw-semibold" for="email">Registered email address</label>
            <input class="form-control form-control-lg mb-4" id="email" name="email" type="email" maxlength="100" autocomplete="email" required>
            <button class="btn btn-primary btn-lg w-100" type="submit">Submit reset request</button>
        </form>
        <p class="text-center small mt-4 mb-0"><a href="parents/parent-login.php">Back to parent login</a></p>
    </main>
    <?php if ($statusUrl !== ''): ?>
        <script>
            document.getElementById('copyStatusLink').addEventListener('click', async function () {
                const field = document.getElementById('statusLink');
                try {
                    await navigator.clipboard.writeText(new URL(field.value, window.location.href).href);
                } catch (error) {
                    field.select();
                    document.execCommand('copy');
                }
                this.textContent = 'Copied';
            });
        </script>
    <?php endif; ?>
<?php if ($message !== ''): ?>
    <script>
        Swal.fire({
            icon: <?= json_encode($messageType === 'success' ? 'success' : 'error') ?>,
            title: <?= json_encode($messageType === 'success' ? 'Request submitted' : 'Unable to submit request') ?>,
            text: <?= json_encode($message, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
            confirmButtonColor: '#2563eb'
        });
    </script>
<?php endif; ?>
</body>
</html>
