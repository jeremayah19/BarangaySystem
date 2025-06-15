<?php
// Login processing can be added here
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Here you can add database code to verify login
    // For now, just a placeholder message
    $message = '<div class="alert alert-info">Login functionality will be implemented with database.</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Kanluran - Login</title>
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
                    <li class="nav-item"><a class="nav-link" href="register.php">REGISTER</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-bg py-5">
        <div class="container text-center">
            <div class="title-green">BARANGAY</div>
            <div class="title-black mb-4">KANLURAN</div>
            
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="form-container p-4">
                        <h2 class="form-title mb-3">Login to your account</h2>
                        
                        <?php echo $message; ?>
                        
                        <form method="post" action="login.php">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" id="username" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" id="password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-custom text-white w-100 mb-3">Login</button>
                            
                            <div class="text-center">
                                <small>Don't have an account? <a href="register.php" class="text-decoration-none" style="color: #2d7c3b;">Register here</a></small>
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