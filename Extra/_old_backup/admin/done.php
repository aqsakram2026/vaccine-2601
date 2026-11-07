
<?php
include('database.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Stock</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 bg-dark text-white min-vh-100 p-3">

            <h5 class="fw-bold mb-5">
                <i class="bi bi-shield-plus text-info me-2"></i>
                E-Vaccine
            </h5>

            <p class="text-secondary fw-bold small mb-3">MAIN MENU</p>

            <ul class="nav flex-column gap-2">

                <li class="nav-item">
                    <a href="admin.php" class="nav-link text-white rounded">
                        <i class="bi bi-grid-fill me-2"></i>
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a href="vaccines.php" class="nav-link text-white rounded">
                        <i class="bi bi-eyedropper me-2"></i>
                        Vaccine View
                    </a>
                </li>

                <li class="nav-item">
                    <a href="vaccine_stock_view.php" class="nav-link active bg-primary text-white rounded">
                        <i class="bi bi-box me-2"></i>
                        Hospital Stock
                    </a>
                </li>

                <li class="nav-item">
                    <a href="adminParentRegistration.php" class="nav-link text-white rounded">
                        <i class="bi bi-person-plus me-2"></i>
                        Parent Register
                    </a>
                </li>

                <li class="nav-item">
                    <a href="admin-hospital_register.php" class="nav-link text-white rounded">
                        <i class="bi bi-hospital me-2"></i>
                        Hospital Register
                    </a>
                </li>

                <li class="nav-item">
                    <a href="adminvaccineadd.php" class="nav-link text-white rounded">
                        <i class="bi bi-plus-circle me-2"></i>
                        Add Vaccine
                    </a>
                </li>

                <li class="nav-item">
                    <a href="admin_vaccine_stock.php" class="nav-link text-white rounded">
                        <i class="bi bi-box-seam me-2"></i>
                        Update Vaccine Stock
                    </a>
                </li>

            </ul>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">

            <div class="mb-4">
                <h2 class="fw-bold">Hospital Vaccine Stock</h2>
                <p class="text-muted">
                    View vaccine quantities and their latest update dates.
                </p>
            </div>

            <div class="card shadow-sm border-0">

                
                
                
                
                
                <form method="POST">
                    <select name="hospital" id="">
                    <option value="">kuch bhi</option>
                </select>
                </form>
                
                
                
                
                
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0">

                            <thead>
                                <tr>
                                    <th>Stock ID</th>
                                    <th>Hospital Name</th>
                                    <th>Vaccine Name</th>
                                    <th>Quantity</th>
                                    <th>Last Update</th>
                                </tr>
                            </thead>
                           

                            $query = "SELECT
                                hospital_vaccine_stock.stock_id,
                                hospitals.hospital_name,
                                vaccines.Vaccine_Name,
                                hospital_vaccine_stock.quantity,
                                hospital_vaccine_stock.updated_at
                            FROM hospital_vaccine_stock
                            JOIN hospitals ON hospital_vaccine_stock.hospital_id = hospitals.id
                            JOIN vaccines ON hospital_vaccine_stock.vaccine_id = vaccines.Vaccine_ID";

                            $run = mysqli_query($connect, $query);
                            while ($result = mysqli_fetch_assoc($run)) {
                            ?>

                                <tr>
                                    <td><?= $result['stock_id']; ?></td>
                                    <td><?= $result['hospital_name']; ?></td>
                                    <td><?= $result['Vaccine_Name']; ?></td>
                                    <td><?= $result['quantity']; ?></td>
                                    <td><?= $result['updated_at']; ?></td>
                                </tr>

                            <?php } ?>

                        </table>
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
