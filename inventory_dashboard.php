<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'inventory_staff') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Inventory Staff Dashboard</title>
  <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css">
</head>
<body class="bg-light">

  <div class="container py-5 text-center">
    <h1 class="mb-4">Welcome to the Inventory Staff Dashboard!</h1>
    <p>Hello, <?php echo htmlspecialchars($_SESSION['first_name']); ?>. You are logged in as <strong>Inventory Staff</strong>.</p>

    <a href="logout.php" class="btn btn-danger mt-3">Logout</a>
  </div>

</body>
</html>
