<?php
$parentPageTitle = $parentPageTitle ?? 'Parent Portal';
$parentAvatarUrl = $parentProfileImage !== '' ? '../' . $parentProfileImage : '';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="parent_dashboard.php">
            <span class="me-2">+</span> Vaccination Management System
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#parentNavbar"
                aria-controls="parentNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="parentNavbar">
            <div class="navbar-nav me-auto">
                <a class="nav-link" href="parent_dashboard.php">Dashboard</a>
                <a class="nav-link" href="vaccine_request.php">Vaccine Request</a>
                <a class="nav-link" href="reset-request-status.php">Password Reset Status</a>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="navbar-text text-white"><?= e($parentPageTitle) ?></span>
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle d-inline-flex align-items-center gap-2"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php if ($parentAvatarUrl !== ''): ?>
                            <img src="<?= e($parentAvatarUrl) ?>" alt="" class="rounded-circle object-fit-cover" width="30" height="30">
                        <?php else: ?>
                            <img src="../images/default-avatar.svg" alt="" class="rounded-circle" width="30" height="30">
                        <?php endif; ?>
                        <span class="text-truncate" style="max-width: 150px"><?= e($parentName) ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header"><?= e($parentName) ?></h6></li>
                        <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                        <li><a class="dropdown-item" href="profile.php#account-settings">Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="parent_logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
