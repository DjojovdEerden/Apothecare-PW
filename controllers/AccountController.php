<?php
require_once MODELS_PATH . '/User.php';

class AccountController {
    private $user;
    
    public function __construct() {
        $this->user = new User();
        
        // Redirect unauthenticated users
        if (!Helpers::isLoggedIn()) {
            Helpers::redirect(APP_URL . '/index.php');
        }
    }
    
    // Display account page
    public function index() {
        $userId = Helpers::getCurrentUserId();
        $userData = $this->user->getUserById($userId);
        $message = '';
        $messageType = '';
        
        if (!$userData) {
            // If we can't get user data, show error message
            $message = 'Unable to retrieve your account information.';
            $messageType = 'danger';
        } else {
            // Process updates if there's a POST request
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['update_profile'])) {
                    list($message, $messageType) = $this->updateProfile();
                } elseif (isset($_POST['update_password'])) {
                    list($message, $messageType) = $this->updatePassword();
                } elseif (isset($_POST['update_description'])) {
                    list($message, $messageType) = $this->updateDescription();
                }
                
                // Get fresh user data after update
                $userData = $this->user->getUserById($userId);
            }
        }
        
        // Load the view
        require VIEWS_PATH . '/account/index.php';
    }
    
    // Handle profile update form submission
    private function updateProfile() {
        $userId = Helpers::getCurrentUserId();
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        // Basic validation
        if (empty($username)) {
            return ['Username cannot be empty.', 'danger'];
        }
        
        if (empty($email)) {
            return ['Email cannot be empty.', 'danger'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['Please enter a valid email address.', 'danger'];
        }
        
        try {
            // Update user information
            $this->user->updateUserInfo($userId, [
                'username' => $username,
                'email' => $email
            ]);
            
            // Update session variables
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            
            return ['Profile updated successfully.', 'success'];
        } catch (Exception $e) {
            return [$e->getMessage(), 'danger'];
        }
    }
    
    // Handle description update form submission
    public function updateDescription() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['update_description'])) {
            return ['', ''];
        }
        
        $userId = Helpers::getCurrentUserId();
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        
        try {
            // Update user description
            $this->user->updateUserInfo($userId, [
                'description' => $description
            ]);
            
            return ['Your information has been updated successfully.', 'success'];
        } catch (Exception $e) {
            return [$e->getMessage(), 'danger'];
        }
    }
    
    // Handle password update form submission
    private function updatePassword() {
        $userId = Helpers::getCurrentUserId();
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validation
        if (empty($currentPassword)) {
            return ['Current password is required.', 'danger'];
        }
        
        if (empty($newPassword)) {
            return ['New password is required.', 'danger'];
        }
        
        if (strlen($newPassword) < 6) {
            return ['Password must be at least 6 characters long.', 'danger'];
        }
        
        if ($newPassword !== $confirmPassword) {
            return ['The new passwords do not match.', 'danger'];
        }
        
        // Verify current password
        if (!$this->user->verifyPassword($userId, $currentPassword)) {
            return ['Current password is incorrect.', 'danger'];
        }
        
        try {
            // Update password
            $this->user->updatePassword($userId, $newPassword);
            return ['Password updated successfully.', 'success'];
        } catch (Exception $e) {
            return [$e->getMessage(), 'danger'];
        }
    }
}
?>
