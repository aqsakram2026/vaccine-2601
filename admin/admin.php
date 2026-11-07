<?php
include 'auth.php';
require_once __DIR__ . '/../includes/admin-account.php';
$adminProfileImage = '';
if (evax_initialize_admin_account($connect, $admin_user, $admin_pass)) {
    $imageResult = mysqli_query($connect, 'SELECT profile_image FROM admin_accounts WHERE id = 1 LIMIT 1');
    if ($imageResult) {
        $imageRow = mysqli_fetch_assoc($imageResult);
        if (!empty($imageRow['profile_image'])) {
            $adminProfileImage = '../' . e($imageRow['profile_image']);
        }
        mysqli_free_result($imageResult);
    } else {
        error_log('E-Vaccine admin dashboard profile image lookup failed: ' . mysqli_error($connect));
    }
}

// Dashboard ke numbers (count_of ../config.php mein hai)
$total_vaccines    = count_of("SELECT COUNT(*) AS total FROM vaccines");
$total_hospitals   = count_of("SELECT COUNT(*) AS total FROM hospitals WHERE STATUS = 'accepted'");
$pending_hospitals = count_of("SELECT COUNT(*) AS total FROM hospitals WHERE STATUS = 'pending'");
$total_parents     = count_of("SELECT COUNT(*) AS total FROM parents");
$total_bookings    = count_of("SELECT COUNT(*) AS total FROM bookings");
$total_vaccinated  = count_of("SELECT COUNT(DISTINCT child_id) AS total FROM bookings WHERE vaccination_status = 'Admin Approved'");

$statusLabels = [];
$statusCounts = [];
$statusResult = mysqli_query(
    $connect,
    "SELECT COALESCE(NULLIF(TRIM(vaccination_status), ''), 'Unknown') AS status_label, COUNT(*) AS status_count
     FROM bookings
     GROUP BY COALESCE(NULLIF(TRIM(vaccination_status), ''), 'Unknown')
     ORDER BY status_label"
);
if (!$statusResult) {
    error_log('E-Vaccine admin dashboard booking status graph query failed: ' . mysqli_error($connect));
} else {
    while ($statusRow = mysqli_fetch_assoc($statusResult)) {
        $statusLabels[] = $statusRow['status_label'];
        $statusCounts[] = (int)$statusRow['status_count'];
    }
    mysqli_free_result($statusResult);
}

$monthlyCounts = [];
$monthlyResult = mysqli_query(
    $connect,
    "SELECT DATE_FORMAT(COALESCE(vaccination_date, created_at), '%Y-%m') AS month_key, COUNT(*) AS record_count
     FROM bookings
     WHERE vaccination_status = 'Admin Approved'
       AND COALESCE(vaccination_date, created_at) >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 5 MONTH), '%Y-%m-01')
     GROUP BY DATE_FORMAT(COALESCE(vaccination_date, created_at), '%Y-%m')
     ORDER BY month_key ASC"
);
if (!$monthlyResult) {
    error_log('E-Vaccine admin dashboard monthly vaccination graph query failed: ' . mysqli_error($connect));
} else {
    while ($monthlyRow = mysqli_fetch_assoc($monthlyResult)) {
        $monthlyCounts[$monthlyRow['month_key']] = (int)$monthlyRow['record_count'];
    }
    mysqli_free_result($monthlyResult);
}

$monthLabels = [];
$monthCounts = [];
for ($monthsAgo = 5; $monthsAgo >= 0; $monthsAgo--) {
    $monthDate = strtotime('-' . $monthsAgo . ' months', strtotime(date('Y-m-01')));
    $monthKey = date('Y-m', $monthDate);
    $monthLabels[] = date('M Y', $monthDate);
    if (isset($monthlyCounts[$monthKey])) {
        $monthCounts[] = $monthlyCounts[$monthKey];
    } else {
        $monthCounts[] = 0;
    }
}

$page_title = 'Dashboard';
$active = 'dashboard';
include 'layout_top.php';
?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark">Admin Dashboard</h2>
        <p class="text-secondary small mb-0">Manage vaccines, hospitals, parents and stock.</p>
    </div>
   
        <!-- <a href="profile.php" class="btn btn-outline-primary" aria-label="View admin profile">
            <i class="bi bi-person-circle me-1" aria-hidden="true"></i>Profile
        </a> -->
        <a href="admin_logout.php" class="btn btn-outline-danger">
            <i class="bi bi-box-arrow-right me-1" aria-hidden="true"></i>Logout
        </a>
    <!-- </div> -->
</div>

<?php if ($pending_hospitals > 0) { ?>
    <div class="alert alert-warning d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-exclamation-circle me-2"></i>
            <strong><?= $pending_hospitals ?></strong> hospital approval ka intezar kar rahe hain.
        </span>
        <a href="hospitalUpdate.php" class="btn btn-sm btn-warning">Review karo</a>
    </div>
<?php } ?>

<!-- Numbers -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg">
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <div class="text-secondary small">Vaccines</div>
            <div class="fs-2 fw-bold"><?= $total_vaccines ?></div>
        </div></div>
    </div>
    <div class="col-6 col-lg">
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <div class="text-secondary small">Approved hospitals</div>
            <div class="fs-2 fw-bold"><?= $total_hospitals ?></div>
        </div></div>
    </div>
    <div class="col-6 col-lg">
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <div class="text-secondary small">Pending hospitals</div>
            <div class="fs-2 fw-bold"><?= $pending_hospitals ?></div>
        </div></div>
    </div>
    <div class="col-6 col-lg">
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <div class="text-secondary small">Parents</div>
            <div class="fs-2 fw-bold"><?= $total_parents ?></div>
        </div></div>
    </div>
    <div class="col-6 col-lg">
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <div class="text-secondary small">Bookings</div>
            <div class="fs-2 fw-bold"><?= $total_bookings ?></div>
        </div></div>
    </div>
    <div class="col-6 col-lg">
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <div class="text-secondary small">Approved vaccinated patients</div>
            <div class="fs-2 fw-bold"><?= $total_vaccinated ?></div>
        </div></div>
    </div>
