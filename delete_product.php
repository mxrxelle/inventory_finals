<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Only admin can delete products
    header("Location: login.php");
    exit();
}

$con = new database();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['product_id'])) {
    $products_id = (int)$_GET['product_id'];

    // Check if product exists
    $check = $con->opencon()->prepare("SELECT * FROM Products WHERE products_id = ?");
    $check->execute([$products_id]);
    $products = $check->fetch(PDO::FETCH_ASSOC);

    if ($products) {
        // Product exists — delete it
        $delete = $con->opencon()->prepare("DELETE FROM Products WHERE products_id = ?");
        if ($delete->execute([$products_id])) {
            // Success
            echo "<script>
                alert('Product deleted successfully!');
                window.location.href='products.php?category_id={$products['category_id']}';
            </script>";
        } else {
            // Error in delete query
            echo "<script>
                alert('Error deleting the product.');
                window.location.href='products.php?category_id={$products['category_id']}';
            </script>";
        }
    } else {
        // Product not found
        echo "<script>
            alert('Product not found.');
            window.location.href='products.php';
        </script>";
    }
} else {
    // Invalid access (no product_id)
    echo "<script>
        alert('Invalid request.');
        window.location.href='products.php';
    </script>";
}
?>
