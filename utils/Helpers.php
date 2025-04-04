<?php
class Helpers {
    // Format price with currency symbol
    public static function formatPrice($price, $symbol = '€') {
        return $symbol . number_format($price, 2);
    }
    
    // Render star rating
    public static function renderStars($rating, $max = 5) {
        $html = '<div class="star-rating">';
        
        // Full stars
        for ($i = 1; $i <= floor($rating); $i++) {
            $html .= '<i class="bi bi-star-fill text-warning"></i>';
        }
        
        // Half star
        if ($rating - floor($rating) >= 0.5) {
            $html .= '<i class="bi bi-star-half text-warning"></i>';
            $i++;
        }
        
        // Empty stars
        for (; $i <= $max; $i++) {
            $html .= '<i class="bi bi-star text-warning"></i>';
        }
        
        $html .= '</div>';
        return $html;
    }
    
    // Truncate text with ellipsis
    public static function truncateText($text, $length = 100) {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . '...';
    }
    
    // Check if a user has marked a review as helpful
    public static function userMarkedHelpful($review_id) {
        return isset($_SESSION['helpful_reviews']) && in_array($review_id, $_SESSION['helpful_reviews']);
    }
    
    // Sanitize output for HTML display
    public static function escape($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
    
    // Redirect to a URL
    public static function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    // Get current URL
    public static function getCurrentUrl() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    
    // Check if user is logged in
    public static function isLoggedIn() {
        return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
    }
    
    // Get current user ID
    public static function getCurrentUserId() {
        return self::isLoggedIn() ? $_SESSION['id'] : null;
    }
    
    // Get current username
    public static function getCurrentUsername() {
        return self::isLoggedIn() ? $_SESSION['username'] : null;
    }
}
?>