</div>

<section class="mb-5" aria-labelledby="admin-analytics-title">
    <div class="mb-3">
        <h5 class="fw-bold mb-1" id="admin-analytics-title">Vaccination analytics</h5>
        <p class="text-secondary small mb-0">Live summaries based on appointment and admin-reviewed vaccination records.</p>
    </div>
    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h6 class="fw-bold">Admin-approved vaccinations by month</h6>
                    <p class="text-secondary small">Last six months, using recorded vaccination date when available.</p>
                    <div style="height:280px"><canvas id="monthlyVaccinationsChart" role="img" aria-label="Bar chart of admin-approved vaccination records by month"></canvas></div>
                    <?php if (array_sum($monthCounts) === 0): ?><p class="small text-secondary mb-0 mt-2">No approved records are available for this period.</p><?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h6 class="fw-bold">Appointment status breakdown</h6>
                    <p class="text-secondary small">Current count of appointment records in each workflow status.</p>
                    <div style="height:280px"><canvas id="bookingStatusesChart" role="img" aria-label="Doughnut chart of appointments by status"></canvas></div>
                    <?php if (count($statusLabels) === 0): ?><p class="small text-secondary mb-0 mt-2">No appointment records are available yet.</p><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Management Modules -->
<h5 class="fw-bold mb-3">Management Modules</h5>
<div class="row g-3">

    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100"><div class="card-body p-4">
            <i class="bi bi-eyedropper fs-3 text-primary"></i>
            <h6 class="fw-bold mt-3">Vaccines</h6>
            <p class="text-secondary small">View, edit or delete vaccines in the system.</p>
            <a href="vaccines.php" class="text-primary text-decoration-none small fw-bold">Open Module</a>
            <span class="text-secondary small mx-1">|</span>
            <a href="adminvaccineadd.php" class="text-primary text-decoration-none small fw-bold">Add Vaccine</a>
        </div></div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100"><div class="card-body p-4">
            <i class="bi bi-hospital fs-3 text-warning"></i>
            <h6 class="fw-bold mt-3">Hospitals</h6>
            <p class="text-secondary small">Approve or reject hospitals, edit their details, or register a new one.</p>
            <a href="hospitalUpdate.php" class="text-primary text-decoration-none small fw-bold">Open Module</a>
            <span class="text-secondary small mx-1">|</span>
            <a href="admin-hospital_register.php" class="text-primary text-decoration-none small fw-bold">Register Hospital</a>
        </div></div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100"><div class="card-body p-4">
            <i class="bi bi-box-seam fs-3 text-success"></i>
            <h6 class="fw-bold mt-3">Hospital Vaccine Stock</h6>
            <p class="text-secondary small">See every hospital's stock and add new quantities.</p>
            <a href="vaccine_stock_view.php" class="text-primary text-decoration-none small fw-bold">Open Module</a>
            <span class="text-secondary small mx-1">|</span>
            <a href="admin_vaccine_stock.php" class="text-primary text-decoration-none small fw-bold">Update Stock</a>
        </div></div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100"><div class="card-body p-4">
            <i class="bi bi-people fs-3 text-info"></i>
            <h6 class="fw-bold mt-3">Parents</h6>
            <p class="text-secondary small">Manage registered parents or guardians, or register a new one.</p>
            <a href="parentsUpdate.php" class="text-primary text-decoration-none small fw-bold">Open Module</a>
            <span class="text-secondary small mx-1">|</span>
            <a href="adminParentRegistration.php" class="text-primary text-decoration-none small fw-bold">Register Parent</a>
        </div></div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100"><div class="card-body p-4">
            <i class="bi bi-clipboard2-pulse fs-3 text-success"></i>
            <h6 class="fw-bold mt-3">Vaccination Report</h6>
            <p class="text-secondary small">Review hospital vaccination records and see the overall number of admin-approved patients.</p>
            <a href="vaccination_report.php" class="text-primary text-decoration-none small fw-bold">Open Report</a>
        </div></div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
(() => {
    if (!window.Chart) return;
    const monthLabels = <?= json_encode($monthLabels, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const monthCounts = <?= json_encode($monthCounts, JSON_NUMERIC_CHECK) ?>;
    const statusLabels = <?= json_encode($statusLabels, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const statusCounts = <?= json_encode($statusCounts, JSON_NUMERIC_CHECK) ?>;
    const monthlyCanvas = document.getElementById("monthlyVaccinationsChart");
    const statusCanvas = document.getElementById("bookingStatusesChart");

    if (monthlyCanvas) {
        new Chart(monthlyCanvas, {
            type: "bar",
            data: {
                labels: monthLabels,
                datasets: [{
                    label: "Approved vaccination records",
                    data: monthCounts,
                    backgroundColor: "rgba(8, 124, 190, .72)",
                    borderColor: "#087cbe",
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
                plugins: { legend: { display: false } }
            }
        });
    }

    if (statusCanvas) {
        new Chart(statusCanvas, {
            type: "doughnut",
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusCounts,
                    backgroundColor: ["#087cbe", "#079b91", "#f3b33d", "#53a66f", "#dc6472", "#758899", "#7d6bc4", "#e78649"],
                    borderWidth: 2,
                    borderColor: "#fff"
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: "bottom" } }
            }
        });
    }
})();
</script>
<?php include 'layout_bottom.php'; ?>
