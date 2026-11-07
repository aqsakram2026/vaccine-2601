<?php
session_start();
unset($_SESSION['admin']);   // sirf admin ko logout karta hai
header('Location: ../index.php');
exit;
