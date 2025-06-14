<?php
require_once('classes/database.php');
$con = new database();

// Check if 'id' is provided in the URL
if (!isset($_GET['id'])) {
    die("No supplier ID provided.");
}

$supplier_id = $_GET['id'];

// Fetch supplier data
$stmt = $con->opencon()->prepare("SELECT * FROM supplier WHERE supplier_id = ?");
$stmt->execute([$supplier_id]);
$supplier = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$supplier) {
    die("Supplier not found.");
}

$sweetAlertConfig = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_name = trim($_POST['supplier_name']);
    $supplier_email = trim($_POST['supplier_email']);
    $supplier_phonenumber = trim($_POST['supplier_phonenumber']);

    // Validations
    if (empty($supplier_name) || empty($supplier_email) || empty($supplier_phonenumber)) {
        $sweetAlertConfig = "
        <script>
            Swal.fire('Error', 'Please fill all fields.', 'error');
        </script>";
    } elseif (!filter_var($supplier_email, FILTER_VALIDATE_EMAIL)) {
        $sweetAlertConfig = "
        <script>
            Swal.fire('Error', 'Invalid email format.', 'error');
        </script>";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $supplier_phonenumber)) {
        $sweetAlertConfig = "
        <script>
            Swal.fire('Error', 'Phone number must be 10 to 15 digits.', 'error');
        </script>";
    } else {
        // Update supplier data
        $updateStmt = $con->opencon()->prepare("UPDATE supplier SET supplier_name=?, supplier_phonenumber=?, supplier_email=? WHERE supplier_id=?");
        $result = $updateStmt->execute([$supplier_name, $supplier_phonenumber, $supplier_email, $supplier_id]);

        if ($result) {
            $sweetAlertConfig = "
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Supplier Updated Successfully',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'suppliers.php';
                });
            </script>";
        } else {
            $sweetAlertConfig = "
            <script>
                Swal.fire('Error', 'Failed to update supplier.', 'error');
            </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Supplier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container mt-5">
    <h2>Edit Supplier</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="supplier_name" class="form-label">Supplier Name</label>
            <input type="text" class="form-control" id="supplier_name" name="supplier_name" value="<?= htmlspecialchars($supplier['supplier_name']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="supplier_email" class="form-label">Email</label>
            <input type="email" class="form-control" id="supplier_email" name="supplier_email" value="<?= htmlspecialchars($supplier['supplier_email']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="supplier_phonenumber" class="form-label">Phone Number</label>
            <input type="text" class="form-control" id="supplier_phonenumber" name="supplier_phonenumber" value="<?= htmlspecialchars($supplier['supplier_phonenumber']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Supplier</button>
        <a href="suppliers.php" class="btn btn-secondary">Back</a>
    </form>
</div>

<?= $sweetAlertConfig ?>
</body>
</html>
