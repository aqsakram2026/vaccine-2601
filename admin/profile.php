<?php
include __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/admin-account.php';
require_once __DIR__ . '/../includes/profile-image.php';

$profileError = '';
$profileMessage = '';
$csrfToken = $_SESSION['admin_profile_csrf'] ?? bin2hex(random_bytes(32));
$_SESSION['admin_profile_csrf'] = $csrfToken;
$accountReady = evax_initialize_admin_account($connect, $admin_user, $admin_pass);
$profileImage = null;
if ($accountReady) {
    $imageResult = mysqli_query($connect, 'SELECT profile_image FROM admin_accounts WHERE id = 1 LIMIT 1');
    if ($imageResult) {
        $imageRow = mysqli_fetch_assoc($imageResult);
        $profileImage = $imageRow['profile_image'] ?? null;
        mysqli_free_result($imageResult);
    } else {
        error_log('E-Vaccine admin profile image lookup failed: ' . mysqli_error($connect));
        $accountReady = false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_admin_profile'])) {
    $newUsername = isset($_POST['username']) && is_string($_POST['username']) ? trim($_POST['username']) : '';
    $currentPassword = isset($_POST['current_password']) && is_string($_POST['current_password'])
        ? $_POST['current_password']
        : '';
    $postedCsrf = isset($_POST['csrf']) && is_string($_POST['csrf']) ? $_POST['csrf'] : '';
    $removeImage = isset($_POST['remove_image']) && $_POST['remove_image'] === '1';
    $hasUpload = isset($_FILES['profile_image'])
        && ($_FILES['profile_image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    $newImage = null;

    if (!hash_equals($csrfToken, $postedCsrf)) {
        $profileError = 'Your form session expired. Please try again.';
    } elseif (!$accountReady) {
        $profileError = 'The admin profile is temporarily unavailable.';
    } elseif ($newUsername === '' || strlen($newUsername) > 100) {
        $profileError = 'Enter a username containing 1 to 100 characters.';
    } elseif ($currentPassword === '') {
        $profileError = 'Enter your current password to confirm this change.';
    } elseif ($removeImage && $hasUpload) {
        $profileError = 'Choose a new image or remove the current image, not both.';
    } else {
        if ($hasUpload) {
            $imageError = '';
            $newImage = evax_store_profile_image($_FILES['profile_image'], $imageError);
            if ($imageError !== '') {
                $profileError = $imageError;
            }
        }
    }

    if ($profileError === '') {
        $lookup = mysqli_prepare($connect, 'SELECT password_hash FROM admin_accounts WHERE id = 1 LIMIT 1');
        if (!$lookup) {
            error_log('E-Vaccine admin profile password query could not be prepared: ' . mysqli_error($connect));
            $profileError = 'The admin profile could not be updated. Please try again.';
        } else {
            if (!mysqli_stmt_execute($lookup)) {
                error_log('E-Vaccine admin profile password lookup failed: ' . mysqli_stmt_error($lookup));
                $profileError = 'The admin profile could not be updated. Please try again.';
            } else {
                mysqli_stmt_bind_result($lookup, $passwordHash);
                $passwordFound = mysqli_stmt_fetch($lookup);
                if (!$passwordFound || !password_verify($currentPassword, $passwordHash)) {
                    $profileError = 'Your current password is incorrect.';
                }
            }
            mysqli_stmt_close($lookup);
        }

        if ($profileError === '') {
            $oldProfileImage = $profileImage;
            $imagePath = $removeImage ? null : ($newImage ?? $profileImage);
            $update = mysqli_prepare(
                $connect,
                'UPDATE admin_accounts SET username = ?, profile_image = ? WHERE id = 1'
            );
            if (!$update) {
                error_log('E-Vaccine admin username update could not be prepared: ' . mysqli_error($connect));
                $profileError = 'The admin profile could not be updated. Please try again.';
            } else {
                mysqli_stmt_bind_param($update, 'ss', $newUsername, $imagePath);
                if (mysqli_stmt_execute($update)) {
                    $_SESSION['admin'] = $newUsername;
                    $profileImage = $imagePath;
                    $_SESSION['admin_profile_csrf'] = bin2hex(random_bytes(32));
                    $csrfToken = $_SESSION['admin_profile_csrf'];
                    if ($newImage !== null || $removeImage) {
                        evax_remove_profile_image($oldProfileImage);
                    }
                    $profileMessage = 'Your profile has been updated.';
                } elseif (mysqli_stmt_errno($update) === 1062) {
                    $profileError = 'That username is already in use.';
                } else {
                    error_log('E-Vaccine admin username update failed: ' . mysqli_stmt_error($update));
                    $profileError = 'The admin profile could not be updated. Please try again.';
                }
                mysqli_stmt_close($update);
            }
        }
    }
    if ($newImage !== null && $profileError !== '') {
        evax_remove_profile_image($newImage);
    }
}

$page_title = 'Admin Profile';
$active = 'profile';
include __DIR__ . '/layout_top.php';
?>

<div class="mb-4">
    <h2 class="fw-bold">Admin Profile</h2>
    <p class="text-secondary small mb-0">Update your administrator username and profile image.</p>
</div>

<section class="card border-0 shadow-sm rounded-4" aria-labelledby="admin-profile-title">
    <div class="card-body p-4 p-md-5">
        <?php if ($profileError !== ''): ?>
            <div class="alert alert-danger" role="alert"><?= e($profileError) ?></div>
        <?php elseif ($profileMessage !== ''): ?>
            <div class="alert alert-success" role="status"><?= e($profileMessage) ?></div>
        <?php endif; ?>
        <div class="d-flex align-items-center gap-3 mb-4">
            <span class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle"
                  style="width:64px;height:64px;overflow:hidden" aria-hidden="true">
                <?php if ($profileImage): ?>
                    <img src="../<?= e($profileImage) ?>" alt="" class="w-100 h-100 object-fit-cover">
                <?php else: ?>
                    <i class="bi bi-person-fill-gear fs-2"></i>
                <?php endif; ?>
            </span>
            <div>
                <h3 class="h4 fw-bold mb-1" id="admin-profile-title"><?= e($_SESSION['admin']) ?></h3>
                <span class="badge text-bg-primary">Administrator</span>
            </div>
        </div>

        <form method="post" enctype="multipart/form-data" class="mb-4">
            <input type="hidden" name="csrf" value="<?= e($csrfToken) ?>">
            <div class="mb-3">
                <label class="form-label fw-semibold" for="adminProfileImage">Profile image</label>
                <input class="form-control" id="adminProfileImage" name="profile_image" type="file" accept="image/jpeg,image/png,image/webp">
                <div class="form-text">JPEG, PNG, or WebP; maximum 2 MB.</div>
                <?php if ($profileImage): ?>
                    <div class="form-check mt-2">
                        <input class="form-check-input" id="removeAdminProfileImage" type="checkbox" name="remove_image" value="1">
                        <label class="form-check-label" for="removeAdminProfileImage">Remove current profile image</label>
                    </div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold" for="adminUsername">Username</label>
                <input class="form-control" id="adminUsername" name="username" maxlength="100" required value="<?= e($_SESSION['admin']) ?>" autocomplete="username">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold" for="currentPassword">Current password</label>
                <input class="form-control" id="currentPassword" name="current_password" type="password" required autocomplete="current-password">
                <div class="form-text">Confirm your current password to save profile changes.</div>
            </div>
            <button type="submit" name="update_admin_profile" value="1" class="btn btn-primary">
                <i class="bi bi-check2-circle me-1" aria-hidden="true"></i>Save profile
            </button>
        </form>

        <a href="admin.php" class="btn btn-outline-primary me-2">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Back to dashboard
        </a>
        <a href="admin_logout.php" class="btn btn-danger">
            <i class="bi bi-box-arrow-right me-1" aria-hidden="true"></i>Logout
        </a>
    </div>
</section>

<?php include __DIR__ . '/layout_bottom.php'; ?>
