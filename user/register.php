<?php
// Include config file
require_once '../config/config.php';
require_once MODELS_PATH . '/User.php';
require_once UTILS_PATH . '/Helpers.php';

// Define response array
$response = [];

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $userModel = new User();
    
    // Validate username
    if(empty(trim($_POST["username"]))) {
        $response["message"] = "Please enter a username.";
        $response["success"] = false;
    } else {
        // Check if username exists
        try {
            if($userModel->usernameExists(trim($_POST["username"]))) {
                $response["message"] = "This username is already taken.";
                $response["success"] = false;
            } else {
                $username = trim($_POST["username"]);
            }
        } catch(Exception $e) {
            $response["message"] = "Oops! Something went wrong. Please try again later.";
            $response["success"] = false;
            error_log("Registration error: " . $e->getMessage());
        }
    }
    
    // Validate email
    if(empty(trim($_POST["email"]))) {
        $response["message"] = "Please enter an email.";
        $response["success"] = false;
    } else {
        // Check if email exists
        try {
            if($userModel->emailExists(trim($_POST["email"]))) {
                $response["message"] = "This email is already registered.";
                $response["success"] = false;
            } else {
                $email = trim($_POST["email"]);
            }
        } catch(Exception $e) {
            $response["message"] = "Oops! Something went wrong. Please try again later.";
            $response["success"] = false;
            error_log("Registration error: " . $e->getMessage());
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))) {
        $response["message"] = "Please enter a password.";
        $response["success"] = false;
    } elseif(strlen(trim($_POST["password"])) < 6) {
        $response["message"] = "Password must have at least 6 characters.";
        $response["success"] = false;
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))) {
        $response["message"] = "Please confirm password.";
        $response["success"] = false;
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($response["message"]) && ($password != $confirm_password)) {
            $response["message"] = "Passwords did not match.";
            $response["success"] = false;
        }
    }
    
    // Check if there are no errors
    if(empty($response["message"])) {
        try {
            // Create user
            $userId = $userModel->createUser($username, $email, $password);
            
            if($userId) {
                // Registration successful
                $response["success"] = true;
                $response["message"] = "Registration successful! You can now log in.";
            } else {
                $response["success"] = false;
                $response["message"] = "Something went wrong. Please try again.";
            }
        } catch(Exception $e) {
            $response["message"] = "Oops! Something went wrong. Please try again later.";
            $response["success"] = false;
            error_log("Registration error: " . $e->getMessage());
        }
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
