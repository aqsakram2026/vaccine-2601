<?php
require_once __DIR__ . '/../includes/parent-auth.php';

require_once __DIR__ . '/../includes/profile-image.php';
$parentId = (int)$_SESSION['user_id']['id'];
$csrfToken = $_SESSION['parent_profile_csrf'] ?? bin2hex(random_bytes(32));
$_SESSION['parent_profile_csrf'] = $csrfToken;
$errors = [];
$flash = $_SESSION['parent_profile_flash'] ?? null;
unset($_SESSION['parent_profile_flash']);

$select = mysqli_prepare(
    $conn,
    'SELECT name, Relation_with_child, Phone_no, email, CNIC, City, address, profile_image
     FROM parents WHERE id = ? LIMIT 1'
);
if (!$select) {
    error_log('E-Vaccine patient profile lookup could not be prepared: ' . mysqli_error($conn));
    http_response_code(500);
    exit('Your profile is temporarily unavailable.');
}
mysqli_stmt_bind_param($select, 'i', $parentId);
if (!mysqli_stmt_execute($select)) {
    error_log('E-Vaccine patient profile lookup failed: ' . mysqli_stmt_error($select));
    mysqli_stmt_close($select);
    http_response_code(500);
    exit('Your profile is temporarily unavailable.');
}
$result = mysqli_stmt_get_result($select);
$profile = null;
if ($result) {
    $profile = mysqli_fetch_assoc($result);
}
mysqli_stmt_close($select);
if (!$profile) {
    http_response_code(404);
    exit('Patient profile not found.');
}

