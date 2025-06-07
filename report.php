<?php
session_start();
require_once 'config.php';

// Require user login to access reports
requireRole('user');

$message = '';

// Simple report submission processing
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $location = $_POST['location'];
    $issue_type = $_POST['issue_type'];
    $description = $_POST['description'];
    
    $image_name = "no-image.jpg"; // Default value
    
    // Handle image upload
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (in_array($_FILES['image']['type'], $allowed_types) && $_FILES['image']['size'] <= $max_size) {
            // Create uploads directory if it doesn't exist
            $upload_dir = 'uploads/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Generate unique filename
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = "report_" . time() . "_" . uniqid() . "." . $file_extension;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name)) {
                // File uploaded successfully
            } else {
                $image_name = "no-image.jpg"; // Reset if upload failed
            }
        }
    }
    
    try {
        // Save report to database
        $stmt = $pdo->prepare("INSERT INTO reports (name, contact, location, issue_type, description, image_path, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$name, $contact, $location, $issue_type, $description, $image_name]);
        $message = '<div class="alert alert-success">Thank you for your report! Your report has been recorded and will be addressed soon.</div>';
    } catch(PDOException $e) {
        $message = '<div class="alert alert-danger">Error submitting report: ' . $e->getMessage() . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Kanluran - Report an Issue</title>
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
        <div class="container text-center">
            <div class="title-green">BARANGAY</div>
            <div class="title-black mb-4">KANLURAN</div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="form-container p-4">
                        <h2 class="form-title mb-2">Report an Issue</h2>
                        <p class="text-muted mb-4">Help us improve our barangay by reporting problems such as street holes, broken street lights, or garbage issues.</p>
                        
                        <?php echo $message; ?>
                        
                        <form method="post" action="report.php" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Your Name</label>
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Juan Dela Cruz" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact" class="form-label">Contact Number</label>
                                        <input type="text" class="form-control" name="contact" id="contact" placeholder="09XX XXX XXXX" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="location" class="form-label">Location of Issue</label>
                                <input type="text" class="form-control" name="location" id="location" placeholder="e.g. Rizal Street corner Bonifacio Avenue" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="issue_type" class="form-label">Type of Issue</label>
                                <select class="form-select" name="issue_type" id="issue_type" required>
                                    <option value="">-- Select Issue Type --</option>
                                    <option value="street_hole">Street Hole or Damage</option>
                                    <option value="street_light">Street Light Not Working</option>
                                    <option value="garbage">Garbage Collection Problem</option>
                                    <option value="drainage">Clogged Drainage</option>
                                    <option value="noise">Noise Complaint</option>
                                    <option value="other">Other Issue</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description of the Issue</label>
                                <textarea class="form-control" name="description" id="description" rows="5" placeholder="Please provide details about the problem." required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="image" class="form-label">Upload a Photo (optional)</label>
                                <input type="file" class="form-control" name="image" id="image" accept="image/*">
                                <div class="form-text">Supported formats: JPG, PNG, GIF. Maximum file size: 5MB.</div>
                            </div>
                            
                            <button type="submit" class="btn btn-custom text-white w-100">Submit Report</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>