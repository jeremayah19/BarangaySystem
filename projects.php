<?php
session_start();
require_once 'config.php';

requireRole('user');

$user = getUserInfo();

// Get all projects from database
try {
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY 
        CASE status 
            WHEN 'ongoing' THEN 1 
            WHEN 'upcoming' THEN 2 
            WHEN 'completed' THEN 3 
        END, start_date DESC");
    $projects = $stmt->fetchAll();
} catch(PDOException $e) {
    $projects = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Kanluran - Projects</title>
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
        .page-title { 
            color: #2d7c3b; 
            font-size: 2rem; 
            margin-bottom: 2rem; 
        }
        .project-card { 
            background-color: white; 
            border-radius: 10px; 
            overflow: hidden; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
            transition: transform 0.3s ease;
            height: 100%;
        }
        .project-card:hover {
            transform: translateY(-5px);
        }
        .project-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        .project-info {
            padding: 1.5rem;
        }
        .project-title {
            color: #2d7c3b;
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 0.75rem;
        }
        .project-date {
            color: #dc3545;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .project-location {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .project-description {
            color: #495057;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        .project-status {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-upcoming {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-ongoing {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .navbar-nav .nav-link { 
            color: white !important; 
            font-size: 14px; 
        }
        .navbar-nav .nav-link:hover { 
            color: #f8f9fa !important; 
        }
        .contact-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-top: 2rem;
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
            <a class="navbar-brand text-white d-lg-none" href="<?php 
                echo ($user['role'] == 'super_admin') ? 'super_admin_dashboard.php' : 
                     (($user['role'] == 'admin') ? 'admin_dashboard.php' : 'user_dashboard.php'); 
            ?>">Barangay Kanluran</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <?php if ($user['role'] == 'super_admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="manage_users.php">MANAGE USERS</a></li>
                        <li class="nav-item"><a class="nav-link" href="manage_feedback.php">MANAGE FEEDBACK</a></li>
                        <li class="nav-item"><a class="nav-link" href="manage_reports.php">MANAGE REPORTS</a></li>
                        <li class="nav-item"><a class="nav-link" href="manage_projects.php">MANAGE PROJECTS</a></li>
                    <?php elseif ($user['role'] == 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="manage_feedback.php">MANAGE FEEDBACK</a></li>
                        <li class="nav-item"><a class="nav-link" href="manage_reports.php">MANAGE REPORTS</a></li>
                        <li class="nav-item"><a class="nav-link" href="manage_projects.php">MANAGE PROJECTS</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="feedback.php">FEEDBACKS</a></li>
                        <li class="nav-item"><a class="nav-link" href="report.php">REPORT</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="projects.php">PROJECTS</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">LOGOUT</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-bg py-5">
        <div class="container text-center">
            <div class="title-green">BARANGAY</div>
            <div class="title-black mb-4">KANLURAN</div>
            
            <h2 class="page-title">Projects & Events</h2>
            
            <?php if (empty($projects)): ?>
                <div class="project-card mx-auto" style="max-width: 500px;">
                    <div class="project-info">
                        <h4 class="project-title">No Projects Available</h4>
                        <p class="project-description">
                            There are currently no projects or events to display. 
                            Please check back later for updates.
                        </p>
                        <?php if ($user['role'] == 'admin' || $user['role'] == 'super_admin'): ?>
                            <a href="manage_projects.php" class="btn btn-custom text-white">
                                Add New Project
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($projects as $project): ?>
                        <div class="col-lg-6">
                            <div class="project-card">
                                <img src="uploads/projects/<?php echo htmlspecialchars($project['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($project['title']); ?>" 
                                     class="project-image"
                                     onerror="this.src='pictures/default-project.jpg'">
                                <div class="project-info">
                                    <h3 class="project-title"><?php echo htmlspecialchars($project['title']); ?></h3>
                                    <div class="project-date">
                                        📅 <?php echo date('M j, Y', strtotime($project['start_date'])); ?> - <?php echo date('M j, Y', strtotime($project['end_date'])); ?>
                                    </div>
                                    <div class="project-location">
                                        📍 <?php echo htmlspecialchars($project['location']); ?>
                                    </div>
                                    <p class="project-description">
                                        <?php echo htmlspecialchars($project['description']); ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="project-status status-<?php echo $project['status']; ?>">
                                            <?php echo ucfirst($project['status']); ?>
                                        </span>
                                        <small class="text-muted">
                                            <?php if ($project['budget']): ?>
                                                ₱<?php echo number_format($project['budget']); ?> Budget
                                            <?php else: ?>
                                                Community Project
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                    
                                    <?php if ($project['contact_info']): ?>
                                        <div class="mt-3 pt-3 border-top">
                                            <small class="text-muted">
                                                <strong>Contact Info:</strong><br>
                                                <?php echo nl2br(htmlspecialchars($project['contact_info'])); ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Contact Information -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="contact-card">
                        <h3 class="project-title text-center">Get Involved!</h3>
                        <p class="project-description text-center">
                            Interested in participating in our projects or events? Contact the Barangay Office for more information.
                        </p>
                        <div class="text-center">
                            <strong>Barangay Kanluran Office:</strong><br>
                            📞 Phone: (02) 123-456789<br>
                            📧 Email: barangaykanluran@gmail.com<br>
                            🕒 Office Hours: Monday - Friday, 8:00 AM - 5:00 PM
                        </div>
                        
                        <?php if ($user['role'] == 'admin' || $user['role'] == 'super_admin'): ?>
                            <div class="text-center mt-4">
                                <a href="manage_projects.php" class="btn btn-custom text-white">
                                    <p style="color: black;">🛠️ Manage Reports</p>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script> 
</body>
</html>