<?php
require_once __DIR__ . '/../includes/parent-auth.php';
require_once __DIR__ . '/../includes/notifications.php';

$parent_id = (int)$_SESSION['user_id']['id'];
$bookingToken = $_SESSION['booking_csrf'] ?? bin2hex(random_bytes(32));
$_SESSION['booking_csrf'] = $bookingToken;


// Login hue parent ke bache
$run = mysqli_query($conn, "SELECT * FROM children WHERE parent_id = $parent_id");

$children = [];
while ($row = mysqli_fetch_assoc($run)) {
    $children[] = $row;
}

$selected_child = $children[0] ?? null;
$vaccine_result = mysqli_query(
    $conn,
    "SELECT Vaccine_ID, Vaccine_Name, Dose, Age_Limit
     FROM vaccines
     WHERE Status = 'Active'
     ORDER BY Vaccine_ID ASC"
);
$vaccines = [];
if (!$vaccine_result) {
    error_log('E-Vaccine appointment vaccine list query failed: ' . mysqli_error($conn));
} else {
    while ($row = mysqli_fetch_assoc($vaccine_result)) {
        $vaccines[] = $row;
    }
}

// Sirf admin se accept hue hospitals dropdown mein aate hain
$hospital_result = mysqli_query($conn, "SELECT * FROM hospitals WHERE STATUS = 'accepted' ORDER BY hospital_name");

$message = '';
$message_type = '';

