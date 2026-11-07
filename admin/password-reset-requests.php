<?php
include 'auth.php';
require_once __DIR__ . '/../includes/parent-reset-requests.php';

if (!evax_ensure_parent_reset_requests_table($connect)) {
    http_response_code(500);
    exit('Password reset requests are temporarily unavailable.');
}

$csrfToken = $_SESSION['admin_reset_request_csrf'] ?? bin2hex(random_bytes(32));
$_SESSION['admin_reset_request_csrf'] = $csrfToken;
$flash = $_SESSION['admin_reset_request_flash'] ?? null;
unset($_SESSION['admin_reset_request_flash']);
if (!isset($_SESSION['admin_reset_links']) || !is_array($_SESSION['admin_reset_links'])) {
    $_SESSION['admin_reset_links'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = isset($_POST['csrf']) && is_string($_POST['csrf']) ? $_POST['csrf'] : '';
    $requestId = isset($_POST['request_id']) && is_string($_POST['request_id'])
        ? filter_var($_POST['request_id'], FILTER_VALIDATE_INT)
        : false;
    $action = isset($_POST['action']) && is_string($_POST['action']) ? $_POST['action'] : '';
    $message = '';
    $messageType = 'danger';

    if (!hash_equals($csrfToken, $csrf)) {
        $message = 'Your form session expired. Refresh the page and try again.';
        $messageType = 'warning';
    } elseif ($requestId === false || $requestId < 1) {
        $message = 'Select a valid request.';
        $messageType = 'warning';
    } elseif ($action === 'approve' || $action === 'reject') {
        $newStatus = $action === 'approve' ? 'Approved' : 'Rejected';
        $update = mysqli_prepare(
            $connect,
            "UPDATE parent_password_reset_requests
             SET status = ? WHERE id = ? AND status = 'Pending'"
        );
        if (!$update) {
            error_log('E-Vaccine admin reset request update could not be prepared: ' . mysqli_error($connect));
            $message = 'The request could not be updated.';
        } else {
            mysqli_stmt_bind_param($update, 'si', $newStatus, $requestId);
            $updated = mysqli_stmt_execute($update);
            if (!$updated) {
                error_log('E-Vaccine admin reset request update failed: ' . mysqli_stmt_error($update));
                $message = 'The request could not be updated.';
            } elseif (mysqli_stmt_affected_rows($update) === 1) {
                $message = 'Request status updated to ' . $newStatus . '.';
                $messageType = 'success';
            } else {
                $message = 'This request is no longer pending.';
                $messageType = 'warning';
            }
            mysqli_stmt_close($update);
        }
    } elseif ($action === 'generate') {
        $requestLookup = mysqli_prepare(
            $connect,
            "SELECT parent_id, parent_name, status
             FROM parent_password_reset_requests WHERE id = ? LIMIT 1"
        );
        $resetRequest = null;
        if (!$requestLookup) {
            error_log('E-Vaccine admin reset request lookup could not be prepared: ' . mysqli_error($connect));
            $message = 'A reset link could not be generated.';
        } else {
            mysqli_stmt_bind_param($requestLookup, 'i', $requestId);
            if (mysqli_stmt_execute($requestLookup)) {
                mysqli_stmt_bind_result($requestLookup, $requestParentId, $requestParentName, $requestStatus);
                if (mysqli_stmt_fetch($requestLookup)) {
                    $resetRequest = [
                        'parent_id' => (int)$requestParentId,
                        'name' => (string)$requestParentName,
                        'status' => (string)$requestStatus
                    ];
                }
            } else {
                error_log('E-Vaccine admin reset request lookup failed: ' . mysqli_stmt_error($requestLookup));
            }
            mysqli_stmt_close($requestLookup);
        }

        if ($resetRequest === null) {
            $message = 'The request was not found.';
            $messageType = 'warning';
        } elseif ($resetRequest['parent_id'] < 1) {
            $message = 'This name and email did not match a parent account. A reset link cannot be generated.';
            $messageType = 'warning';
        } elseif (!in_array($resetRequest['status'], ['Approved', 'Link Generated', 'Sent'], true)) {
            $message = 'Approve the request before generating a reset link.';
            $messageType = 'warning';
        } else {
            $host = (string)($_SERVER['SERVER_NAME'] ?? '');
            if (preg_match('/^[A-Za-z0-9.-]+$/', $host) !== 1) {
                $message = 'The server host is not configured for reset links.';
            } else {
                $rawToken = bin2hex(random_bytes(32));
                $tokenHash = hash('sha256', $rawToken);
                $https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== ''
                    && strtolower((string)$_SERVER['HTTPS']) !== 'off';
                $scheme = $https ? 'https' : 'http';
                $port = (int)($_SERVER['SERVER_PORT'] ?? ($https ? 443 : 80));
                $portSuffix = ($https && $port === 443) || (!$https && $port === 80) ? '' : ':' . $port;
                $scriptDirectory = str_replace('\\', '/', dirname((string)($_SERVER['SCRIPT_NAME'] ?? '/admin/password-reset-requests.php')));
                $projectDirectory = dirname($scriptDirectory);
                $path = trim($projectDirectory, '/');
                $encodedPath = $path === ''
                    ? ''
                    : '/' . implode('/', array_map('rawurlencode', explode('/', $path)));
                $resetUrl = $scheme . '://' . $host . $portSuffix . $encodedPath
                    . '/reset-password.php?type=parent&token=' . rawurlencode($rawToken);

                if (!mysqli_begin_transaction($connect)) {
                    error_log('E-Vaccine admin reset token transaction could not start: ' . mysqli_error($connect));
                    $message = 'A reset link could not be generated.';
                } else {
                    $invalidate = mysqli_prepare(
                        $connect,
                        "UPDATE password_reset_tokens SET used_at = CURRENT_TIMESTAMP
                         WHERE account_type = 'parent' AND account_id = ? AND used_at IS NULL"
                    );
                    $insert = mysqli_prepare(
                        $connect,
                        "INSERT INTO password_reset_tokens (account_type, account_id, token_hash, expires_at)
                         VALUES ('parent', ?, ?, DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 1 HOUR))"
                    );
                    $updateRequest = mysqli_prepare(
                        $connect,
                        "UPDATE parent_password_reset_requests
                         SET status = 'Link Generated', reset_token_hash = ?
                         WHERE id = ? AND status IN ('Approved', 'Link Generated', 'Sent')"
                    );
                    $stored = false;
                    if ($invalidate && $insert && $updateRequest) {
                        mysqli_stmt_bind_param($invalidate, 'i', $resetRequest['parent_id']);
                        $invalidated = mysqli_stmt_execute($invalidate);
                        mysqli_stmt_bind_param($insert, 'is', $resetRequest['parent_id'], $tokenHash);
                        $inserted = $invalidated && mysqli_stmt_execute($insert);
                        mysqli_stmt_bind_param($updateRequest, 'si', $tokenHash, $requestId);
                        $requestUpdated = $inserted && mysqli_stmt_execute($updateRequest)
                            && mysqli_stmt_affected_rows($updateRequest) === 1;
                        $stored = $requestUpdated;
                        if (!$stored) {
                            error_log('E-Vaccine admin reset token could not be stored: ' . mysqli_error($connect));
                        }
                    } else {
                        error_log('E-Vaccine admin reset token statements could not be prepared: ' . mysqli_error($connect));
                    }
                    if ($invalidate) {
                        mysqli_stmt_close($invalidate);
                    }
                    if ($insert) {
                        mysqli_stmt_close($insert);
                    }
                    if ($updateRequest) {
                        mysqli_stmt_close($updateRequest);
                    }

                    if ($stored && mysqli_commit($connect)) {
                        $_SESSION['admin_reset_links'][(string)$requestId] = $resetUrl;
                        $message = 'Reset link generated for ' . $resetRequest['name'] . '. It expires in one hour.';
                        $messageType = 'success';
                    } else {
                        mysqli_rollback($connect);
                        $message = 'A reset link could not be generated.';
                    }
                }
            }
        }
    } elseif ($action === 'send') {
        $resetUrl = $_SESSION['admin_reset_links'][(string)$requestId] ?? '';
        $fromAddress = $password_reset_from_email ?? '';
        if ($resetUrl === '') {
            $message = 'Generate a new link before sending it.';
            $messageType = 'warning';
        } elseif (!is_string($fromAddress) || !filter_var($fromAddress, FILTER_VALIDATE_EMAIL)) {
            error_log('E-Vaccine reset email unavailable: configure EVAX_MAIL_FROM with a verified sender address.');
            $message = 'Email delivery is not configured. Copy the link and share it with the parent securely.';
            $messageType = 'warning';
        } else {
            $requestLookup = mysqli_prepare(
                $connect,
                "SELECT parent_email, parent_name FROM parent_password_reset_requests
                 WHERE id = ? AND status = 'Link Generated' LIMIT 1"
            );
            $recipient = null;
            if ($requestLookup) {
                mysqli_stmt_bind_param($requestLookup, 'i', $requestId);
                if (mysqli_stmt_execute($requestLookup)) {
                    mysqli_stmt_bind_result($requestLookup, $recipientEmail, $recipientName);
                    if (mysqli_stmt_fetch($requestLookup)) {
                        $recipient = ['email' => $recipientEmail, 'name' => $recipientName];
                    }
                } else {
                    error_log('E-Vaccine reset email recipient lookup failed: ' . mysqli_stmt_error($requestLookup));
                }
                mysqli_stmt_close($requestLookup);
            } else {
                error_log('E-Vaccine reset email recipient lookup could not be prepared: ' . mysqli_error($connect));
            }

            if (!$recipient) {
                $message = 'This request is no longer waiting for a reset link to be sent.';
                $messageType = 'warning';
            } else {
                $subject = 'E-Vaccine password reset';
                $body = "Hello " . $recipient['name'] . ",\n\n"
                    . "An administrator approved your password reset request. Use this link within one hour:\n"
                    . $resetUrl . "\n\nIf you did not request this, contact the site administrator.";
                $headers = "From: E-Vaccine <" . $fromAddress . ">\r\n"
                    . "Content-Type: text/plain; charset=UTF-8\r\n"
                    . "X-Mailer: PHP/" . PHP_VERSION;
                if (!mail($recipient['email'], $subject, $body, $headers)) {
                    error_log('E-Vaccine parent reset email could not be sent.');
                    $message = 'The email could not be sent. Check the mail server and try again.';
                } else {
                    $update = mysqli_prepare(
                        $connect,
                        "UPDATE parent_password_reset_requests
                         SET status = 'Sent' WHERE id = ? AND status = 'Link Generated'"
                    );
                    if ($update) {
                        mysqli_stmt_bind_param($update, 'i', $requestId);
                        $sent = mysqli_stmt_execute($update);
                        if (!$sent) {
                            error_log('E-Vaccine parent reset email status update failed: ' . mysqli_stmt_error($update));
                        } else {
                            $sent = mysqli_stmt_affected_rows($update) === 1;
                        }
                        mysqli_stmt_close($update);
                        if ($sent) {
                            unset($_SESSION['admin_reset_links'][(string)$requestId]);
                        }
                        $message = $sent ? 'Reset link sent to ' . $recipient['email'] . '.' : 'Email was sent, but request status could not be updated.';
                        $messageType = $sent ? 'success' : 'warning';
                    } else {
                        error_log('E-Vaccine parent reset email status update could not be prepared: ' . mysqli_error($connect));
                        $message = 'Email was sent, but request status could not be updated.';
                        $messageType = 'warning';
                    }
                }
            }
        }
    } else {
        $message = 'Choose a valid request action.';
        $messageType = 'warning';
    }

    $_SESSION['admin_reset_request_flash'] = [
        'message' => $message,
        'type' => $messageType,
        'request_id' => (int)$requestId
    ];
    header('Location: password-reset-requests.php');
    exit;
}

