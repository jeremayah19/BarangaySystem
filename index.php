<?php
session_start();
require_once 'config.php';

$login_message = '';
$register_message = '';

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type'])) {
    if ($_POST['form_type'] == 'login') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                // Redirect based on role
                if ($user['role'] == 'super_admin') {
                    header("Location: super_admin_dashboard.php");
                } elseif ($user['role'] == 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: user_dashboard.php");
                }
                exit();
            } else {
                $login_message = '<div class="alert alert-danger">Invalid username or password!</div>';
            }
        } catch(PDOException $e) {
            $login_message = '<div class="alert alert-danger">Login error: ' . $e->getMessage() . '</div>';
        }
    }
    
    // Handle registration
    if ($_POST['form_type'] == 'register') {
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($password != $confirm_password) {
            $register_message = '<div class="alert alert-danger">Passwords do not match!</div>';
        } else {
            try {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (fullname, email, username, password, role) VALUES (?, ?, ?, ?, 'user')");
                $stmt->execute([$fullname, $email, $username, $hashed_password]);
                $register_message = '<div class="alert alert-success">Registration successful! You can now login.</div>';
            } catch(PDOException $e) {
                if ($e->getCode() == 23000) {
                    $register_message = '<div class="alert alert-danger">Username or email already exists!</div>';
                } else {
                    $register_message = '<div class="alert alert-danger">Registration error: ' . $e->getMessage() . '</div>';
                }
            }
        }
    }
}

// If user is already logged in, redirect to appropriate dashboard
if (isLoggedIn()) {
    $role = getUserRole();
    if ($role == 'super_admin') {
        header("Location: super_admin_dashboard.php");
    } elseif ($role == 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Kanluran</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom styles for Barangay Kanluran theme */
        .navbar-custom {
            background: linear-gradient(to right, #7de6ae, #197c4d) !important;
        }
        
        .main-bg {
            background-color: #f0f0f0;
            background-image: url('images/pattern-bg.png');
            min-height: 80vh;
        }
        
        .title-green {
            color: #2d7c3b;
            font-size: 2rem;
            font-weight: bold;
        }
        
        .title-black {
            color: #333;
            font-size: 4.5rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .btn-custom {
            background-color: #2d7c3b;
            border-color: #2d7c3b;
        }
        
        .btn-custom:hover {
            background-color: #1e5529;
            border-color: #1e5529;
        }
        
        .form-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .form-title {
            color: #2d7c3b;
            font-size: 1.5rem;
        }
        
        .logo-img {
            max-width: 100%;
            height: auto;
        }
        
        .navbar-nav .nav-link {
            color: white !important;
            font-size: 14px;
        }
        
        .navbar-nav .nav-link:hover {
            color: #f8f9fa !important;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .title-black {
                font-size: 3rem;
            }
            .title-green {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <!-- Brand -->
            <a class="navbar-brand text-white d-lg-none" href="index.php">Barangay Kanluran</a>
            
            <!-- Mobile menu toggle button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigation items - empty for login page -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <!-- No navigation items for login page -->
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-bg py-5">
        <div class="container text-center">
            <!-- Barangay Title -->
            <div class="title-green">BARANGAY</div>
            <div class="title-black mb-4">KANLURAN</div>
            
            <!-- Logo -->
            <img src="pictures/logo.png" alt="Barangay Kanluran Logo" class="logo-img mb-5" style="max-width: 500px;">
            
            <!-- Login and Register Forms -->
            <div class="row justify-content-center g-4">
                <!-- Login Form -->
                <div class="col-md-6 col-lg-5">
                    <div class="form-container p-4">
                        <h2 class="form-title mb-3">Login</h2>
                        
                        <?php if ($login_message) echo $login_message; ?>
                        
                        <form method="post" action="index.php">
                            <input type="hidden" name="form_type" value="login">
                            <div class="mb-3">
                                <label for="login_username" class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" id="login_username" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="login_password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" id="login_password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-custom text-white w-100">Login</button>
                        </form>
                        

                    </div>
                </div>
                
                <!-- Register Form -->
                <div class="col-md-6 col-lg-5">
                    <div class="form-container p-4">
                        <h2 class="form-title mb-3">Register</h2>
                        
                        <?php if ($register_message) echo $register_message; ?>
                        
                        <form method="post" action="index.php">
                            <input type="hidden" name="form_type" value="register">
                            <div class="mb-3">
                                <label for="fullname" class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="fullname" id="fullname" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email" id="email" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" id="username" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" id="password" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-custom text-white w-100">Register</button>
                        </form>
                        
                        <div class="mt-3 text-center">
                            <small class="text-muted">New users will be registered as regular users</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>