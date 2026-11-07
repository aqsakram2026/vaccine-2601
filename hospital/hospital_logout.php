<?php
session_start();
unset($_SESSION['hospital_id']);     // sirf hospital ko logout karta hai
unset($_SESSION['hospital_name']);
header("Location: hospital_login.php");
exit();