if (is_array($flash) && isset($flash['request_id'])) {
    $flash['link'] = $_SESSION['admin_reset_links'][(string)(int)$flash['request_id']] ?? '';
}

$requests = mysqli_query(
    $connect,
    'SELECT id, parent_id, parent_name, parent_email, status, created_at, updated_at
     FROM parent_password_reset_requests ORDER BY created_at DESC, id DESC'
);
if (!$requests) {
    error_log('E-Vaccine admin reset request list query failed: ' . mysqli_error($connect));
}

$page_title = 'Password Reset Requests';
$active = 'password_resets';
include 'layout_top.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h2 class="fw-bold">Parent Password Reset Requests</h2>
        <p class="text-secondary small mb-0">Review requests, approve them, then generate a one-time link.</p>
    </div>
</div>

<?php if (is_array($flash)): ?>
    <div class="alert alert-<?= e($flash['type'] ?? 'info') ?>" role="status"><?= e($flash['message'] ?? '') ?></div>
    <?php if (!empty($flash['link'])): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <label class="form-label fw-semibold" for="generatedResetLink">One-time reset link</label>
                <div class="input-group">
                    <input class="form-control" id="generatedResetLink" type="text" readonly value="<?= e($flash['link']) ?>">
                    <button class="btn btn-outline-primary" type="button" id="copyGeneratedLink">Copy Link</button>
                </div>
                <form method="post" class="mt-3">
                    <input type="hidden" name="csrf" value="<?= e($csrfToken) ?>">
                    <input type="hidden" name="request_id" value="<?= (int)$flash['request_id'] ?>">
                    <?php if (($flash['type'] ?? '') !== 'success' || strpos((string)($flash['message'] ?? ''), 'Reset link sent to ') !== 0): ?>
                        <button class="btn btn-primary" type="submit" name="action" value="send">Send Link to Parent</button>
                    <?php endif; ?>
                    <span class="small text-secondary ms-2">Link expires one hour after generation.</span>
                </form>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Parent</th><th>Email</th><th>Status</th><th>Requested</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
            <?php if ($requests && mysqli_num_rows($requests) > 0): ?>
                <?php while ($request = mysqli_fetch_assoc($requests)): ?>
                    <?php
                    $badgeClass = 'text-bg-warning';
                    if (in_array($request['status'], ['Approved', 'Link Generated'], true)) {
                        $badgeClass = 'text-bg-info';
                    } elseif (in_array($request['status'], ['Sent', 'Completed'], true)) {
                        $badgeClass = 'text-bg-success';
                    } elseif ($request['status'] === 'Rejected') {
                        $badgeClass = 'text-bg-danger';
                    }
                    ?>
                    <tr>
                        <td>
                            <strong><?= e($request['parent_name']) ?></strong>
                            <?php if (empty($request['parent_id'])): ?><div class="small text-danger">No matching parent account</div><?php endif; ?>
                        </td>
                        <td><?= e($request['parent_email']) ?></td>
                        <td><span class="badge <?= $badgeClass ?>"><?= e($request['status']) ?></span></td>
                        <td><?= e($request['created_at']) ?></td>
                        <td class="text-end text-nowrap">
                            <?php if ($request['status'] === 'Pending'): ?>
                                <?php if (!empty($request['parent_id'])): ?>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="csrf" value="<?= e($csrfToken) ?>">
                                        <input type="hidden" name="request_id" value="<?= (int)$request['id'] ?>">
                                        <button class="btn btn-sm btn-success" name="action" value="approve">Approve</button>
                                    </form>
                                <?php endif; ?>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="csrf" value="<?= e($csrfToken) ?>">
                                    <input type="hidden" name="request_id" value="<?= (int)$request['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger" name="action" value="reject">Reject</button>
                                </form>
                            <?php elseif (in_array($request['status'], ['Approved', 'Link Generated', 'Sent'], true)): ?>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="csrf" value="<?= e($csrfToken) ?>">
                                    <input type="hidden" name="request_id" value="<?= (int)$request['id'] ?>">
                                    <button class="btn btn-sm btn-primary" name="action" value="generate">
                                        <?= $request['status'] === 'Approved' ? 'Generate Link' : 'Generate New Link' ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php elseif ($requests): ?>
                <tr><td colspan="5" class="text-center text-secondary py-4">No password reset requests yet.</td></tr>
            <?php else: ?>
                <tr><td colspan="5" class="text-center text-danger py-4">Requests are temporarily unavailable.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (is_array($flash) && !empty($flash['link'])): ?>
    <script>
        document.getElementById('copyGeneratedLink').addEventListener('click', async function () {
            const field = document.getElementById('generatedResetLink');
            try {
                await navigator.clipboard.writeText(field.value);
                this.textContent = 'Copied';
            } catch (error) {
                field.select();
                document.execCommand('copy');
                this.textContent = 'Copied';
            }
        });
    </script>
<?php endif; ?>
<?php if (is_array($flash)): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: <?= json_encode(($flash['type'] ?? '') === 'success' ? 'success' : 'error') ?>,
            title: <?= json_encode(($flash['type'] ?? '') === 'success' ? 'Request updated' : 'Action could not be completed') ?>,
            text: <?= json_encode((string)($flash['message'] ?? ''), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
            confirmButtonColor: '#2563eb'
        });
    </script>
<?php endif; ?>

<?php include 'layout_bottom.php'; ?>
