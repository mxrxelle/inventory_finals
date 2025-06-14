<?php

class database{

    function opencon() {
        return new PDO(
            'mysql:host=localhost; dbname=inventory_db',
            username: 'root',
            password: ''
        );
    }

    function signupUser($firstname, $lastname, $username, $email, $password, $role, $created_at){
        $con = $this->opencon();

        if($created_at === null){
            $created_at = date('Y-m-d H:i:s'); // Set current date and time if not provided
        }

        try{
            $con->beginTransaction();


            $stmt = $con->prepare("INSERT INTO users (first_name, last_name, username, email, password, role, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([$firstname, $lastname, $username, $email, $password, $role, $created_at]);

            //Get the newly inserted user_id
            $userID = $con->lastInsertID();
            $con->commit();

            //returns the new admin's ID so it can be used in other operations
            return $userID;
        }catch(PDOException $e){

            //reverts any chnages made during the transaction. This keeps the database clean and consistent in case of an error
            $con->rollBack();
            error_log("Signup Error: " . $e->getMessage());
            return false;
        }
    }

    function isUsernameExists($username) {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);

        // fetches the result of the sql query. fetchColumn() returns the first column of the first row-in this case, the number of matching records.
        $count = $stmt->fetchColumn();

        //returns true if one or more records were found(i.e., te username already exists)
        return $count > 0;
    }

    function isEmailExists($email){
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);

        // fetches the result of the sql query. fetchColumn() returns the first column of the first row-in this case, the number of matching records.
        $count = $stmt->fetchColumn();

        //returns true if one or more records were found(i.e., the email already exists)
        return $count > 0;
    }

    function loginUser($username, $password){
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT * FROM users WHERE username = ?"); //? stands for placeholder
        $stmt->execute([$username]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])){

            return $user;
        }
        return false;

    }

    function getUserById($user_id){
    $con = $this->opencon();
    $stmt = $con->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateUser($firstname, $lastname, $email, $role, $user_id){
    try {
        $con = $this->opencon();
        $con->beginTransaction();

        $stmt = $con->prepare("UPDATE users SET first_name=?, last_name=?, email=?, role=? WHERE user_id=?");
        $stmt->execute([$firstname, $lastname, $email, $role, $user_id]);

        $con->commit();
        return true;
    } catch(PDOException $e){
        $con->rollBack();
        return false;
    }
}

function deleteUser($user_id){
    try {
        $con = $this->opencon();
        $stmt = $con->prepare("DELETE FROM users WHERE user_id=?");
        return $stmt->execute([$user_id]);
    } catch(PDOException $e){
        return false;
    }
}

public function getAllCategories() {
    $con = $this->opencon();
    $stmt = $con->prepare("SELECT * FROM Category");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function addProduct($name, $category_id, $price, $stock) {
    $con = $this->opencon();
    $stmt = $con->prepare("INSERT INTO Products (product_name, category_id, product_price, product_stock) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$name, $category_id, $price, $stock]);
}

public function updateProduct($product_name, $product_price, $product_stock, $category_id, $products_id) {
    try {
        $con = $this->opencon();
        $con->beginTransaction();

        $stmt = $con->prepare("UPDATE Products SET product_name=?, product_price=?, product_stock=?, category_id=? WHERE products_id=?");
        $stmt->execute([$product_name, $product_price, $product_stock, $category_id, $products_id]);

        $con->commit();
        return true;
    } catch(PDOException $e){
        $con->rollBack();
        return false;
    }
}

public function getAvailableProducts($con) {
        $stmt = $con->prepare("SELECT * FROM products WHERE product_stock > 0");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get product details (price, stock)
    public function getProductById($con, $products_id) {
        $stmt = $con->prepare("SELECT product_price, product_stock FROM products WHERE products_id = ?");
        $stmt->execute([$products_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Insert new order
    public function createOrder($con, $user_id, $order_date, $total_amount, $order_status) {
        $stmt = $con->prepare("INSERT INTO orders (user_id, order_date, total_amount, order_status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $order_date, $total_amount, $order_status]);
        return $con->lastInsertId();
    }

    // Insert into order_items
    public function addOrderItem($con, $order_id, $products_id, $quantity, $price) {
        $stmt = $con->prepare("INSERT INTO order_items (order_id, products_id, order_quantity, order_price) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$order_id, $products_id, $quantity, $price]);
    }

    // Update stock after sale
    public function updateProductStock($con, $products_id, $new_stock) {
        $stmt = $con->prepare("UPDATE products SET product_stock = ? WHERE products_id = ?");
        return $stmt->execute([$new_stock, $products_id]);
    }
}


