<?php
require_once __DIR__ . '/../includes/parent-auth.php';

$parent_id = (int)$_SESSION['user_id']['id'];
$message = '';
$message_type = '';
$childManagementToken = $_SESSION['child_management_csrf'] ?? bin2hex(random_bytes(32));
$_SESSION['child_management_csrf'] = $childManagementToken;
$editChildId = (int)($_GET['id'] ?? 0);
$existingChild = null;

if ($editChildId > 0) {
    $childResult = mysqli_query($conn, "SELECT * FROM children WHERE id = $editChildId AND parent_id = $parent_id LIMIT 1");
    if (!$childResult) {
        error_log('E-Vaccine child profile lookup failed: ' . mysqli_error($conn));
        http_response_code(500);
        exit('The child profile is temporarily unavailable.');
    }
    $existingChild = mysqli_fetch_assoc($childResult);
    if (!$existingChild) {
        http_response_code(404);
        exit('The selected child was not found in your account.');
    }
}

if (isset($_POST['delete_child'])) {
    $deleteChildId = (int)($_POST['child_id'] ?? 0);
    if (!isset($_POST['csrf']) || !is_string($_POST['csrf']) || !hash_equals($childManagementToken, $_POST['csrf'])) {
        $message = 'Your form session expired. Please reload and try again.';
        $message_type = 'warning';
    } elseif ($deleteChildId < 1) {
        $message = 'Select a valid child to delete.';
        $message_type = 'warning';
    } else {
        $deleted = mysqli_query(
            $conn,
            "DELETE FROM children WHERE id = $deleteChildId AND parent_id = $parent_id"
        );
        if (!$deleted) {
            error_log('E-Vaccine child deletion failed: ' . mysqli_error($conn));
            $message = 'The child could not be deleted. Please try again.';
            $message_type = 'danger';
        } elseif (mysqli_affected_rows($conn) !== 1) {
            $message = 'The selected child was not found in your account.';
            $message_type = 'warning';
        } else {
            header('Location: parent_dashboard.php?notice=child-deleted');
            exit;
        }
    }
}

