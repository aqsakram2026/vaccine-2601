<?php
include 'auth.php';
$active = 'hospital_add';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Registration - E-Vaccine Portal</title>
    <?php include __DIR__ . '/../includes/portal-head.php'; ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../includes/theme.js?v=6"></script>
    <link href="../includes/theme.css?v=5" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            background-color: #212529;
            min-height: 100vh;
        }

        .sidebar .nav-link {
            color: #ffffff;
            padding: 10px 12px;
            border-radius: 6px;
            margin-bottom: 4px;
        }

        .sidebar .nav-link.active {
            background-color: #0d6efd;
            color: #ffffff;
        }

        .sidebar .nav-link:hover:not(.active) {
            background-color: #343a40;
            color: #ffffff;
        }

        .portal-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        .form-control,
        .form-select {
            border-radius: 6px;
            border: 1px solid #ced4da;
            padding: 10px 12px;
            font-size: 0.9rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }

        .suggestions-container {
            position: absolute;
            background: #ffffff;
            border: 1px solid #ced4da;
            border-radius: 6px;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .suggestion-item {
            padding: 10px 12px;
            cursor: pointer;
            font-size: 0.9rem;
            color: #212529;
            border-bottom: 1px solid #f1f5f9;
        }

        .suggestion-item:hover {
            background-color: #f1f5f9;
        }

        .btn-register {
            background-color: #0d6efd;
            color: #ffffff;
            font-weight: 600;
            border-radius: 6px;
            padding: 10px 20px;
            border: none;
        }

        .btn-register:hover {
            background-color: #0b5ed7;
            color: #ffffff;
        }

        .auth-footer-links a {
            color: #0d6efd;
            font-weight: 600;
            text-decoration: none;
        }

        .auth-footer-links a:hover {
            text-decoration: underline;
        }
        .new{
            font-size: .9em;
        }

        @media (max-width: 767px) {
            .sidebar {
                min-height: auto;
            }

            .auth-footer-links {
                align-items: flex-start !important;
                gap: 12px;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4 p-lg-5">

            <div class="mb-4">
                <h2 class="fw-bold">Hospital Registration</h2>
                <p class="text-muted mb-0">
                    Register a healthcare center with its official and location details.
                </p>
            </div>

            <div class="portal-card p-4 p-md-5">

                <div class="border-bottom pb-3 mb-4">
                    <h4 class="fw-bold mb-1">Hospital Registration Form</h4>
                    <p class="text-muted mb-0 small">
                        Register your healthcare center with complete location and address details.
                    </p>
                </div>

                <form method="POST">
                    <div class="row g-3">

                        <div class="col-12 position-relative">
                            <label class="form-label">Hospital Name:</label>
                            <input type="text" class="form-control"
                                name="hospitalNm" id="hospitalInput"
                                placeholder="Select from list or type custom hospital name..."
                                autocomplete="off" required
                                onkeyup="filterHospital()" onclick="filterHospital()">
                            <div id="suggestionsBox" class="suggestions-container"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Authorized Official Name:</label>
                            <input type="text" class="form-control"
                                name="authName" placeholder="e.g. Dr. Sarah Ahmed" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Official Email Address:</label>
                            <input type="email" class="form-control"
                                name="offEmail" placeholder="admin@hospitaldomain.com" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Contact Number:</label>
                            <input type="tel" class="form-control"
                                name="conNumber" placeholder="021-XXXXXXXX" maxlength="12"
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
                            <input type="password" class="form-control"
                                name="password" placeholder="••••••••••••" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Full Address:</label>
                            <textarea class="form-control" rows="3" name="address"
                                placeholder="Plot No, Street, Sector, Main Area..." required></textarea>
                        </div>

                        <div class="col-12 pt-2">
                            <button type="submit" name="btn" class="btn btn-register">
                                Register Account
                            </button>
                        </div>

                    </div>
                </form>

                

            </div>
        </div>

    </div>
</div>

<!-- Hospital Suggestions -->
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

    document.addEventListener('click', function(e) {
        const input = document.getElementById('hospitalInput');
        const box = document.getElementById('suggestionsBox');

        if (!input.contains(e.target) && !box.contains(e.target)) {
            box.style.display = 'none';
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php
// Hospital register hone ka logic. Admin khud register kar raha hai,
// is liye hospital seedha "accepted" ho jata hai (approval ki zaroorat nahi).
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $hosName  = trim($_POST['hospitalNm'] ?? '');
    $authName = trim($_POST['authName'] ?? '');
    $email    = trim($_POST['offEmail'] ?? '');
    $number   = trim($_POST['conNumber'] ?? '');
    $pass     = trim($_POST['password'] ?? '');
    $city     = trim($_POST['city'] ?? '');
    $address  = trim($_POST['address'] ?? '');

    $error = '';

    if ($hosName == '' || $authName == '' || $email == '' || $number == '' || $pass == '' || $city == '' || $address == '') {
        $error = 'Please fill all fields including Hospital Name!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format!';
    } elseif (!is_numeric($number)) {
        $error = 'Only numbers are allowed in contact number, not text!';
    } elseif (strlen($number) != 12) {
        $error = 'Contact number must be exactly 12 digits!';
    } elseif (strlen($pass) < 6) {
        $error = 'Password must be at least 6 characters long!';
    } else {
        $sql_email = esc($email);
        $sql_name  = esc($hosName);

        $check = mysqli_query($connect, "SELECT id FROM hospitals WHERE email = '$sql_email'");
        if (mysqli_num_rows($check) > 0) {
            $error = 'This email is already registered!';
        } else {
            $check = mysqli_query($connect, "SELECT id FROM hospitals WHERE hospital_name = '$sql_name'");
            if (mysqli_num_rows($check) > 0) {
                $error = 'This hospital name is already registered in the system!';
            }
        }
    }

    if ($error == '') {
        $hashed = password_hash($pass, PASSWORD_DEFAULT);

        mysqli_query($connect, "INSERT INTO hospitals (hospital_name, authorized_name, email, contact_number, city, password, address, STATUS)
                                VALUES ('$sql_name', '" . esc($authName) . "', '$sql_email', '" . esc($number) . "',
                                        '" . esc($city) . "', '$hashed', '" . esc($address) . "', 'accepted')");

        echo "<script>Swal.fire({icon: 'success', title: 'Success', text: 'Hospital register ho gaya aur accept bhi.'})
              .then(() => { window.location.href = 'hospitalUpdate.php'; });</script>";
    } else {
        echo "<script>Swal.fire({icon: 'error', title: 'Error', text: " . json_encode($error) . "});</script>";
    }
}
?>

<?php include __DIR__ . '/../includes/portal-footer.php'; ?>
</body>
</html>
