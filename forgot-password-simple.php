
<?php

include("config.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
</head>

<body>

<h1>Forgot Password</h1>

<form method="post">

    Email:
    <input type="text" name="mail">

    <br><br>

    New Password:
    <input type="password" name="new_password">

    <br><br>

    <button type="submit" name="reset_btn">
        Reset Password
    </button>

</form>


<?php

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reset_btn"])){

    $user_email = $_POST['mail'];
    $new_password = $_POST['new_password'];

    // CHECK IF EMAIL EXISTS
    $query = "SELECT * FROM parents WHERE email = '$user_email'";

    $run = mysqli_query($connect, $query);

    if(mysqli_num_rows($run) > 0){

        // CREATE PASSWORD HASH

        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        // UPDATE PASSWORD
        $update_query = "UPDATE parents
                         SET Hashed_Password = '$password_hash'
                         WHERE email = '$user_email'";

        $update_run = mysqli_query($connect, $update_query);

        if($update_run){

            echo "Password changed successfully!";
            header('location: parents/parent-login.php');
            exit();

        } else {

            echo "Password could not be changed.";

        }

    } else {

        echo "No account found with this email.";

    }

}

?>

</body>
</html>

