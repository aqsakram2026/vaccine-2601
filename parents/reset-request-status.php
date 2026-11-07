<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$isParentLoggedIn = isset($_SESSION['user_id']['id']);
if ($isParentLoggedIn) {
    require_once __DIR__ . '/../includes/parent-auth.php';
} else {
    require_once __DIR__ . '/../config.php';
}
require_once __DIR__ . '/../includes/parent-reset-requests.php';

$request = null;
$requests = [];
$error = '';
$requestId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
$statusKey = isset($_GET['key']) && is_string($_GET['key']) ? $_GET['key'] : '';

if (!evax_ensure_parent_reset_requests_table($connect)) {
    http_response_code(500);
    $error = 'Request status is temporarily unavailable.';
} elseif ($isParentLoggedIn) {
    $parentId = (int)$_SESSION['user_id']['id'];
    $result = mysqli_query(
        $connect,
        "SELECT parent_name, status, created_at, updated_at
         FROM parent_password_reset_requests
         WHERE parent_id = $parentId ORDER BY created_at DESC, id DESC"
    );
    if (!$result) {
        error_log('E-Vaccine parent reset status list failed: ' . mysqli_error($connect));
        $error = 'Request status is temporarily unavailable.';
    } else {
        while ($row = mysqli_fetch_assoc($result)) {
            $requests[] = $row;
        }
        mysqli_free_result($result);
    }
} elseif (!$requestId || $requestId < 1 || !preg_match('/^[a-f0-9]{64}$/', $statusKey)) {
    http_response_code(400);
    $error = 'This request status link is invalid.';
} else {
    $statusHash = hash('sha256', $statusKey);
    $lookup = mysqli_prepare(
        $connect,
        'SELECT parent_name, status, created_at, updated_at
         FROM parent_password_reset_requests
         WHERE id = ? AND status_token_hash = ? LIMIT 1'
    );
    if (!$lookup) {
        error_log('E-Vaccine parent reset status lookup could not be prepared: ' . mysqli_error($connect));
        $error = 'Request status is temporarily unavailable.';
    } else {
        mysqli_stmt_bind_param($lookup, 'is', $requestId, $statusHash);
        if (!mysqli_stmt_execute($lookup)) {
            error_log('E-Vaccine parent reset status lookup failed: ' . mysqli_stmt_error($lookup));
            $error = 'Request status is temporarily unavailable.';
        } else {
            mysqli_stmt_bind_result($lookup, $requestName, $requestStatus, $requestCreated, $requestUpdated);
            if (mysqli_stmt_fetch($lookup)) {
                $request = [
                    'name' => $requestName,
                    'status' => $requestStatus,
                    'created_at' => $requestCreated,
                    'updated_at' => $requestUpdated
                ];
            } else {
                http_response_code(404);
                $error = 'This request status link was not found.';
            }
        }
        mysqli_stmt_close($lookup);
    }
}

$statusDetails = [
    'Pending' => 'Your request is waiting for administrator review.',
    'Approved' => 'Your request was approved. The reset link is being prepared.',
    'Link Generated' => 'A reset link has been generated. Contact the administrator if it has not been shared with you.',
    'Sent' => 'The reset link was sent to your registered email address. Check your inbox and spam folder.',
    'Completed' => 'Your password has been changed successfully.',
    'Rejected' => 'The administrator could not approve this request. Contact support if you believe this is an error.'
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Reset Request Status | E-Vaccine</title>
    <?php include __DIR__ . '/../includes/portal-head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../includes/theme.js?v=6"></script>
    <link href="../includes/theme.css?v=5" rel="stylesheet">
</head>
<body class="<?= $isParentLoggedIn ? 'bg-light min-vh-100' : 'bg-light min-vh-100 d-flex align-items-center justify-content-center p-3' ?>">
    <?php if ($isParentLoggedIn): ?>
        <?php $parentPageTitle = 'Password Reset Status'; include __DIR__ . '/../includes/parent-navbar.php'; ?>
    <?php endif; ?>
    <main class="<?= $isParentLoggedIn ? 'container py-5' : 'col-12 col-md-8 col-lg-6 bg-white p-4 p-md-5 rounded-4 shadow border border-light-subtle' ?>">
        <h1 class="h3 fw-bold text-primary mb-3">Password reset request status</h1>
        <?php if ($error !== ''): ?>
            <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
        <?php elseif ($isParentLoggedIn): ?>
            <?php if ($requests): ?>
                <?php foreach ($requests as $request): ?>
                    <?php
                    $statusClass = 'text-bg-warning';
                    if (in_array($request['status'], ['Approved', 'Link Generated'], true)) {
                        $statusClass = 'text-bg-info';
                    } elseif (in_array($request['status'], ['Sent', 'Completed'], true)) {
                        $statusClass = 'text-bg-success';
                    } elseif ($request['status'] === 'Rejected') {
                        $statusClass = 'text-bg-danger';
                    }
                    ?>
                    <div class="border rounded-3 p-3 mb-3">
                        <p class="mb-1">Current status: <span class="badge <?= e($statusClass) ?>"><?= e($request['status']) ?></span></p>
                        <p class="text-secondary mb-2"><?= e($statusDetails[$request['status']] ?? 'Your request is being reviewed.') ?></p>
                        <p class="small text-secondary mb-0">Submitted: <?= e($request['created_at']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-secondary">You have not submitted a password reset request.</p>
            <?php endif; ?>
        <?php elseif ($request): ?>
            <?php
            $statusClass = 'text-bg-warning';
            if (in_array($request['status'], ['Approved', 'Link Generated'], true)) {
                $statusClass = 'text-bg-info';
            } elseif (in_array($request['status'], ['Sent', 'Completed'], true)) {
                $statusClass = 'text-bg-success';
            } elseif ($request['status'] === 'Rejected') {
                $statusClass = 'text-bg-danger';
            }
            ?>
            <p>Hello, <strong><?= e($request['name']) ?></strong>.</p>
            <p>Current status: <span class="badge <?= e($statusClass) ?>"><?= e($request['status']) ?></span></p>
            <p class="text-secondary"><?= e($statusDetails[$request['status']] ?? 'Your request is being reviewed.') ?></p>
            <p class="small text-secondary mb-0">Submitted: <?= e($request['created_at']) ?></p>
        <?php endif; ?>
        <?php if (!$isParentLoggedIn): ?>
            <a class="btn btn-outline-primary mt-4" href="parent-login.php">Parent login</a>
        <?php endif; ?>
    </main>
</body>
</html>
