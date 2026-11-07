<?php
session_start();
unset($_SESSION['user_id']);   // sirf parent ko logout karta hai
header('Location: parent-login.php');
exit;
