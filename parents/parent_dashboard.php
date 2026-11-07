<?php
require_once __DIR__ . '/../includes/parent-auth.php';
$parentId = (int)$_SESSION['user_id']['id'];
$childManagementToken = $_SESSION['child_management_csrf'] ?? bin2hex(random_bytes(32));
$_SESSION['child_management_csrf'] = $childManagementToken;
$profileImage = $parentProfileImage !== '' ? '../' . e($parentProfileImage) : '';
$parentNameEscaped = e($parentName);
$childrenResult = mysqli_query(
    $conn,
    "SELECT c.id, c.child_name, c.date_of_birth, c.gender, c.allergies, c.last_vaccine,
            c.vaccination_date,
            EXISTS(
                SELECT 1 FROM bookings b
                WHERE b.child_id = c.id AND b.parent_id = c.parent_id
                  AND b.vaccination_status = 'Admin Approved'
            ) AS report_ready
     FROM children c
     WHERE c.parent_id = $parentId
     ORDER BY c.child_name"
);
if (!$childrenResult) {
    error_log('E-Vaccine patient dashboard child list query failed: ' . mysqli_error($conn));
}
$appointmentResult = mysqli_query(
    $conn,
    "SELECT b.id, b.child_id, b.hospital_name, b.vaccination_status, b.created_at,
            v.Vaccine_Name AS vaccine_name
     FROM bookings b
     LEFT JOIN vaccines v ON b.vaccine_id = v.Vaccine_ID
     WHERE b.parent_id = $parentId
     ORDER BY b.created_at DESC"
);
if (!$appointmentResult) {
    error_log('E-Vaccine patient appointment list query failed: ' . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard | Vaccination Management System</title>
    <?php include __DIR__ . '/../includes/portal-head.php'; ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../includes/theme.js?v=6"></script>
    <link href="../includes/theme.css?v=5" rel="stylesheet">
</head>

<body class="bg-light min-vh-100">

    <?php $parentPageTitle = 'Parent Dashboard'; include __DIR__ . '/../includes/parent-navbar.php'; ?>

    <div class="container py-5">
        <?php
        $childNotices = [
            'child-updated' => 'Child details updated.',
            'child-deleted' => 'Child profile and associated appointment records deleted.'
        ];
        if (isset($_GET['notice']) && isset($childNotices[$_GET['notice']])): ?>
            <div class="alert alert-success" role="status"><?= htmlspecialchars($childNotices[$_GET['notice']], ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm rounded-4 mb-5">
            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <p class="text-primary fw-semibold mb-2">
                            PARENT PORTAL
                        </p>
                        <h1 class="fw-bold text-dark mb-3">
                            Hello, <?= $parentNameEscaped ?>!
                        </h1>
                        <p class="text-secondary mb-0">
                            Welcome to your vaccination dashboard. Manage your child's
                            registration and vaccination requests in one place.
                        </p>
                    </div>

                    <div class="col-md-4 text-md-end mt-4 mt-md-0">
                        <?php if ($profileImage !== ''): ?>
                            <img src="<?= $profileImage ?>" alt="Your profile image" class="rounded-circle object-fit-cover shadow-sm" width="100" height="100">
                        <?php else: ?>
                            <a href="profile.php" class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle text-decoration-none" style="width:100px;height:100px" aria-label="Add a profile image">
                                <span class="display-3 fw-bold text-white">+</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <h2 class="h4 fw-bold text-dark">Dashboard Services</h2>
            <p class="text-secondary">
                Choose an action to manage your child's vaccination information.
            </p>
        </div>

        <div class="row g-4">

            <div class="col-12 col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-lg-5">
                        <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-3 mb-4"
                             style="width: 60px; height: 60px;">
                            <span class="fs-2 fw-bold text-white">+</span>
                        </div>

                        <h3 class="h5 fw-bold mb-3">Vaccine Request</h3>

                        <p class="text-secondary mb-4">
                            Submit a vaccination request and manage your child's
                            vaccination-related needs.
                        </p>

                        <a href="vaccine_request.php"
                           class="btn btn-primary w-100 py-2 fw-semibold rounded-3">
                            Make a Request
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-lg-5">
                        <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-3 mb-4"
                             style="width: 60px; height: 60px;">
                            <span class="fs-2 fw-bold text-white">+</span>
                        </div>

                        <h3 class="h5 fw-bold mb-3">Register Your Child</h3>

                        <p class="text-secondary mb-4">
                            Register your child to keep their personal information
                            and vaccination records organized.
                        </p>

                        <a href="child.php"
                           class="btn btn-success w-100 py-2 fw-semibold rounded-3">
                            Register Child
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <section class="mb-5" aria-labelledby="children-title">
            <div class="mb-3">
                <h2 class="h4 fw-bold text-dark mb-1" id="children-title">Your children</h2>
                <p class="text-secondary mb-0">Review and manage each child's profile. A vaccination report appears after an administered dose is approved.</p>
            </div>
            <div class="row g-3">
                <?php if ($childrenResult && mysqli_num_rows($childrenResult) > 0): ?>
                    <?php while ($child = mysqli_fetch_assoc($childrenResult)): ?>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4">
                                <div class="card-body">
                                    <h3 class="h5 fw-bold mb-3"><?= htmlspecialchars($child['child_name'], ENT_QUOTES, 'UTF-8') ?></h3>
                                    <dl class="small row mb-3">
                                        <dt class="col-5">Date of birth</dt>
                                        <dd class="col-7"><?= htmlspecialchars($child['date_of_birth'], ENT_QUOTES, 'UTF-8') ?></dd>
                                        <dt class="col-5">Gender</dt>
                                        <dd class="col-7"><?= htmlspecialchars($child['gender'], ENT_QUOTES, 'UTF-8') ?></dd>
                                        <dt class="col-5">Allergies</dt>
                                        <dd class="col-7"><?= htmlspecialchars($child['allergies'] ?: 'None recorded', ENT_QUOTES, 'UTF-8') ?></dd>
                                        <dt class="col-5">Last vaccine</dt>
                                        <dd class="col-7"><?= htmlspecialchars($child['last_vaccine'] ?: 'None recorded', ENT_QUOTES, 'UTF-8') ?></dd>
                                    </dl>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a class="btn btn-sm btn-outline-primary" href="child.php?id=<?= (int)$child['id'] ?>">Edit details</a>
                                        <?php if ((int)$child['report_ready'] === 1): ?>
                                            <a class="btn btn-sm btn-success" href="vaccination_report.php?child_id=<?= (int)$child['id'] ?>">View vaccination report</a>
                                        <?php else: ?>
                                            <span class="small text-secondary align-self-center">Report available after vaccination approval</span>
                                        <?php endif; ?>
                                    </div>
                                    <form method="post" action="child.php" class="mt-3" onsubmit="return confirm('Delete this child and all associated appointment records? This cannot be undone.');">
                                        <input type="hidden" name="child_id" value="<?= (int)$child['id'] ?>">
                                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($childManagementToken, ENT_QUOTES, 'UTF-8') ?>">
                                        <button class="btn btn-sm btn-outline-danger" type="submit" name="delete_child" value="1">Delete child</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php elseif ($childrenResult): ?>
                    <div class="col-12"><p class="text-secondary">No children are registered yet.</p></div>
                <?php else: ?>
                    <div class="col-12"><p class="text-danger">The child list is temporarily unavailable.</p></div>
                <?php endif; ?>
            </div>
        </section>

        <section class="mt-5" aria-labelledby="patient-appointments-title">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
                <div>
                    <h2 class="h4 fw-bold text-dark mb-1" id="patient-appointments-title">Appointment progress</h2>
                    <p class="text-secondary mb-0">Follow hospital and admin decisions for your requests.</p>
                </div>
                <a class="btn btn-outline-primary btn-sm" href="vaccine_request.php">Book an appointment</a>
            </div>
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <caption class="visually-hidden">Your appointment requests and their current workflow status</caption>
                        <thead><tr><th>Hospital</th><th>Vaccine</th><th>Requested</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php if ($appointmentResult && mysqli_num_rows($appointmentResult) > 0): ?>
                                <?php while ($appointment = mysqli_fetch_assoc($appointmentResult)): ?>
                                    <?php
                                    $appointmentStatus = 'Pending';
                                    if (isset($appointment['vaccination_status'])) {
                                        $appointmentStatus = (string)$appointment['vaccination_status'];
                                    }
                                    $statusClass = 'text-bg-warning';
                                    if (in_array($appointmentStatus, ['Hospital Approved', 'Vaccinated'], true)) {
                                        $statusClass = 'text-bg-info';
                                    } elseif ($appointmentStatus === 'Admin Approved') {
                                        $statusClass = 'text-bg-success';
                                    } elseif (in_array($appointmentStatus, ['Hospital Rejected', 'Admin Rejected', 'Not Vaccinated'], true)) {
                                        $statusClass = 'text-bg-danger';
                                    }
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($appointment['hospital_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($appointment['vaccine_name'] ?? 'Not specified', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($appointment['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><span class="badge <?= $statusClass ?>"><?= htmlspecialchars($appointmentStatus, ENT_QUOTES, 'UTF-8') ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php elseif ($appointmentResult): ?>
                                <tr><td colspan="4" class="text-center text-secondary py-4">No appointment requests yet.</td></tr>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-danger py-4">Appointment status is temporarily unavailable.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </div>

<?php include __DIR__ . '/../includes/portal-footer.php'; ?>
</body>
</html>
