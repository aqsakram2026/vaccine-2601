<?php
include 'auth.php';

// Wahi areas jo register form mein hain
$cities = ['Karachi Central', 'Karachi East', 'Karachi South', 'Karachi West'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn'])) {

    $id       = (int)$_POST['id'];
    $name     = esc($_POST['newname']);
    $auth     = esc($_POST['auth_name']);
    $mail     = esc($_POST['mail']);
    $contact  = esc($_POST['cont']);
    $city     = esc($_POST['city']);
    $address  = esc($_POST['address']);
    $status   = $_POST['status'];

    if ($status != 'accepted' && $status != 'rejected') { $status = 'pending'; }

    // Email ya hospital ka naam kisi doosre hospital ka to nahi?
    $dup = mysqli_query($connect, "SELECT id FROM hospitals WHERE (email = '$mail' OR hospital_name = '$name') AND id <> $id");

    if ($name == '' || $mail == '' || $contact == '') {
        $alert = ['danger', 'Hospital name, email aur contact zaroori hain.'];
    } elseif (mysqli_num_rows($dup) > 0) {
        $alert = ['warning', 'Ye email ya hospital ka naam kisi doosre hospital ke paas hai.'];
    } else {
        mysqli_query($connect, "UPDATE hospitals
                                SET hospital_name = '$name', authorized_name = '$auth', email = '$mail',
                                    contact_number = '$contact', city = '$city', address = '$address', STATUS = '$status'
                                WHERE id = $id");
        header('Location: hospitalUpdate.php?msg=updated');
        exit;
    }
    $hospital = [
        'id' => $id, 'hospital_name' => $_POST['newname'], 'authorized_name' => $_POST['auth_name'],
        'email' => $_POST['mail'], 'contact_number' => $_POST['cont'], 'city' => $_POST['city'],
        'address' => $_POST['address'], 'STATUS' => $status,
    ];
} else {
    $id = 0;
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
    }
    $run = mysqli_query($connect, "SELECT * FROM hospitals WHERE id = $id");
    $hospital = mysqli_fetch_assoc($run);

    if (!$hospital) {
        header('Location: hospitalUpdate.php?msg=notfound');
        exit;
    }
}

if (!in_array($hospital['city'], $cities)) { $cities[] = $hospital['city']; }

$page_title = 'Edit Hospital';
$active = 'hospitals';
include 'layout_top.php';
?>

<div class="mb-4">
    <h2 class="fw-bold">Edit Hospital</h2>
    <p class="text-secondary small mb-0">Hospital ID: <?= (int)$hospital['id'] ?></p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="POST">
            <input type="hidden" name="id" value="<?= (int)$hospital['id'] ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Hospital Name</label>
                    <input type="text" name="newname" class="form-control" value="<?= e($hospital['hospital_name']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Authorized Person</label>
                    <input type="text" name="auth_name" class="form-control" value="<?= e($hospital['authorized_name']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="mail" class="form-control" value="<?= e($hospital['email']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Contact Number</label>
                    <input type="text" name="cont" class="form-control" value="<?= e($hospital['contact_number']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">City</label>
                    <select name="city" class="form-select">
                        <?php foreach ($cities as $c) { ?>
                            <option value="<?= e($c) ?>" <?php if ($hospital['city'] === $c) { echo 'selected'; } ?>><?= e($c) ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="pending" <?php if ($hospital['STATUS'] === 'pending') { echo 'selected'; } ?>>Pending</option>
                        <option value="accepted" <?php if ($hospital['STATUS'] === 'accepted') { echo 'selected'; } ?>>Accepted</option>
                        <option value="rejected" <?php if ($hospital['STATUS'] === 'rejected') { echo 'selected'; } ?>>Rejected</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Address</label>
                    <textarea name="address" class="form-control" rows="2"><?= e($hospital['address']) ?></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" name="btn" class="btn btn-primary px-4">Save Changes</button>
                    <a href="hospitalUpdate.php" class="btn btn-outline-secondary px-4">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include 'layout_bottom.php'; ?>