$form = [
    'name' => (string)$profile['name'],
    'relation' => (string)$profile['Relation_with_child'],
    'phone' => (string)$profile['Phone_no'],
    'email' => (string)$profile['email'],
    'cnic' => (string)$profile['CNIC'],
    'city' => (string)$profile['City'],
    'address' => (string)$profile['address']
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($form as $key => $unused) {
        $form[$key] = '';
        if (isset($_POST[$key]) && is_string($_POST[$key])) {
            $form[$key] = trim($_POST[$key]);
        }
    }
    $password = '';
    if (isset($_POST['password']) && is_string($_POST['password'])) {
        $password = $_POST['password'];
    }
    $removeImage = isset($_POST['remove_image']) && $_POST['remove_image'] === '1';

    if (!isset($_POST['csrf']) || !is_string($_POST['csrf']) || !hash_equals($csrfToken, $_POST['csrf'])) {
        $errors[] = 'Your form session expired. Please reload and try again.';
    }
    if ($form['name'] === '' || strlen($form['name']) > 100
        || $form['relation'] === '' || strlen($form['relation']) > 50
        || $form['city'] === '' || strlen($form['city']) > 50
        || $form['address'] === '' || strlen($form['address']) > 1000) {
        $errors[] = 'Complete your profile fields within their allowed lengths.';
    }
    if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL) || strlen($form['email']) > 100) {
        $errors[] = 'Enter a valid email address.';
    }
    if (!preg_match('/^\d{10,15}$/', $form['phone'])) {
        $errors[] = 'Phone number must contain 10 to 15 digits.';
    }
    if (!preg_match('/^\d{13}$/', $form['cnic'])) {
        $errors[] = 'CNIC must contain exactly 13 digits.';
    }
    if ($password !== '' && strlen($password) < 6) {
        $errors[] = 'A new password must be at least 6 characters.';
    }
    if ($removeImage && isset($_FILES['profile_image']) && ($_FILES['profile_image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Choose a new image or remove the current image, not both.';
    }

    $newImage = null;
    if (!$errors && isset($_FILES['profile_image'])) {
        $imageError = '';
        $newImage = evax_store_profile_image($_FILES['profile_image'], $imageError);
        if ($imageError !== '') {
            $errors[] = $imageError;
        }
    }

    if (!$errors) {
        $duplicate = mysqli_prepare($conn, 'SELECT id FROM parents WHERE (email = ? OR CNIC = ?) AND id <> ? LIMIT 1');
        if (!$duplicate) {
            error_log('E-Vaccine patient profile duplicate check could not be prepared: ' . mysqli_error($conn));
            $errors[] = 'Your profile could not be saved right now.';
        } else {
            mysqli_stmt_bind_param($duplicate, 'ssi', $form['email'], $form['cnic'], $parentId);
            if (!mysqli_stmt_execute($duplicate)) {
                error_log('E-Vaccine patient profile duplicate check failed: ' . mysqli_stmt_error($duplicate));
                $errors[] = 'Your profile could not be saved right now.';
            } else {
                $duplicateResult = mysqli_stmt_get_result($duplicate);
                if ($duplicateResult && mysqli_num_rows($duplicateResult) > 0) {
                    $errors[] = 'That email or CNIC is already in use.';
                }
            }
            mysqli_stmt_close($duplicate);
        }
    }

    if (!$errors) {
        $imagePath = $profile['profile_image'];
        if ($removeImage) {
            $imagePath = null;
        }
        if ($newImage !== null) {
            $imagePath = $newImage;
        }
        $update = mysqli_prepare(
            $conn,
            'UPDATE parents
             SET name = ?, Relation_with_child = ?, Phone_no = ?, email = ?, CNIC = ?,
                 City = ?, address = ?, profile_image = ?
             WHERE id = ?'
        );
        if (!$update) {
            error_log('E-Vaccine patient profile update could not be prepared: ' . mysqli_error($conn));
            $errors[] = 'Your profile could not be saved right now.';
        } else {
            mysqli_begin_transaction($conn);
            mysqli_stmt_bind_param(
                $update,
                'ssssssssi',
                $form['name'],
                $form['relation'],
                $form['phone'],
                $form['email'],
                $form['cnic'],
                $form['city'],
                $form['address'],
                $imagePath,
                $parentId
            );
            $saved = mysqli_stmt_execute($update);
            if (!$saved) {
                error_log('E-Vaccine patient profile update failed: ' . mysqli_stmt_error($update));
            }
            mysqli_stmt_close($update);

            if ($saved && $password !== '') {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $passwordUpdate = mysqli_prepare($conn, 'UPDATE parents SET Hashed_password = ? WHERE id = ?');
                if ($passwordUpdate) {
                    mysqli_stmt_bind_param($passwordUpdate, 'si', $passwordHash, $parentId);
                    $saved = mysqli_stmt_execute($passwordUpdate);
                    if (!$saved) {
                        error_log('E-Vaccine patient password update failed: ' . mysqli_stmt_error($passwordUpdate));
                    }
                    mysqli_stmt_close($passwordUpdate);
                } else {
                    error_log('E-Vaccine patient password update could not be prepared: ' . mysqli_error($conn));
                    $saved = false;
                }
            }

            if ($saved) {
                mysqli_commit($conn);
                $_SESSION['user_id']['name'] = $form['name'];
                $_SESSION['user_id']['email'] = $form['email'];
                $_SESSION['user_id']['profile_image'] = $imagePath;
                if ($newImage !== null || $removeImage) {
                    evax_remove_profile_image($profile['profile_image']);
                }
                $_SESSION['parent_profile_flash'] = ['type' => 'success', 'message' => 'Your profile has been updated.'];
                header('Location: profile.php');
                exit;
            }
            mysqli_rollback($conn);
            $errors[] = 'Your profile could not be saved. Please try again.';
        }
        if ($newImage !== null && $errors) {
            evax_remove_profile_image($newImage);
        }
    } elseif ($newImage !== null) {
        evax_remove_profile_image($newImage);
    }
}

$e = static function ($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};
$profileImage = $profile['profile_image'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Patient Profile | E-Vaccine</title>
    <?php include __DIR__ . '/../includes/portal-head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../includes/theme.js?v=6"></script>
    <link href="../includes/theme.css?v=5" rel="stylesheet">
</head>
<body class="bg-light min-vh-100">
    <?php $parentPageTitle = 'Profile & Settings'; include __DIR__ . '/../includes/parent-navbar.php'; ?>
    <main class="container py-5">
        <div class="card border-0 shadow-sm rounded-4 mx-auto" style="max-width: 760px">
            <div class="card-body p-4 p-md-5" id="account-settings">
                <h1 class="h3 fw-bold">Edit patient profile</h1>
                <p class="text-secondary">Update your contact details and profile image.</p>

                <?php if (is_array($flash)): ?>
                    <div class="alert alert-success" role="status"><?= $e($flash['message']) ?></div>
                <?php endif; ?>
                <?php if ($errors): ?>
                    <div class="alert alert-danger" role="alert"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= $e($error) ?></li><?php endforeach; ?></ul></div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf" value="<?= $e($csrfToken) ?>">
                    <input type="hidden" name="MAX_FILE_SIZE" value="2097152">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <?php if ($profileImage !== ''): ?>
                            <img src="../<?= $e($profileImage) ?>" alt="Current profile image" class="rounded-circle border object-fit-cover" width="88" height="88">
                        <?php else: ?>
                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold fs-2" style="width:88px;height:88px" aria-label="No profile image">+</div>
                        <?php endif; ?>
                        <div class="flex-grow-1">
                            <label for="profileImage" class="form-label fw-semibold">Profile image</label>
                            <input class="form-control" id="profileImage" name="profile_image" type="file" accept="image/jpeg,image/png,image/webp">
                            <div class="form-text">JPEG, PNG or WebP; up to 2 MB.</div>
                            <?php if ($profileImage !== ''): ?><div class="form-check mt-2"><input class="form-check-input" id="removeImage" name="remove_image" value="1" type="checkbox"><label class="form-check-label" for="removeImage">Remove current image</label></div><?php endif; ?>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="name">Full name</label><input class="form-control" id="name" name="name" maxlength="100" required value="<?= $e($form['name']) ?>"></div>
                        <div class="col-md-6"><label class="form-label" for="relation">Relationship with child</label><input class="form-control" id="relation" name="relation" maxlength="50" required value="<?= $e($form['relation']) ?>"></div>
                        <div class="col-md-6"><label class="form-label" for="phone">Phone number</label><input class="form-control" id="phone" name="phone" inputmode="numeric" maxlength="15" required value="<?= $e($form['phone']) ?>"></div>
                        <div class="col-md-6"><label class="form-label" for="email">Email</label><input class="form-control" id="email" name="email" type="email" maxlength="100" required value="<?= $e($form['email']) ?>"></div>
                        <div class="col-md-6"><label class="form-label" for="cnic">CNIC</label><input class="form-control" id="cnic" name="cnic" inputmode="numeric" maxlength="13" required value="<?= $e($form['cnic']) ?>"></div>
                        <div class="col-md-6"><label class="form-label" for="city">City</label><input class="form-control" id="city" name="city" maxlength="50" required value="<?= $e($form['city']) ?>"></div>
                        <div class="col-12"><label class="form-label" for="address">Address</label><textarea class="form-control" id="address" name="address" rows="2" required><?= $e($form['address']) ?></textarea></div>
                        <div class="col-12"><label class="form-label" for="password">New password <span class="text-secondary fw-normal">(leave blank to keep current)</span></label><input class="form-control" id="password" name="password" type="password" minlength="6" autocomplete="new-password"></div>
                        <div class="col-12 d-flex gap-2"><button class="btn btn-primary" type="submit">Save profile</button><a class="btn btn-outline-secondary" href="parent_dashboard.php">Cancel</a></div>
                    </div>
                </form>
            </div>
        </div>
    </main>
<?php include __DIR__ . '/../includes/portal-footer.php'; ?>
</body>
</html>
