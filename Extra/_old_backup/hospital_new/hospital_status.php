<?php
include('hospital_db.php');

$statusMessage = "";
$alertType = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['offEmail']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $statusMessage = "Please enter a valid email address!";
        $alertType = "error";
    } else {
        $cleanEmail = filter_var($email, FILTER_SANITIZE_EMAIL);

        // Database se email aur status find karein
        $query = "SELECT hospital_name, status FROM hospitals WHERE email = '$cleanEmail'";
        $result = mysqli_query($connect, $query);

        if ($row = mysqli_fetch_assoc($result)) {
            $hospitalName = $row['hospital_name'];
            $status = $row['status'];

            if ($status == 'pending') {
                $statusMessage = "Hello $hospitalName, your account is currently **PENDING**. Please wait for admin approval.";
                $alertType = "warning";
            } elseif ($status == 'approved') {
                $statusMessage = "Congratulations $hospitalName! Your account has been **APPROVED**. You can now login.";
                $alertType = "success";
            } elseif ($status == 'rejected') {
                $statusMessage = "Sorry $hospitalName, your account request has been **REJECTED** by the admin.";
                $alertType = "error";
            }
        } else {
            $statusMessage = "This email is not registered in our system.";
            $alertType = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Hospital Status - E-Vaccine Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 CDN -->
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

        label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
            display: block;
        }

        input[type="email"] {
            width: 100%;
            border-radius: 6px;
            border: 1px solid #CBD5E1;
            padding: 10px 12px;
            font-size: 0.9rem;
            background-color: #ffffff;
            color: #1A2530;
            outline: none;
            transition: all 0.2s ease;
        }

        input[type="email"]:focus {
            border-color: var(--medical-teal);
            box-shadow: 0 0 0 3px rgba(60, 157, 147, 0.15);
        }

        .btn-teal {
            background-color: var(--medical-teal);
            color: #ffffff;
            font-weight: 600;
            border-radius: 6px;
            padding: 10px 20px;
            border: none;
            width: 20%;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .btn-teal:hover {
            background-color: var(--teal-dark);
            color: #ffffff;
        }

        .auth-footer-links a {
            color: var(--medical-teal);
            font-weight: 600;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .auth-footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Main Container -->
    <div class="container py-4">

        <!-- Top Overview Banner -->
        <div class="overview-box mb-4">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-check2-circle text-teal fs-4"></i>
                <h4 class="fw-bold m-0">Hospital Registration Status</h4>
            </div>
            <p class="text-secondary m-0" style="max-width: 850px; font-size: 0.95rem;">
                Check the current approval status of your hospital partnership request to access the portal dashboard.
            </p>
        </div>

        <!-- Sidebar + Form Layout -->
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
                        <a href="hospital_dashboard.php" class="nav-link">
                            <span><i class="bi bi-speedometer2 me-2"></i> Hospital Dashboard</span>
                            <i class="bi bi-chevron-right small"></i>
                        </a>
                        <a href="hospital_status.php" class="nav-link active">
                            <span><i class="bi bi-check2-circle me-2"></i> Hospital Status</span>
                            <i class="bi bi-chevron-right small"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Status Form Area -->
            <div class="col-lg-9">
                <div class="portal-card p-4 p-md-5" style="min-height: 520px; display: flex; flex-direction: column; justify-content: space-between;">
                    
                    <div>
                        <div class="border-bottom pb-3 mb-4">
                            <h2 class="fw-bold mb-1" style="font-size: 1.5rem;">Check Registration Status</h2>
                            <p class="text-muted mb-0 small">Enter your official registered email to check your approval status.</p>
                        </div>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label>Official Email Address:</label>
                                <input type="email" name="offEmail" placeholder="@hospitaldomain.com" required>
                            </div>
                            
                            <div class="pt-2">
                                <button type="submit" name="check_status" class="btn btn-teal">Check Status</button>
                            </div>
                        </form>
                    </div>

                    <!-- Links Below Form -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top small text-muted auth-footer-links">
                        <a href="hospital_login.php">&larr; Back to Login</a> 
                        <a href="hospital_register.php">Register New Hospital</a>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (!empty($statusMessage)) { 
        // HTML tags remove karke SweetAlert mein show karne ke liye
        $cleanText = strip_tags($statusMessage);
        echo "<script>
            Swal.fire({
                icon: '$alertType',
                title: 'Registration Status',
                text: '$cleanText'
            });
        </script>";
    } ?>

</body>
</html>