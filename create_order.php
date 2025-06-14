<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$con = new database();
$stmt = $con->opencon()->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Order</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #ecf0f1; padding: 40px; }
        table { width: 100%; border-collapse: collapse; background: white; margin-bottom: 20px; }
        th, td { padding: 10px; text-align: center; border: 1px solid #ccc; }
        th { background-color: #2c3e50; color: white; }
        .submit-btn {
            background-color: #27ae60; color: white;
            padding: 10px 20px; border: none; cursor: pointer;
            border-radius: 4px; width: 100%;
        }
        .submit-btn:hover { background-color: #219150; }
    </style>
</head>
<body>

<h2>Create New Order</h2>

<form action="process_order.php" method="POST">
    <table>
        <tr>
            <th>Select</th>
            <th>Product</th>
            <th>Price (₱)</th>
            <th>Quantity</th>
            <th>Subtotal (₱)</th>
        </tr>
        <?php foreach ($products as $product): ?>
        <tr>
            <td><input type="checkbox" name="products[]" value="<?= $product['products_id'] ?>" class="product-check" data-price="<?= $product['product_price'] ?>"></td>
            <td><?= htmlspecialchars($product['product_name']) ?></td>
            <td><?= number_format($product['product_price'], 2) ?></td>
            <td><input type="number" name="quantities[<?= $product['products_id'] ?>]" min="1" value="1" class="quantity-input" disabled></td>
            <td><span class="subtotal">0.00</span></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Total Amount: ₱<span id="totalAmount">0.00</span></h3>

    <button type="submit" class="submit-btn">Place Order</button>
</form>

<script>
    const checkboxes = document.querySelectorAll('.product-check');
    const totalAmountDisplay = document.getElementById('totalAmount');

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const row = this.closest('tr');
            const qtyInput = row.querySelector('.quantity-input');
            const subtotal = row.querySelector('.subtotal');
            const price = parseFloat(this.getAttribute('data-price'));

            if (this.checked) {
                qtyInput.disabled = false;
                subtotal.textContent = (qtyInput.value * price).toFixed(2);
            } else {
                qtyInput.disabled = true;
                subtotal.textContent = "0.00";
                qtyInput.value = 1;
            }

            updateTotal();
        });
    });

    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('input', function() {
            const row = this.closest('tr');
            const checkbox = row.querySelector('.product-check');
            const subtotal = row.querySelector('.subtotal');
            const price = parseFloat(checkbox.getAttribute('data-price'));
            if (checkbox.checked) {
                subtotal.textContent = (this.value * price).toFixed(2);
                updateTotal();
            }
        });
    });

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal').forEach(span => {
            total += parseFloat(span.textContent);
        });
        totalAmountDisplay.textContent = total.toFixed(2);
    }
</script>

</body>
</html>
