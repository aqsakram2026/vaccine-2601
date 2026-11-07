<?php
include 'auth.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($connect, "DELETE FROM parents WHERE id = $id");
}

header('Location: parentsUpdate.php?msg=deleted');
exit;
