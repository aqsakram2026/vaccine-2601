<?php
include 'auth.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($connect, "DELETE FROM hospitals WHERE id = $id");
}

header('Location: hospitalUpdate.php?msg=deleted');
exit;
