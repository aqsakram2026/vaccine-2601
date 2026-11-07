<?php
require_once __DIR__ . '/../includes/admin-account.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/parent-reset-requests.php';

if (!isset($active)) {
    $active = '';
}

// Simple direct MySQL query (No PDO, No Prepared Statements)
$adminProfileImage = '';
$result = mysqli_query($connect, "SELECT profile_image FROM admin_accounts WHERE id = 1 LIMIT 1");
$pendingParentResetRequests = 0;
if (evax_ensure_parent_reset_requests_table($connect)) {
    $pendingResult = mysqli_query($connect, "SELECT COUNT(*) AS total FROM parent_password_reset_requests WHERE status = 'Pending'");
    if ($pendingResult) {
        $pendingRow = mysqli_fetch_assoc($pendingResult);
        $pendingParentResetRequests = (int)($pendingRow['total'] ?? 0);
        mysqli_free_result($pendingResult);
    } else {
        error_log('E-Vaccine admin reset request notification count failed: ' . mysqli_error($connect));
    }
}

if ($result && $row = mysqli_fetch_assoc($result)) {
    if (!empty($row['profile_image'])) {
        $adminProfileImage = '../' . $row['profile_image'];
    }
}
?>

<!-- Sidebar -->
<aside class="admin-sidebar col-12 col-md-3 col-lg-2 bg-dark text-white p-3 d-flex flex-column">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-bold mb-0">
            <i class="bi bi-shield-plus text-info me-2"></i>E-Vaccine
        </h5>
        <button class="navbar-toggler d-md-none border border-light text-white" type="button"
                data-bs-toggle="collapse" data-bs-target="#adminSidebarMenu">
            <i class="bi bi-list fs-4"></i>
        </button>
    </div>

    <div class="collapse d-md-flex flex-column flex-grow-1" id="adminSidebarMenu">
        
 
        <!-- Navigation Links -->
        <ul class="nav flex-column gap-1 flex-grow-1">
            <li class="nav-item">
                <a href="admin.php" class="nav-link text-white rounded <?= ($active == 'dashboard') ? 'active bg-primary' : '' ?>">
                    <i class="bi bi-grid-fill me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="profile.php" class="nav-link text-white rounded <?= ($active == 'profile') ? 'active bg-primary' : '' ?>">
                     <?php if (!empty($adminProfileImage)): ?>
                <img src="<?= $adminProfileImage ?>" class="rounded-circle me-2" width="32" height="32">
            <?php else: ?>
                <i class="bi bi-person-fill-gear text-primary me-2 fs-5"></i>
            <?php endif; ?>
           <?= $_SESSION['admin'] ?? 'Admin' ?> Profile
                </a>
            </li>

       

            <li class="nav-item">
                <a href="vaccines.php" class="nav-link text-white rounded <?= ($active == 'vaccines') ? 'active bg-primary' : '' ?>">
                    <i class="bi bi-eyedropper me-2"></i>Vaccines
                </a>
            </li>
            <li class="nav-item">
                <a href="adminvaccineadd.php" class="nav-link text-white rounded <?= ($active == 'vaccine_add') ? 'active bg-primary' : '' ?>">
                    <i class="bi bi-plus-circle me-2"></i>Add Vaccine
                </a>
            </li>
            <li class="nav-item">
                <a href="hospitalUpdate.php" class="nav-link text-white rounded <?= ($active == 'hospitals') ? 'active bg-primary' : '' ?>">
                    <i class="bi bi-hospital me-2"></i>Hospitals
                </a>
            </li>
            <li class="nav-item">
                <a href="admin-hospital_register.php" class="nav-link text-white rounded <?= ($active == 'hospital_add') ? 'active bg-primary' : '' ?>">
                    <i class="bi bi-building-add me-2"></i>Register Hospital
                </a>
            </li>
            <li class="nav-item">
                <a href="vaccine_stock_view.php" class="nav-link text-white rounded <?= ($active == 'stock_view') ? 'active bg-primary' : '' ?>">
                    <i class="bi bi-box me-2"></i>Hospital Stock
                </a>
            </li>
            <li class="nav-item">
                <a href="admin_vaccine_stock.php" class="nav-link text-white rounded <?= ($active == 'stock_update') ? 'active bg-primary' : '' ?>">
                    <i class="bi bi-box-seam me-2"></i>Update Stock
                </a>
            </li>
            <li class="nav-item">
                <a href="parentsUpdate.php" class="nav-link text-white rounded <?= ($active == 'parents') ? 'active bg-primary' : '' ?>">
                    <i class="bi bi-people me-2"></i>Parents
                </a>
            </li>
            <li class="nav-item">
                <a href="adminParentRegistration.php" class="nav-link text-white rounded <?= ($active == 'parent_add') ? 'active bg-primary' : '' ?>">
                    <i class="bi bi-person-plus me-2"></i>Register Parent
                </a>
            </li>
            <li class="nav-item">
                <a href="password-reset-requests.php" class="nav-link text-white rounded <?= ($active == 'password_resets') ? 'active bg-primary' : '' ?>">
                    <i class="bi bi-key me-2"></i>Password Reset Requests
                    <?php if ($pendingParentResetRequests > 0): ?><span class="badge rounded-pill text-bg-danger ms-1"><?= $pendingParentResetRequests > 99 ? '99+' : $pendingParentResetRequests ?></span><?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a href="vaccination_report.php" class="nav-link text-white rounded <?= ($active == 'vaccination_report') ? 'active bg-primary' : '' ?>">
                    <i class="bi bi-clipboard2-pulse me-2"></i>Vaccination Report
                </a>
            </li>
        </ul>

        <!-- Bottom Links -->
        <div class="border-top border-secondary pt-3 mt-3">
            <a href="../index.php" class="nav-link text-secondary px-3 py-1">
                <i class="bi bi-house me-2"></i>Home page
            </a>
            <a href="admin_logout.php" class="nav-link text-danger px-3 py-1">
                <i class="bi bi-box-arrow-right me-2"></i>Logout
            </a>
        </div>

    </div>
</aside>
