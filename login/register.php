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
    <title>Register - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/register.js"></script>
    <style>
        /* your same CSS styles here */
    </style>
</head>

<body>
    <div class="container register-container">
        <div class="row justify-content-center animate__animated animate__fadeInDown">
            <div class="col-md-6">
                <div class="card animate__animated animate__zoomIn">
                    <div class="card-header text-center highlight">
                        <h4>Register</h4>
                    </div>
                    <div class="card-body">
                        <!-- ✅ Post form directly to same PHP file -->
                        <form method="POST" class="mt-4" id="register-form">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name <i class="fa fa-user"></i></label>
                                <input type="text" class="form-control" id="name" name="name" required maxlength="100">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <i class="fa fa-envelope"></i></label>
                                <input type="email" class="form-control" id="email" name="email" required maxlength="50">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password <i class="fa fa-lock"></i></label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="country" class="form-label">Country <i class="fa fa-flag"></i></label>
                                <input type="text" class="form-control" id="country" name="country" required maxlength="30">
                            </div>
                            <div class="mb-3">
                                <label for="city" class="form-label">City <i class="fa fa-city"></i></label>
                                <input type="text" class="form-control" id="city" name="city" required maxlength="30">
                            </div>
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Phone Number <i class="fa fa-phone"></i></label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" required maxlength="15">
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Register As</label>
                                <div class="d-flex justify-content-start">
                                    <div class="form-check me-3 custom-radio">
                                        <input class="form-check-input" type="radio" name="role" id="customer" value="1" checked>
                                        <label class="form-check-label" for="customer">Customer</label>
                                    </div>
                                    <div class="form-check custom-radio">
                                        <input class="form-check-input" type="radio" name="role" id="owner" value="2">
                                        <label class="form-check-label" for="owner">Restaurant Owner</label>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-custom w-100 animate-pulse-custom">Register</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        Already have an account? <a href="login.php" class="highlight">Login here</a>.
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
