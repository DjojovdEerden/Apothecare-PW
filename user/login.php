<?php
// Initialize the session
session_start();

// Include config file
require_once '../config/config.php';
require_once MODELS_PATH . '/User.php';
require_once UTILS_PATH . '/Helpers.php';

// Define response array
$response = [];

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Check if identifier is empty
    if(empty(trim($_POST["identifier"]))) {
        $response["message"] = "Please enter your username or email.";
        $response["success"] = false;
    } else {
        $identifier = trim($_POST["identifier"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))) {
        $response["message"] = "Please enter your password.";
        $response["success"] = false;
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(!isset($response["message"])) {
        try {
            $userModel = new User();
            
            // Check if database connection is working
            try {
                $db = Database::getInstance();
                $conn = $db->getConnection();
                // Check if users table exists
                $stmt = $conn->query("SHOW TABLES LIKE 'users'");
                if ($stmt->rowCount() == 0) {
                    throw new Exception("Users table does not exist. Please import the database SQL file first.");
                }
            } catch (Exception $e) {
                throw new Exception("Database connection issue: " . $e->getMessage());
            }
            
            $user = $userModel->getUserByUsernameOrEmail($identifier);
            
            if($user) {
                // Check if password is correct
                if(password_verify($password, $user["password"])) {
                    // Password is correct, start a new session
                    
                    // Store data in session variables
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = $user["id"];
                    $_SESSION["username"] = $user["username"];
                    $_SESSION["email"] = $user["email"];
                    
                    // Return success response
                    $response["success"] = true;
                    $response["message"] = "Login successful!";
                } else {
                    // Display an error message if password is not valid
                    $response["success"] = false;
                    $response["message"] = "Invalid password.";
                }
            } else {
                // Display an error message if account doesn't exist
                $response["success"] = false;
                $response["message"] = "No account found with those credentials.";
            }
        } catch(Exception $e) {
            $response["success"] = false;
            $response["message"] = "Error: " . $e->getMessage();
            error_log("Login error: " . $e->getMessage());
        }
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
