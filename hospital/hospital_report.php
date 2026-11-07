<?php
session_start();
include_once __DIR__ . '/../config.php';

if (!isset($_SESSION['hospital_id'], $_SESSION['hospital_name'])) {
    header('Location: hospital_login.php');
    exit;
}

$bookingId = filter_input(INPUT_GET, 'booking_id', FILTER_VALIDATE_INT);
if (!$bookingId || $bookingId < 1) {
    http_response_code(400);
    exit('A valid appointment is required.');
}

$hospitalName = (string)$_SESSION['hospital_name'];
$statement = mysqli_prepare(
    $connect,
    "SELECT b.id, b.hospital_name, b.vaccination_status, b.created_at, b.vaccination_date,
            v.Vaccine_Name AS vaccine_name,
            c.child_name, c.date_of_birth, p.name AS parent_name, p.email AS parent_email
     FROM bookings b
     LEFT JOIN children c ON c.id = b.child_id
     LEFT JOIN parents p ON p.id = b.parent_id
     LEFT JOIN vaccines v ON v.Vaccine_ID = b.vaccine_id
     WHERE b.id = ? AND b.hospital_name = ?
       AND b.vaccination_status IN ('Vaccinated', 'Admin Approved', 'Admin Rejected')
     LIMIT 1"
);
if (!$statement) {
    error_log('E-Vaccine hospital report query could not be prepared: ' . mysqli_error($connect));
    http_response_code(500);
    exit('The patient report is temporarily unavailable.');
}
mysqli_stmt_bind_param($statement, 'is', $bookingId, $hospitalName);
if (!mysqli_stmt_execute($statement)) {
    error_log('E-Vaccine hospital report query failed: ' . mysqli_stmt_error($statement));
    mysqli_stmt_close($statement);
    http_response_code(500);
    exit('The patient report is temporarily unavailable.');
}
$result = mysqli_stmt_get_result($statement);
$report = null;
if ($result) {
    $report = mysqli_fetch_assoc($result);
}
mysqli_stmt_close($statement);

if (!$report) {
    http_response_code(404);
    exit('The completed patient record was not found for this hospital.');
}

$isCertified = $report['vaccination_status'] === 'Admin Approved';
$vaccinationDate = $report['vaccination_date'];
if ($vaccinationDate === null || $vaccinationDate === '') {
    $vaccinationDate = 'Not recorded';
}
$reportTitle = 'Patient Vaccination Report';
$certificateHeading = 'Vaccination Record Report';
$statusBackground = '#fff3d4';
$statusColor = '#865900';
$printDisabled = 'disabled';
if ($isCertified) {
    $reportTitle = 'Vaccination Completion Certificate';
    $certificateHeading = 'Vaccination Completion Certificate';
    $statusBackground = '#e5f7ef';
    $statusColor = '#087b54';
    $printDisabled = '';
}
$e = static function ($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e($reportTitle) ?> | E-Vaccine</title>
    <?php include __DIR__ . '/../includes/portal-head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../includes/theme.js?v=6"></script>
    <link href="../includes/theme.css?v=5" rel="stylesheet">
    <style>
        body { background: #f3f8fa; color: #17364b; }
        .report-sheet { max-width: 860px; margin: 3rem auto; padding: 3rem; border: 1px solid #dce8ed; border-radius: 20px; background: #fff; box-shadow: 0 18px 48px rgba(18,51,74,.09); }
        .report-mark { color: #078f87; letter-spacing: .12em; font-size: .78rem; font-weight: 800; text-transform: uppercase; }
        .report-status { display: inline-block; padding: .45rem .8rem; border-radius: 999px; background: <?= $statusBackground ?>; color: <?= $statusColor ?>; font-weight: 700; }
        @media print { body { background: #fff; } .report-sheet { margin: 0 auto; border: 0; box-shadow: none; } .no-print { display: none !important; } }
    </style>
</head>
<body>
    <main class="report-sheet">
        <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
            <div>
                <div class="report-mark">E-Vaccine · Patient report</div>
                <h1 class="h2 fw-bold mt-2"><?= $e($certificateHeading) ?></h1>
                <p class="text-secondary mb-0">Appointment reference #<?= (int)$report['id'] ?></p>
            </div>
            <span class="report-status"><?= $e($report['vaccination_status']) ?></span>
        </div>

        <?php if (!$isCertified): ?>
            <div class="alert alert-warning">
                This vaccination record is awaiting final admin approval and is not yet a completion certificate.
            </div>
        <?php else: ?>
            <p class="lead">This report certifies that the vaccination recorded for this appointment was administered by the hospital shown below and approved by the E-Vaccine administrator.</p>
        <?php endif; ?>

        <div class="row g-4 mt-2">
            <div class="col-md-6">
                <h2 class="h6 text-uppercase text-secondary">Patient</h2>
                <dl class="row mb-0">
                    <dt class="col-5">Name</dt><dd class="col-7"><?= $e($report['child_name'] ?? 'Unavailable') ?></dd>
                    <dt class="col-5">Date of birth</dt><dd class="col-7"><?= $e($report['date_of_birth'] ?? 'Not recorded') ?></dd>
                    <dt class="col-5">Parent / guardian</dt><dd class="col-7"><?= $e($report['parent_name'] ?? 'Unavailable') ?></dd>
                    <dt class="col-5">Contact email</dt><dd class="col-7"><?= $e($report['parent_email'] ?? 'Unavailable') ?></dd>
                </dl>
            </div>
            <div class="col-md-6">
                <h2 class="h6 text-uppercase text-secondary">Vaccination record</h2>
                <dl class="row mb-0">
                    <dt class="col-5">Hospital</dt><dd class="col-7"><?= $e($report['hospital_name']) ?></dd>
                    <dt class="col-5">Vaccine</dt><dd class="col-7"><?= $e($report['vaccine_name'] ?? 'Not specified') ?></dd>
                    <dt class="col-5">Appointment date</dt><dd class="col-7"><?= $e($report['created_at']) ?></dd>
                    <dt class="col-5">Vaccination date</dt><dd class="col-7"><?= $e($vaccinationDate) ?></dd>
                    <dt class="col-5">Record status</dt><dd class="col-7"><?= $e($report['vaccination_status']) ?></dd>
                </dl>
            </div>
        </div>

        <p class="small text-secondary mt-4">This certificate applies to the vaccination record associated with this appointment only. It does not replace a complete immunization history or clinical advice.</p>
        <div class="d-flex gap-2 mt-4 no-print">
            <button type="button" class="btn btn-success" onclick="window.print()" <?= $printDisabled ?>>Print certificate</button>
            <a href="hospital_dashboard.php" class="btn btn-outline-secondary">Back to dashboard</a>
        </div>
    </main>
<?php include __DIR__ . '/../includes/portal-footer.php'; ?>
</body>
</html>