if (isset($_POST['submit'])) {
    $editChildId = (int)($_POST['child_id'] ?? 0);
    if (!isset($_POST['csrf']) || !is_string($_POST['csrf']) || !hash_equals($childManagementToken, $_POST['csrf'])) {
        $message = 'Your form session expired. Please reload and try again.';
        $message_type = 'warning';
    }

    $child_name = '';
    if (isset($_POST['child_name']) && is_string($_POST['child_name'])) {
        $child_name = trim($_POST['child_name']);
    }
    $dob = '';
    if (isset($_POST['date_of_birth']) && is_string($_POST['date_of_birth'])) {
        $dob = trim($_POST['date_of_birth']);
    }
    $gender = 'Male';
    if (isset($_POST['gender']) && $_POST['gender'] === 'Female') {
        $gender = 'Female';
    }
    $allergies = '';
    if (isset($_POST['allergies']) && is_string($_POST['allergies'])) {
        $allergies = trim($_POST['allergies']);
    }
    $last_vaccine = '';
    if (isset($_POST['last_vaccine']) && is_string($_POST['last_vaccine'])) {
        $last_vaccine = trim($_POST['last_vaccine']);
    }
    $vaccination_date = '';
    if (isset($_POST['vaccination_date']) && is_string($_POST['vaccination_date'])) {
        $vaccination_date = trim($_POST['vaccination_date']);
    }
    $today = new DateTimeImmutable('today');
    $birthDate = DateTimeImmutable::createFromFormat('!Y-m-d', $dob);
    $dateIsValid = $birthDate && $birthDate->format('Y-m-d') === $dob;
    $vaccineDate = null;
    if ($vaccination_date !== '') {
        $vaccineDate = DateTimeImmutable::createFromFormat('!Y-m-d', $vaccination_date);
    }

    if ($message === '' && ($child_name === '' || strlen($child_name) > 100 || !$dateIsValid || $birthDate > $today
        || !in_array($gender, ['Male', 'Female'], true) || strlen($allergies) > 100)) {
        $message = 'Enter a valid child name, date of birth, gender, and allergies (up to 100 characters).';
        $message_type = 'warning';
    } elseif ($message === '' && $vaccination_date !== '' && (!$vaccineDate || $vaccineDate->format('Y-m-d') !== $vaccination_date
        || $vaccineDate > $today || $vaccineDate < $birthDate)) {
        $message = 'Enter a valid vaccination date between the child’s birth date and today.';
        $message_type = 'warning';
    } elseif ($message === '' && $last_vaccine === '' && $vaccination_date !== '') {
        $message = 'A vaccination date can only be entered when a last vaccine is selected.';
        $message_type = 'warning';
    }

    if ($message === '' && $editChildId > 0) {
        $ownedChild = mysqli_query($conn, "SELECT id FROM children WHERE id = $editChildId AND parent_id = $parent_id LIMIT 1");
        if (!$ownedChild) {
            error_log('E-Vaccine child ownership check failed: ' . mysqli_error($conn));
            $message = 'Unable to verify this child. Please try again.';
            $message_type = 'danger';
        } elseif (mysqli_num_rows($ownedChild) !== 1) {
            http_response_code(404);
            exit('The selected child was not found in your account.');
        }
    }

    if ($message === '' && $last_vaccine !== '') {
        $vaccineSql = mysqli_real_escape_string($conn, $last_vaccine);
        $vaccineCheck = mysqli_query($conn, "SELECT Vaccine_ID FROM vaccines WHERE Vaccine_Name = '$vaccineSql' LIMIT 1");
        if (!$vaccineCheck) {
            error_log('E-Vaccine child vaccine history validation failed: ' . mysqli_error($conn));
            $message = 'Unable to verify vaccine history. Please try again.';
            $message_type = 'danger';
        } elseif (mysqli_num_rows($vaccineCheck) === 0) {
            $message = 'Select a valid vaccine from the list.';
            $message_type = 'warning';
        }
    }

    if ($message === '') {
        $nameSql = mysqli_real_escape_string($conn, $child_name);
        $dobSql = mysqli_real_escape_string($conn, $dob);
        $genderSql = mysqli_real_escape_string($conn, $gender);
        $allergiesSql = mysqli_real_escape_string($conn, $allergies);
        $vaccineSql = $last_vaccine === '' ? 'NULL' : "'" . mysqli_real_escape_string($conn, $last_vaccine) . "'";
        $vaccinationDateSql = $vaccination_date === '' ? 'NULL' : "'" . mysqli_real_escape_string($conn, $vaccination_date) . "'";

        if ($editChildId > 0) {
            $saved = mysqli_query(
                $conn,
                "UPDATE children
                 SET child_name = '$nameSql', date_of_birth = '$dobSql', gender = '$genderSql',
                     allergies = '$allergiesSql', last_vaccine = $vaccineSql,
                     vaccination_date = $vaccinationDateSql
                 WHERE id = $editChildId AND parent_id = $parent_id"
            );
        } else {
            $saved = mysqli_query(
                $conn,
                "INSERT INTO children (parent_id, child_name, date_of_birth, gender, allergies, last_vaccine, vaccination_date)
                 VALUES ($parent_id, '$nameSql', '$dobSql', '$genderSql', '$allergiesSql', $vaccineSql, $vaccinationDateSql)"
            );
        }

        if (!$saved) {
            error_log('E-Vaccine child profile save failed: ' . mysqli_error($conn));
            $message = 'Unable to save the child profile. Please try again.';
            $message_type = 'danger';
        } elseif ($editChildId > 0) {
            header('Location: parent_dashboard.php?notice=child-updated');
            exit;
        } else {
            $message = 'Child registered successfully!';
            $message_type = 'success';
        }
    }
}

if ($editChildId > 0 && !$existingChild) {
    $childResult = mysqli_query($conn, "SELECT * FROM children WHERE id = $editChildId AND parent_id = $parent_id LIMIT 1");
    if (!$childResult) {
        error_log('E-Vaccine child edit profile lookup failed: ' . mysqli_error($conn));
        http_response_code(500);
        exit('The child profile is temporarily unavailable.');
    }
    $existingChild = mysqli_fetch_assoc($childResult);
    if (!$existingChild) {
        http_response_code(404);
        exit('The selected child was not found in your account.');
    }
}

