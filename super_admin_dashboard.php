<?php
session_start();
require_once 'config.php';

// Check if user is logged in and has super admin role
requireRole('super_admin');

$user = getUserInfo();

// Get statistics
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
    $total_users = $stmt->fetch()['total_users'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total_admins FROM users WHERE role IN ('admin', 'super_admin')");
    $total_admins = $stmt->fetch()['total_admins'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total_feedback FROM feedback");
    $total_feedback = $stmt->fetch()['total_feedback'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total_reports FROM reports");
    $total_reports = $stmt->fetch()['total_reports'];
} catch(PDOException $e) {
    $total_users = $total_admins = $total_feedback = $total_reports = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Kanluran - Super Admin Dashboard</title>
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
            background-color: #dc3545; 
            color: white; 
            padding: 3px 8px; 
            border-radius: 12px; 
            font-size: 12px; 
        }
        .stat-card { 
            border-left: 4px solid #dc3545; 
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
            <a class="navbar-brand text-white d-lg-none" href="super_admin_dashboard.php">Barangay Kanluran</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="manage_users.php">MANAGE USERS</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_feedback.php">MANAGE FEEDBACK</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_reports.php">MANAGE REPORTS</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_projects.php">MANAGE PROJECTS</a></li>
                    <li class="nav-item"><a class="nav-link" href="projects.php">PROJECTS</a></li>
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
                        <h3 class="text-success mb-3">Welcome, Super Admin <?php echo htmlspecialchars($user['fullname']); ?>!</h3>
                        <span class="role-badge">SUPER ADMIN</span>
                        <p class="mt-3 mb-0">You have full system access. You can manage users, assign admin roles, and oversee all barangay operations.</p>
                    </div>
                </div>
            </div>
            
            <!-- Statistics -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card-custom p-4 stat-card">
                        <h6 class="text-muted">Total Users</h6>
                        <h3 class="text-success mb-0"><?php echo $total_users; ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-custom p-4 stat-card">
                        <h6 class="text-muted">Admins</h6>
                        <h3 class="text-danger mb-0"><?php echo $total_admins; ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-custom p-4 stat-card">
                        <h6 class="text-muted">Total Feedback</h6>
                        <h3 class="text-success mb-0"><?php echo $total_feedback; ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-custom p-4 stat-card">
                        <h6 class="text-muted">Total Reports</h6>
                        <h3 class="text-success mb-0"><?php echo $total_reports; ?></h3>
                    </div>
                </div>
            </div>
            
            <!-- Super Admin Actions -->
            <div class="row g-4">
                <div class="col-md-6 col-lg-2-4">
                    <div class="card-custom p-4 text-center h-100">
                        <h5 class="text-success mb-3">👥 Manage Users</h5>
                        <p>Add, edit, delete users and assign admin roles.</p>
                        <a href="manage_users.php" class="btn btn-custom text-white">Manage Users</a>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-2-4">
                    <div class="card-custom p-4 text-center h-100">
                        <h5 class="text-success mb-3">📝 Manage Feedback</h5>
                        <p>Review all feedback from residents.</p>
                        <a href="manage_feedback.php" class="btn btn-custom text-white">Manage Feedback</a>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-2-4">
                    <div class="card-custom p-4 text-center h-100">
                        <h5 class="text-success mb-3">📋 Manage Reports</h5>
                        <p>Handle all issue reports and assignments.</p>
                        <a href="manage_reports.php" class="btn btn-custom text-white">Manage Reports</a>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-2-4">
                    <div class="card-custom p-4 text-center h-100">
                        <h5 class="text-success mb-3">🏗️ Manage Projects</h5>
                        <p>Create and manage barangay projects and events.</p>
                        <a href="manage_projects.php" class="btn btn-custom text-white">Manage Projects</a>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-2-4">
                    <div class="card-custom p-4 text-center h-100">
                        <h5 class="text-success mb-3">👀 View Projects</h5>
                        <p>Oversee all barangay projects and events.</p>
                        <a href="projects.php" class="btn btn-custom text-white">View Projects</a>
                    </div>
                </div>
            </div>
            
            <!-- Super Admin Info -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-6">
                    <div class="card-custom p-4">
                        <h5 class="text-success mb-3">Your Super Admin Account</h5>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['fullname']); ?></p>
                        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>Role:</strong> <span class="role-badge">SUPER ADMIN</span></p>
                        <div class="mt-3">
                            <small class="text-muted">
                                <strong>Super Admin Privileges:</strong><br>
                                • Full system access<br>
                                • User management (create/edit/delete)<br>
                                • Role assignment (promote to admin)<br>
                                • All feedback, report, and project management
                            </small>
                        </div>
                        
                        <div class="mt-3">
                            <a href="change_password.php" class="btn btn-outline-danger btn-sm">
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