<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $con = new database();
    $db = $con->opencon();

    $user_id = $_SESSION['user_id'];
    $order_date = date('Y-m-d H:i:s');
    $total_amount = 0;

    // Calculate total from selected products
    if (isset($_POST['products'])) {
        foreach ($_POST['products'] as $product_id) {
            $quantity = $_POST['quantities'][$product_id];
            $stmt = $db->prepare("SELECT product_price, product_stock FROM products WHERE products_id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                $subtotal = $product['product_price'] * $quantity;
                $total_amount += $subtotal;

                // Check if stock is enough
                if ($product['product_stock'] < $quantity) {
                    die("Not enough stock for product ID $product_id.");
                }
            }
        }

        // Insert into orders table
        $orderStmt = $db->prepare("INSERT INTO orders (user_id, order_date, total_amount, order_status) VALUES (?, ?, ?, ?)");
        $orderStmt->execute([$user_id, $order_date, $total_amount, 'Pending']);

        $order_id = $db->lastInsertId();

        // Insert each product into order_items
        foreach ($_POST['products'] as $product_id) {
            $quantity = $_POST['quantities'][$product_id];

            $stmt = $db->prepare("SELECT product_price, product_stock FROM products WHERE products_id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            $price = $product['product_price'];

            $itemStmt = $db->prepare("INSERT INTO order_items (order_id, products_id, order_quantity, order_price) VALUES (?, ?, ?, ?)");
            $itemStmt->execute([$order_id, $product_id, $quantity, $price]);

            // Reduce stock in products table
            $newStock = $product['product_stock'] - $quantity;
            $updateStock = $db->prepare("UPDATE products SET product_stock = ? WHERE products_id = ?");
            $updateStock->execute([$newStock, $product_id]);
        }

        header("Location: view_order_items.php"); // after successful order
        exit();
    } else {
        echo "No products selected.";
    }
} else {
    echo "Invalid request method.";
}
?>
