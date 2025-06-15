<?php
include 'classes/database.php';
header('Content-Type: application/json');

if (!isset($_GET['q'])) {
    echo json_encode([]);
    exit;
}

$q = trim($_GET['q']);
if ($q === '') {
    echo json_encode([]);
    exit;
}

$db = new database();
$con = $db->opencon();

$stmt = $con->prepare("SELECT products_id, product_name FROM Products WHERE product_name LIKE ? LIMIT 10");
$stmt->execute(["%$q%"]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($results);