<?php
require_once __DIR__ . '/../includes/parent-auth.php';
$parentId = (int)$_SESSION['user_id']['id'];
$childId = filter_var($_GET['child_id'] ?? null, FILTER_VALIDATE_INT);
if (isset($_GET['child_id']) && (!$childId || $childId < 1)) {
    http_response_code(400);
    exit('Select a valid child to view the vaccination report.');
}
if (!$childId) {
    $childrenResult = mysqli_query(
        $connect,
        "SELECT DISTINCT c.id, c.child_name
         FROM children c
         INNER JOIN bookings b ON b.child_id = c.id AND b.parent_id = c.parent_id
         WHERE c.parent_id = $parentId AND b.vaccination_status = 'Admin Approved'
         ORDER BY c.child_name"
    );
    if (!$childrenResult) {
        error_log('E-Vaccine parent report child list query failed: ' . mysqli_error($connect));
        http_response_code(500);
        exit('The vaccination report is temporarily unavailable.');
    }
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Choose a Child | E-Vaccine</title>
        <?php include __DIR__ . '/../includes/portal-head.php'; ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="../includes/theme.js?v=6"></script>
        <link href="../includes/theme.css?v=5" rel="stylesheet">
    </head>
    <body class="bg-light">
        <?php $parentPageTitle = 'Vaccination Report'; include __DIR__ . '/../includes/parent-navbar.php'; ?>
        <main class="container py-5" style="max-width:760px">
            <h1 class="h2 fw-bold">Choose a child</h1>
            <p class="text-secondary">Select whose vaccination report you want to view.</p>
            <div class="list-group shadow-sm">
                <?php while ($row = mysqli_fetch_assoc($childrenResult)): ?>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="vaccination_report.php?child_id=<?= (int)$row['id'] ?>">
                        <?= htmlspecialchars($row['child_name'], ENT_QUOTES, 'UTF-8') ?>
                        <span class="btn btn-sm btn-outline-primary">View approved report</span>
                    </a>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($childrenResult) === 0): ?>
                    <div class="list-group-item text-secondary">Reports appear after a hospital-recorded vaccination has been approved by an administrator.</div>
                <?php endif; ?>
            </div>
            <a href="parent_dashboard.php" class="btn btn-outline-secondary mt-4">Back to dashboard</a>
        </main>
    <?php include __DIR__ . '/../includes/portal-footer.php'; ?>
    </body>
    </html>
    <?php
    exit;
}

$childResult = mysqli_query(
    $connect,
    "SELECT child_name, date_of_birth, gender, last_vaccine, vaccination_date
     FROM children WHERE id = $childId AND parent_id = $parentId LIMIT 1"
);
if (!$childResult) {
    error_log('E-Vaccine parent report child query failed: ' . mysqli_error($connect));
    http_response_code(500);
    exit('The vaccination report is temporarily unavailable.');
}
$child = mysqli_fetch_assoc($childResult);
if (!$child) {
    http_response_code(404);
    exit('The selected child was not found in your account.');
}

