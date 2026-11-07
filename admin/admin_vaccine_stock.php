<?php
include 'auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn'])) {

    $hospital = (int)$_POST['hospital'];
    $vaccine  = (int)$_POST['vaccine'];
    $quantity = (int)$_POST['quantity'];

    if ($hospital < 1 || $vaccine < 1 || $quantity < 1) {
        $alert = ['danger', 'Hospital, vaccine aur quantity (1 ya zyada) chuno.'];
    } else {
        // Is hospital ke paas ye vaccine pehle se hai?
        $check = mysqli_query($connect, "SELECT stock_id FROM hospital_vaccine_stock
                                         WHERE hospital_id = $hospital AND vaccine_id = $vaccine");

        if (mysqli_num_rows($check) > 0) {
            // Haan -> maujooda stock mein quantity jama kar do
            mysqli_query($connect, "UPDATE hospital_vaccine_stock SET quantity = quantity + $quantity
                                    WHERE hospital_id = $hospital AND vaccine_id = $vaccine");
        } else {
            // Nahi -> nayi entry
            mysqli_query($connect, "INSERT INTO hospital_vaccine_stock (hospital_id, vaccine_id, quantity)
                                    VALUES ($hospital, $vaccine, $quantity)");
        }
        header('Location: vaccine_stock_view.php?msg=updated');
        exit;
    }
}

// Sirf accepted hospitals aur active vaccines dropdown mein aate hain
$hospitals = mysqli_query($connect, "SELECT id, hospital_name FROM hospitals WHERE STATUS = 'accepted' ORDER BY hospital_name");
$vaccines  = mysqli_query($connect, "SELECT Vaccine_ID, Vaccine_Name FROM vaccines WHERE Status = 'Active' ORDER BY Vaccine_Name");

$page_title = 'Update Stock';
$active = 'stock_update';
include 'layout_top.php';
?>

<div class="mb-4">
    <h2 class="fw-bold">Update Vaccine Stock</h2>
    <p class="text-secondary small mb-0">Jo quantity likhoge wo hospital ke maujooda stock mein jama ho jayegi.</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Hospital</label>
                    <select name="hospital" class="form-select" required>
                        <option value="">Select hospital</option>
                        <?php while ($row = mysqli_fetch_assoc($hospitals)) { ?>
                            <option value="<?= (int)$row['id'] ?>"><?= e($row['hospital_name']) ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Vaccine</label>
                    <select name="vaccine" class="form-select" required>
                        <option value="">Select vaccine</option>
                        <?php while ($row = mysqli_fetch_assoc($vaccines)) { ?>
                            <option value="<?= (int)$row['Vaccine_ID'] ?>"><?= e($row['Vaccine_Name']) ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Quantity to add</label>
                    <input type="number" name="quantity" class="form-control" min="1" required>
                </div>
                <div class="col-12">
                    <button type="submit" name="btn" class="btn btn-primary px-4">Update Stock</button>
                    <a href="vaccine_stock_view.php" class="btn btn-outline-secondary px-4">View Stock</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include 'layout_bottom.php'; ?>
