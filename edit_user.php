<?php
require_once 'classes/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$db = new database();
$con = $db->opencon();

if (!isset($_GET['id'])) {
    die("No user ID provided.");
}

$user_id = $_GET['id'];

// Get user data
$stmt = $con->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Update user if form submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $con->prepare("UPDATE users SET first_name=?, last_name=?, username=?, email=?, role=? WHERE user_id=?");
    if ($stmt->execute([$first_name, $last_name, $username, $email, $role, $user_id])) {
        header("Location: users.php");
        exit();
    } else {
        echo "Error updating user.";
    }
}
?>

<h2>Edit User</h2>
<form method="post" action="">
    First Name: <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required><br>
    Last Name: <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required><br>
    Username: <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required><br>
    Email: <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br>
    Role: 
    <select name="role">
        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
        <option value="inventory_staff" <?= $user['role'] == 'inventory_staff' ? 'selected' : '' ?>>Inventory Staff</option>
    </select><br>
    <button type="submit">Update User</button>
</form>
