<?php
session_start();
require_once 'config.php';

// Check if user is logged in and has admin role or higher
requireRole('admin');

$user = getUserInfo();
$message = '';

// Handle project creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_project'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];
    $budget = $_POST['budget'];
    $contact_info = $_POST['contact_info'];
    
    $image_name = "default-project.jpg"; // Default image
    
    // Handle image upload
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (in_array($_FILES['image']['type'], $allowed_types) && $_FILES['image']['size'] <= $max_size) {
            $upload_dir = 'uploads/projects/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = "project_" . time() . "_" . uniqid() . "." . $file_extension;
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name)) {
                $image_name = "default-project.jpg";
            }
        }
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO projects (title, description, location, start_date, end_date, status, budget, contact_info, image_path, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $location, $start_date, $end_date, $status, $budget, $contact_info, $image_name, $user['id']]);
        $message = '<div class="alert alert-success">Project created successfully!</div>';
    } catch(PDOException $e) {
        $message = '<div class="alert alert-danger">Error creating project: ' . $e->getMessage() . '</div>';
    }
}

// Handle project updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_project'])) {
    $project_id = $_POST['project_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];
    $budget = $_POST['budget'];
    $contact_info = $_POST['contact_info'];
    
    try {
        $stmt = $pdo->prepare("UPDATE projects SET title = ?, description = ?, location = ?, start_date = ?, end_date = ?, status = ?, budget = ?, contact_info = ? WHERE id = ?");
        $stmt->execute([$title, $description, $location, $start_date, $end_date, $status, $budget, $contact_info, $project_id]);
        $message = '<div class="alert alert-success">Project updated successfully!</div>';
    } catch(PDOException $e) {
        $message = '<div class="alert alert-danger">Error updating project: ' . $e->getMessage() . '</div>';
    }
}

// Handle project deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_project'])) {
    $project_id = $_POST['project_id'];
    
    try {
        // Get image path to delete file
        $stmt = $pdo->prepare("SELECT image_path FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $project = $stmt->fetch();
        
        // Delete project from database
        $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        
        // Delete image file if it exists and is not default
        if ($project && $project['image_path'] != 'default-project.jpg') {
            $image_file = 'uploads/projects/' . $project['image_path'];
            if (file_exists($image_file)) {
                unlink($image_file);
            }
        }
        
        $message = '<div class="alert alert-success">Project deleted successfully!</div>';
    } catch(PDOException $e) {
        $message = '<div class="alert alert-danger">Error deleting project: ' . $e->getMessage() . '</div>';
    }
}

