<?php
// Include config file
require_once '../config/config.php';
require_once MODELS_PATH . '/Database.php';

try {
    // Get database connection
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Database Update</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    </head>
    <body>
    <div class='container py-5'>
        <h1>Updating Users Table</h1>
        <div class='card my-4'>
            <div class='card-body'>";
    
    // First, check if users table exists
    $tables = $conn->query("SHOW TABLES LIKE 'users'")->fetchAll();
    if (count($tables) === 0) {
        echo "<div class='alert alert-danger'>The users table does not exist yet. Please import the database SQL file first.</div>";
    } else {
        // Check if description column already exists
        $result = $conn->query("SHOW COLUMNS FROM users LIKE 'description'");
        if ($result->rowCount() > 0) {
            echo "<div class='alert alert-success'>The description column already exists in the users table.</div>";
        } else {
            // Add description column
            try {
                $sql = "ALTER TABLE users ADD COLUMN description TEXT DEFAULT NULL";
                $conn->exec($sql);
                echo "<div class='alert alert-success'>Description column added to users table successfully!</div>";
            } catch (PDOException $e) {
                echo "<div class='alert alert-danger'>Error adding description column: " . $e->getMessage() . "</div>";
            }
        }
    }
    
    echo "</div>
        </div>
        <div class='d-flex gap-2'>
            <a href='" . APP_URL . "/account.php' class='btn btn-primary'>Go to My Account</a>
            <a href='" . APP_URL . "/' class='btn btn-outline-secondary'>Go to Homepage</a>
        </div>
    </div>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
    </body>
    </html>";
} catch (Exception $e) {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Database Update Error</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    </head>
    <body>
    <div class='container py-5'>
        <h1>Error</h1>
        <div class='alert alert-danger'>
            <p>An error occurred: " . $e->getMessage() . "</p>
        </div>
        <a href='" . APP_URL . "/' class='btn btn-primary'>Return to Homepage</a>
    </div>
    </body>
    </html>";
}
?>
