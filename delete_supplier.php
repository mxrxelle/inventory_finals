<?php
require_once('classes/database.php');
$con = new database();

$sweetAlertConfig = "";

// Check if 'id' is provided in the URL
if (!isset($_GET['id'])) {
    die("No supplier ID provided.");
}

$supplier_id = $_GET['id'];

// Check if supplier exists
$stmt = $con->opencon()->prepare("SELECT * FROM supplier WHERE supplier_id = ?");
$stmt->execute([$supplier_id]);
$supplier = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$supplier) {
    die("Supplier not found.");
}

// Proceed to delete
$deleteStmt = $con->opencon()->prepare("DELETE FROM supplier WHERE supplier_id = ?");
$result = $deleteStmt->execute([$supplier_id]);

if ($result) {
    // Success alert then redirect
    $sweetAlertConfig = "
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Supplier Deleted Successfully',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'suppliers.php';
        });
    </script>";
} else {
    // Error alert
    $sweetAlertConfig = "
    <script>
        Swal.fire('Error', 'Failed to delete supplier.', 'error');
    </script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Supplier</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?= $sweetAlertConfig ?>
</body>
</html>