// Get all projects
try {
    $stmt = $pdo->query("SELECT p.*, u.fullname as created_by_name FROM projects p LEFT JOIN users u ON p.created_by = u.id ORDER BY p.created_at DESC");
    $projects = $stmt->fetchAll();
} catch(PDOException $e) {
    $projects = [];
    $message = '<div class="alert alert-danger">Error loading projects: ' . $e->getMessage() . '</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Kanluran - Manage Projects</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-custom { background: linear-gradient(to right, #7de6ae, #197c4d) !important; }
        .main-bg { background-color: #f0f0f0; background-image: url('images/pattern-bg.png'); min-height: 80vh; }
        .title-green { color: #2d7c3b; font-size: 2rem; font-weight: bold; }
        .title-black { color: #333; font-size: 4.5rem; font-weight: bold; text-transform: uppercase; }
        .btn-custom { background-color: #2d7c3b; border-color: #2d7c3b; }
        .btn-custom:hover { background-color: #1e5529; border-color: #1e5529; }
        .card-custom { background-color: white; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .navbar-nav .nav-link { color: white !important; font-size: 14px; }
        .navbar-nav .nav-link:hover { color: #f8f9fa !important; }
        .project-image { width: 80px; height: 60px; object-fit: cover; border-radius: 5px; }
        @media (max-width: 768px) { .title-black { font-size: 3rem; } .title-green { font-size: 1.5rem; } }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand text-white d-lg-none" href="<?php echo ($user['role'] == 'super_admin') ? 'super_admin_dashboard.php' : 'admin_dashboard.php'; ?>">Barangay Kanluran</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <?php if ($user['role'] == 'super_admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="manage_users.php">MANAGE USERS</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="manage_feedback.php">MANAGE FEEDBACK</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_reports.php">MANAGE REPORTS</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_projects.php">MANAGE PROJECTS</a></li>
                    <li class="nav-item"><a class="nav-link" href="projects.php">VIEW PROJECTS</a></li>
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
                <h2 class="text-success">Manage Projects</h2>
            </div>

            <?php echo $message; ?>

            <!-- Create New Project Button -->
            <div class="mb-4 text-center">
                <button type="button" class="btn btn-custom text-white" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                    ➕ Create New Project
                </button>
            </div>

            <!-- Projects List -->
            <div class="card-custom p-4">
                <h4 class="text-success mb-3">All Projects</h4>
                
                <?php if (empty($projects)): ?>
                    <div class="text-center py-5">
                        <h5 class="text-muted">No projects found</h5>
                        <p class="text-muted">Click "Create New Project" to add your first project.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-success">
                                <tr>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Location</th>
                                    <th>Dates</th>
                                    <th>Status</th>
                                    <th>Budget</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($projects as $project): ?>
                                    <tr>
                                        <td>
                                            <img src="uploads/projects/<?php echo htmlspecialchars($project['image_path']); ?>" 
                                                 alt="Project Image" class="project-image"
                                                 onerror="this.src='pictures/default-project.jpg'">
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($project['title']); ?></strong>
                                            <br><small class="text-muted"><?php echo htmlspecialchars(substr($project['description'], 0, 60)); ?>...</small>
                                        </td>
                                        <td><?php echo htmlspecialchars($project['location']); ?></td>
                                        <td>
                                            <small>
                                                <strong>Start:</strong> <?php echo date('M j, Y', strtotime($project['start_date'])); ?><br>
                                                <strong>End:</strong> <?php echo date('M j, Y', strtotime($project['end_date'])); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $project['status'] == 'upcoming' ? 'warning' : 
                                                    ($project['status'] == 'ongoing' ? 'info' : 'success'); 
                                            ?>">
                                                <?php echo ucfirst($project['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo $project['budget'] ? '₱' . number_format($project['budget']) : 'N/A'; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($project['created_by_name']); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary mb-1" 
                                                    onclick="editProject(<?php echo htmlspecialchars(json_encode($project)); ?>)">
                                                Edit
                                            </button>
                                            <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this project?')">
                                                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                                <button type="submit" name="delete_project" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <div class="text-center mt-4">
                    <a href="<?php echo ($user['role'] == 'super_admin') ? 'super_admin_dashboard.php' : 'admin_dashboard.php'; ?>" class="btn btn-custom text-white me-2">
                        Back to Dashboard
                    </a>
                    <a href="projects.php" class="btn btn-outline-success">
                        View Public Projects Page
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Project Modal -->
    <div class="modal fade" id="createProjectModal" tabindex="-1" aria-labelledby="createProjectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createProjectModalLabel">Create New Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Project Title</label>
                                    <input type="text" class="form-control" name="title" id="title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control" name="location" id="location" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="description" rows="3" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" name="start_date" id="start_date" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" class="form-control" name="end_date" id="end_date" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" name="status" id="status" required>
                                        <option value="upcoming">Upcoming</option>
                                        <option value="ongoing">Ongoing</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="budget" class="form-label">Budget (PHP)</label>
                                    <input type="number" class="form-control" name="budget" id="budget" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="image" class="form-label">Project Image</label>
                                    <input type="file" class="form-control" name="image" id="image" accept="image/*">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="contact_info" class="form-label">Contact Information</label>
                            <textarea class="form-control" name="contact_info" id="contact_info" rows="2" placeholder="Phone numbers, email, office hours, etc."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="create_project" class="btn btn-custom text-white">Create Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Project Modal -->
    <div class="modal fade" id="editProjectModal" tabindex="-1" aria-labelledby="editProjectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="post" id="editProjectForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProjectModalLabel">Edit Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="project_id" id="edit_project_id">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_title" class="form-label">Project Title</label>
                                    <input type="text" class="form-control" name="title" id="edit_title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_location" class="form-label">Location</label>
                                    <input type="text" class="form-control" name="location" id="edit_location" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="3" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_start_date" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" name="start_date" id="edit_start_date" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_end_date" class="form-label">End Date</label>
                                    <input type="date" class="form-control" name="end_date" id="edit_end_date" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_status" class="form-label">Status</label>
                                    <select class="form-select" name="status" id="edit_status" required>
                                        <option value="upcoming">Upcoming</option>
                                        <option value="ongoing">Ongoing</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_budget" class="form-label">Budget (PHP)</label>
                                    <input type="number" class="form-control" name="budget" id="edit_budget" step="0.01">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_contact_info" class="form-label">Contact Information</label>
                            <textarea class="form-control" name="contact_info" id="edit_contact_info" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_project" class="btn btn-custom text-white">Update Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function editProject(project) {
            document.getElementById('edit_project_id').value = project.id;
            document.getElementById('edit_title').value = project.title;
            document.getElementById('edit_location').value = project.location;
            document.getElementById('edit_description').value = project.description;
            document.getElementById('edit_start_date').value = project.start_date;
            document.getElementById('edit_end_date').value = project.end_date;
            document.getElementById('edit_status').value = project.status;
            document.getElementById('edit_budget').value = project.budget;
            document.getElementById('edit_contact_info').value = project.contact_info;
            
            var editModal = new bootstrap.Modal(document.getElementById('editProjectModal'));
            editModal.show();
        }
    </script>
</body>
</html>