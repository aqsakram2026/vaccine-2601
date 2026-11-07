<?php
include('database.php')
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Registration - E-Vaccine Portal</title>
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
        .form-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }
        .form-control, .form-select {
            border-radius: 6px;
            border: 1px solid #CBD5E1;
            padding: 10px 12px;
            font-size: 0.9rem;
            background-color: #ffffff;
            color: #1A2530;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--medical-teal);
            box-shadow: 0 0 0 3px rgba(60, 157, 147, 0.15);
            background-color: #ffffff;
            color: #1A2530;
        }

        /* Custom White Suggestions Box */
        .suggestions-container {
            position: absolute;
            background: #ffffff;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .suggestion-item {
            padding: 10px 12px;
            cursor: pointer;
            font-size: 0.9rem;
            color: #1A2530;
            border-bottom: 1px solid #F1F5F9;
        }
        .suggestion-item:hover {
            background-color: #F4F7F6;
            color: var(--medical-teal);
        }

        .btn-teal {
            background-color: var(--medical-teal);
            color: #ffffff;
            font-weight: 600;
            border-radius: 6px;
            padding: 10px 20px;
            border: none;
        }
        .btn-teal:hover {
            background-color: var(--teal-dark);
            color: #ffffff;
        }
        .auth-footer-links a {
            color: var(--medical-teal);
            font-weight: 600;
            text-decoration: none;
        }
        .auth-footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Main Container -->
    <div class="container py-4">

        <!-- Top Hospital Details / Info Banner -->
        <div class="overview-box mb-4">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-building-gear text-teal fs-4"></i>
                <h4 class="fw-bold m-0">Hospital Partner Registration</h4>
            </div>
            <p class="text-secondary m-0" style="max-width: 850px; font-size: 0.95rem;">
                Register your healthcare center with complete location and address details to coordinate vaccination schedules, track patient dosages, and update vaccination records seamlessly.
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
                        <a href="hospital_register.php" class="nav-link active">
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
                        <a href="hospital_status.php" class="nav-link">
                            <span><i class="bi bi-check2-circle me-2"></i> Hospital Status</span>
                            <i class="bi bi-chevron-right small"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Registration Form Area -->
            <div class="col-lg-9">
                <div class="portal-card p-4 p-md-5">
                    
                    <div class="border-bottom pb-3 mb-4">
                        <h4 class="fw-bold mb-1">Hospital Registration Form</h4>
                        <p class="text-muted mb-0 small">Register your healthcare center with complete location and address details.</p>
                    </div>

                    <form method="POST">
                        <div class="row g-3">
                            
                            <!-- Custom White Searchable Input -->
                            <div class="col-12 position-relative">
                                <label class="form-label">Hospital Name:</label>
                                <input type="text" class="form-control" name="hospitalNm" id="hospitalInput" placeholder="Select from list or type custom hospital name..." autocomplete="off" required onkeyup="filterHospital()" onclick="filterHospital()">
                                <div id="suggestionsBox" class="suggestions-container"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Authorized Official Name:</label>
                                <input type="text" class="form-control" name="authName" placeholder="e.g. Dr. Sarah Ahmed" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Official Email Address:</label>
                                <input type="email" class="form-control" name="offEmail" placeholder="admin@hospitaldomain.com" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Contact Number:</label>
                                <input type="tel" class="form-control" name="conNumber" placeholder="021-XXXXXXXX" maxlength="12" 
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12);" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">City / Area Location:</label>
                                <select class="form-select" name="city" required>
                                    <option value="" selected disabled>-- Select Area / Location --</option>
                                    <option value="Karachi Central">Karachi Central</option>
                                    <option value="Karachi East">Karachi East</option>
                                    <option value="Karachi South">Karachi South</option>
                                    <option value="Karachi West">Surjani Town / West</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Password:</label>
                                <input type="password" class="form-control" name="password" placeholder="••••••••••••" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Full Address:</label>
                                <textarea class="form-control" rows="3" name="address" placeholder="Plot No, Street, Sector, Main Area..." required></textarea>
                            </div>

                            <div class="col-12 pt-2">
                                <button type="submit" name="btn" class="btn btn-teal">Register Account</button>
                            </div>

                        </div>
                    </form>

                    <!-- Links Below Form -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top small text-muted auth-footer-links">
                        <p class="m-0">Already registered? <a href="hospital_login.php">Login Here</a></p>
                        <p class="m-0">Check your approval status: <a href="hospital_status.php">Check Status</a></p>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Script for Custom White Suggestions Box -->
    <script>
        const hospitals = [
            "Aga Khan University Hospital",
            "Indus Hospital & Health Network",
            "Civil Hospital Karachi",
            "Liaquat National Hospital",
            "Tabba Heart Institute",
            "Darul Sehat Hospital",
            "Saifee Hospital",
            "OMI Hospital",
            "Bahria Town International Hospital",
            "National Institute of Child Health (NICH)",
            "Jinnah Postgraduate Medical Centre (JPMC)",
            "Dr. Ziauddin Hospital",
            "Patel Hospital",
            "Abbasi Shaheed Hospital",
            "Memon Medical Institute Hospital (MMIH)",
            "South City Hospital",
            "Sindh Government Hospital, Liaquatabad"
        ];

        function filterHospital() {
            const input = document.getElementById('hospitalInput');
            const filter = input.value.toLowerCase();
            const box = document.getElementById('suggestionsBox');
            
            box.innerHTML = '';
            
            const filtered = hospitals.filter(h => h.toLowerCase().includes(filter));
            
            if (filtered.length > 0 && filter !== '') {
                box.style.display = 'block';
                filtered.forEach(h => {
                    const item = document.createElement('div');
                    item.className = 'suggestion-item';
                    item.innerText = h;
                    item.onclick = function() {
                        input.value = h;
                        box.style.display = 'none';
                    };
                    box.appendChild(item);
                });
            } else if (filter === '') {
                box.style.display = 'block';
                hospitals.forEach(h => {
                    const item = document.createElement('div');
                    item.className = 'suggestion-item';
                    item.innerText = h;
                    item.onclick = function() {
                        input.value = h;
                        box.style.display = 'none';
                    };
                    box.appendChild(item);
                });
            } else {
                box.style.display = 'none';
            }
        }

        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            const input = document.getElementById('hospitalInput');
            const box = document.getElementById('suggestionsBox');
            if (!input.contains(e.target) && !box.contains(e.target)) {
                box.style.display = 'none';
            }
        });
    </script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Empty fields check
    if (
        empty($_POST['hospitalNm']) || 
        empty($_POST['authName']) || 
        empty($_POST['offEmail']) || 
        empty($_POST['conNumber']) || 
        empty($_POST['password']) || 
        empty($_POST['city']) || 
        empty($_POST['address'])
    ) {
        echo "<script>Swal.fire({icon: 'error', title: 'Error', text: 'Please fill all fields including Hospital Name!'});</script>";
        exit;
    } 

    // 2. Sanitize aur Trim
    $hosName  = trim($_POST['hospitalNm']);
    $authName = trim($_POST['authName']);
    $email    = trim($_POST['offEmail']);
    $number   = trim($_POST['conNumber']);
    $pass     = trim($_POST['password']);
    $city     = trim($_POST['city']);
    $address  = trim($_POST['address']);

    // 3. Email format check
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>Swal.fire({icon: 'error', title: 'Error', text: 'Invalid email format!'});</script>";
        exit;
    }

    $cleanEmail = filter_var($email, FILTER_SANITIZE_EMAIL);

    // 4. Number validation check
    $numberError = "";
    if (!is_numeric($number)) {
        $numberError = "Error: Only numbers are allowed, not text!";
    } elseif (strlen($number) != 12) {
        $numberError = "Error: Contact number must be exactly 12 digits!";
    }

    if (!empty($numberError)) {
        echo "<script>Swal.fire({icon: 'error', title: 'Error', text: '$numberError'});</script>";
        exit;
    }

    // 5. Password length check
    if (strlen($pass) < 6) {
        echo "<script>Swal.fire({icon: 'error', title: 'Error', text: 'Password must be at least 6 characters long!'});</script>";
        exit;
    }

    // 6. Duplicate Email Check in Database
    $emailCheckQuery = "SELECT * FROM hospitals WHERE email = '$cleanEmail'";
    $emailCheckResult = mysqli_query($connect, $emailCheckQuery);

    if (mysqli_num_rows($emailCheckResult) > 0) {
        echo "<script>Swal.fire({icon: 'error', title: 'Error', text: 'This email is already registered!'});</script>";
        exit;
    }

    // 7. Duplicate Hospital Name Check
    $hosCheckQuery = "SELECT * FROM hospitals WHERE hospital_name = '$hosName'";
    $hosCheckResult = mysqli_query($connect, $hosCheckQuery);

    if (mysqli_num_rows($hosCheckResult) > 0) {
        echo "<script>Swal.fire({icon: 'error', title: 'Error', text: 'This hospital name is already registered in the system!'});</script>";
        exit;
    }

    // 8. Password Hashing
    $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

    // 9. Database Insert Query (status 'pending' ke sath)
    $status = 'pending';
    $query = "INSERT INTO hospitals (hospital_name, authorized_name, email, contact_number, city, password, address, status) 
              VALUES ('$hosName', '$authName', '$cleanEmail', '$number', '$city', '$hashedPassword', '$address', '$status')";

    $run = mysqli_query($connect,$query);

    if ($run) {
        echo "<script>Swal.fire({icon: 'success', title: 'Success', text: 'Registration completed successfully! You can login after admin approval.'});</script>";
    } else {
        echo "<script>Swal.fire({icon: 'error', title: 'Error', text: 'Failed to save data into database.'});</script>";
    }

}
?>