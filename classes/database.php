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

        $role = $_POST['role'];

        if($created_at === null){
            $created_at = date('Y-m-d H:i:s'); // Set current date and time if not provided
        }

        try{
            $con->beginTransaction();

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $con->prepare("INSERT INTO users (first_name, last_name, username, email, password, role, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([$firstname, $lastname, $username, $email, $hashedPassword, $role, $created_at]);

            //Get the newly inserted user_id
            $userID = $con->lastInsertID();
            $con->commit();

            //returns the new admin's ID so it can be used in other operations
            return $userID;
        }catch(PDOException $e){

            //reverts any chnages made during the transaction. This keeps the database clean and consistent in case of an error
            $con->rollBack();
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
        $stmt = $con->prepare("SELECT * FROM user WHERE username = ?"); //? stands for placeholder
        $stmt->execute([$username]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])){

            return $user;
        }


    }
    
    function getStudents(){
        $con = $this->opencon();

        return $con->query("SELECT * FROM students")->fetchAll();
    }

    function getStudentById($student_id){
        $con = $this->opencon();

        $stmt = $con->prepare("SELECT * FROM students WHERE student_id = ?");
        $stmt->execute([$student_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function updateStudent($student_FN, $student_LN, $student_email, $student_id){
        try{

            $con = $this->opencon();
            $con->beginTransaction();

            $stmt = $con->prepare("UPDATE students SET student_FN=?, student_LN=?, student_email=? WHERE student_id=?");
            $stmt->execute([$student_FN, $student_LN, $student_email, $student_id]);

            $con->commit();
            return true;
        }catch(PDOException $e){
            $con->rollBack();
            return false;

        }
        
    }

}