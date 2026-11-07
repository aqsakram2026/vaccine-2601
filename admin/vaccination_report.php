<?php
include __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/notifications.php';

$csrfToken = $_SESSION['vaccination_report_csrf'] ?? bin2hex(random_bytes(32));
$_SESSION['vaccination_report_csrf'] = $csrfToken;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_record'])) {
    $bookingId = filter_input(INPUT_POST, 'booking_id', FILTER_VALIDATE_INT);
    $decision = '';
    if (isset($_POST['decision']) && is_string($_POST['decision'])) {
        $decision = $_POST['decision'];
    }
    $flash = ['type' => 'danger', 'message' => 'The vaccination record could not be updated.'];

    if (!isset($_POST['csrf']) || !is_string($_POST['csrf']) || !hash_equals($csrfToken, $_POST['csrf'])) {
        $flash['message'] = 'Your form session expired. Please try again.';
    } elseif (!$bookingId || $bookingId < 1 || !in_array($decision, ['Admin Approved', 'Admin Rejected'], true)) {
        $flash['message'] = 'Select a valid vaccination record and review decision.';
    } else {
        $details = mysqli_prepare(
            $connect,
            'SELECT b.parent_id, b.hospital_name, b.child_id, c.child_name, h.id
             FROM bookings b
             LEFT JOIN children c ON c.id = b.child_id
             LEFT JOIN hospitals h ON h.hospital_name = b.hospital_name
             WHERE b.id = ?
             LIMIT 1'
        );
        $bookingDetails = null;
        if ($details) {
            mysqli_stmt_bind_param($details, 'i', $bookingId);
            if (mysqli_stmt_execute($details)) {
                $result = mysqli_stmt_get_result($details);
                if ($result) {
                    $bookingDetails = mysqli_fetch_assoc($result);
                }
            } else {
                error_log('E-Vaccine admin vaccination details lookup failed: ' . mysqli_stmt_error($details));
            }
            mysqli_stmt_close($details);
        } else {
            error_log('E-Vaccine admin vaccination details lookup could not be prepared: ' . mysqli_error($connect));
        }

        $statement = mysqli_prepare(
            $connect,
            "UPDATE bookings SET vaccination_status = ?
             WHERE id = ? AND vaccination_status = 'Vaccinated'"
        );
        if ($statement) {
            mysqli_stmt_bind_param($statement, 'si', $decision, $bookingId);
            if (mysqli_stmt_execute($statement) && mysqli_stmt_affected_rows($statement) === 1) {
                if ($bookingDetails) {
                    evax_notify_booking_status(
                        $connect,
                        $bookingId,
                        (int)$bookingDetails['parent_id'],
                        (int)$bookingDetails['id'],
                        (string)$bookingDetails['child_name'],
                        $decision
                    );
                }
                $flash = [
                    'type' => 'success',
                    'message' => $decision === 'Admin Approved'
                        ? 'Vaccination approved. The hospital can now print its completion certificate.'
                        : 'Vaccination record rejected.'
                ];
            } else {
                error_log('E-Vaccine admin vaccination review failed: ' . mysqli_stmt_error($statement));
                $flash['message'] = 'The record changed or could not be updated. Refresh and try again.';
            }
            mysqli_stmt_close($statement);
        } else {
            error_log('E-Vaccine admin vaccination review could not be prepared: ' . mysqli_error($connect));
        }
    }

    $_SESSION['vaccination_report_flash'] = $flash;
    header('Location: vaccination_report.php');
    exit;
}

$metricsResult = mysqli_query(
    $connect,
    "SELECT
        COUNT(*) AS vaccination_records,
        COUNT(DISTINCT CASE WHEN vaccination_status = 'Admin Approved' THEN child_id END) AS approved_patients,
        SUM(vaccination_status = 'Vaccinated') AS pending_review
     FROM bookings
     WHERE vaccination_status IN ('Vaccinated', 'Admin Approved', 'Admin Rejected')"
);
if (!$metricsResult) {
    error_log('E-Vaccine admin vaccination report totals failed: ' . mysqli_error($connect));
    $reportMetrics = ['vaccination_records' => 0, 'approved_patients' => 0, 'pending_review' => 0];
} else {
    $reportMetrics = mysqli_fetch_assoc($metricsResult);
    mysqli_free_result($metricsResult);
}

