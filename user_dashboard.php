<?php
session_start();
require_once 'config.php';

// Check if user is logged in and has user role
requireRole('user');

$user = getUserInfo();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Kanluran - User Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-custom { 
            background: linear-gradient(to right, #7de6ae, #197c4d) !important; 
        }
        .main-bg { 
            background-color: #f0f0f0; 
            background-image: urSSl('images/pattern-bg.png'); 
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
        .card-custom { 
            background-color: white; 
            border-radius: 10px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
        }
        .navbar-nav .nav-link { 
            color: white !important; 
            font-size: 14px; 
        }
        .navbar-nav .nav-link:hover { 
            color: #f8f9fa !important; 
        }
        .role-badge { 
            background-color: #28a745; 
            color: white; 
            padding: 3px 8px; 
            border-radius: 12px; 
            font-size: 12px; 
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
            <a class="navbar-brand text-white d-lg-none" href="user_dashboard.php">Barangay Kanluran</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="feedback.php">FEEDBACKS</a></li>
                    <li class="nav-item"><a class="nav-link" href="projects.php">PROJECTS</a></li>
                    <li class="nav-item"><a class="nav-link" href="report.php">REPORT</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">LOGOUT</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-bg py-5">
        <div class="container">
            <div class="text-center mb-4">
                <div class="title-green">BARANGAY</div>
                <div class="title-black mb-3">KANLURAN</div>
            </div>
            
            <!-- Welcome Message -->
            <div class="row justify-content-center mb-4">
                <div class="col-lg-8">
                    <div class="card-custom p-4 text-center">
                        <h3 class="text-success mb-3">Welcome, <?php echo htmlspecialchars($user['fullname']); ?>!</h3>
                        <span class="role-badge">USER</span>
                        <p class="mt-3 mb-0">You are logged in as a regular user. You can submit feedback and reports to help improve our barangay.</p>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card-custom p-4 text-center h-100">
                        <h5 class="text-success mb-3">📝 Submit Feedback</h5>
                        <p>Share your thoughts and suggestions about barangay services.</p>
                        <a href="feedback.php" class="btn btn-custom text-white">Go to Feedback</a>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card-custom p-4 text-center h-100">
                        <h5 class="text-success mb-3">🏗️ View Projects</h5>
                        <p>Check out upcoming events and ongoing barangay projects.</p>
                        <a href="projects.php" class="btn btn-custom text-white">View Projects</a>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card-custom p-4 text-center h-100">
                        <h5 class="text-success mb-3">📋 Report Issues</h5>
                        <p>Report problems like street damage, broken lights, or other concerns.</p>
                        <a href="report.php" class="btn btn-custom text-white">Report Issue</a>
                    </div>
                </div>
            </div>
            
            <!-- User Info -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-6">
                    <div class="card-custom p-4">
                        <h5 class="text-success mb-3">Your Account Information</h5>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['fullname']); ?></p>
                        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>Role:</strong> <span class="role-badge">USER</span></p>
                        
                        <div class="mt-3">
                            <a href="change_password.php" class="btn btn-outline-success btn-sm">
                                🔒 Change Password
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>