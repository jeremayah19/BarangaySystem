<?php
// Database configuration
$servername = "localhost";
$username = "root";           // Change this to your database username
$password = "";               // Change this to your database password
$dbname = "barangay_kanluran";

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Function to check user role
function getUserRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
}

// Function to check if user has specific role or higher
function hasRole($required_role) {
    if (!isLoggedIn()) return false;
    
    $user_role = getUserRole();
    $roles = ['user' => 1, 'admin' => 2, 'super_admin' => 3];
    
    return $roles[$user_role] >= $roles[$required_role];
}

// Function to redirect if not authorized
function requireRole($required_role, $redirect = 'index.php') {
    if (!hasRole($required_role)) {
        header("Location: $redirect");
        exit();
    }
}

// Function to get user info
function getUserInfo() {
    if (!isLoggedIn()) return null;
    
    return [
        'id' => $_SESSION['user_id'],
        'fullname' => $_SESSION['fullname'],
        'username' => $_SESSION['username'],
        'email' => $_SESSION['email'],
        'role' => $_SESSION['role']
    ];
}
?>