<?php
// ============================================================
//  E-Vaccine - database connection (poore project ke liye ek hi jagah)
//
//  Shared MySQL connection used by the portal and website pages.
// ============================================================

// XAMPP/WAMP ki default settings. Password set ho to yahan badal do.
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'vaccine_database';

// Set this to a verified sender address and configure PHP's mail transport.
$password_reset_from_email = getenv('EVAX_MAIL_FROM') ?: '';

// Admin panel ka login (admin/admin_login.php). Apni marzi se badal lo.
$admin_user = 'admin';
$admin_pass = 'admin123';

mysqli_report(MYSQLI_REPORT_OFF);
$connect = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$connect) {
    die('<h3>Database se connect nahi ho saka.</h3>'
      . '<p>1) XAMPP/WAMP mein MySQL start karo.<br>'
      . '2) phpMyAdmin mein <b>vaccine_database</b> naam ka database banao aur '
      . '<b>database/vaccine_database.sql</b> import karo.</p>');
}

$conn = $connect;   // parents wali files $conn use karti hain, baqi $connect
if ($connect) {
    mysqli_query($connect, "SET NAMES utf8mb4");
}


// ------------------------------------------------------------
//  Chhote helper functions
// ------------------------------------------------------------

// Text ko SQL query mein daalne se pehle safe banata hai
function esc($value) {
    return addslashes(trim((string)$value));
}

// Text ko HTML page par dikhane se pehle safe banata hai
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// "SELECT COUNT(*) AS total FROM ..." chala kar number wapas deta hai
function count_of($sql) {
    global $connect;
    $run = mysqli_query($connect, $sql);
    $row = mysqli_fetch_assoc($run);
    if ($row) {
        return (int)$row['total'];
    }
    return 0;
}
?>
