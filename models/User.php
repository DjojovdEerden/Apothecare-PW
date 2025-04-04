<?php
require_once MODELS_PATH . '/Database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Get user by ID
    public function getUserById($id) {
        try {
            $sql = "SELECT id, username, email, created_at FROM users WHERE id = :id";
            return $this->db->fetchOne($sql, [':id' => $id]);
        } catch (Exception $e) {
            error_log('Error fetching user: ' . $e->getMessage());
            return null;
        }
    }
    
    // Get user by email
    public function getUserByEmail($email) {
        try {
            $sql = "SELECT id, username, email, password, created_at FROM users WHERE email = :email";
            return $this->db->fetchOne($sql, [':email' => $email]);
        } catch (Exception $e) {
            error_log('Error fetching user by email: ' . $e->getMessage());
            return null;
        }
    }
    
    // Get user by username or email
    public function getUserByUsernameOrEmail($identifier) {
        try {
            // First check if the users table exists
            try {
                $this->db->query("SELECT 1 FROM users LIMIT 1");
            } catch (Exception $tableError) {
                error_log('Users table may not exist: ' . $tableError->getMessage());
                throw new Exception('Users table does not exist. Please run the SQL setup script.');
            }
            
            // Fix: using different parameter names for username and email conditions
            $sql = "SELECT id, username, email, password, created_at FROM users 
                   WHERE username = :username OR email = :email";
            
            return $this->db->fetchOne($sql, [
                ':username' => $identifier,
                ':email' => $identifier
            ]);
        } catch (Exception $e) {
            error_log('Error fetching user by username or email: ' . $e->getMessage());
            return null;
        }
    }
    
    // Create a new user
    public function createUser($username, $email, $password) {
        try {
            $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            return $this->db->insert($sql, [
                ':username' => $username,
                ':email' => $email,
                ':password' => $hashed_password
            ]);
        } catch (Exception $e) {
            error_log('Error creating user: ' . $e->getMessage());
            return false;
        }
    }
    
    // Check if username exists
    public function usernameExists($username) {
        try {
            $sql = "SELECT id FROM users WHERE username = :username";
            $result = $this->db->fetchOne($sql, [':username' => $username]);
            return !empty($result);
        } catch (Exception $e) {
            error_log('Error checking username: ' . $e->getMessage());
            return false;
        }
    }
    
    // Check if email exists
    public function emailExists($email) {
        try {
            $sql = "SELECT id FROM users WHERE email = :email";
            $result = $this->db->fetchOne($sql, [':email' => $email]);
            return !empty($result);
        } catch (Exception $e) {
            error_log('Error checking email: ' . $e->getMessage());
            return false;
        }
    }
    
    // Update user information
    public function updateUserInfo($userId, $data) {
        try {
            $updateFields = [];
            $params = [':id' => $userId];
            
            if (!empty($data['username'])) {
                // Check if the new username is already taken by someone else
                if ($this->usernameExists($data['username'])) {
                    $user = $this->getUserByUsername($data['username']);
                    if ($user && $user['id'] != $userId) {
                        throw new Exception('This username is already taken.');
                    }
                }
                $updateFields[] = "username = :username";
                $params[':username'] = $data['username'];
            }
            
            if (!empty($data['email'])) {
                // Check if the new email is already taken by someone else
                if ($this->emailExists($data['email'])) {
                    $user = $this->getUserByEmail($data['email']);
                    if ($user && $user['id'] != $userId) {
                        throw new Exception('This email is already registered.');
                    }
                }
                $updateFields[] = "email = :email";
                $params[':email'] = $data['email'];
            }
            
            if (empty($updateFields)) {
                return true; // Nothing to update
            }
            
            $sql = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = :id";
            $this->db->query($sql, $params);
            
            return true;
        } catch (Exception $e) {
            error_log('Error updating user info: ' . $e->getMessage());
            throw $e;
        }
    }
    
    // Update user password
    public function updatePassword($userId, $newPassword) {
        try {
            $sql = "UPDATE users SET password = :password WHERE id = :id";
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->db->query($sql, [
                ':password' => $hashedPassword,
                ':id' => $userId
            ]);
            return true;
        } catch (Exception $e) {
            error_log('Error updating password: ' . $e->getMessage());
            throw $e;
        }
    }
    
    // Verify current password
    public function verifyPassword($userId, $password) {
        try {
            $sql = "SELECT password FROM users WHERE id = :id";
            $user = $this->db->fetchOne($sql, [':id' => $userId]);
            
            if ($user && password_verify($password, $user['password'])) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log('Error verifying password: ' . $e->getMessage());
            return false;
        }
    }
    
    // Get user by username
    public function getUserByUsername($username) {
        try {
            $sql = "SELECT id, username, email, created_at FROM users WHERE username = :username";
            return $this->db->fetchOne($sql, [':username' => $username]);
        } catch (Exception $e) {
            error_log('Error fetching user by username: ' . $e->getMessage());
            return null;
        }
    }
}
?>