$formChild = $_POST;
if (!$formChild && $existingChild) {
    $formChild = $existingChild;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $editChildId > 0 ? 'Edit Child' : 'Child Registration' ?> | Vaccination Management System</title>
    <?php include __DIR__ . '/../includes/portal-head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../includes/theme.js?v=6"></script>
    <link href="../includes/theme.css?v=5" rel="stylesheet">
</head>
<body class="bg-light min-vh-100">

    <?php $parentPageTitle = $editChildId > 0 ? 'Edit Child' : 'Child Registration'; include __DIR__ . '/../includes/parent-navbar.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-9 col-lg-7 col-xl-6">
                <div class="card border-0 shadow rounded-4">
                    <div class="card-header bg-white border-0 text-center pt-4 pb-2">
                        <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3"
                             style="width: 65px; height: 65px;">
                            <span class="fs-1 fw-bold text-white">+</span>
                        </div>
                        <h2 class="fw-bold text-primary mb-2"><?= $editChildId > 0 ? 'Edit Child Details' : 'Child Registration' ?></h2>
                        <p class="text-secondary mb-3"><?= $editChildId > 0 ? 'Update your child’s details and parent-entered vaccine history.' : 'Register your child to manage vaccination records. If your baby has not received a vaccine yet, leave the vaccine and date fields empty.' ?></p>
                    </div>

                    <div class="card-body p-4 p-md-5 pt-3">

                        <?php if ($message != '') { ?>
                            <div class="alert alert-<?= e($message_type) ?> text-center rounded-3">
                                <?= e($message) ?>
                                <?php if ($message_type == 'success') { ?>
                                    <br><a href="vaccine_request.php" class="alert-link">Ab appointment book karo</a>
                                <?php } ?>
                            </div>
                        <?php } ?>

                        <form method="POST" action="child.php<?= $editChildId > 0 ? '?id=' . $editChildId : '' ?>">
                            <input type="hidden" name="child_id" value="<?= $editChildId ?>">
                            <input type="hidden" name="csrf" value="<?= e($childManagementToken) ?>">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Child Name</label>
                                <input type="text" name="child_name" class="form-control form-control-lg"
                                       placeholder="Enter child's full name" maxlength="100" value="<?= e($formChild['child_name'] ?? '') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control form-control-lg"
                                       max="<?= date('Y-m-d') ?>" value="<?= e($formChild['date_of_birth'] ?? '') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Gender</label>
                                <select name="gender" class="form-select form-select-lg" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?= ($formChild['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= ($formChild['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Allergies (if any)</label>
                                <input type="text" name="allergies" class="form-control form-control-lg"
                                       placeholder="Enter allergies, if any" maxlength="100" value="<?= e($formChild['allergies'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Last Vaccine Received <span class="text-secondary fw-normal">(if any)</span></label>
                                <select name="last_vaccine" class="form-select form-select-lg">
                                    <option value="">No vaccine received yet</option>
                                    <?php
                                    $run = mysqli_query($conn, "SELECT Vaccine_Name FROM vaccines ORDER BY Vaccine_ID");
                                    if (!$run) {
                                        error_log('E-Vaccine child vaccine list query failed: ' . mysqli_error($conn));
                                    } else {
                                        while ($result = mysqli_fetch_assoc($run)) {
                                        ?>
                                            <option value="<?= e($result['Vaccine_Name']) ?>" <?= ($formChild['last_vaccine'] ?? '') === $result['Vaccine_Name'] ? 'selected' : '' ?>><?= e($result['Vaccine_Name']) ?></option>
                                        <?php }
                                    } ?>
                                </select>
                             
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Last Vaccination Date <span class="text-secondary fw-normal">(if known)</span></label>
                                <input type="date" name="vaccination_date" class="form-control form-control-lg"
                                       min="<?= e($formChild['date_of_birth'] ?? '') ?>" max="<?= date('Y-m-d') ?>"
                                       value="<?= e($formChild['vaccination_date'] ?? '') ?>">
                                <div class="form-text">Leave blank if no vaccine has been received or the date is unknown.</div>
                            </div>

                            <button type="submit" name="submit" class="btn btn-primary btn-lg w-100 fw-semibold shadow-sm">
                                <?= $editChildId > 0 ? 'Save Child Details' : 'Register Child' ?>
                            </button>
                        </form>
                        <?php if ($editChildId > 0): ?>
                            <form method="post" action="child.php" class="mt-3" onsubmit="return confirm('Delete this child and all associated appointment records? This cannot be undone.');">
                                <input type="hidden" name="child_id" value="<?= $editChildId ?>">
                                <input type="hidden" name="csrf" value="<?= e($childManagementToken) ?>">
                                <button type="submit" class="btn btn-outline-danger w-100" name="delete_child" value="1">Delete child and records</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="parent_dashboard.php" class="text-decoration-none text-secondary">&larr; Back to Parent Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php include __DIR__ . '/../includes/portal-footer.php'; ?>
</body>
</html>
