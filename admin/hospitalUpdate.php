<?php
include 'auth.php';
require_once __DIR__ . '/../includes/notifications.php';

// Accept / Reject ke buttons yahin handle hote hain:  hospitalUpdate.php?action=accepted&id=3
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id     = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action == 'accepted' || $action == 'rejected') {
        $update = mysqli_query($connect, "UPDATE hospitals SET STATUS = '$action' WHERE id = $id");
        if (!$update) {
            error_log('E-Vaccine hospital status update failed: ' . mysqli_error($connect));
        } elseif (mysqli_affected_rows($connect) === 1 && $action === 'accepted') {
            evax_create_notification(
                $connect,
                'hospital',
                $id,
                'Hospital account approved',
                'Your hospital account has been approved. You can now sign in to the hospital portal.',
                'hospital/hospital_dashboard.php',
                'hospital-' . $id . '-account-approved'
            );
        }
        header("Location: hospitalUpdate.php?msg=$action");
        exit;
    }
}

// Pending hospitals sab se upar dikhte hain
$run = mysqli_query($connect, "SELECT * FROM hospitals ORDER BY (STATUS = 'pending') DESC, id DESC");

$page_title = 'Hospitals';
$active = 'hospitals';
include 'layout_top.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Hospitals</h2>
        <p class="text-secondary small mb-0">Hospital login sirf <strong>accepted</strong> hospitals kar sakte hain.</p>
    </div>
    <a href="admin-hospital_register.php" class="btn btn-primary">
        <i class="bi bi-building-add me-1"></i> Register Hospital
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Hospital</th>
                    <th>Authorized Person</th>
                    <th>Contact</th>
                    <th>City</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($run) > 0) { ?>
                <?php while ($result = mysqli_fetch_assoc($run)) { ?>
                    <?php
                    $status = $result['STATUS'];
                    if ($status == 'accepted') {
                        $badge = 'bg-success';
                    } elseif ($status == 'rejected') {
                        $badge = 'bg-danger';
                    } else {
                        $badge = 'bg-warning text-dark';
                    }
                    $displayStatus = $status;
                    if ($displayStatus === '') {
                        $displayStatus = 'pending';
                    }
                    ?>
                    <tr>
                        <td><?= (int)$result['id'] ?></td>
                        <td>
                            <div class="fw-semibold"><?= e($result['hospital_name']) ?></div>
                            <div class="small text-secondary"><?= e($result['address']) ?></div>
                        </td>
                        <td>
                            <div><?= e($result['authorized_name']) ?></div>
                            <div class="small text-secondary"><?= e($result['email']) ?></div>
                        </td>
                        <td><?= e($result['contact_number']) ?></td>
                        <td><?= e($result['city']) ?></td>
                        <td><span class="badge <?= $badge ?>"><?= e(ucfirst($displayStatus)) ?></span></td>
                        <td class="text-end text-nowrap">
                            <?php if ($status != 'accepted') { ?>
                                <a href="hospitalUpdate.php?action=accepted&id=<?= (int)$result['id'] ?>" class="btn btn-sm btn-success">Accept</a>
                            <?php } ?>
                            <?php if ($status != 'rejected') { ?>
                                <a href="hospitalUpdate.php?action=rejected&id=<?= (int)$result['id'] ?>" class="btn btn-sm btn-outline-warning">Reject</a>
                            <?php } ?>
                            <a href="editHospital.php?id=<?= (int)$result['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <a href="deleteHospital.php?id=<?= (int)$result['id'] ?>" class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Is hospital ko delete karna hai? Iska stock bhi delete ho jayega.');">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr><td colspan="7" class="text-center text-secondary py-4">Abhi koi hospital register nahi hua.</td></tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layout_bottom.php'; ?>
