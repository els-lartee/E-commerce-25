<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Commerce Store</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f8f9fa;
            background-image:
                repeating-linear-gradient(0deg,
                    #b77a7a,
                    #b77a7a 1px,
                    transparent 1px,
                    transparent 20px),
                repeating-linear-gradient(90deg,
                    #b77a7a,
                    #b77a7a 1px,
                    transparent 1px,
                    transparent 20px),
                linear-gradient(rgba(183, 122, 122, 0.1),
                    rgba(183, 122, 122, 0.1));
            background-blend-mode: overlay;
            background-size: 20px 20px;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 500px;
            animation: fadeInDown 0.8s;
        }

        .card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: zoomIn 0.8s;
        }

        .card-header {
            background-color: #D19C97;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .card-header h4 {
            margin: 0;
            font-size: 24px;
        }

        .card-body {
            padding: 30px;
        }

        .card-footer {
            background: #f8f9fa;
            padding: 15px;
            text-align: center;
            border-top: 1px solid #ddd;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        label i {
            margin-left: 5px;
            color: #b77a7a;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #D19C97;
        }

        .btn-custom {
            width: 100%;
            background-color: #D19C97;
            border: none;
            color: white;
            padding: 12px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            animation: pulse 2s infinite;
        }

        .btn-custom:hover {
            background-color: #b77a7a;
        }

        .highlight {
            color: #D19C97;
            text-decoration: none;
            transition: color 0.3s;
        }

        .highlight:hover {
            color: #b77a7a;
            text-decoration: underline;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes zoomIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .alert-info {
            padding: 15px;
            background: #d1ecf1;
            color: #0c5460;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
            animation: fadeIn 1s;
        }
    </style>
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