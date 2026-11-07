<?php
session_start();
require __DIR__ . '/config.php';
require_once __DIR__ . '/includes/notifications.php';

$recipientType = '';
$recipientId = 0;
if (isset($_SESSION['admin'])) {
    $recipientType = 'admin';
} elseif (isset($_SESSION['hospital_id'])) {
    $recipientType = 'hospital';
    $recipientId = (int)$_SESSION['hospital_id'];
} elseif (isset($_SESSION['user_id']['id'])) {
    $recipientType = 'parent';
    $recipientId = (int)$_SESSION['user_id']['id'];
}
if ($recipientType === '') {
    header('Location: index.php');
    exit;
}

if ($recipientType === 'parent') {
    evax_generate_vaccine_reminders($connect, $recipientId);
}

$csrfToken = $_SESSION['notifications_csrf'] ?? bin2hex(random_bytes(32));
$_SESSION['notifications_csrf'] = $csrfToken;
$notificationError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf']) || !is_string($_POST['csrf']) || !hash_equals($csrfToken, $_POST['csrf'])) {
        $notificationError = 'Your form session expired. Please try again.';
    } elseif (isset($_POST['mark_all'])) {
        $statement = mysqli_prepare(
            $connect,
            'UPDATE notifications SET read_at = CURRENT_TIMESTAMP
             WHERE recipient_type = ? AND recipient_id = ? AND read_at IS NULL'
        );
        if ($statement) {
            mysqli_stmt_bind_param($statement, 'si', $recipientType, $recipientId);
            if (!mysqli_stmt_execute($statement)) {
                error_log('E-Vaccine mark-all-read action failed: ' . mysqli_stmt_error($statement));
                $notificationError = 'Notifications could not be updated. Please try again.';
            }
            mysqli_stmt_close($statement);
        } else {
            error_log('E-Vaccine mark-all-read action could not be prepared: ' . mysqli_error($connect));
            $notificationError = 'Notifications could not be updated. Please try again.';
        }
    } elseif (isset($_POST['notification_id'])) {
        $notificationId = filter_var($_POST['notification_id'], FILTER_VALIDATE_INT);
        if (!$notificationId || $notificationId < 1) {
            $notificationError = 'Select a valid notification.';
        } else {
            $statement = mysqli_prepare(
                $connect,
                'UPDATE notifications SET read_at = CURRENT_TIMESTAMP
                 WHERE id = ? AND recipient_type = ? AND recipient_id = ? AND read_at IS NULL'
            );
            if ($statement) {
                mysqli_stmt_bind_param($statement, 'isi', $notificationId, $recipientType, $recipientId);
                if (!mysqli_stmt_execute($statement)) {
                    error_log('E-Vaccine mark-read action failed: ' . mysqli_stmt_error($statement));
                    $notificationError = 'The notification could not be updated. Please try again.';
                }
                mysqli_stmt_close($statement);
            } else {
                error_log('E-Vaccine mark-read action could not be prepared: ' . mysqli_error($connect));
                $notificationError = 'The notification could not be updated. Please try again.';
            }
        }
    } else {
        $notificationError = 'Select a notification action.';
    }

    if ($notificationError === '') {
        header('Location: notifications.php');
        exit;
    }
}

$statement = mysqli_prepare(
    $connect,
    'SELECT id, title, message, target_url, created_at, read_at
     FROM notifications
     WHERE recipient_type = ? AND recipient_id = ?
     ORDER BY created_at DESC, id DESC
     LIMIT 100'
);
$notifications = [];
if (!$statement) {
    error_log('E-Vaccine notifications list could not be prepared: ' . mysqli_error($connect));
    $notificationError = 'Notifications are temporarily unavailable.';
} else {
    mysqli_stmt_bind_param($statement, 'si', $recipientType, $recipientId);
    if (mysqli_stmt_execute($statement)) {
        $result = mysqli_stmt_get_result($statement);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $notifications[] = $row;
            }
        } else {
            error_log('E-Vaccine notifications list results could not be read: ' . mysqli_stmt_error($statement));
            $notificationError = 'Notifications are temporarily unavailable.';
        }
    } else {
        error_log('E-Vaccine notifications list query failed: ' . mysqli_stmt_error($statement));
        $notificationError = 'Notifications are temporarily unavailable.';
    }
    mysqli_stmt_close($statement);
}

$pageTitle = 'Notifications | E-Vaccine';
$pageDescription = 'View appointment, hospital workflow, and vaccine schedule notifications.';
$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Notifications']
];
require __DIR__ . '/includes/site-header.php';

$scriptDirectory = str_replace('\\', '/', dirname((string)($_SERVER['SCRIPT_NAME'] ?? '')));
if ($scriptDirectory === '.' || $scriptDirectory === '/') {
    $scriptDirectory = '';
}
$notificationBase = rtrim($scriptDirectory, '/') . '/';
?>
<main class="container section-space">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <span class="eyebrow mb-2"><i class="bi bi-bell-fill" aria-hidden="true"></i> Updates</span>
            <h1 class="h2 fw-bold mb-0">Notifications</h1>
        </div>
        <?php if ($notifications): ?>
            <form method="post">
                <input type="hidden" name="csrf" value="<?= site_escape($csrfToken) ?>">
                <button class="btn btn-outline-vax rounded-pill" type="submit" name="mark_all" value="1">Mark all as read</button>
            </form>
        <?php endif; ?>
    </div>

    <?php if ($notificationError !== ''): ?>
        <div class="alert alert-warning" role="alert"><?= site_escape($notificationError) ?></div>
    <?php endif; ?>

    <?php if (!$notifications): ?>
        <div class="surface-card p-5 text-center">
            <i class="bi bi-bell display-5 text-secondary" aria-hidden="true"></i>
            <p class="text-secondary mt-3 mb-0">You have no notifications yet.</p>
        </div>
    <?php else: ?>
        <div class="d-grid gap-3">
            <?php foreach ($notifications as $notification): ?>
                <?php
                $targetUrl = trim((string)$notification['target_url']);
                if ($targetUrl === '' || preg_match('/^(?:https?:|\\/|\\\\)/i', $targetUrl)) {
                    $targetUrl = 'notifications.php';
                }
                $createdAt = strtotime($notification['created_at']);
                ?>
                <article class="surface-card p-4 <?= $notification['read_at'] === null ? 'border-primary' : '' ?>">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                        <div class="flex-grow-1">
                            <h2 class="h5 fw-bold mb-1">
                                <?php if ($notification['read_at'] === null): ?><span class="badge text-bg-primary me-2">New</span><?php endif; ?>
                                <?= site_escape($notification['title']) ?>
                            </h2>
                            <p class="text-secondary mb-2"><?= site_escape($notification['message']) ?></p>
                            <?php if ($createdAt !== false): ?><time class="small text-secondary" datetime="<?= date('c', $createdAt) ?>"><?= date('M j, Y g:i A', $createdAt) ?></time><?php endif; ?>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-sm btn-vax rounded-pill" href="<?= site_escape($notificationBase . $targetUrl) ?>">Open</a>
                            <?php if ($notification['read_at'] === null): ?>
                                <form method="post">
                                    <input type="hidden" name="csrf" value="<?= site_escape($csrfToken) ?>">
                                    <input type="hidden" name="notification_id" value="<?= (int)$notification['id'] ?>">
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill" type="submit">Mark read</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/includes/site-footer.php'; ?>
