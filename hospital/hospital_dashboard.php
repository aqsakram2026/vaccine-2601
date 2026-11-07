
<?php
session_start();
include_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/notifications.php';

if (!isset($_SESSION['hospital_id'])) {
    header("Location: hospital_login.php");
    exit();
}

$hospital_id = $_SESSION['hospital_id'];
$hospital_name = $_SESSION['hospital_name'];
$sql_hospital_name = esc($hospital_name);
$profileImageResult = mysqli_query($connect, 'SELECT profile_image FROM hospitals WHERE id = ' . (int)$hospital_id . ' LIMIT 1');
if (!$profileImageResult) {
    error_log('E-Vaccine hospital dashboard profile image query failed: ' . mysqli_error($connect));
}
$profileImageRow = null;
if ($profileImageResult) {
    $profileImageRow = mysqli_fetch_assoc($profileImageResult);
}
$profileImage = '';
if (isset($profileImageRow['profile_image'])) {
    $profileImage = $profileImageRow['profile_image'];
}
$csrfToken = $_SESSION['hospital_booking_csrf'] ?? bin2hex(random_bytes(32));
$_SESSION['hospital_booking_csrf'] = $csrfToken;

if (isset($_POST['update_status_btn'])) {
    $booking_id = filter_input(INPUT_POST, 'booking_id', FILTER_VALIDATE_INT);
    $decision = isset($_POST['decision']) && is_string($_POST['decision'])
        ? $_POST['decision']
        : '';
    $current_status = '';
    $status_error = '';

    if (!isset($_POST['csrf']) || !is_string($_POST['csrf']) || !hash_equals($csrfToken, $_POST['csrf'])) {
        $status_error = 'Your form session expired. Please try again.';
    } elseif (!$booking_id || $booking_id < 1) {
        $status_error = 'The selected appointment is invalid.';
    } else {
        $lookup = mysqli_prepare(
            $connect,
            'SELECT b.vaccination_status, b.parent_id, b.child_id, c.child_name, h.id
             FROM bookings b
             LEFT JOIN children c ON c.id = b.child_id
             LEFT JOIN hospitals h ON h.hospital_name = b.hospital_name
             WHERE b.id = ? AND b.hospital_name = ?
             LIMIT 1'
        );
        if ($lookup) {
            $parentId = 0;
            $childId = 0;
            $childName = '';
            $hospitalId = 0;
            mysqli_stmt_bind_param($lookup, 'is', $booking_id, $hospital_name);
            if (mysqli_stmt_execute($lookup)) {
                mysqli_stmt_bind_result($lookup, $current_status, $parentId, $childId, $childName, $hospitalId);
                mysqli_stmt_fetch($lookup);
            } else {
                $status_error = 'Could not verify this appointment.';
                error_log('E-Vaccine hospital booking lookup failed: ' . mysqli_stmt_error($lookup));
            }
            mysqli_stmt_close($lookup);
        } else {
            $status_error = 'Could not verify this appointment.';
            error_log('E-Vaccine hospital booking lookup could not be prepared: ' . mysqli_error($connect));
        }
    }

    $transitions = [
        'approve_appointment' => ['Pending', 'Hospital Approved'],
        'reject_appointment' => ['Pending', 'Hospital Rejected'],
        'record_vaccinated' => ['Hospital Approved', 'Vaccinated'],
        'record_not_vaccinated' => ['Hospital Approved', 'Not Vaccinated']
    ];
    if ($status_error === '' && isset($transitions[$decision])) {
        [$required_status, $new_status] = $transitions[$decision];
        if ($current_status !== $required_status) {
            $status_error = 'This appointment has already changed or is not ready for that action.';
        } else {
            $update = mysqli_prepare(
                $connect,
                "UPDATE bookings
                 SET vaccination_status = ?,
                     vaccination_date = CASE WHEN ? = 'Vaccinated' THEN CURDATE() ELSE vaccination_date END
                 WHERE id = ? AND hospital_name = ? AND vaccination_status = ?"
            );
            if ($update) {
                mysqli_stmt_bind_param($update, 'ssiss', $new_status, $new_status, $booking_id, $hospital_name, $required_status);
                if (!mysqli_stmt_execute($update) || mysqli_stmt_affected_rows($update) !== 1) {
                    $status_error = 'The appointment could not be updated. Please refresh and try again.';
                    error_log('E-Vaccine hospital booking update failed: ' . mysqli_stmt_error($update));
                } else {
                    evax_notify_booking_status(
                        $connect,
                        $booking_id,
                        $parentId,
                        $hospitalId,
                        (string)$childName,
                        $new_status
                    );
                }
                mysqli_stmt_close($update);
            } else {
                $status_error = 'The appointment could not be updated. Please try again.';
                error_log('E-Vaccine hospital booking update could not be prepared: ' . mysqli_error($connect));
            }
        }
    } elseif ($status_error === '') {
        $status_error = 'Select a valid appointment action.';
    }

    $_SESSION['hospital_booking_flash'] = [
        'type' => 'success',
        'message' => 'Appointment updated successfully.'
    ];
    if ($status_error !== '') {
        $_SESSION['hospital_booking_flash']['type'] = 'danger';
        $_SESSION['hospital_booking_flash']['message'] = $status_error;
    }
    header("Location: hospital_dashboard.php");
    exit();
}

