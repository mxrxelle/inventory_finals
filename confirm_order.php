<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'inventory_staff')) {
    header("Location: supplier_orders.php?status=confirmed");
    exit();
}

$con = new database();
$db = $con->opencon();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $db->prepare("UPDATE supplier_orders SET order_status = 'Approved' WHERE supplier_order_id = ?");
    $stmt->execute([$id]);
}

header("Location: supplier_orders.php");
exit();
