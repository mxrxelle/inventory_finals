<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: supplier_orders.php?status=deleted");
    exit();
}

$con = new database();
$db = $con->opencon();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $db->prepare("DELETE FROM supplier_orders WHERE supplier_order_id = ?");
    $stmt->execute([$id]);
}

header("Location: supplier_orders.php");
exit();
