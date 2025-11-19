<?php
// --- Backend PHP logic at the very top ---
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     // Connect to DB
//     $conn = new mysqli("localhost", "root", "", "shoppn");

//     if ($conn->connect_error) {
//         die("DB connection failed: " . $conn->connect_error);
//     }

//     // Collect form data safely
//     $name     = $conn->real_escape_string($_POST['name']);
//     $email    = $conn->real_escape_string($_POST['email']);
//     $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
//     $country  = $conn->real_escape_string($_POST['country']);
//     $city     = $conn->real_escape_string($_POST['city']);
//     $phone    = $conn->real_escape_string($_POST['phone_number']);
//     $role     = intval($_POST['role']); // 1 = customer, 2 = restaurant owner

//     // Check if email exists
//     $check = $conn->query("SELECT * FROM customer WHERE customer_email='$email'");
//     if ($check->num_rows > 0) {
//         echo "<script>alert('Email already exists! Please login instead.');</script>";
//     } else {
//         // Insert new user
//         $sql = "INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) 
//                 VALUES ('$name', '$email', '$password', '$country', '$city', '$phone', '$role')";
//         if ($conn->query($sql)) {
//             echo "<script>alert('Registration successful! Redirecting to login...'); window.location='login.php';</script>";
//         } else {
//             echo "<script>alert('Error: Could not register.');</script>";
//         }
//     }

//     $conn->close();
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - E-Commerce Store</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="register-container">
        <div class="card">
            <div class="card-header">
                <h4>Register</h4>
            </div>
            <div class="card-body">
                <form method="POST" id="register-form">
                    <div class="form-group">
                        <label for="name">Name <i class="fa fa-user"></i></label>
                        <input type="text" id="name" name="name" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="email">Email <i class="fa fa-envelope"></i></label>
                        <input type="email" id="email" name="email" required maxlength="50">
                    </div>
                    <div class="form-group">
                        <label for="password">Password <i class="fa fa-lock"></i></label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="country">Country <i class="fa fa-flag"></i></label>
                        <input type="text" id="country" name="country" required maxlength="30">
                    </div>
                    <div class="form-group">
                        <label for="city">City <i class="fa fa-city"></i></label>
                        <input type="text" id="city" name="city" required maxlength="30">
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Phone Number <i class="fa fa-phone"></i></label>
                        <input type="text" id="phone_number" name="phone_number" required maxlength="15">
                    </div>
                    <div class="form-group">
                        <label>Register As</label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" name="role" id="customer" value="1" checked>
                                <label for="customer">Customer</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" name="role" id="owner" value="2">
                                <label for="owner">Restaurant Owner</label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn-custom">Register</button>
                </form>
            </div>
            <div class="card-footer">
                Already have an account? <a href="login.php" class="highlight">Login here</a>.
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/register.js"></script>
</body>
</html>
