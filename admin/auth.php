<?php
// Har admin page ke sab se upar include hoti hai:  include 'auth.php';
// 1) session shuru karti hai  2) database connect karti hai  3) login check karti hai
session_start();
include_once __DIR__ . '/../config.php';

if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit;
}
