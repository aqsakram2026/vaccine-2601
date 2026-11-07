<?php
include 'auth.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($connect, "DELETE FROM vaccines WHERE Vaccine_ID = $id");
}

header('Location: vaccines.php?msg=deleted');
exit;
