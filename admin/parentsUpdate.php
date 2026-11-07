<?php
include 'auth.php';

$run = mysqli_query($connect, "SELECT * FROM parents ORDER BY id");

$page_title = 'Parents';
$active = 'parents';
include 'layout_top.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Parents</h2>
        <p class="text-secondary small mb-0">Registered parents and guardians.</p>
    </div>
    <a href="adminParentRegistration.php" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Register Parent
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Relation</th>
                    <th>CNIC</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>City</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($run) > 0) { ?>
                <?php while ($result = mysqli_fetch_assoc($run)) { ?>
                    <tr>
                        <td><?= (int)$result['id'] ?></td>
                        <td class="fw-semibold"><?= e($result['name']) ?></td>
                        <td><?= e(ucfirst($result['Relation_with_child'])) ?></td>
                        <td><?= e($result['CNIC']) ?></td>
                        <td><?= e($result['email']) ?></td>
                        <td><?= e($result['Phone_no']) ?></td>
                        <td><?= e($result['City']) ?></td>
                        <td class="text-end text-nowrap">
                            <a href="editParents.php?id=<?= (int)$result['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <a href="deleteParents.php?id=<?= (int)$result['id'] ?>" class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Is parent ko delete karna hai? Unke bachon aur bookings ka record bhi delete ho jayega.');">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr><td colspan="8" class="text-center text-secondary py-4">Abhi koi parent register nahi hua.</td></tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layout_bottom.php'; ?>
