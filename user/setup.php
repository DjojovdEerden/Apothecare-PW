<?php
// Include config file
require_once '../config/config.php';
require_once MODELS_PATH . '/Database.php';

// Initialize response
$output = [];

try {
    // Get database connection
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Check if users table already exists
    $tableExists = false;
    try {
        $stmt = $conn->query("SELECT 1 FROM users LIMIT 1");
        $tableExists = true;
        $output[] = "Users table already exists.";
    } catch (PDOException $e) {
        // Table doesn't exist, we'll create it
        $output[] = "Users table not found. Creating now...";
    }
    
    // Create users table if it doesn't exist
    if (!$tableExists) {
        $createTableSQL = "CREATE TABLE IF NOT EXISTS `users` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `username` varchar(50) NOT NULL,
            `email` varchar(100) NOT NULL,
            `password` varchar(255) NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `username` (`username`),
            UNIQUE KEY `email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        if ($conn->exec($createTableSQL) !== false) {
            $output[] = "Users table created successfully!";
        } else {
            $output[] = "Error creating users table.";
        }
    }
    
    // Add a test user if requested
    if (isset($_GET['add_test_user']) && $_GET['add_test_user'] == 1) {
        // Check if test user already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = 'test' OR email = 'test@example.com'");
        $stmt->execute();
        if ($stmt->fetch()) {
            $output[] = "Test user already exists.";
        } else {
            // Create test user
            $hashedPassword = password_hash('password', PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES ('test', 'test@example.com', :password)");
            $stmt->execute([':password' => $hashedPassword]);
            $output[] = "Test user created. Username: 'test', Email: 'test@example.com', Password: 'password'";
        }
    }
    
    // Display success message
    echo "<h1>Database Setup</h1>";
    echo "<div style='margin: 20px; padding: 15px; background-color: #f0f0f0; border-radius: 5px;'>";
    foreach ($output as $message) {
        echo "<p>$message</p>";
    }
    echo "</div>";
    
    echo "<p>Go back to <a href='" . APP_URL . "'>homepage</a> to continue.</p>";
    
    echo "<p><a href='" . APP_URL . "/user/setup.php?add_test_user=1'>Add test user</a> if you want a demo account.</p>";
    
} catch (Exception $e) {
    echo "<h1>Setup Error</h1>";
    echo "<div style='margin: 20px; padding: 15px; background-color: #f8d7da; border-radius: 5px;'>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "</div>";
}
?>
