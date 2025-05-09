<?php
// Set headers to allow CORS and JSON content type
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Better error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_log("Chat proxy called at " . date('Y-m-d H:i:s'));

// Create a detailed error handler for debugging
function detailed_error($message, $code = 500) {
    $error_details = [
        'error' => [
            'message' => $message,
            'type' => 'proxy_error',
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ];
    
    error_log("API Proxy Error: $message");
    http_response_code($code);
    echo json_encode($error_details);
    exit;
}

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// API configuration - Mistral AI
$api_key = 'vHrsX9bWGF3AW7fR0hblwIQzSekJQHa1';
$api_url = 'https://api.mistral.ai/v1/chat/completions';
$model = 'mistral-medium'; // Default Mistral model

// Check if this is a test request
$is_test = isset($_GET['test']) && $_GET['test'] === '1';

try {
    if ($is_test) {
        // This is a test request - use a simple predefined message
        $message = "Hello, can you respond with a simple test message?";
    } else {
        // Regular API usage - check for POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            detailed_error('Only POST requests are allowed for regular usage', 405);
        }

        // Get the request body and decode JSON
        $request_body = file_get_contents('php://input');
        if (empty($request_body)) {
            detailed_error('Empty request body');
        }
        
        error_log("Received request body: " . $request_body);
        
        $data = json_decode($request_body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            detailed_error('Invalid JSON in request: ' . json_last_error_msg());
        }

        // Validate request has message field
        if (!isset($data['message']) || empty($data['message'])) {
            detailed_error('No message provided or message is empty');
        }
        
        $message = $data['message'];
    }

    // Log the message being sent for debugging
    error_log("Sending message to Mistral AI: " . $message);

    // Fix the role parameter to use proper Mistral API format
    $payload = [
        'model' => $model,
        'messages' => [
            [
                'role' => 'system', 
                'content' => 'You are a helpful assistant for a pharmacy website called Apothecare. Focus on health and wellness topics. Keep responses very concise (2-3 short sentences maximum).'
            ],
            [
                'role' => 'user',
                'content' => $message
            ]
        ],
        'max_tokens' => 300,
        'temperature' => 0.7
    ];

    // Initialize cURL session
    $ch = curl_init($api_url);
    if (!$ch) {
        detailed_error("Failed to initialize cURL");
    }

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    
    // Encode the payload
    $json_payload = json_encode($payload);
    if (json_last_error() !== JSON_ERROR_NONE) {
        detailed_error('JSON encoding failed: ' . json_last_error_msg());
    }
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key,
        'Accept: application/json'
    ]);

    // Enable more reliable error reporting from cURL
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    
    // For debugging, capture verbose output
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    $verbose = fopen('php://temp', 'w+');
    curl_setopt($ch, CURLOPT_STDERR, $verbose);

    // Execute the request
    $response = curl_exec($ch);
    
    // Check for cURL errors
    if ($response === false) {
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        
        // Get verbose debug information
        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        
        error_log("cURL Error ($curl_errno): $curl_error\nVerbose log: $verboseLog");
        detailed_error("cURL Error ($curl_errno): $curl_error");
    }

    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Log response for debugging
    error_log("API responded with status code: $status_code");
    error_log("API response preview: " . substr($response, 0, 200) . "...");

    // Check for non-200 status code
    if ($status_code < 200 || $status_code >= 300) {
        detailed_error("API returned error status code: $status_code, Response: " . substr($response, 0, 200));
    }

    // Decode the API response
    $result = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Failed to decode response: " . $response);
        detailed_error('Failed to decode API response: ' . json_last_error_msg());
    }

    // Check for API errors
    if (isset($result['error'])) {
        $error_msg = isset($result['error']['message']) ? $result['error']['message'] : 'Unknown API error';
        detailed_error("API error: $error_msg", $status_code);
    }
    
    // Log success
    error_log("Successfully received valid API response");
    
    // If in test mode, add diagnostic info
    if ($is_test) {
        echo json_encode([
            'status' => 'test_completed',
            'api_status_code' => $status_code,
            'response' => $result
        ], JSON_PRETTY_PRINT);
    } else {
        // Otherwise, return the API response directly
        echo $response;
    }
    
} catch (Exception $e) {
    // Log error and return nice error response
    $error_message = $e->getMessage();
    error_log("Caught exception in chat proxy: $error_message");
    detailed_error($error_message);
}
?>
