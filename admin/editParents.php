<?php
include 'auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn'])) {

    $id       = (int)$_POST['id'];
    $name     = esc($_POST['newname']);
    $relation = esc($_POST['relation']);
    $mail     = esc($_POST['mail']);
    $cnic     = esc($_POST['cnic']);
    $contact  = esc($_POST['cont']);
    $city     = esc($_POST['city']);
    $address  = esc($_POST['address']);

    if ($name == '' || $mail == '') {
        $alert = ['danger', 'Name aur email zaroori hain.'];
    } elseif (!is_numeric($cnic) || strlen($cnic) != 13) {
        $alert = ['danger', 'CNIC 13 digits ka hona chahiye.'];
    } else {
        mysqli_query($connect, "UPDATE parents
                                SET name = '$name', Relation_with_child = '$relation', Phone_no = '$contact',
                                    email = '$mail', CNIC = '$cnic', City = '$city', address = '$address'
                                WHERE id = $id");
        header('Location: parentsUpdate.php?msg=updated');
        exit;
    }
    $parent = [
        'id' => $id, 'name' => $_POST['newname'], 'Relation_with_child' => $_POST['relation'],
        'email' => $_POST['mail'], 'CNIC' => $_POST['cnic'], 'Phone_no' => $_POST['cont'],
        'City' => $_POST['city'], 'address' => $_POST['address'],
    ];
} else {
    $id = 0;
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
    }
    $run = mysqli_query($connect, "SELECT * FROM parents WHERE id = $id");
    $parent = mysqli_fetch_assoc($run);

    if (!$parent) {
        header('Location: parentsUpdate.php?msg=notfound');
        exit;
    }
}

$relation = strtolower($parent['Relation_with_child']);

$page_title = 'Edit Parent';
$active = 'parents';
include 'layout_top.php';
?>

<div class="mb-4">
    <h2 class="fw-bold">Edit Parent</h2>
    <p class="text-secondary small mb-0">Parent ID: <?= (int)$parent['id'] ?></p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="POST">
            <input type="hidden" name="id" value="<?= (int)$parent['id'] ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Full Name</label>
                    <input type="text" name="newname" class="form-control" value="<?= e($parent['name']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Relation with child</label>
                    <select name="relation" class="form-select">
                        <option value="mother" <?php if ($relation === 'mother') { echo 'selected'; } ?>>Mother</option>
                        <option value="father" <?php if ($relation === 'father') { echo 'selected'; } ?>>Father</option>
                        <option value="guardian" <?php if ($relation === 'guardian') { echo 'selected'; } ?>>Guardian</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">CNIC</label>
                    <input type="text" name="cnic" class="form-control" maxlength="13" value="<?= e($parent['CNIC']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="mail" class="form-control" value="<?= e($parent['email']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Phone</label>
                    <input type="text" name="cont" class="form-control" value="<?= e($parent['Phone_no']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">City</label>
                    <input type="text" name="city" class="form-control" value="<?= e($parent['City']) ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Address</label>
                    <textarea name="address" class="form-control" rows="2"><?= e($parent['address']) ?></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" name="btn" class="btn btn-primary px-4">Save Changes</button>
                    <a href="parentsUpdate.php" class="btn btn-outline-secondary px-4">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include 'layout_bottom.php'; ?>