// Get bookings for the logged-in hospital only
$query = "SELECT bookings.*,
                 children.child_name AS child_name,
                 parents.email AS parent_email,
                 vaccines.Vaccine_Name AS vaccine_name
          FROM bookings
          LEFT JOIN children
              ON bookings.child_id = children.id
          LEFT JOIN parents
              ON bookings.parent_id = parents.id
          LEFT JOIN vaccines
              ON bookings.vaccine_id = vaccines.Vaccine_ID
          WHERE bookings.hospital_name = '$sql_hospital_name'
          ORDER BY bookings.created_at DESC";

$result = mysqli_query($connect, $query);
if (!$result) {
    error_log('E-Vaccine hospital booking list query failed: ' . mysqli_error($connect));
}
$bookingFlash = $_SESSION['hospital_booking_flash'] ?? null;
unset($_SESSION['hospital_booking_flash']);

// Manage vaccine stock link
if (isset($_GET['stock'])) {
    header("Location: hospital_vaccine.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hospital Dashboard - E-Vaccine Portal</title>
    <?php include __DIR__ . '/../includes/portal-head.php'; ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../includes/theme.js?v=6"></script>
    <link href="../includes/theme.css?v=5" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

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
            white-space: nowrap;
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
            min-width: 130px;
        }

        .update-btn {
            background-color: var(--medical-teal);
            color: #ffffff;
            font-weight: 600;
            border-radius: 6px;
            padding: 6px 14px;
            border: none;
            font-size: 0.85rem;
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
            padding: 8px 14px;
            text-decoration: none;
            font-size: 0.85rem;
        }

        .logout-btn:hover {
            background-color: #DC2626;
            color: #ffffff;
        }
    </style>
</head>

<body>

    <div class="container py-4">

        <!-- Welcome Banner -->
        <div class="overview-box mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-speedometer2 fs-4"
                       style="color: var(--medical-teal);"></i>

                    <h4 class="fw-bold m-0">
                        Welcome,
                        <?php echo htmlspecialchars($hospital_name); ?>
                    </h4>
                </div>

                <p class="text-secondary m-0 mb-3">
                    Manage your patient appointments and vaccination records.
                </p>

                <a href="hospital_vaccine.php" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-box2 me-1"></i>
                    Manage Vaccine Stock
                </a>
                <a href="profile.php" class="btn btn-outline-secondary btn-sm ms-1">Edit hospital profile</a>
            </div>

            <div class="d-flex align-items-center gap-2">
                <?php if ($profileImage !== ''): ?>
                    <img src="../<?= htmlspecialchars($profileImage, ENT_QUOTES, 'UTF-8') ?>" alt="Hospital profile image" class="rounded-circle object-fit-cover" width="42" height="42">
                <?php endif; ?>
                <a href="hospital_logout.php" class="logout-btn">
                    <i class="bi bi-box-arrow-right me-1"></i>
                    Logout
                </a>
            </div>

        </div>

        <!-- Dashboard Layout -->
        <div class="row g-4">

            <!-- Sidebar -->
            <div class="col-lg-3">

                <div class="portal-card p-3">

                    <div class="px-2 mb-3">
                        <small class="text-uppercase text-muted fw-bold">
                            Portal Navigation
                        </small>
                    </div>

                    <div class="nav flex-column sidebar-menu">
                        <a href="../index.php" class="nav-link">
                            <span><i class="bi bi-house me-2"></i> Home</span>
                            <i class="bi bi-chevron-right small"></i>
                        </a>

                        <a href="hospital_dashboard.php" class="nav-link active">
                            <span>
                                <i class="bi bi-speedometer2 me-2"></i>
                                Hospital Dashboard
                            </span>
                            <i class="bi bi-chevron-right small"></i>
                        </a>

                        <a href="hospital_status.php" class="nav-link">
                            <span>
                                <i class="bi bi-check2-circle me-2"></i>
                                Hospital Status
                            </span>
                            <i class="bi bi-chevron-right small"></i>
                        </a>

                        <a href="hospital_vaccine.php" class="nav-link">
                            <span>
                                <i class="bi bi-box2 me-2"></i>
                                Update Stock
                            </span>
                            <i class="bi bi-chevron-right small"></i>
                        </a>

                    </div>

                </div>

            </div>

            <!-- Booking Records -->
            <div class="col-lg-9">

                <div class="portal-card p-4">

                    <?php if (is_array($bookingFlash)): ?>
                        <div class="alert alert-<?= htmlspecialchars($bookingFlash['type'], ENT_QUOTES, 'UTF-8') ?>" role="status">
                            <?= htmlspecialchars($bookingFlash['message'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>

                    <div class="border-bottom pb-3 mb-4">
                        <h4 class="fw-bold mb-1" style="font-size: 1.25rem;">
                            Patient Appointments List
                        </h4>

                        <p class="text-muted mb-0 small">
                            Approve appointment requests, record vaccination outcomes, and prepare patient reports.
                        </p>
                    </div>

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Child Name</th>
                                    <th>Vaccine</th>
                                    <th>Parent Email</th>
                                    <th>Booking Date</th>
                                    <th>Current Status</th>
                                    <th>Hospital action / report</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php
                                if ($result && mysqli_num_rows($result) > 0) {

                                    while ($row = mysqli_fetch_assoc($result)) {
                                ?>

                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars($row['id']); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($row['vaccine_name'] ?? 'Not specified'); ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $row['child_name'] ?? 'Child not found'
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $row['parent_email'] ?? 'Parent not found'
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo htmlspecialchars($row['created_at'] ?? 'Not available');
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            $status = $row['vaccination_status'] ?? 'Pending';
                                            $badgeClass = 'bg-warning text-dark';
                                            if (in_array($status, ['Hospital Approved', 'Vaccinated'], true)) {
                                                $badgeClass = 'bg-info text-dark';
                                            } elseif ($status === 'Admin Approved') {
                                                $badgeClass = 'bg-success';
                                            } elseif (in_array($status, ['Hospital Rejected', 'Admin Rejected', 'Not Vaccinated'], true)) {
                                                $badgeClass = 'bg-danger';
                                            }
                                            ?>

                                            <span class="badge <?php echo $badgeClass; ?>">
                                                <?php echo htmlspecialchars($status); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?php if ($status === 'Pending' || $status === 'Hospital Approved'): ?>
                                                <form method="POST" class="d-flex flex-wrap gap-2">
                                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                                    <input type="hidden" name="booking_id" value="<?= (int)$row['id'] ?>">
                                                    <input type="hidden" name="update_status_btn" value="1">
                                                    <?php if ($status === 'Pending'): ?>
                                                        <button type="submit" name="decision" value="approve_appointment" class="btn update-btn btn-sm">Approve</button>
                                                        <button type="submit" name="decision" value="reject_appointment" class="btn btn-outline-danger btn-sm">Reject</button>
                                                    <?php else: ?>
                                                        <button type="submit" name="decision" value="record_vaccinated" class="btn update-btn btn-sm">Record vaccinated</button>
                                                        <button type="submit" name="decision" value="record_not_vaccinated" class="btn btn-outline-danger btn-sm">Not vaccinated</button>
                                                    <?php endif; ?>
                                                </form>
                                            <?php elseif (in_array($status, ['Vaccinated', 'Admin Approved', 'Admin Rejected'], true)): ?>
                                                <a class="btn btn-outline-success btn-sm" href="hospital_report.php?booking_id=<?= (int)$row['id'] ?>">
                                                    <i class="bi bi-file-earmark-medical me-1" aria-hidden="true"></i>Patient report
                                                </a>
                                                <?php if ($status === 'Admin Approved'): ?>
                                                    <a class="btn btn-success btn-sm" href="hospital_report.php?booking_id=<?= (int)$row['id'] ?>&amp;certificate=1">Certificate</a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-secondary small">No further action</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                <?php
                                    }

                                } else {
                                ?>

                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            No bookings found for your hospital.
                                        </td>
                                    </tr>

                                <?php
                                }
                                ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php include __DIR__ . '/../includes/portal-footer.php'; ?>
</body>
</html>
