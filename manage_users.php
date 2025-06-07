<?php
session_start();
require_once 'config.php';

// Check if user is logged in and has super admin role
requireRole('super_admin');

$user = getUserInfo();

// Handle role updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$new_role, $user_id]);
        $success_message = "User role updated successfully!";
    } catch(PDOException $e) {
        $error_message = "Error updating role: " . $e->getMessage();
    }
}

// Handle user deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    
    // Prevent deleting self
    if ($user_id == $user['id']) {
        $error_message = "You cannot delete your own account!";
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $success_message = "User deleted successfully!";
        } catch(PDOException $e) {
            $error_message = "Error deleting user: " . $e->getMessage();
        }
    }
}

// Handle new user creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_user'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    try {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (fullname, email, username, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$fullname, $email, $username, $hashed_password, $role]);
        $success_message = "User created successfully!";
    } catch(PDOException $e) {
        if ($e->getCode() == 23000) {
            $error_message = "Username or email already exists!";
        } else {
            $error_message = "Error creating user: " . $e->getMessage();
        }
    }
}

// Get all users
try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch(PDOException $e) {
    $users = [];
    $error_message = "Error loading users: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Kanluran - Manage Users</title>
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
        @media (max-width: 768px) { .title-black { font-size: 3rem; } .title-green { font-size: 1.5rem; } }
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
                <h2 class="text-success">Manage Users</h2>
            </div>

            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <!-- Create New User Form -->
            <div class="card-custom p-4 mb-4">
                <h4 class="text-success mb-3">Create New User</h4>
                <form method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fullname" class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="fullname" id="fullname" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="email" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" id="username" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" id="password" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" name="role" id="role" required>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                    <option value="super_admin">Super Admin</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="create_user" class="btn btn-custom text-white">Create User</button>
                </form>
            </div>

            <!-- Users List -->
            <div class="card-custom p-4">
                <h4 class="text-success mb-3">All Users</h4>
                <?php if (empty($users)): ?>
                    <div class="text-center py-5">
                        <h5 class="text-muted">No users found</h5>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-success">
                                <tr>
                                    <th>ID</th>
                                    <th>Full Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $u): ?>
                                    <tr>
                                        <td><?php echo $u['id']; ?></td>
                                        <td><?php echo htmlspecialchars($u['fullname']); ?></td>
                                        <td><?php echo htmlspecialchars($u['username']); ?></td>
                                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $u['role'] == 'super_admin' ? 'danger' : 
                                                    ($u['role'] == 'admin' ? 'warning' : 'success'); 
                                            ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $u['role'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($u['created_at'])); ?></td>
                                        <td>
                                            <?php if ($u['id'] != $user['id']): // Don't allow editing own account ?>
                                                <form method="post" class="d-inline me-2">
                                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                                    <select name="role" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                                                        <option value="user" <?php echo $u['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                                                        <option value="admin" <?php echo $u['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                        <option value="super_admin" <?php echo $u['role'] == 'super_admin' ? 'selected' : ''; ?>>Super Admin</option>
                                                    </select>
                                                    <input type="hidden" name="update_role" value="1">
                                                </form>
                                                
                                                <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                                    <button type="submit" name="delete_user" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-muted">Current User</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <div class="text-center mt-4">
                    <a href="super_admin_dashboard.php" class="btn btn-custom text-white">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>