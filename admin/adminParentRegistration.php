<?php
include 'auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn'])) {

    $name     = trim($_POST['nm']);
    $mail     = trim($_POST['mail']);
    $relation = trim($_POST['relation']);
    $contact  = trim($_POST['contact']);
    $cnic     = trim($_POST['cnic']);
    $address  = trim($_POST['address']);
    $city     = trim($_POST['city']);
    $pass     = $_POST['pass'];

    if ($name == '' || $mail == '' || $pass == '' || $address == '' || $city == '') {
        $alert = ['danger', 'Saari fields bharna zaroori hai.'];
    } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $alert = ['danger', 'Email sahi nahi hai.'];
    } elseif (!is_numeric($contact) || strlen($contact) != 11) {
        $alert = ['danger', 'Phone number 11 digits ka hona chahiye.'];
    } elseif (!is_numeric($cnic) || strlen($cnic) != 13) {
        $alert = ['danger', 'CNIC 13 digits ka hona chahiye.'];
    } elseif (strlen($pass) < 6) {
        $alert = ['danger', 'Password kam az kam 6 characters ka ho.'];
    } else {
        $sql_mail = esc($mail);
        $check = mysqli_query($connect, "SELECT id FROM parents WHERE email = '$sql_mail' OR CNIC = '" . esc($cnic) . "'");

        if (mysqli_num_rows($check) > 0) {
            $alert = ['warning', 'Is email ya CNIC se parent pehle se registered hai.'];
        } else {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);

            mysqli_query($connect, "INSERT INTO parents (name, Relation_with_child, Phone_no, email, CNIC, Hashed_password, City, address)
                                    VALUES ('" . esc($name) . "', '" . esc($relation) . "', '" . esc($contact) . "', '$sql_mail',
                                            '" . esc($cnic) . "', '$hashed', '" . esc($city) . "', '" . esc($address) . "')");
            header('Location: parentsUpdate.php?msg=added');
            exit;
        }
    }
}

$page_title = 'Register Parent';
$active = 'parent_add';
include 'layout_top.php';
?>

<div class="mb-4">
    <h2 class="fw-bold">Register Parent</h2>
    <p class="text-secondary small mb-0">Parent ya guardian ko system mein register karo.</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Full Name</label>
                    <input type="text" name="nm" class="form-control" placeholder="adil hussain"
                           value="<?= e($_POST['nm'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="mail" class="form-control" placeholder="adil@gmail.com"
                           value="<?= e($_POST['mail'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Relation with child</label>
                    <select name="relation" class="form-select">
                        <option value="guardian">Guardian</option>
                        <option value="father">Father</option>
                        <option value="mother">Mother</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Phone Number</label>
                    <input type="tel" name="contact" class="form-control" maxlength="11" placeholder="03XXXXXXXXX"
                           value="<?= e($_POST['contact'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">CNIC (13 digits, bina dashes)</label>
                    <input type="text" name="cnic" class="form-control" inputmode="numeric" maxlength="13"
                           value="<?= e($_POST['cnic'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">City</label>
                    <input type="text" name="city" class="form-control" placeholder="karachi"
                           value="<?= e($_POST['city'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Address</label>
                    <input type="text" name="address" class="form-control" placeholder="Street, area"
                           value="<?= e($_POST['address'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Password</label>
                    <input type="password" name="pass" class="form-control" minlength="6" required>
                </div>
                <div class="col-12">
                    <button type="submit" name="btn" class="btn btn-primary px-4">Register Parent</button>
                    <a href="parentsUpdate.php" class="btn btn-outline-secondary px-4">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include 'layout_bottom.php'; ?>
