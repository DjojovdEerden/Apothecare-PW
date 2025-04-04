<?php
// Application initialization
require_once 'config/config.php';
require_once UTILS_PATH . '/Helpers.php';

// Function to load controllers dynamically
function loadController($controllerName) {
    $controllerFile = CONTROLLERS_PATH . '/' . $controllerName . '.php';
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        $controller = new $controllerName();
        return $controller;
    }
    return null;
}

// Simple router function
function route($path, $controller, $action, $params = []) {
    $requestPath = $_SERVER['REQUEST_URI'];
    // Strip query string
    $requestPath = strtok($requestPath, '?');
    // Remove base path
    $basePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', BASE_PATH);
    $requestPath = str_replace($basePath, '', $requestPath);
    
    if ($requestPath === $path) {
        $controller = loadController($controller);
        if ($controller && method_exists($controller, $action)) {
            call_user_func_array([$controller, $action], $params);
            return true;
        }
    }
    return false;
}
?>
