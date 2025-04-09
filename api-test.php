<?php
// Bootstrap the application if needed
require_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Key Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        pre {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow: auto;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <h1>Mistral AI API Key Test</h1>
        <p>This page tests if your Mistral AI API key is configured correctly.</p>
        
        <div class="card mb-4">
            <div class="card-header">
                API Key Information
            </div>
            <div class="card-body">
                <p><strong>API Key:</strong> vHrs************************<?= substr('vHrsX9bWGF3AW7fR0hblwIQzSekJQHa1', -4) ?></p>
                <p><strong>API Endpoint:</strong> https://api.mistral.ai/v1/chat/completions</p>
                <p><strong>Model:</strong> mistral-medium</p>
                <p><strong>Ports:</strong> 7860 and 5000</p>
            </div>
        </div>
        
        <button id="testButton" class="btn btn-primary mb-4">Test API Connection</button>
        
        <div id="loadingIndicator" class="d-none">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span class="ms-2">Testing API connection...</span>
        </div>
        
        <div id="resultContainer" class="d-none">
            <h3>Test Results</h3>
            <div class="alert" id="statusAlert" role="alert"></div>
            <h4>Response Details:</h4>
            <pre id="responseDetails"></pre>
        </div>
    </div>
    
    <script>
        document.getElementById('testButton').addEventListener('click', async function() {
            const loadingIndicator = document.getElementById('loadingIndicator');
            const resultContainer = document.getElementById('resultContainer');
            const statusAlert = document.getElementById('statusAlert');
            const responseDetails = document.getElementById('responseDetails');
            
            // Show loading, hide results
            loadingIndicator.classList.remove('d-none');
            resultContainer.classList.add('d-none');
            
            try {
                // Call the proxy endpoint with test mode
                const response = await fetch('<?= APP_URL ?>/api/chat-proxy.php?test=1');
                const data = await response.json();
                
                // Display results
                loadingIndicator.classList.add('d-none');
                resultContainer.classList.remove('d-none');
                
                // Determine if test was successful
                let success = false;
                if (response.ok && data.api_status_code >= 200 && data.api_status_code < 300 && data.response && data.response.choices) {
                    success = true;
                    statusAlert.textContent = 'Success! Your API key is working correctly.';
                    statusAlert.classList.add('alert-success');
                    statusAlert.classList.remove('alert-danger');
                } else {
                    statusAlert.textContent = 'Error! There is a problem with your API key or connection.';
                    statusAlert.classList.add('alert-danger');
                    statusAlert.classList.remove('alert-success');
                }
                
                // Show full response for debugging
                responseDetails.textContent = JSON.stringify(data, null, 2);
                
            } catch (error) {
                // Handle errors
                console.error('Test failed:', error);
                loadingIndicator.classList.add('d-none');
                resultContainer.classList.remove('d-none');
                statusAlert.textContent = 'Test failed: ' + error.message;
                statusAlert.classList.add('alert-danger');
                statusAlert.classList.remove('alert-success');
                responseDetails.textContent = 'Error: ' + error.message;
            }
        });
    </script>
</body>
</html>
