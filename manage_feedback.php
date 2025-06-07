<?php
session_start();
require_once 'config.php';

// Check if user is logged in and has admin role or higher
requireRole('admin');

$user = getUserInfo();

// Handle status updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $feedback_id = $_POST['feedback_id'];
    $new_status = $_POST['status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE feedback SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $feedback_id]);
        $success_message = "Feedback status updated successfully!";
    } catch(PDOException $e) {
        $error_message = "Error updating status: " . $e->getMessage();
    }
}

// Get filter from URL
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Build query based on filter
$query = "SELECT * FROM feedback";
if ($filter == 'pending') {
    $query .= " WHERE status = 'pending'";
} elseif ($filter == 'reviewed') {
    $query .= " WHERE status = 'reviewed'";
} elseif ($filter == 'resolved') {
    $query .= " WHERE status = 'resolved'";
}
$query .= " ORDER BY created_at DESC";

// Get feedback based on filter
try {
    $stmt = $pdo->query($query);
    $feedbacks = $stmt->fetchAll();
    
    // Get counts for each status
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM feedback WHERE status = 'pending'");
    $pending_count = $stmt->fetch()['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM feedback WHERE status = 'reviewed'");
    $reviewed_count = $stmt->fetch()['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM feedback WHERE status = 'resolved'");
    $resolved_count = $stmt->fetch()['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM feedback");
    $total_count = $stmt->fetch()['count'];
    
} catch(PDOException $e) {
    $feedbacks = [];
    $error_message = "Error loading feedback: " . $e->getMessage();
    $pending_count = $reviewed_count = $resolved_count = $total_count = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Kanluran - Manage Feedback</title>
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
        .status-pending { color: #ffc107; }
        .status-reviewed { color: #17a2b8; }
        .status-resolved { color: #28a745; }
        .filter-btn { margin-right: 10px; margin-bottom: 10px; }
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
                <h2 class="text-success">Manage Feedback</h2>
            </div>

            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="card-custom p-4">
                <!-- Filter Buttons -->
                <div class="mb-4">
                    <h5 class="text-success mb-3">Filter Feedback</h5>
                    <div class="d-flex flex-wrap">
                        <a href="manage_feedback.php?filter=all" class="btn <?php echo $filter == 'all' ? 'btn-custom text-white' : 'btn-outline-secondary'; ?> filter-btn">
                            All Feedback (<?php echo $total_count; ?>)
                        </a>
                        <a href="manage_feedback.php?filter=pending" class="btn <?php echo $filter == 'pending' ? 'btn-warning' : 'btn-outline-warning'; ?> filter-btn">
                            Pending (<?php echo $pending_count; ?>)
                        </a>
                        <a href="manage_feedback.php?filter=reviewed" class="btn <?php echo $filter == 'reviewed' ? 'btn-info' : 'btn-outline-info'; ?> filter-btn">
                            Reviewed (<?php echo $reviewed_count; ?>)
                        </a>
                        <a href="manage_feedback.php?filter=resolved" class="btn <?php echo $filter == 'resolved' ? 'btn-success' : 'btn-outline-success'; ?> filter-btn">
                            Resolved (<?php echo $resolved_count; ?>)
                        </a>
                    </div>
                </div>

                <?php if (empty($feedbacks)): ?>
                    <div class="text-center py-5">
                        <h5 class="text-muted">
                            <?php if ($filter == 'all'): ?>
                                No feedback submitted yet
                            <?php else: ?>
                                No <?php echo ucfirst($filter); ?> feedback found
                            <?php endif; ?>
                        </h5>
                        <p class="text-muted">
                            <?php if ($filter == 'all'): ?>
                                Feedback from residents will appear here.
                            <?php else: ?>
                                Try selecting a different filter above.
                            <?php endif; ?>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="mb-3">
                        <strong>Showing: <?php echo count($feedbacks); ?> 
                        <?php echo $filter == 'all' ? 'total' : ucfirst($filter); ?> feedback entries</strong>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-success">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Feedback</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($feedbacks as $feedback): ?>
                                    <tr>
                                        <td><?php echo $feedback['id']; ?></td>
                                        <td><?php echo htmlspecialchars($feedback['name']); ?></td>
                                        <td><?php echo htmlspecialchars($feedback['email']); ?></td>
                                        <td>
                                            <div style="max-width: 300px;">
                                                <?php if (strlen($feedback['feedback']) > 150): ?>
                                                    <span id="short-<?php echo $feedback['id']; ?>">
                                                        <?php echo htmlspecialchars(substr($feedback['feedback'], 0, 150)); ?>...
                                                        <br><button type="button" class="btn btn-link btn-sm p-0" onclick="toggleFeedback(<?php echo $feedback['id']; ?>)">Read More</button>
                                                    </span>
                                                    <span id="full-<?php echo $feedback['id']; ?>" style="display: none;">
                                                        <?php echo htmlspecialchars($feedback['feedback']); ?>
                                                        <br><button type="button" class="btn btn-link btn-sm p-0" onclick="toggleFeedback(<?php echo $feedback['id']; ?>)">Show Less</button>
                                                    </span>
                                                <?php else: ?>
                                                    <?php echo htmlspecialchars($feedback['feedback']); ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $feedback['status'] == 'pending' ? 'warning' : 
                                                    ($feedback['status'] == 'reviewed' ? 'info' : 'success'); 
                                            ?>">
                                                <?php echo ucfirst($feedback['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($feedback['created_at'])); ?></td>
                                        <td>
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="feedback_id" value="<?php echo $feedback['id']; ?>">
                                                <select name="status" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                                                    <option value="pending" <?php echo $feedback['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="reviewed" <?php echo $feedback['status'] == 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                                                    <option value="resolved" <?php echo $feedback['status'] == 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                                </select>
                                                <input type="hidden" name="update_status" value="1">
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <div class="text-center mt-4">
                    <a href="<?php echo ($user['role'] == 'super_admin') ? 'super_admin_dashboard.php' : 'admin_dashboard.php'; ?>" class="btn btn-custom text-white">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function toggleFeedback(id) {
            var shortDiv = document.getElementById('short-' + id);
            var fullDiv = document.getElementById('full-' + id);
            
            if (shortDiv.style.display === 'none') {
                shortDiv.style.display = 'inline';
                fullDiv.style.display = 'none';
            } else {
                shortDiv.style.display = 'none';
                fullDiv.style.display = 'inline';
            }
        }
    </script>
</body>
</html>