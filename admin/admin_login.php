<?php
session_start();
include_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/admin-account.php';

// Pehle se login hai to seedha dashboard
if (isset($_SESSION['admin'])) {
    header('Location: admin.php');
    exit;
}

$error = '';
$accountReady = evax_initialize_admin_account($connect, $admin_user, $admin_pass);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn'])) {
    $user = isset($_POST['username']) && is_string($_POST['username']) ? trim($_POST['username']) : '';
    $pass = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';

    if (!$accountReady) {
        $error = 'Admin login is temporarily unavailable. Please try again later.';
    } else {
        $statement = mysqli_prepare($connect, 'SELECT password_hash FROM admin_accounts WHERE username = ? LIMIT 1');
        if (!$statement) {
            error_log('E-Vaccine admin login query could not be prepared: ' . mysqli_error($connect));
            $error = 'Admin login is temporarily unavailable. Please try again later.';
        } else {
            mysqli_stmt_bind_param($statement, 's', $user);
            if (!mysqli_stmt_execute($statement)) {
                error_log('E-Vaccine admin login query failed: ' . mysqli_stmt_error($statement));
                $error = 'Admin login is temporarily unavailable. Please try again later.';
            } else {
                mysqli_stmt_bind_result($statement, $passwordHash);
                if (mysqli_stmt_fetch($statement) && password_verify($pass, $passwordHash)) {
                    session_regenerate_id(true);
                    $_SESSION['admin'] = $user;
                    header('Location: admin.php');
                    exit;
                }
            }
            mysqli_stmt_close($statement);
        }
    }
    if ($error === '') {
        $error = 'Username or password is incorrect.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | E-Vaccine</title>
    <?php include __DIR__ . '/../includes/portal-head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../includes/theme.js?v=6"></script>
    <link href="../includes/theme.css?v=5" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.css" integrity="sha512-x9WwyMYBnlXMNQ6kQ/Lyzu1NqIhLQKL5Oq6xByfXuRj7s9CskyCbLv/1IjqzJmXwFXWr0ov6jBV7Qbc0hh9nHg==" crossorigin="anonymous" referrerpolicy="no-referrer">

</head>
<body class="bg-light min-vh-100 d-flex align-items-center justify-content-center">
    <?php include __DIR__ . '/../includes/virus-scene-background.php'; ?>

    <form method="POST" class="col-11 col-md-6 col-lg-4 bg-white p-4 p-md-5 rounded-4 shadow border border-light-subtle">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center bg-dark text-white rounded-circle mb-3"
                 style="width: 64px; height: 64px;">
                <i class="bi bi-shield-lock fs-3"></i>
            </div>
            <h2 class="fw-bold mb-1">Admin Login</h2>
            <p class="text-secondary mb-0">E-Vaccine Management System</p>
        </div>

        <?php if ($error != '') { ?>
            <div class="alert alert-danger py-2"><?= e($error) ?></div>
        <?php } ?>

        <div class="form-floating mb-3">
            <input type="text" class="form-control rounded-3" id="username" name="username" placeholder="Username" required>
            <label for="username">Username</label>
        </div>
        <div class="form-floating mb-4">
            <input type="password" class="form-control rounded-3" id="password" name="password" placeholder="Password" required>
            <label for="password">Password</label>
            <span id="show_hide"><i class="fa-solid fa-eye" id="icon"></i></span>
        </div>

        <button name="btn" class="btn btn-dark btn-lg w-100 rounded-3 fw-semibold">Login</button>

        <div class="text-center mt-4">
            <a href="../index.php" class="text-decoration-none text-secondary small">&larr; Back to Home</a>
        </div>
    </form>

<?php include __DIR__ . '/../includes/portal-footer.php'; ?>
</body>
</html>



<script>
    let span = document.getElementById('show_hide');
    let password = document.getElementById('password');
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