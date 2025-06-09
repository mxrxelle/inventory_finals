<?php

require_once('classes/database.php');
$con = new database();

$sweetAlertConfig = "";
if (isset($_POST['register'])){
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password_raw = $_POST['password'];
  $firstname = $_POST['first_name'];
  $lastname = $_POST['last_name'];
  $role = isset($_POST['role']) ? $_POST['role'] : '';

  $valid_roles = ['admin', 'inventory_staff'];
  if (!in_array($role, $valid_roles)) {
    $sweetAlertConfig = "
      <script>
        Swal.fire({
          icon: 'error',
          title: 'Invalid Role',
          text: 'Please select a valid user role.'
        });
      </script>";
  } else {
    // Proceed with password validation and user creation
    $passwordValid = preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{6,}$/', $password_raw);

    if (!$passwordValid) {
      $sweetAlertConfig = "
        <script>
          Swal.fire({
            icon: 'error',
            title: 'Invalid Password',
            text: 'Password must be at least 6 characters long, include an uppercase letter, a number, and a special character.'
          });
        </script>";
    } else {
      $password = password_hash($password_raw, PASSWORD_BCRYPT);
  }

  $userID = $con->signupUser($firstname, $lastname, $username, $email, $password, $role, $created_at);

  if ($userID){
    $sweetAlertConfig = "
    <script> 
    Swal.fire({
      icon: 'success',
      title: 'Registration Successful',
      text: 'You have successfully registered as a an admin.',
      confirmButtonText: 'OK'
    }).then(() => {
      window.location.href = 'login.php'
    });
    </script>";
    
  }else{
    $sweetAlertConfig = "
    <script>
    Swal.fire({
      icon: 'error',
      title: 'Registration Failed',
      text: 'An error occured during registration. Please try again.',
      confirmButtonText: 'OK'
    });
    </script>";
  }
}
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Registration</title>
  <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css" />
  <link rel="stylesheet" href="./package/dist/sweetalert2.css" />
</head>
<body class="bg-light">
  <div class="container py-5">
    <h2 class="mb-4 text-center">Admin Registration</h2>
    <form id="registrationForm" method="POST" action="" class="bg-white p-4 rounded shadow-sm" autocomplete="off" novalidate>
      <div class="mb-3">
        <label for="first_name" class="form-label">First Name</label>
        <input type="text" name="first_name" id="first_name" class="form-control" placeholder="Enter your first name" required />
        <div class="invalid-feedback">First name is required.</div>
      </div>
      <div class="mb-3">
        <label for="last_name" class="form-label">Last Name</label>
        <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Enter your last name" required />
        <div class="invalid-feedback">Last name is required.</div>
      </div>
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username" required />
        <div class="invalid-feedback">Username is required.</div>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control" placeholder="Enter your Email" required />
        <div class="invalid-feedback">Email is required.</div>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required />
        <div class="invalid-feedback">
          Password must be at least 6 characters long, include an uppercase letter, a number, and a special character.
        </div>
      </div>
      <div class="mb-3">
        <label for="role" class="form-label">Select Role</label>
        <select name="role" id="role" class="form-select" required>
          <option value="" disabled selected>Choose a role</option>
          <option value="admin">Admin</option>
          <option value="inventory_staff">Inventory Staff</option>
        </select>
      <div class="invalid-feedback">Please select a role.</div>
    </div>


      <button id="registerButton" type="submit" name="register" class="btn btn-primary w-100" disabled>Register</button>
    </form>
  </div>
  
  <script src="./bootstrap-5.3.3-dist/js/bootstrap.js"></script>
  <script src="./package/dist/sweetalert2.js"></script>
  <?php echo $sweetAlertConfig?>

  <script>

  // Function to validate individual fields
  function validateField(field, validationFn) {
    field.addEventListener('input', () => {
      if (validationFn(field.value)) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
      } else {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
      }
    });
  }

  // Validation functions for each field
  const isNotEmpty = (value) => (value).trim() !== '';
  const isPasswordValid = (value) => {
    const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/;
    return passwordRegex.test(value);
  };

  // Real-time username validation using AJAX
  const checkUsernameAvailability = (usernameField) => {
    usernameField.addEventListener('input', () => {
      const username = usernameField.value.trim();

      if (username === '') {
        usernameField.classList.remove('is-valid');
        usernameField.classList.add('is-invalid');
        usernameField.nextElementSibling.textContent = 'Username is required.';
        registerButton.disabled = true; //disabled the button
        return;
      }

      // Send AJAX request to check username availability
      fetch('ajax/check_username.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `username=${encodeURIComponent(username)}`,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.exists) {
            usernameField.classList.remove('is-valid');
            usernameField.classList.add('is-invalid');
            usernameField.nextElementSibling.textContent = 'Username is already taken.';
            registerButton.disabled = true; //disabled the button
          } else {
            usernameField.classList.remove('is-invalid');
            usernameField.classList.add('is-valid');
            usernameField.nextElementSibling.textContent = '';
            registerButton.disabled = false; //disabled the button
          }
        })
        .catch((error) => {
          console.error('Error:', error);
          registerButton.disabled = true; //disabled the button
        });
    });
  };

  const checkEmailAvailability = (emailField) => {
    emailField.addEventListener('input', () => {
      const email = emailField.value.trim();

      if (email === '') {
        emailField.classList.remove('is-valid');
        emailField.classList.add('is-invalid');
        emailField.nextElementSibling.textContent = 'Email is required.';
        registerButton.disabled = true; //disabled the button
        return;
      }

      // Send AJAX request to check email availability
      fetch('ajax/check_email.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `email=${encodeURIComponent(email)}`,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.exists) {
            emailField.classList.remove('is-valid');
            emailField.classList.add('is-invalid');
            emailField.nextElementSibling.textContent = 'Email is already taken.';
            registerButton.disabled = true; //disabled the button
          } else {
            emailField.classList.remove('is-invalid');
            emailField.classList.add('is-valid');
            emailField.nextElementSibling.textContent = '';
            registerButton.disabled = false; //disabled the button
          }
        })
        .catch((error) => {
          console.error('Error:', error);
          registerButton.disabled = true; //disabled the button
        });
    });
  };

  // Get form fields
  const firstName = document.getElementById('first_name');
  const lastName = document.getElementById('last_name');
  const username = document.getElementById('username');
  const email = document.getElementById('email');
  const password = document.getElementById('password');

  // Attach real-time validation to each field
  validateField(firstName, isNotEmpty);
  validateField(lastName, isNotEmpty);
  checkUsernameAvailability(username);
  checkEmailAvailability(email);
  validateField(password, isPasswordValid);
  

  // Form submission validation
  document.getElementById('registrationForm').addEventListener('submit', function (e) {
    //e.preventDefault(); // Prevent form submission for validation

    let isValid = true;

    // Validate all fields on submit
    [firstName, lastName, username, email, password].forEach((field) => {
      if (!field.classList.contains('is-valid')) {
        field.classList.add('is-invalid');
        isValid = false;
      }
    });

    // If all fields are valid, submit the form
    if (isValid) {
      this.submit();
    }
  });
</script>


</body>
</html>