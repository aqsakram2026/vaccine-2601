<?php
session_start();
include('hospital_db.php');

// 1. Security Check
if (!isset($_SESSION['hospital_id'])) {
    header("Location: hospital_login.php");
    exit();
}

$hospital_id = $_SESSION['hospital_id'];
$hospital_name = $_SESSION['hospital_name'];

// 2. Status Update Logic 
if (isset($_POST['update_status_btn'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['vaccination_status']; // 'Vaccinated' ya 'Not Vaccinated'

    //Database Status Update
    $updateQuery = "UPDATE bookings SET vaccination_status = '$new_status' WHERE id = '$booking_id' AND hospital_id = '$hospital_id'";
    
    if (mysqli_query($connect, $updateQuery)) {
        echo "<script>alert('Vaccination status successfully updated!'); window.location.href='hospital_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error: Failed to update status.');</script>";
    }
}

$query = "SELECT * FROM bookings WHERE id = '$hospital_id'";
$result = mysqli_query($connect, $query);


        if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['stock'])){
            $_SESSION['hospital_id']= $hospital_id;
            $_SESSION['hospital_name']=$hospital_name;
            header('location:hospital_vaccine.php');
            exit();
        }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Dashboard - E-Vaccine Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --medical-teal: #3C9D93;
            --teal-dark: #2A736B;
            --dark-navy: #1A2530;
            --bg-light: #F4F7F6;
            --border-color: #E2E8F0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-light);
            color: var(--dark-navy);
        }

        .portal-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 8px;
        }

        .overview-box {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 24px;
        }

        .sidebar-menu .nav-link {
            color: var(--dark-navy);
            font-weight: 600;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s ease;
        }

        .sidebar-menu .nav-link:hover {
            background-color: #F1F5F9;
            color: var(--medical-teal);
        }

        .sidebar-menu .nav-link.active {
            background-color: var(--medical-teal);
            color: #ffffff !important;
        }

        /* Table Custom Styling */
        .table {
            color: var(--dark-navy);
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .table th {
            background-color: #F8FAFC;
            color: #334155;
            font-weight: 600;
            border-bottom: 2px solid var(--border-color);
            padding: 12px;
        }

        .table td {
            padding: 12px;
            border-color: var(--border-color);
        }

        .form-select-sm {
            border-radius: 6px;
            border: 1px solid #CBD5E1;
            padding: 6px 10px;
            font-size: 0.85rem;
        }

        .form-select-sm:focus {
            border-color: var(--medical-teal);
            box-shadow: 0 0 0 3px rgba(60, 157, 147, 0.15);
        }

        .update-btn {
            background-color: var(--medical-teal);
            color: #ffffff;
            font-weight: 600;
            border-radius: 6px;
            padding: 6px 14px;
            border: none;
            font-size: 0.85rem;
            transition: background-color 0.2s ease;
        }

        .update-btn:hover {
            background-color: var(--teal-dark);
            color: #ffffff;
        }

        .logout-btn {
            background-color: #EF4444;
            color: #ffffff;
            font-weight: 600;
            border-radius: 6px;
            padding: 6px 14px;
            text-decoration: none;
            font-size: 0.85rem;
            transition: background-color 0.2s ease;
        }

        .logout-btn:hover {
            background-color: #DC2626;
            color: #ffffff;
        }
        button{
            border:  none;
            background-color: #ffffff ;
            width: 100%;
            height: 100%;
            display: block;
        }
        
    </style>
</head>
<body>

    <!-- Main Container -->
    <div class="container py-4">

        <!-- Top Overview Banner -->
        <div class="overview-box mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-speedometer2 text-teal fs-4"></i>
                    <h4 class="fw-bold m-0">Welcome, <?php echo isset($hospital_name) ?$hospital_name : 'Hospital Partner'; ?></h4>
                </div>
                <p class="text-secondary m-0" style="max-width: 750px; font-size: 0.95rem;">
                    Manage your patient appointments and update their vaccination status below.
                </p>

                <button name="stock">manage vaccine stock</button>
            </div>
            <div>
                <a href="hospital_logout.php" class="logout-btn"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
            </div>
        </div>

        <!-- Sidebar + Dashboard Content Layout -->
        <div class="row g-4">

            <!-- Sidebar Navigation -->
            <div class="col-lg-3">
                <div class="portal-card p-3">
                    <div class="px-2 mb-3">
                        <small class="text-uppercase text-muted fw-bold">Portal Navigation</small>
                    </div>
                    <div class="nav flex-column sidebar-menu">
                        <a href="hospital_register.php" class="nav-link">
                            <span><i class="bi bi-building-add me-2"></i> Hospital Registration</span>
                            <i class="bi bi-chevron-right small"></i>
                        </a>
                        <a href="hospital_login.php" class="nav-link">
                            <span><i class="bi bi-box-arrow-in-right me-2"></i> Hospital Login</span>
                            <i class="bi bi-chevron-right small"></i>
                        </a>
                        <a href="hospital_dashboard.php" class="nav-link active">
                            <span><i class="bi bi-speedometer2 me-2"></i> Hospital Dashboard</span>
                            <i class="bi bi-chevron-right small"></i>
                        </a>
                        <a href="hospital_status.php" class="nav-link">
                            <span><i class="bi bi-check2-circle me-2"></i> Hospital Status</span>
                            <i class="bi bi-chevron-right small"></i>
                        </a>
                        <form method="GET">
                           
                                <a href="hospital_dashboard.php" class="nav-link " >
                            
                                <span> <button name="stock"><i class="bi bi-check2-circle me-2"></i>Add Vaccine</button></span>
                            <i class="bi bi-chevron-right small"></i>
                            
                        </a>
                            
                        </form>
                    </div>
                </div>
            </div>

            <!-- Dashboard Table Area -->
            <div class="col-lg-9">
                <div class="portal-card p-4">
                    
                    <div class="border-bottom pb-3 mb-4">
                        <h4 class="fw-bold mb-1" style="font-size: 1.25rem;">Patient Appointments List</h4>
                        <p class="text-muted mb-0 small">Review scheduled appointments and update vaccination records in real-time.</p>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Child Name</th>
                                    <th>Parent Email</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Current Status</th>
                                    <th>Action / Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($result) && $result && mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>
                                            <td><b>#{$row['id']}</b></td>
                                            <td>{$row['child_name']}</td>
                                            <td>{$row['parent_email']}</td>
                                            <td>{$row['appointment_date']}</td>
                                            <td>{$row['appointment_time']}</td>
                                            <td><span class='badge bg-light text-dark border'>{$row['vaccination_status']}</span></td>
                                            <td>
                                                <form method='POST' class='d-flex align-items-center gap-2'>
                                                    <input type='hidden' name='booking_id' value='{$row['id']}'>
                                                    <select name='vaccination_status' class='form-select form-select-sm'>
                                                        <option value='Vaccinated' " . ($row['vaccination_status'] == 'Vaccinated' ? 'selected' : '') . ">Vaccinated</option>
                                                        <option value='Not Vaccinated' " . ($row['vaccination_status'] == 'Not Vaccinated' ? 'selected' : '') . ">Not Vaccinated</option>
                                                    </select>
                                                    <button type='submit' name='update_status_btn' class='update-btn'>Update</button>
                                                </form>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center py-4 text-muted'>No appointments found for your hospital.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>