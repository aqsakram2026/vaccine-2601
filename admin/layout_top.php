<?php
// Admin pages ka upar wala hissa (head + sidebar).
// Use karne se pehle page mein $page_title aur $active set karo, phir:
//     include 'layout_top.php';
if (!isset($page_title)) { $page_title = 'Admin'; }
if (!isset($active))     { $active = ''; }

// ?msg=added jaisi cheez URL mein ho to ye alert dikhta hai
$admin_messages = [
    'added'     => ['success', 'Record successfully add ho gaya.'],
    'updated'   => ['success', 'Record successfully update ho gaya.'],
    'deleted'   => ['success', 'Record delete ho gaya.'],
    'accepted'  => ['success', 'Hospital accept ho gaya. Ab wo login kar sakta hai.'],
    'rejected'  => ['warning', 'Hospital reject kar diya gaya.'],
    'exists'    => ['warning', 'Ye record pehle se maujood hai.'],
    'notfound'  => ['danger', 'Record nahi mila.'],
];
if (!isset($alert) && isset($_GET['msg']) && isset($admin_messages[$_GET['msg']])) {
    $alert = $admin_messages[$_GET['msg']];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> | E-Vaccine Admin</title>
    <?php include __DIR__ . '/../includes/portal-head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../includes/theme.js?v=6"></script>
    <link href="../includes/theme.css?v=5" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @media (min-width: 768px) {
            .admin-sidebar { min-height: 100vh; }
        }

        @media (max-width: 767.98px) {
            .admin-sidebar { min-height: 0; }
            .admin-page .evax-portal-tools { top: 4.5rem; }
        }
    </style>
</head>
<body class="bg-light admin-page">
<div class="container-fluid">
    <div class="row min-vh-100">

        <?php include __DIR__ . '/sidebar.php'; ?>

        <main class="col-12 col-md-9 col-lg-10 p-4 p-lg-5">

            <?php if (isset($alert)) { ?>
                <div class="alert alert-<?= e($alert[0]) ?> alert-dismissible fade show" role="alert">
                    <?= e($alert[1]) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php } ?>
