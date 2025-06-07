<?php
// Start the session
session_start();

// Destroy all session data
session_destroy();

// Clear all session variables
$_SESSION = array();

// Delete the session cookie if it exists
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Optional: Add a logout message to display on index.php
session_start();
$_SESSION['logout_message'] = 'You have been successfully logged out.';

// Redirect to index.php
header("Location: index.php");
exit();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging out...</title>
    <meta http-equiv="refresh" content="2;url=index.php">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="text-center mt-5">
                    <div class="card p-4">
                        <h2 class="text-success">Logging you out...</h2>
                        <p class="text-muted">Please wait while we redirect you to the homepage.</p>
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3">
                            <small>If you are not redirected automatically, <a href="index.php" class="text-success">click here</a>.</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>