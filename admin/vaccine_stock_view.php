<?php
include 'auth.php';

$run = mysqli_query($connect, "SELECT hospital_vaccine_stock.stock_id,
                                      hospitals.hospital_name,
                                      vaccines.Vaccine_Name,
                                      hospital_vaccine_stock.quantity,
                                      hospital_vaccine_stock.updated_at
                               FROM hospital_vaccine_stock
                               JOIN hospitals ON hospital_vaccine_stock.hospital_id = hospitals.id
                               JOIN vaccines  ON hospital_vaccine_stock.vaccine_id = vaccines.Vaccine_ID
                               ORDER BY hospitals.hospital_name, vaccines.Vaccine_Name");

$page_title = 'Hospital Stock';
$active = 'stock_view';
include 'layout_top.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Hospital Vaccine Stock</h2>
        <p class="text-secondary small mb-0">Current stock of every hospital.</p>
    </div>
    <a href="admin_vaccine_stock.php" class="btn btn-primary">
        <i class="bi bi-box-seam me-1"></i> Update Stock
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Hospital</th>
                    <th>Vaccine</th>
                    <th>Quantity</th>
                    <th>Last Updated</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($run) > 0) { ?>
                <?php while ($result = mysqli_fetch_assoc($run)) { ?>
                    <tr>
                        <td><?= (int)$result['stock_id'] ?></td>
                        <td class="fw-semibold"><?= e($result['hospital_name']) ?></td>
                        <td><?= e($result['Vaccine_Name']) ?></td>
                        <td>
                            <?php if ($result['quantity'] > 0) { ?>
                                <?= (int)$result['quantity'] ?>
                            <?php } else { ?>
                                <span class="badge bg-danger">Out of stock</span>
                            <?php } ?>
                        </td>
                        <td class="small text-secondary"><?= e($result['updated_at']) ?></td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr><td colspan="5" class="text-center text-secondary py-4">Abhi kisi hospital ka stock add nahi hua.</td></tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layout_bottom.php'; ?>
