<?php
require_once 'classes/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("No user ID provided.");
}

$user_id = $_GET['id'];

$db = new database();
$con = $db->opencon();

$stmt = $con->prepare("DELETE FROM users WHERE user_id = ?");
if ($stmt->execute([$user_id])) {
    header("Location: users.php");
    exit();
} else {
    echo "Error deleting user.";
}
?>
