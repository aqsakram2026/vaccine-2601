<?php
session_start();
include_once __DIR__ . '/../config.php';

if (!isset($_SESSION['hospital_id'], $_SESSION['hospital_name'])) {
    header('Location: hospital_login.php');
    exit;
}

$hospital_id = (int)$_SESSION['hospital_id'];

if (isset($_POST['btn'])) {

    $vaccine_id = (int)$_POST['vaccine'];
    $quantity   = (int)$_POST['quantity'];

    if ($vaccine_id < 1 || $quantity < 1) {
        $message = "Vaccine chuno aur quantity 1 ya zyada likho.";
        $type = "warning";
    } else {
        // Is vaccine ka stock pehle se hai?
        $check = mysqli_query($connect, "SELECT stock_id FROM hospital_vaccine_stock
                                         WHERE hospital_id = $hospital_id AND vaccine_id = $vaccine_id");

        if (mysqli_num_rows($check) > 0) {
            // Haan -> maujooda stock mein quantity jama karo
            $ok = mysqli_query($connect, "UPDATE hospital_vaccine_stock SET quantity = quantity + $quantity
                                          WHERE hospital_id = $hospital_id AND vaccine_id = $vaccine_id");
        } else {
            // Nahi -> nayi entry
            $ok = mysqli_query($connect, "INSERT INTO hospital_vaccine_stock (hospital_id, vaccine_id, quantity)
                                          VALUES ($hospital_id, $vaccine_id, $quantity)");
        }

        if ($ok) {
            $message = "Vaccine stock added successfully!";
            $type = "success";
        } else {
            $message = "Failed to add vaccine stock!";
            $type = "danger";
        }
    }
}

// Is hospital ka maujooda stock (neeche table mein dikhta hai)
$my_stock = mysqli_query($connect, "SELECT vaccines.Vaccine_Name, hospital_vaccine_stock.quantity, hospital_vaccine_stock.updated_at
                                    FROM hospital_vaccine_stock
                                    JOIN vaccines ON hospital_vaccine_stock.vaccine_id = vaccines.Vaccine_ID
                                    WHERE hospital_vaccine_stock.hospital_id = $hospital_id
                                    ORDER BY vaccines.Vaccine_Name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Vaccine Stock</title>
    <?php include __DIR__ . '/../includes/portal-head.php'; ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <script src="../includes/theme.js?v=6"></script>
    <link href="../includes/theme.css?v=5" rel="stylesheet">

    <style>
        body {
            background: #f3f7f7;
            color: #172b3a;
            font-family: Arial, sans-serif;
        }

        .dashboard-container {
            max-width: 1150px;
            margin: 35px auto;
        }

        .dashboard-card {
            background: #fff;
            border: 1px solid #dce5ee;
            border-radius: 9px;
        }

        .welcome-card {
            padding: 28px 24px;
            margin-bottom: 24px;
        }

        .welcome-title {
            font-size: 25px;
            font-weight: 700;
        }

        .welcome-description {
            color: #738092;
            margin-bottom: 0;
        }

        .sidebar-link {
            display: block;
            padding: 13px 15px;
            margin-bottom: 5px;
            color: #172b3a;
            text-decoration: none;
            border-radius: 6px;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: #389f95;
            color: white;
        }

        .sidebar-heading {
            font-size: 14px;
            font-weight: 600;
            color: #435467;
            margin-bottom: 16px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            padding: 12px 14px;
            border-color: #d7e0e8;
            border-radius: 6px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #389f95;
            box-shadow: 0 0 0 0.2rem rgba(56, 159, 149, 0.15);
        }

        .btn-teal {
            background: #389f95;
            border-color: #389f95;
            color: white;
            padding: 11px 22px;
            font-weight: 600;
            border-radius: 6px;
        }

        .btn-teal:hover {
            background: #2d887f;
            border-color: #2d887f;
            color: white;
        }

        .stock-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 8px;
            background: #e1f3f0;
            color: #389f95;
            font-size: 25px;
            font-weight: bold;
        }

        @media (max-width: 767px) {
            .dashboard-container {
                margin: 20px auto;
            }

            .welcome-title {
                font-size: 21px;
            }
        }
    </style>
</head>

<body>

<div class="container dashboard-container">

    <div class="dashboard-card welcome-card">
        <div class="row align-items-center g-3">
            <div class="col-md-9">
                <h1 class="welcome-title">
                    <span class="text-success">&#9681;</span>
                    Welcome, <?= htmlspecialchars($_SESSION['hospital_name']) ?>
                </h1>

                <p class="welcome-description">
                    Manage your hospital's vaccine inventory and available stock below.
                </p>
            </div>

            <div class="col-md-3 text-md-end">
                <a href="hospital_dashboard.php" class="btn btn-light btn-sm fw-semibold me-1">Dashboard</a>
                <a href="hospital_logout.php" class="btn btn-danger btn-sm fw-semibold">Logout</a>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <div class="col-md-3">
            <div class="dashboard-card p-3">
                <p class="sidebar-heading">PORTAL NAVIGATION</p>

                <a href="hospital_dashboard.php" class="sidebar-link">
                    &#9681; &nbsp; Hospital Dashboard &rsaquo;
                </a>

                <a href="hospital_status.php" class="sidebar-link">
                    &#10003; &nbsp; Hospital Status &rsaquo;
                </a>

                <a href="hospital_vaccine.php" class="sidebar-link active">
                    &#10003; &nbsp; Manage Vaccine Stock &rsaquo;
                </a>
            </div>
        </div>

        <div class="col-md-9">
            <div class="dashboard-card p-4">

                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="stock-icon">&#43;</div>

                    <div>
                        <h2 class="section-title mb-1">Manage Vaccine Stock</h2>
                        <p class="text-secondary small mb-0">
                            Add vaccine quantities to your hospital inventory.
                        </p>
                    </div>
                </div>

                <hr class="my-4">

                <?php if (isset($message)) { ?>
                    <div class="alert alert-<?= $type ?> rounded-3">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php } ?>

                <form method="POST">

                    <div class="mb-4">
                        <label for="vaccine" class="form-label">
                            Select Vaccine
                        </label>

                        <select class="form-select" id="vaccine"
                                name="vaccine" required>
                            <option value="">Choose a vaccine</option>

                            <?php
                            $query = "SELECT * FROM vaccines WHERE Status = 'Active' ORDER BY Vaccine_Name ASC";
                            $run = mysqli_query($connect, $query);

                            while ($result = mysqli_fetch_assoc($run)) {
                            ?>
                                <option value="<?= $result['Vaccine_ID'] ?>">
                                    <?= htmlspecialchars($result['Vaccine_Name']) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="quantity" class="form-label">
                            Available Quantity
                        </label>

                        <input type="number" id="quantity" name="quantity"
                               class="form-control"
                               placeholder="Enter vaccine quantity"
                               min="0" step="1" required>

                        <div class="form-text">
                            Enter the number of vaccine doses available.
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" name="btn" class="btn btn-teal">
                            + Add Vaccine Stock
                        </button>

                        <a href="hospital_dashboard.php"
                           class="btn btn-outline-secondary px-4">
                            Cancel
                        </a>
                    </div>

                </form>
                <h5 class="fw-bold mt-5 mb-3">Your current stock</h5>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr><th>Vaccine</th><th>Quantity</th><th>Last updated</th></tr>
                        </thead>
                        <tbody>
                        <?php if (mysqli_num_rows($my_stock) > 0) { ?>
                            <?php while ($row = mysqli_fetch_assoc($my_stock)) { ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['Vaccine_Name']) ?></td>
                                    <td><?= (int)$row['quantity'] ?></td>
                                    <td class="small text-secondary"><?= htmlspecialchars($row['updated_at']) ?></td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr><td colspan="3" class="text-center text-secondary py-3">Abhi koi stock add nahi hua.</td></tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>


            </div>
        </div>

    </div>

    <p class="text-center text-secondary small mt-4">
        Vaccination Management System &bull; Hospital Administration Portal
    </p>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include __DIR__ . '/../includes/portal-footer.php'; ?>
</body>
</html>
