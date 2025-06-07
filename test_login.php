<?php
require_once 'config.php';

echo "<h2>Login System Test</h2>";

// Test all three accounts
$test_accounts = [
    ['username' => 'superadmin', 'password' => 'admin123'],
    ['username' => 'admin', 'password' => 'admin123'],
    ['username' => 'user', 'password' => 'user123']
];

foreach ($test_accounts as $account) {
    echo "<h3>Testing: {$account['username']} / {$account['password']}</h3>";
    
    try {
        // Get user from database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$account['username']]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "✅ User found in database<br>";
            echo "- Stored password hash: " . substr($user['password'], 0, 20) . "...<br>";
            
            // Test password verification
            if (password_verify($account['password'], $user['password'])) {
                echo "✅ <strong>Password verification SUCCESSFUL!</strong><br>";
                echo "- Role: {$user['role']}<br>";
            } else {
                echo "❌ <strong>Password verification FAILED!</strong><br>";
                echo "- This means the stored password hash is incorrect<br>";
                
                // Generate correct hash
                $correct_hash = password_hash($account['password'], PASSWORD_DEFAULT);
                echo "- Correct hash should be: " . substr($correct_hash, 0, 30) . "...<br>";
            }
        } else {
            echo "❌ User not found in database<br>";
        }
        
        echo "<hr>";
        
    } catch(PDOException $e) {
        echo "❌ Error: " . $e->getMessage() . "<br><hr>";
    }
}

echo "<h3>🔧 Quick Fix SQL (if passwords failed):</h3>";
echo "<p>If any passwords failed verification, run this SQL to fix them:</p>";
echo "<pre style='background-color: #f8f9fa; padding: 15px; border-radius: 5px;'>";
echo "-- Fix password hashes\n";
echo "UPDATE users SET password = '" . password_hash('admin123', PASSWORD_DEFAULT) . "' WHERE username = 'superadmin';\n";
echo "UPDATE users SET password = '" . password_hash('admin123', PASSWORD_DEFAULT) . "' WHERE username = 'admin';\n";
echo "UPDATE users SET password = '" . password_hash('user123', PASSWORD_DEFAULT) . "' WHERE username = 'user';\n";
echo "</pre>";

echo "<h3>🎯 Next Steps:</h3>";
echo "<ol>";
echo "<li>If all passwords show ✅ SUCCESSFUL - try logging in again on index.php</li>";
echo "<li>If any show ❌ FAILED - run the SQL fix above</li>";
echo "<li>Check if there are any PHP errors on index.php</li>";
echo "</ol>";
?>