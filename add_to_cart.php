<?php
session_start();
require_once ('classes/database.php');

if (isset($_POST['products_id']) && isset($_SESSION['user_id'])) {
    $db = new database();
    $user_id = $_SESSION['user_id'];
    $products_id = $_POST['products_id'];
    $cart_quantity = isset($_POST['cart_quantity']) ? (int) $_POST['cart_quantity'] : 1;

    $db->addToCart($user_id, $products_id, $cart_quantity);

    header("Location: cart.php");
    exit;
} else {
    echo "Missing data.";
}
