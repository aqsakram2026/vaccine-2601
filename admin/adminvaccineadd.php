<?php
include 'auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn'])) {

    $name        = esc($_POST['vaccine_name']);
    $dose        = (int)$_POST['dose'];
    $age_limit   = esc($_POST['ageLimit']);
    $description = esc($_POST['description']);
    $status = 'Active';
    if ($_POST['status'] === 'Inactive') {
        $status = 'Inactive';
    }

    if ($name == '' || $dose < 1 || $age_limit == '') {
        $alert = ['danger', 'Vaccine name, dose aur age limit zaroori hain.'];
    } else {
        // Same naam ki vaccine pehle se ho to dobara add nahi hogi
        $check = mysqli_query($connect, "SELECT Vaccine_ID FROM vaccines WHERE Vaccine_Name = '$name'");

        if (mysqli_num_rows($check) > 0) {
            $alert = ['warning', 'Is naam ki vaccine pehle se maujood hai.'];
        } else {
            mysqli_query($connect, "INSERT INTO vaccines (Vaccine_Name, Dose, Age_Limit, Status, description)
                                    VALUES ('$name', $dose, '$age_limit', '$status', '$description')");
            header('Location: vaccines.php?msg=added');
            exit;
        }
    }
}

$page_title = 'Add Vaccine';
$active = 'vaccine_add';
include 'layout_top.php';
?>

<div class="mb-4">
    <h2 class="fw-bold">Add Vaccine</h2>
    <p class="text-secondary small mb-0">Add a new vaccine to the central database.</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Vaccine Name</label>
                    <input type="text" name="vaccine_name" class="form-control" placeholder="e.g. BCG"
                           value="<?= e($_POST['vaccine_name'] ?? '') ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Dose</label>
                    <input type="number" name="dose" class="form-control" min="1" value="<?= e($_POST['dose'] ?? '1') ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Age Limit</label>
                    <input type="text" name="ageLimit" class="form-control" list="age-options" placeholder="e.g. 6 weeks"
                           value="<?= e($_POST['ageLimit'] ?? '') ?>" required>
                    <datalist id="age-options">
                        <option value="At birth">
                        <option value="6 weeks">
                        <option value="10 weeks">
                        <option value="14 weeks">
                        <option value="9 months">
                        <option value="15 months">
                        <option value="0-5 years">
                    </datalist>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="3"
                              placeholder="What does this vaccine protect against?"><?= e($_POST['description'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" name="btn" class="btn btn-primary px-4">Add Vaccine</button>
                    <a href="vaccines.php" class="btn btn-outline-secondary px-4">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include 'layout_bottom.php'; ?>