$records = mysqli_query(
    $connect,
    "SELECT b.id, b.hospital_name, b.vaccination_status, b.vaccination_date, b.created_at,
            v.Vaccine_Name AS vaccine_name,
            c.child_name, p.name AS parent_name
     FROM bookings b
     LEFT JOIN children c ON c.id = b.child_id
     LEFT JOIN parents p ON p.id = b.parent_id
     LEFT JOIN vaccines v ON v.Vaccine_ID = b.vaccine_id
     WHERE b.vaccination_status IN ('Vaccinated', 'Admin Approved', 'Admin Rejected')
     ORDER BY b.created_at DESC"
);
if (!$records) {
    error_log('E-Vaccine admin vaccination report records query failed: ' . mysqli_error($connect));
}
$reportFlash = $_SESSION['vaccination_report_flash'] ?? null;
unset($_SESSION['vaccination_report_flash']);

$page_title = 'Vaccination Report';
$active = 'vaccination_report';
include __DIR__ . '/layout_top.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Vaccination report</h2>
        <p class="text-secondary small mb-0">Review hospital-submitted vaccination records and see overall approved patient totals.</p>
    </div>
    <button type="button" class="btn btn-outline-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print report</button>
</div>

<?php if (is_array($reportFlash)): ?>
    <div class="alert alert-<?= e($reportFlash['type']) ?>" role="status"><?= e($reportFlash['message']) ?></div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-secondary small">Approved vaccinated patients</div><div class="fs-2 fw-bold"><?= (int)$reportMetrics['approved_patients'] ?></div></div></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-secondary small">Vaccination records</div><div class="fs-2 fw-bold"><?= (int)$reportMetrics['vaccination_records'] ?></div></div></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-secondary small">Awaiting admin review</div><div class="fs-2 fw-bold"><?= (int)$reportMetrics['pending_review'] ?></div></div></div></div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <caption class="visually-hidden">Hospital vaccination records pending or completed admin review</caption>
                <thead><tr><th>Patient</th><th>Parent / guardian</th><th>Hospital</th><th>Vaccine</th><th>Vaccination date</th><th>Status</th><th class="text-end">Review</th></tr></thead>
                <tbody>
                    <?php if ($records && mysqli_num_rows($records) > 0): ?>
                        <?php while ($record = mysqli_fetch_assoc($records)): ?>
                            <?php
                            $statusClass = 'warning';
                            if ($record['vaccination_status'] === 'Admin Approved') {
                                $statusClass = 'success';
                            } elseif ($record['vaccination_status'] === 'Admin Rejected') {
                                $statusClass = 'danger';
                            }
                            $vaccinationDate = $record['vaccination_date'];
                            if ($vaccinationDate === null || $vaccinationDate === '') {
                                $vaccinationDate = 'Not recorded';
                            }
                            ?>
                            <tr>
                                <th scope="row"><?= e($record['child_name'] ?? 'Patient not found') ?></th>
                                <td><?= e($record['parent_name'] ?? 'Not available') ?></td>
                                <td><?= e($record['hospital_name']) ?></td>
                                <td><?= e($record['vaccine_name'] ?? 'Not specified') ?></td>
                                <td><?= e($vaccinationDate) ?></td>
                                <td><span class="badge text-bg-<?= $statusClass ?>"><?= e($record['vaccination_status']) ?></span></td>
                                <td class="text-end">
                                    <?php if ($record['vaccination_status'] === 'Vaccinated'): ?>
                                        <form method="post" class="d-inline-flex gap-2">
                                            <input type="hidden" name="csrf" value="<?= e($csrfToken) ?>">
                                            <input type="hidden" name="booking_id" value="<?= (int)$record['id'] ?>">
                                            <input type="hidden" name="review_record" value="1">
                                            <button class="btn btn-sm btn-success" name="decision" value="Admin Approved">Approve</button>
                                            <button class="btn btn-sm btn-outline-danger" name="decision" value="Admin Rejected">Reject</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-secondary small">Reviewed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php elseif ($records): ?>
                        <tr><td colspan="7" class="text-center text-secondary py-4">No vaccination records are available yet.</td></tr>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-danger py-4">The vaccination report could not be loaded.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/layout_bottom.php'; ?>