$recordResult = mysqli_query(
    $connect,
    "SELECT b.id, b.hospital_name, b.vaccination_status, b.created_at, b.vaccination_date,
            v.Vaccine_Name AS vaccine_name
     FROM bookings b
     LEFT JOIN vaccines v ON b.vaccine_id = v.Vaccine_ID
     WHERE b.child_id = $childId AND b.parent_id = $parentId
       AND b.vaccination_status = 'Admin Approved'
     ORDER BY COALESCE(b.vaccination_date, DATE(b.created_at)) DESC, b.id DESC"
);
if (!$recordResult) {
    error_log('E-Vaccine parent report records query failed: ' . mysqli_error($connect));
    http_response_code(500);
    exit('The vaccination report is temporarily unavailable.');
}
$records = [];
while ($record = mysqli_fetch_assoc($recordResult)) {
    $records[] = $record;
}
if (!$records) {
    http_response_code(404);
    exit('The vaccination report becomes available after an administered vaccine has been approved.');
}
$approvedCount = count($records);
$e = static function ($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vaccination Report | E-Vaccine</title>
    <?php include __DIR__ . '/../includes/portal-head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../includes/theme.js?v=6"></script>
    <link href="../includes/theme.css?v=5" rel="stylesheet">
    <style>
        body { background: #f3f8fa; color: #17364b; }
        .report-sheet { max-width: 960px; margin: 2rem auto; padding: 2.5rem; border: 1px solid #dce8ed; border-radius: 20px; background: #fff; box-shadow: 0 18px 48px rgba(18,51,74,.09); }
        .report-mark { color: #078f87; letter-spacing: .12em; font-size: .78rem; font-weight: 800; text-transform: uppercase; }
        @media print { body { background: #fff; } .report-sheet { margin: 0 auto; border: 0; box-shadow: none; } .no-print { display: none !important; } }
    </style>
</head>
<body>
<?php $parentPageTitle = 'Vaccination Report'; include __DIR__ . '/../includes/parent-navbar.php'; ?>
<main class="report-sheet">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <div class="report-mark">E-Vaccine · Parent portal</div>
            <h1 class="h2 fw-bold mt-2">Vaccination Report</h1>
            <p class="text-secondary mb-0">Child vaccination and appointment records</p>
        </div>
        <span class="badge rounded-pill text-bg-success fs-6"><?= $approvedCount ?> admin-approved
            <?php
            if ($approvedCount === 1) {
                echo 'record';
            } else {
                echo 'records';
            }
            ?>
        </span>
    </div>

    <section class="rounded-4 bg-body-tertiary p-4 mb-4" aria-labelledby="child-details-title">
        <h2 class="h6 text-uppercase text-secondary" id="child-details-title">Child details</h2>
        <dl class="row mb-0">
            <dt class="col-sm-3">Name</dt><dd class="col-sm-9"><?= $e($child['child_name']) ?></dd>
            <dt class="col-sm-3">Date of birth</dt><dd class="col-sm-9"><?= $e($child['date_of_birth']) ?></dd>
            <dt class="col-sm-3">Gender</dt><dd class="col-sm-9"><?= $e($child['gender']) ?></dd>
        </dl>
    </section>

    <section class="mb-4" aria-labelledby="history-title">
        <h2 class="h5 fw-bold" id="history-title">Vaccination history</h2>
        <?php if (!empty($child['last_vaccine'])): ?>
            <p class="text-secondary">Last vaccine entered in the child profile: <strong><?= $e($child['last_vaccine']) ?></strong>
                <?php if (!empty($child['vaccination_date'])): ?> on <strong><?= $e($child['vaccination_date']) ?></strong><?php endif; ?>.
                This is parent-provided history and is not an approval record from a hospital.
            </p>
        <?php else: ?>
            <p class="text-secondary">No earlier vaccine history has been entered in the child profile.</p>
        <?php endif; ?>
        <div class="table-responsive border rounded-3">
            <table class="table table-hover align-middle mb-0">
                <caption class="visually-hidden">Appointment and vaccination records for this child</caption>
                <thead class="table-light"><tr><th>Vaccine</th><th>Hospital</th><th>Appointment date</th><th>Vaccination date</th><th>Status</th></tr></thead>
                <tbody>
                    <?php if ($records): ?>
                        <?php foreach ($records as $record): ?>
                            <?php
                            $status = (string)$record['vaccination_status'];
                            $statusClass = 'warning';
                            if ($status === 'Admin Approved') {
                                $statusClass = 'success';
                            } elseif (in_array($status, ['Admin Rejected', 'Hospital Rejected', 'Not Vaccinated'], true)) {
                                $statusClass = 'danger';
                            }
                            $vaccinationDate = $record['vaccination_date'];
                            if ($vaccinationDate === null || $vaccinationDate === '') {
                                $vaccinationDate = 'Not recorded';
                            }
                            ?>
                            <tr>
                                <td><?= $e($record['vaccine_name'] ?? 'Not specified') ?></td>
                                <td><?= $e($record['hospital_name']) ?></td>
                                <td><?= $e($record['created_at']) ?></td>
                                <td><?= $e($vaccinationDate) ?></td>
                                <td><span class="badge text-bg-<?= $statusClass ?>"><?= $e($status) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center text-secondary py-4">There are no appointment records for this child yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <p class="small text-secondary">This report contains only hospital-recorded vaccinations approved by an administrator. Parent-entered history is separate and has not been clinically verified. Keep the child's official vaccination card and consult a qualified health professional for care decisions.</p>
    <div class="d-flex flex-wrap gap-2 mt-4 no-print">
        <button type="button" class="btn btn-success" onclick="window.print()">Print / Save report</button>
        <a href="parent_dashboard.php" class="btn btn-outline-secondary">Back to dashboard</a>
    </div>
</main>
<?php include __DIR__ . '/../includes/portal-footer.php'; ?>
</body>
</html>
