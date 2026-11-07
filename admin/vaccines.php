<?php
include 'auth.php';

$run = mysqli_query($connect, "SELECT * FROM vaccines ORDER BY Vaccine_ID");

$page_title = 'Vaccines';
$active = 'vaccines';
include 'layout_top.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Vaccines</h2>
        <p class="text-secondary small mb-0">All vaccines available in the system.</p>
    </div>
    <a href="adminvaccineadd.php" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Add Vaccine
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Vaccine</th>
                    <th>Dose</th>
                    <th>Age Limit</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($run) > 0) { ?>
                <?php while ($result = mysqli_fetch_assoc($run)) { ?>
                    <tr>
                        <td><?= e($result['Vaccine_ID']) ?></td>
                        <td class="fw-semibold"><?= e($result['Vaccine_Name']) ?></td>
                        <td><?= e($result['Dose']) ?></td>
                        <td><?= e($result['Age_Limit']) ?></td>
                        <td class="small text-secondary"><?= e($result['description']) ?></td>
                        <td>
                            <?php if (strtolower($result['Status']) == 'active') { ?>
                                <span class="badge bg-success">Active</span>
                            <?php } else { ?>
                                <span class="badge bg-secondary">Inactive</span>
                            <?php } ?>
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="editVaccines.php?id=<?= (int)$result['Vaccine_ID'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <a href="deleteVaccines.php?id=<?= (int)$result['Vaccine_ID'] ?>" class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Is vaccine ko delete karna hai?');">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr><td colspan="7" class="text-center text-secondary py-4">Abhi koi vaccine nahi hai.</td></tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layout_bottom.php'; ?>