if (isset($_POST['book'])) {
    $child_id = (int)($_POST['child_name'] ?? 0);
    $vaccine_id = (int)($_POST['vaccine_id'] ?? 0);
    $hospital = trim((string)($_POST['hospital_name'] ?? ''));

    if (!isset($_POST['csrf']) || !is_string($_POST['csrf']) || !hash_equals($bookingToken, $_POST['csrf'])) {
        $message = 'Your form session expired. Please reload and try again.';
        $message_type = 'warning';
    } elseif ($child_id < 1) {
        $message = 'Please select a child.';
        $message_type = 'warning';
    } elseif ($vaccine_id < 1) {
        $message = 'Please select a vaccine.';
        $message_type = 'warning';
    } else {
        $childCheck = mysqli_query($conn, "SELECT id FROM children WHERE id = $child_id AND parent_id = $parent_id LIMIT 1");
        if (!$childCheck) {
            error_log('E-Vaccine appointment child check failed: ' . mysqli_error($conn));
            $message = 'Unable to verify the selected child. Please try again.';
            $message_type = 'danger';
        } elseif (mysqli_num_rows($childCheck) !== 1) {
            $message = 'Please select a valid child.';
            $message_type = 'warning';
        }

        if ($message === '') {
            $vaccineCheck = mysqli_query(
                $conn,
                "SELECT Vaccine_ID FROM vaccines WHERE Vaccine_ID = $vaccine_id AND Status = 'Active' LIMIT 1"
            );
            if (!$vaccineCheck) {
                error_log('E-Vaccine appointment vaccine check failed: ' . mysqli_error($conn));
                $message = 'Unable to verify the selected vaccine. Please try again.';
                $message_type = 'danger';
            } elseif (mysqli_num_rows($vaccineCheck) !== 1) {
                $message = 'Please select an active vaccine.';
                $message_type = 'warning';
            }
        }

        if ($message === '') {
            $hospitalSql = mysqli_real_escape_string($conn, $hospital);
            $hospitalCheck = mysqli_query(
                $conn,
                "SELECT id, hospital_name FROM hospitals WHERE hospital_name = '$hospitalSql' AND STATUS = 'accepted' LIMIT 1"
            );
            $hospitalRow = $hospitalCheck ? mysqli_fetch_assoc($hospitalCheck) : null;
            if (!$hospitalCheck) {
                error_log('E-Vaccine appointment hospital check failed: ' . mysqli_error($conn));
                $message = 'Unable to verify the selected hospital. Please try again.';
                $message_type = 'danger';
            }

            if (!$hospitalRow) {
                if ($message === '') {
                    $message = 'Please select a valid hospital.';
                    $message_type = 'warning';
                }
            } else {
                $hospitalSql = mysqli_real_escape_string($conn, (string)$hospitalRow['hospital_name']);
                $bookingSaved = mysqli_query(
                    $conn,
                    "INSERT INTO bookings (hospital_name, vaccination_status, parent_id, child_id, vaccine_id)
                     VALUES ('$hospitalSql', 'Pending', $parent_id, $child_id, $vaccine_id)"
                );
                if (!$bookingSaved) {
                    error_log('E-Vaccine appointment booking failed: ' . mysqli_error($conn));
                }

                if ($bookingSaved) {
                    $message = 'Appointment booked successfully!';
                    $message_type = 'success';
                    $bookingId = (int)mysqli_insert_id($conn);
                    $childNameResult = mysqli_query($conn, "SELECT child_name FROM children WHERE id = $child_id AND parent_id = $parent_id");
                    $childRow = $childNameResult ? mysqli_fetch_assoc($childNameResult) : null;
                    $childName = (string)($childRow['child_name'] ?? 'your child');
                    evax_create_notification(
                        $conn,
                        'hospital',
                        (int)$hospitalRow['id'],
                        'New appointment request',
                        $childName . ' has an appointment request waiting for review.',
                        'hospital/hospital_dashboard.php',
                        'booking-' . $bookingId . '-created-hospital'
                    );
                    evax_create_notification(
                        $conn,
                        'admin',
                        0,
                        'New appointment booked',
                        'A new appointment for ' . $childName . ' at ' . $hospitalRow['hospital_name'] . ' is waiting for hospital review.',
                        'admin/admin.php',
                        'booking-' . $bookingId . '-created-admin'
                    );
                } else {
                    $message = 'Booking failed. Please try again.';
                    $message_type = 'danger';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Vaccine Appointment Booking | Vaccination Management System</title>
    <?php include __DIR__ . '/../includes/portal-head.php'; ?>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <script src="../includes/theme.js?v=6"></script>
    <link href="../includes/theme.css?v=5" rel="stylesheet">
</head>

<body class="bg-light min-vh-100">

    <?php $parentPageTitle = 'Appointment Booking'; include __DIR__ . '/../includes/parent-navbar.php'; ?>

    <main class="container py-5">

        <div class="row justify-content-center">
            <div class="col-12 col-md-9 col-lg-7 col-xl-6">

                <div class="text-center mb-4">
                    <div
                        class="d-inline-flex align-items-center justify-content-center
                               bg-primary bg-opacity-10 text-primary rounded-circle mb-3"
                        style="width: 70px; height: 70px;"
                    >
                        <span class="fs-1 fw-bold text-white">+</span>
                    </div>

                    <h1 class="h2 fw-bold text-primary">
                        Book Vaccine Appointment
                    </h1>

                    <p class="text-secondary">
                        Select the vaccine recorded on your healthcare provider's recommendation.
                    </p>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 px-4 pt-4 pb-0">
                        <h2 class="h5 fw-bold mb-1">Appointment Details</h2>
                        <p class="text-secondary small mb-0">
                            Complete the information below to submit your request.
                        </p>
                    </div>

                    <div class="card-body p-4">

                        <?php if ($message !== ''): ?>
                            <div
                                class="alert alert-<?= htmlspecialchars($message_type) ?> rounded-3"
                                role="alert"
                            >
                                <?= htmlspecialchars($message) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (empty($children)): ?>

                            <div class="text-center py-4">
                                <h3 class="h5 fw-bold text-dark">
                                    No registered children found
                                </h3>

                                <p class="text-secondary">
                                    Register your child before booking an appointment.
                                </p>

                                <a href="child.php"
                                   class="btn btn-primary rounded-3 px-4">
                                    Register Your Child
                                </a>
                            </div>

                        <?php else: ?>

                            <form method="POST" action="">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($bookingToken, ENT_QUOTES, 'UTF-8') ?>">

                                <div class="mb-4">
                                    <label for="child_name"
                                           class="form-label fw-semibold">
                                        Child Name
                                    </label>

                                    <select
                                        name="child_name"
                                        id="child_name"
                                        class="form-select form-select-lg rounded-3"
                                        required
                                    >
                                        <option value="">Select Child</option>

                                        <?php foreach ($children as $child): ?>
                                            <option
                                                value="<?= (int)$child['id'] ?>"
                                                <?php
                                                if (isset($_POST['child_name']) && (int)$_POST['child_name'] === (int)$child['id']) {
                                                    echo 'selected';
                                                }
                                                ?>
                                            >
                                                <?= htmlspecialchars($child['child_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="suggested_vaccines"
                                           class="form-label fw-semibold">
                                        Vaccine
                                    </label>

                                    <select
                                        name="vaccine_id"
                                        id="suggested_vaccines"
                                        class="form-select form-select-lg rounded-3"
                                        required
                                    >
                                        <option value="">
                                            Select a vaccine
                                        </option>

                                        <?php foreach ($vaccines as $vaccine): ?>
                                            <option
                                                value="<?= (int)$vaccine['Vaccine_ID'] ?>"
                                                <?= isset($_POST['vaccine_id']) && (int)$_POST['vaccine_id'] === (int)$vaccine['Vaccine_ID'] ? 'selected' : '' ?>
                                            >
                                                <?= htmlspecialchars($vaccine['Vaccine_Name'] . ' — ' . $vaccine['Age_Limit'] . ', dose ' . $vaccine['Dose'], ENT_QUOTES, 'UTF-8') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <div class="form-text">
                                        This catalogue is not an age-based recommendation. Confirm vaccine selection and timing with a qualified healthcare professional.
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="hospital_name"
                                           class="form-label fw-semibold">
                                        Hospital Name
                                    </label>

                                    <select
                                        name="hospital_name"
                                        id="hospital_name"
                                        class="form-select form-select-lg rounded-3"
                                        required
                                    >
                                        <option value="">Select Hospital</option>

                                        <?php while ($hospital_row = mysqli_fetch_assoc($hospital_result)): ?>
                                            <option
                                                value="<?= htmlspecialchars($hospital_row['hospital_name']) ?>"
                                                <?php
                                                if (isset($_POST['hospital_name']) && $_POST['hospital_name'] === $hospital_row['hospital_name']) {
                                                    echo 'selected';
                                                }
                                                ?>
                                            >
                                                <?= htmlspecialchars($hospital_row['hospital_name']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="parent_email"
                                           class="form-label fw-semibold">
                                        Parent Email
                                    </label>

                                    <input
                                        type="email"
                                        name="parent_email"
                                        id="parent_email"
                                        class="form-control form-control-lg rounded-3"
                                        placeholder="Enter your email address"
                                        value="<?= htmlspecialchars($_SESSION['user_id']['email'] ?? $_POST['parent_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        required
                                    >
                                </div>

                                <div class="bg-primary bg-opacity-10 rounded-3 p-3 mb-4">
                                    <div class="d-flex align-items-start gap-2">
                                        <span class="text-white fw-bold fs-5">i</span>
                                        <div>
                                            <p class="fw-semibold mb-1 text-white">
                                                Booking Information
                                            </p>
                                            <p class="small text-secondary mb-0">
                                                Your appointment will be submitted with
                                                Pending status until it is processed.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <button
                                    type="submit"
                                    name="book"
                                    class="btn btn-primary btn-lg w-100 rounded-3 fw-semibold shadow-sm"
                                >
                                    Book Appointment
                                </button>

                            </form>

                        <?php endif; ?>

                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="parent_dashboard.php"
                       class="text-decoration-none text-secondary">
                        &larr; Back to Parent Dashboard
                    </a>
                </div>

               </div>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include __DIR__ . '/../includes/portal-footer.php'; ?>
</body>
</html>
