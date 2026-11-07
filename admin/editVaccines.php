<?php
include 'auth.php';

// Update button dabane par
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn'])) {

    $id          = (int)$_POST['id'];
    $name        = esc($_POST['newname']);
    $dose        = (int)$_POST['dose'];
    $age_limit   = esc($_POST['agelimit']);
    $description = esc($_POST['description']);
    $status = 'Active';
    if ($_POST['status'] === 'Inactive') {
        $status = 'Inactive';
    }

    // Kisi doosri vaccine ka naam same to nahi?
    $check = mysqli_query($connect, "SELECT Vaccine_ID FROM vaccines WHERE Vaccine_Name = '$name' AND Vaccine_ID <> $id");

    if ($name == '' || $dose < 1 || $age_limit == '') {
        $alert = ['danger', 'Vaccine name, dose aur age limit zaroori hain.'];
    } elseif (mysqli_num_rows($check) > 0) {
        $alert = ['warning', 'Is naam ki doosri vaccine pehle se maujood hai.'];
    } else {
        mysqli_query($connect, "UPDATE vaccines
                                SET Vaccine_Name = '$name', Dose = $dose, Age_Limit = '$age_limit',
                                    Status = '$status', description = '$description'
                                WHERE Vaccine_ID = $id");
        header('Location: vaccines.php?msg=updated');
        exit;
    }
    $vaccine = [
        'Vaccine_ID' => $id, 'Vaccine_Name' => $_POST['newname'], 'Dose' => $_POST['dose'],
        'Age_Limit' => $_POST['agelimit'], 'Status' => $status, 'description' => $_POST['description'],
    ];
} else {
    // Page kholne par vaccine database se load hoti hai
    $id = 0;
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
    }
    $run = mysqli_query($connect, "SELECT * FROM vaccines WHERE Vaccine_ID = $id");
    $vaccine = mysqli_fetch_assoc($run);

    if (!$vaccine) {
        header('Location: vaccines.php?msg=notfound');
        exit;
    }
}

$page_title = 'Edit Vaccine';
$active = 'vaccines';
include 'layout_top.php';
?>

<div class="mb-4">
    <h2 class="fw-bold">Edit Vaccine</h2>
    <p class="text-secondary small mb-0">Vaccine ID: <?= (int)$vaccine['Vaccine_ID'] ?></p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="POST">
            <input type="hidden" name="id" value="<?= (int)$vaccine['Vaccine_ID'] ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Vaccine Name</label>
                    <input type="text" name="newname" class="form-control" value="<?= e($vaccine['Vaccine_Name']) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Dose</label>
                    <input type="number" name="dose" class="form-control" min="1" value="<?= e($vaccine['Dose']) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="Active" <?php if ($vaccine['Status'] === 'Active') { echo 'selected'; } ?>>Active</option>
                        <option value="Inactive" <?php if ($vaccine['Status'] === 'Inactive') { echo 'selected'; } ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Age Limit</label>
                    <input type="text" name="agelimit" class="form-control" value="<?= e($vaccine['Age_Limit']) ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?= e($vaccine['description']) ?></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" name="btn" class="btn btn-primary px-4">Save Changes</button>
                    <a href="vaccines.php" class="btn btn-outline-secondary px-4">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include 'layout_bottom.php'; ?>
