<?php
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Basic validation
    if ($password != $confirm_password) {
        $message = '<div class="alert alert-danger">Passwords do not match!</div>';
    } else {
        // Here you can add database code to save the user
        $message = '<div class="alert alert-success">Registration successful! You can now <a href="login.php">login</a>.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Kanluran - Register</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
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
        .navbar-nav .nav-link { 
            color: white !important; 
            font-size: 14px; 
        }
        .navbar-nav .nav-link:hover { 
            color: #f8f9fa !important; 
        }
        @media (max-width: 768px) { 
            .title-black { font-size: 3rem; } 
            .title-green { font-size: 1.5rem; } 
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand text-white d-lg-none" href="index.php">Barangay Kanluran</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="login.php">LOG IN</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-bg py-5">
        <div class="container text-center">
            <div class="title-green">BARANGAY</div>
            <div class="title-black mb-4">KANLURAN</div>
            
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="form-container p-4">
                        <h2 class="form-title mb-3">Create an Account</h2>
                        
                        <?php echo $message; ?>
                        
                        <form method="post" action="register.php">
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
                            
                            <button type="submit" class="btn btn-custom text-white w-100 mb-3">Register</button>
                            
                            <div class="text-center">
                                <small>Already have an account? <a href="login.php" class="text-decoration-none" style="color: #2d7c3b;">Login here</a></small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>