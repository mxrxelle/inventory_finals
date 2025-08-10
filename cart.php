<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$db = new database();

$cartItems = $db->getCartItems($_SESSION['user_id']);
$totalAmount = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Cart</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
        }
        h2 {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead {
            background-color: #333;
            color: white;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .actions form {
            display: inline-block;
        }
        .total {
            text-align: right;
            font-size: 18px;
            margin-top: 15px;
        }
        .checkout-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 16px;
            float: right;
            margin-top: 10px;
        }
        .checkout-btn:hover {
            background-color: #218838;
        }

        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 16px;
            margin-top: 20px;
        }   
        .back-btn:hover {
            background-color: #5a6268;
        }

    </style>
</head>
<body>

<div class="container">
    <h2>My Cart</h2>

    <?php if (empty($cartItems)): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): 
                    $subtotal = $item['product_price'] * $item['cart_quantity'];
                    $totalAmount += $subtotal;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                    <td>₱<?php echo number_format($item['product_price'], 2); ?></td>
                    <td><?php echo (int)$item['cart_quantity']; ?></td>
                    <td>₱<?php echo number_format($subtotal, 2); ?></td>
                    <td class="actions">
                        <form method="POST" action="remove_from_cart.php" onsubmit="return confirm('Are you sure you want to remove this item?');">
                            <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                            <button type="submit" style="background-color: #dc3545; color: white; padding: 6px 10px; border: none; border-radius: 4px;">Remove</button>
                        </form>
                    </td>
                    
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total">
            <strong>Total: ₱<?php echo number_format($totalAmount, 2); ?></strong>
        </div>

        <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
        
    <?php endif; ?>
        <a href="browse_products.php" class="back-btn">← Back</a>


</div>

</body>
</html>
