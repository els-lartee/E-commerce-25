<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Commerce Store</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="card">
            <div class="card-header">
                <h4>Login</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="" id="login-form">
                    <div class="form-group">
                        <label for="email">Email <i class="fa fa-envelope"></i></label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password <i class="fa fa-lock"></i></label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn-custom">Login</button>
                </form>
            </div>
            <div class="card-footer">
                Don't have an account? <a href="register.php" class="highlight">Register here</a>.
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/login.js"></script>
</body>
</html>