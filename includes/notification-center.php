<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$notificationRecipientType = '';
$notificationRecipientId = 0;
if (isset($_SESSION['admin'])) {
    $notificationRecipientType = 'admin';
} elseif (isset($_SESSION['hospital_id'])) {
    $notificationRecipientType = 'hospital';
    $notificationRecipientId = (int)$_SESSION['hospital_id'];
} elseif (isset($_SESSION['user_id']['id'])) {
    $notificationRecipientType = 'parent';
    $notificationRecipientId = (int)$_SESSION['user_id']['id'];
}

if ($notificationRecipientType !== '') {
    if (!isset($connect)) {
        require_once __DIR__ . '/../config.php';
    }
    require_once __DIR__ . '/notifications.php';

    if ($notificationRecipientType === 'parent') {
        evax_generate_vaccine_reminders($connect, $notificationRecipientId);
    }

    $unreadResult = mysqli_query(
        $connect,
        "SELECT COUNT(*) AS unread_count FROM notifications
         WHERE recipient_type = '" . mysqli_real_escape_string($connect, $notificationRecipientType) . "'
           AND recipient_id = " . $notificationRecipientId . "
           AND read_at IS NULL"
    );
    $unreadCount = 0;
    if ($unreadResult) {
        $unreadRow = mysqli_fetch_assoc($unreadResult);
        $unreadCount = (int)($unreadRow['unread_count'] ?? 0);
        mysqli_free_result($unreadResult);
    } else {
        error_log('E-Vaccine notification count query failed: ' . mysqli_error($connect));
    }

    $scriptDirectory = str_replace('\\', '/', dirname((string)($_SERVER['SCRIPT_NAME'] ?? '')));
    if (preg_match('#/(parents|hospital|admin)$#', $scriptDirectory)) {
        $scriptDirectory = dirname($scriptDirectory);
    }
    if ($scriptDirectory === '.' || $scriptDirectory === '/') {
        $scriptDirectory = '';
    }
    $notificationBase = rtrim($scriptDirectory, '/') . '/';
    ?>
    <a class="btn btn-primary rounded-pill shadow position-fixed bottom-0 start-0 m-3 d-inline-flex align-items-center gap-2"
       style="z-index:1080"
       href="<?= e($notificationBase . 'notifications.php') ?>"
       aria-label="Notifications<?= $unreadCount > 0 ? ', ' . $unreadCount . ' unread' : '' ?>">
        <i class="bi bi-bell-fill" aria-hidden="true"></i>
        <span>Notifications</span>
        <?php if ($unreadCount > 0): ?><span class="badge rounded-pill text-bg-danger"><?= $unreadCount > 99 ? '99+' : $unreadCount ?></span><?php endif; ?>
    </a>
    <?php
}
