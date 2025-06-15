<?php
session_start();
require_once 'config.php';

// Check if user is logged in and has admin role or higher
requireRole('admin');

$user = getUserInfo();

// Handle status updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $report_id = $_POST['report_id'];
    $new_status = $_POST['status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE reports SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $report_id]);
        $success_message = "Report status updated successfully!";
    } catch(PDOException $e) {
        $error_message = "Error updating status: " . $e->getMessage();
    }
}

// Get filter from URL
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Build query based on filter
$query = "SELECT * FROM reports";
if ($filter == 'pending') {
    $query .= " WHERE status = 'pending'";
} elseif ($filter == 'in_progress') {
    $query .= " WHERE status = 'in_progress'";
} elseif ($filter == 'resolved') {
    $query .= " WHERE status = 'resolved'";
}
$query .= " ORDER BY created_at DESC";

// Get reports based on filter
try {
    $stmt = $pdo->query($query);
    $reports = $stmt->fetchAll();
    
    // Get counts for each status
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM reports WHERE status = 'pending'");
    $pending_count = $stmt->fetch()['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM reports WHERE status = 'in_progress'");
    $in_progress_count = $stmt->fetch()['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM reports WHERE status = 'resolved'");
    $resolved_count = $stmt->fetch()['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM reports");
    $total_count = $stmt->fetch()['count'];
    
} catch(PDOException $e) {
    $reports = [];
    $error_message = "Error loading reports: " . $e->getMessage();
    $pending_count = $in_progress_count = $resolved_count = $total_count = 0;
}

// Function to format issue type
function formatIssueType($type) {
    $types = [
        'street_hole' => 'Street Hole/Damage',
        'street_light' => 'Street Light',
        'garbage' => 'Garbage Collection',
        'drainage' => 'Clogged Drainage',
        'noise' => 'Noise Complaint',
        'other' => 'Other Issue'
    ];
    return isset($types[$type]) ? $types[$type] : ucfirst(str_replace('_', ' ', $type));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Kanluran - Manage Reports</title>
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
                <h2 class="text-success">Manage Reports</h2>
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
                    <h5 class="text-success mb-3">Filter Reports</h5>
                    <div class="d-flex flex-wrap">
                        <a href="manage_reports.php?filter=all" class="btn <?php echo $filter == 'all' ? 'btn-custom text-white' : 'btn-outline-secondary'; ?> filter-btn">
                            All Reports (<?php echo $total_count; ?>)
                        </a>
                        <a href="manage_reports.php?filter=pending" class="btn <?php echo $filter == 'pending' ? 'btn-warning' : 'btn-outline-warning'; ?> filter-btn">
                            Pending (<?php echo $pending_count; ?>)
                        </a>
                        <a href="manage_reports.php?filter=in_progress" class="btn <?php echo $filter == 'in_progress' ? 'btn-info' : 'btn-outline-info'; ?> filter-btn">
                            In Progress (<?php echo $in_progress_count; ?>)
                        </a>
                        <a href="manage_reports.php?filter=resolved" class="btn <?php echo $filter == 'resolved' ? 'btn-success' : 'btn-outline-success'; ?> filter-btn">
                            Resolved (<?php echo $resolved_count; ?>)
                        </a>
                    </div>
                </div>

                <?php if (empty($reports)): ?>
                    <div class="text-center py-5">
                        <h5 class="text-muted">
                            <?php if ($filter == 'all'): ?>
                                No reports submitted yet
                            <?php else: ?>
                                No <?php echo ucfirst(str_replace('_', ' ', $filter)); ?> reports found
                            <?php endif; ?>
                        </h5>
                        <p class="text-muted">
                            <?php if ($filter == 'all'): ?>
                                Issue reports from residents will appear here.
                            <?php else: ?>
                                Try selecting a different filter above.
                            <?php endif; ?>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="mb-3">
                        <strong>Showing: <?php echo count($reports); ?> 
                        <?php echo $filter == 'all' ? 'total' : ucfirst(str_replace('_', ' ', $filter)); ?> reports</strong>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-success">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Contact</th>
                                    <th>Location</th>
                                    <th>Issue Type</th>
                                    <th>Description</th>
                                    <th>Image</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reports as $report): ?>
                                    <tr>
                                        <td><?php echo $report['id']; ?></td>
                                        <td><?php echo htmlspecialchars($report['name']); ?></td>
                                        <td><?php echo htmlspecialchars($report['contact']); ?></td>
                                        <td>
                                            <div style="max-width: 150px; overflow: hidden; text-overflow: ellipsis;">
                                                <?php echo htmlspecialchars($report['location']); ?>
                                            </div>
                                        </td>
                                        <td><?php echo formatIssueType($report['issue_type']); ?></td>
                                        <td>
                                            <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                                <?php echo htmlspecialchars(substr($report['description'], 0, 100)); ?>
                                                <?php if (strlen($report['description']) > 100): ?>...<?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($report['image_path'] && $report['image_path'] != 'no-image.jpg'): ?>
                                                <img src="uploads/<?php echo htmlspecialchars($report['image_path']); ?>" 
                                                     alt="Report Image" 
                                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px; cursor: pointer;"
                                                     onclick="showImageModal('uploads/<?php echo htmlspecialchars($report['image_path']); ?>', 'Report #<?php echo $report['id']; ?> Image')"
                                                     title="Click to view full size">
                                            <?php else: ?>
                                                <span class="text-muted">No image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $report['status'] == 'pending' ? 'warning' : 
                                                    ($report['status'] == 'in_progress' ? 'info' : 'success'); 
                                            ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $report['status'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($report['created_at'])); ?></td>
                                        <td>
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
                                                <select name="status" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                                                    <option value="pending" <?php echo $report['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="in_progress" <?php echo $report['status'] == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                                    <option value="resolved" <?php echo $report['status'] == 'resolved' ? 'selected' : ''; ?>>Resolved</option>
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

    <!-- Image Modal for viewing full-size images -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Report Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Report Image" class="img-fluid" style="max-height: 500px;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a id="downloadLink" href="" download class="btn btn-custom text-white">Download Image</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function showImageModal(imageSrc, imageTitle) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModalLabel').textContent = imageTitle;
            document.getElementById('downloadLink').href = imageSrc;
            
            var imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
            imageModal.show();
        }
    </script>
</body>
</html>