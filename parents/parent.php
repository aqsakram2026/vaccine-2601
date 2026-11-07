<?php
session_start();
include_once __DIR__ . '/../config.php';

$error = '';
$done  = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn'])) {

    $name     = trim($_POST['nm']);
    $mail     = trim($_POST['mail']);
    $relation = trim($_POST['relation']);
    $contact  = trim($_POST['contact']);
    $cnic     = trim($_POST['cnic']);
    $address  = trim($_POST['address']);
    $city     = trim($_POST['city']);
    $pass     = $_POST['pass'];

    if (!isset($_POST['checkbox'])) {
        $error = 'Terms aur policies accept karna zaroori hai.';
    } elseif ($name == '' || $mail == '' || $pass == '' || $address == '' || $city == '') {
        $error = 'Please fill all the fields.';
    } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email is invalid.';
    } elseif (!is_numeric($contact) || strlen($contact) != 11) {
        $error = 'Phone number 11 digits ka hona chahiye.';
    } elseif (!is_numeric($cnic) || strlen($cnic) != 13) {
        $error = 'CNIC 13 digits ka hona chahiye (bina dashes).';
    } elseif (strlen($pass) < 6) {
        $error = 'Password kam az kam 6 characters ka ho.';
    } else {
        $sql_mail = mysqli_real_escape_string($conn, $mail);
        $sql_cnic = mysqli_real_escape_string($conn, $cnic);

        $check = mysqli_query($conn, "SELECT id FROM parents WHERE email = '$sql_mail' OR CNIC = '$sql_cnic'");

        if (!$check) {
            error_log('E-Vaccine parent duplicate account check failed: ' . mysqli_error($conn));
            $error = 'Unable to verify your account details. Please try again.';
        } elseif (mysqli_num_rows($check) > 0) {
            $error = 'Is email ya CNIC se account pehle se bana hua hai.';
        } else {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);
            $nameSql = mysqli_real_escape_string($conn, $name);
            $relationSql = mysqli_real_escape_string($conn, $relation);
            $contactSql = mysqli_real_escape_string($conn, $contact);
            $citySql = mysqli_real_escape_string($conn, $city);
            $addressSql = mysqli_real_escape_string($conn, $address);
            $hashSql = mysqli_real_escape_string($conn, $hashed);
            $done = mysqli_query(
                $conn,
                "INSERT INTO parents (name, Relation_with_child, Phone_no, email, CNIC, Hashed_password, City, address)
                 VALUES ('$nameSql', '$relationSql', '$contactSql', '$sql_mail',
                         '$sql_cnic', '$hashSql', '$citySql', '$addressSql')"
            );
            if (!$done) {
                error_log('E-Vaccine parent registration failed: ' . mysqli_error($conn));
                $error = 'Unable to create your account. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Registration | Vaccination Management System</title>
    <?php include __DIR__ . '/../includes/portal-head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../includes/theme.js?v=6"></script>
    <link href="../includes/theme.css?v=5" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.css" integrity="sha512-x9WwyMYBnlXMNQ6kQ/Lyzu1NqIhLQKL5Oq6xByfXuRj7s9CskyCbLv/1IjqzJmXwFXWr0ov6jBV7Qbc0hh9nHg==" crossorigin="anonymous" referrerpolicy="no-referrer">

</head>
<body class="bg-light min-vh-100">
    <?php include __DIR__ . '/../includes/virus-scene-background.php'; ?>

    <nav class="navbar navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../index.php"><span class="me-2">+</span> Vaccination Management System</a>
            <a href="parent-login.php" class="btn btn-outline-light btn-sm">Login</a>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-7">
                <div class="card border-0 shadow rounded-4">
                    <div class="card-body p-4 p-md-5">

                        <h2 class="fw-bold text-primary mb-1">Parent Registration</h2>
                        <p class="text-secondary mb-4">Account banao taake bachon ki vaccination book kar sako.</p>

                        <form method="POST" class="row g-3">
                            <div class="col-md-6">
                                <label for="nm" class="form-label fw-semibold">Full Name</label>
                                <input type="text" class="form-control" id="nm" name="nm" placeholder="adil hussain"
                                       value="<?= e($_POST['nm'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="mail" class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" id="mail" name="mail" placeholder="adil@gmail.com"
                                       value="<?= e($_POST['mail'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="relation" class="form-label fw-semibold">Relation with child</label>
                                <select id="relation" class="form-select" name="relation">
                                    <option value="guardian">Guardian</option>
                                    <option value="father">Father</option>
                                    <option value="mother">Mother</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="contact" class="form-label fw-semibold">Phone number</label>
                                <input type="tel" class="form-control" id="contact" name="contact" maxlength="11" placeholder="03XXXXXXXXX"
                                       value="<?= e($_POST['contact'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="cnic" class="form-label fw-semibold">CNIC (13 digits)</label>
                                <input type="text" class="form-control" id="cnic" name="cnic" inputmode="numeric" maxlength="13"
                                       value="<?= e($_POST['cnic'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="city" class="form-label fw-semibold">City</label>
                                <input type="text" class="form-control" id="city" name="city" placeholder="karachi"
                                       value="<?= e($_POST['city'] ?? '') ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold">Address</label>
                                <input type="text" class="form-control" id="address" name="address" placeholder="1234 Main St"
                                       value="<?= e($_POST['address'] ?? '') ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="pass" class="form-label fw-semibold">Password</label>
                                <input type="password" class="form-control" id="pass" name="pass" minlength="6" required>
                                <span id="show_hide"><i class="fa-solid fa-eye" id="icon"></i></span>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="gridCheck" name="checkbox" required>
                                    <label class="form-check-label" for="gridCheck">I agree to the <a href="../terms.php" target="_blank" rel="noopener noreferrer">Terms &amp; Privacy</a>.</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold" name="btn">Register</button>
                            </div>
                        </form>

                        <p class="text-center small text-secondary mt-4 mb-0">
                            Pehle se account hai? <a href="parent-login.php" class="text-decoration-none fw-semibold">Login karo</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($done) { ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Registration complete!',
                text: 'Ab aap login kar sakte hain.',
                confirmButtonColor: '#2563eb'
            }).then(() => { window.location.href = 'parent-login.php'; });
        </script>
    <?php } elseif ($error != '') { ?>
        <script>
            Swal.fire({ icon: 'error', title: 'Registration failed', text: <?= json_encode($error) ?>, confirmButtonColor: '#2563eb' });
        </script>
    <?php } ?>

<?php include __DIR__ . '/../includes/portal-footer.php'; ?>
</body>
</html>



<script>
    let span = document.getElementById('show_hide');
    let password = document.getElementById('pass');
    let i = document.getElementById('icon');


    span.addEventListener('click', function(){
        if(password.type == 'text'){
            password.type = "password";
        }else{
            password.type = 'text';
        }
        i.classList.toggle('fa-eye-slash');
        i.classList.toggle('fa-eye');
    })

</script>