<?php
session_start();
include_once __DIR__ . '/../config.php';

// Pehle se login hai to seedha dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: parent_dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn'])) {
    $mail = isset($_POST['mail']) && is_string($_POST['mail']) ? trim($_POST['mail']) : '';
    $pass = isset($_POST['pass']) && is_string($_POST['pass']) ? $_POST['pass'] : '';
    $lookup = mysqli_prepare($conn, 'SELECT id, name, email, Hashed_password, profile_image FROM parents WHERE email = ? LIMIT 1');
    if (!$lookup) {
        error_log('E-Vaccine parent login lookup could not be prepared: ' . mysqli_error($conn));
        $error = 'Login is temporarily unavailable. Please try again later.';
    } else {
        mysqli_stmt_bind_param($lookup, 's', $mail);
        if (!mysqli_stmt_execute($lookup)) {
            error_log('E-Vaccine parent login lookup failed: ' . mysqli_stmt_error($lookup));
            $error = 'Login is temporarily unavailable. Please try again later.';
        } else {
            mysqli_stmt_bind_result($lookup, $parentId, $parentName, $parentEmail, $passwordHash, $profileImage);
            if (mysqli_stmt_fetch($lookup) && password_verify($pass, $passwordHash)) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = [
                    'id' => (int)$parentId,
                    'name' => $parentName,
                    'email' => $parentEmail,
                    'profile_image' => (string)($profileImage ?? '')
                ];
                mysqli_stmt_close($lookup);
                header('Location: parent_dashboard.php');
                exit;
            }
        }
        mysqli_stmt_close($lookup);
    }
    if ($error === '') {
        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Login | Vaccination Management System</title>
    <?php include __DIR__ . '/../includes/portal-head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../includes/theme.js?v=6"></script>
    <link href="../includes/theme.css?v=5" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.css" integrity="sha512-x9WwyMYBnlXMNQ6kQ/Lyzu1NqIhLQKL5Oq6xByfXuRj7s9CskyCbLv/1IjqzJmXwFXWr0ov6jBV7Qbc0hh9nHg==" crossorigin="anonymous" referrerpolicy="no-referrer">

</head>
<body class="bg-light min-vh-100 d-flex align-items-center justify-content-center">
    <?php include __DIR__ . '/../includes/virus-scene-background.php'; ?>

    <form method="POST" class="col-11 col-md-6 col-lg-4 bg-white p-4 p-md-5 rounded-4 shadow border border-light-subtle">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 64px; height: 64px;">
                <span class="fs-2 fw-bold text-white">+</span>
            </div>
            <h2 class="fw-bold text-primary mb-2">Parent Login</h2>
            <p class="text-secondary mb-0">Vaccination Management System</p>
        </div>

        <div class="form-floating mb-3">
            <input type="email" class="form-control rounded-3" id="floatingInput" placeholder="name@example.com" name="mail" required>
            <label for="floatingInput">Email address</label>
        </div>

        <div class="form-floating mb-4">


           
<input type="password" class="form-control rounded-3" id="floatingPassword" placeholder="Password" name="pass" required>
<label for="floatingPassword">Password  </label> <span id="show_hide"><i class="fa-solid fa-eye" id="icon"></i></span>
</div>

        <button name="btn" class="btn btn-primary btn-lg w-100 rounded-3 fw-semibold shadow-sm">Login</button>

        <div class="text-center mt-4">
            <p class="small mb-2"><a href="../forgot-password.php" class="text-decoration-none fw-semibold">Forgot password?</a></p>
            <p class="small text-secondary mb-2">Don't have Account? <a href="parent.php" class="text-decoration-none fw-semibold">Sign Up</a></p>
            <a href="../index.php" class="text-decoration-none text-secondary small">&larr; Back to Home</a>
        </div>
    </form>

    <?php if ($error != '') { ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Login Failed!',
                text: <?= json_encode($error) ?>,
                confirmButtonText: 'Try Again',
                confirmButtonColor: '#2563eb'
            });
        </script>
    <?php } ?>

<?php include __DIR__ . '/../includes/portal-footer.php'; ?>
</body>
</html>


<script>
    let span = document.getElementById('show_hide');
    let password = document.getElementById('floatingPassword');
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