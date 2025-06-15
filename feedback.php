<?php
session_start();
require_once 'config.php';

// Require user login to access feedback
requireRole('user');

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $feedback = $_POST['feedback'];
    
    try {
        // Save feedback to database
        $stmt = $pdo->prepare("INSERT INTO feedback (name, email, feedback, status) VALUES (?, ?, ?, 'pending')");
        $stmt->execute([$name, $email, $feedback]);
        $message = '<div class="alert alert-success">Thank you for your feedback! It has been submitted successfully.</div>';
    } catch(PDOException $e) {
        $message = '<div class="alert alert-danger">Error submitting feedback: ' . $e->getMessage() . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Kanluran - Feedback</title>
    <!-- Bootstrap CSS -->
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
    <!-- Navigation Bar -->
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

    <!-- Main Content -->
    <div class="main-bg py-5">
        <div class="container text-center">
            <div class="title-green">BARANGAY</div>
            <div class="title-black mb-4">KANLURAN</div>
            
            <!-- Feedback Form -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="form-container p-4">
                        <h2 class="form-title mb-3">Share Your Feedback</h2>
                        
                        <?php echo $message; ?>
                        
                        <form method="post" action="feedback.php">
                            <div class="mb-3">
                                <label for="name" class="form-label">Your Name</label>
                                <input type="text" class="form-control" name="name" id="name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email" id="email" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="feedback" class="form-label">Your Feedback</label>
                                <textarea class="form-control" name="feedback" id="feedback" rows="6" required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-custom text-white w-100">Submit Feedback</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>