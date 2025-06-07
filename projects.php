<?php
session_start();
require_once 'config.php';

requireRole('user');

$user = getUserInfo();
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
                    <?php elseif ($user['role'] == 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="manage_feedback.php">MANAGE FEEDBACK</a></li>
                        <li class="nav-item"><a class="nav-link" href="manage_reports.php">MANAGE REPORTS</a></li>
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
            
            <h2 class="page-title">Upcoming Events & Projects</h2>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="project-card">
                        <img src="pictures/image 1.png" alt="Community Health Program" class="project-image">
                        <div class="project-info">
                            <h3 class="project-title">Community Health & Wellness Program</h3>
                            <div class="project-date">📅 December 15, 2025 | 8:00 AM - 4:00 PM</div>
                            <div class="project-location">📍 Barangay Kanluran Multi-Purpose Hall</div>
                            <p class="project-description">
                                Join us for a comprehensive health and wellness program featuring free medical check-ups, 
                                health screenings, vaccination drives, and wellness seminars. This quarterly event aims to 
                                promote preventive healthcare and improve the overall health of our community members.
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="project-status status-upcoming">Upcoming</span>
                                <small class="text-muted">Free for all residents</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="project-card">
                        <img src="pictures/image 2.png" alt="Infrastructure Project" class="project-image">
                        <div class="project-info">
                            <h3 class="project-title">Road Improvement & Drainage System</h3>
                            <div class="project-date">📅 January 2025 - March 2025 | Ongoing Construction</div>
                            <div class="project-location">📍 Main Street to Rizal Avenue</div>
                            <p class="project-description">
                                Major infrastructure upgrade project focusing on road resurfacing, installation of proper 
                                drainage systems, and street lighting improvements. This project aims to enhance transportation 
                                and reduce flooding issues during the rainy season.
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="project-status status-ongoing">Ongoing</span>
                                <small class="text-muted">₱2.5M Budget</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="project-card">
                        <img src="pictures/image 3.png" alt="Educational Program" class="project-image">
                        <div class="project-info">
                            <h3 class="project-title">Youth Skills Development Workshop</h3>
                            <div class="project-date">📅 December 20-22, 2025 | 9:00 AM - 5:00 PM</div>
                            <div class="project-location">📍 Barangay Learning Center</div>
                            <p class="project-description">
                                A 3-day intensive workshop designed for young adults aged 18-30, featuring digital literacy, 
                                entrepreneurship training, basic computer skills, and career guidance. The program includes 
                                certification and potential job placement assistance.
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="project-status status-upcoming">Upcoming</span>
                                <small class="text-muted">50 slots available</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="project-card">
                        <img src="pictures/image 4.png" alt="Environmental Project" class="project-image">
                        <div class="project-info">
                            <h3 class="project-title">Community Garden & Recycling Initiative</h3>
                            <div class="project-date">📅 Completed November 2025 | Monthly Maintenance</div>
                            <div class="project-location">📍 Barangay Park & Recreation Area</div>
                            <p class="project-description">
                                Successfully established community garden promoting sustainable living and food security. 
                                The project includes composting facilities, recycling centers, and regular environmental 
                                education programs for families and school children.
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="project-status status-completed">Completed</span>
                                <small class="text-muted">50+ families participating</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-12">
                    <div class="project-card">
                        <div class="project-info">
                            <h3 class="project-title text-center">Get Involved!</h3>
                            <p class="project-description text-center">
                                Interested in participating in our projects or events? Contact the Barangay Office for more information 
                                or to volunteer. We welcome community involvement and appreciate your support in making our barangay better.
                            </p>
                            <div class="text-center">
                                <strong>Contact Information:</strong><br>
                                📞 Phone: (02) 123-456789<br>
                                📧 Email: barangaykanluran@gmail.com<br>
                                🕒 Office Hours: Monday - Friday, 8:00 AM - 5:00 PM
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script> 
</body>
</html>