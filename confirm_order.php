<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'inventory_staff')) {
    header("Location: login.php");
    exit();
}

$con = new database();
$db = $con->opencon();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $db->prepare("UPDATE supplier_orders SET order_status = 'Approved' WHERE supplier_order_id = ?");
    $stmt->execute([$id]);

    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Order Confirmed!',
        showConfirmButton: false,
        timer: 1500
    }).then(() => {
        window.location.href = 'supplier_orders.php';
    });
    </script>";
    exit();
}
