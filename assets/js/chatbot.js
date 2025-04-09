/**
 * AI Chatbot JavaScript Functionality
 * Handles the chat UI and prepares for future API integration
 */

document.addEventListener('DOMContentLoaded', function() {
    // Chat Elements
    const chatbotIcon = document.getElementById('chatbot-icon');
    const chatbotWindow = document.getElementById('chatbot-window');
    const chatbotMaximize = document.getElementById('chatbot-maximize');
    const chatbotClose = document.getElementById('chatbot-close');
    const chatbotInput = document.getElementById('chatbot-input');
    const chatbotSend = document.getElementById('chatbot-send');
    const chatbotMessages = document.getElementById('chatbot-messages');
    const chatbotHeader = document.querySelector('.ai-chatbot-header');
    const chatbotResizeHandle = document.getElementById('chatbot-resize-handle');
    
    // Store original dimensions to restore after maximize
    let originalChatWidth = null;
    let originalChatHeight = null;
    let originalChatLeft = null;
    let originalChatTop = null;
    let isMaximized = false;
    
    // Dragging variables
    let isDragging = false;
    let dragOffsetX = 0;
    let dragOffsetY = 0;
    
    // Resizing variables
    let isResizing = false;
    let originalWidth = 0;
    let originalHeight = 0;
    let originalX = 0;
    let originalY = 0;
    
    // Get the correct base URL from the global APP_URL variable if available
    const baseUrl = (typeof APP_URL !== 'undefined') ? APP_URL : '';
    
    // Update configuration to use our proxy with Mistral AI credentials
    const config = {
        apiKey: 'vHrsX9bWGF3AW7fR0hblwIQzSekJQHa1', // Mistral AI API key
        apiEndpoint: `${baseUrl}/api/chat-proxy.php`, // Fixed: Use proper path to API
        model: 'mistral-medium', // Default Mistral AI model
        debugMode: false, // Debug mode flag
        retryAttempts: 1, // Number of retry attempts for API calls
        retryDelay: 1000 // Delay between retry attempts in milliseconds
    };
    
    // Open chat window when icon is clicked
    chatbotIcon.addEventListener('click', function() {
        chatbotWindow.style.display = 'flex';
        chatbotIcon.style.display = 'none';
        // Reset position to default when opening
        chatbotWindow.style.left = 'auto';
        chatbotWindow.style.top = 'auto';
        chatbotWindow.style.right = '0';
        chatbotWindow.style.bottom = '80px';
        // Focus on input
        setTimeout(() => chatbotInput.focus(), 300);
    });
    
    // Close chat window
    chatbotClose.addEventListener('click', function() {
        chatbotWindow.style.display = 'none';
        chatbotIcon.style.display = 'flex';
    });
    
    // Maximize/restore chat window
    chatbotMaximize.addEventListener('click', function() {
        if (!isMaximized) {
            // Store current dimensions and position before maximizing
            const rect = chatbotWindow.getBoundingClientRect();
            originalChatWidth = rect.width;
            originalChatHeight = rect.height;
            originalChatLeft = chatbotWindow.style.left;
            originalChatTop = chatbotWindow.style.top;
            
            // Set to maximized dimensions (85% of viewport)
            const maxWidth = window.innerWidth * 0.85;
            const maxHeight = window.innerHeight * 0.85;
            
            // Center on screen
            const leftPos = (window.innerWidth - maxWidth) / 2;
            const topPos = (window.innerHeight - maxHeight) / 2;
            
            // Apply new dimensions and position
            chatbotWindow.style.width = `${maxWidth}px`;
            chatbotWindow.style.height = `${maxHeight}px`;
            chatbotWindow.style.left = `${leftPos}px`;
            chatbotWindow.style.top = `${topPos}px`;
            
            // Update icon to show restore option
            chatbotMaximize.innerHTML = '<i class="bi bi-arrows-angle-contract"></i>';
            chatbotMaximize.title = "Restore window";
            
            // Set maximized state
            isMaximized = true;
        } else {
            // Restore to original dimensions and position
            chatbotWindow.style.width = originalChatWidth ? `${originalChatWidth}px` : '350px';
            chatbotWindow.style.height = originalChatHeight ? `${originalChatHeight}px` : '450px';
            chatbotWindow.style.left = originalChatLeft || 'auto';
            chatbotWindow.style.top = originalChatTop || 'auto';
            
            // If original position was not set, use default
            if (!originalChatLeft && !originalChatTop) {
                chatbotWindow.style.right = '0';
                chatbotWindow.style.bottom = '80px';
            }
            
            // Update icon to show maximize option
            chatbotMaximize.innerHTML = '<i class="bi bi-arrows-fullscreen"></i>';
            chatbotMaximize.title = "Maximize window";
            
            // Reset maximized state
            isMaximized = false;
        }
        
        // Adjust scrolling after resize
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    });
    
    // Make the chatbot draggable by the header
    chatbotHeader.addEventListener('mousedown', function(e) {
        // Only allow dragging from the header itself, not from buttons
        if (e.target.closest('.ai-chatbot-controls')) return;
        
        // Prevent default behavior and text selection
        e.preventDefault();
        
        // Get the initial mouse position
        const initialX = e.clientX;
        const initialY = e.clientY;
        
        // Get the current position of the chatbot window
        const chatbotRect = chatbotWindow.getBoundingClientRect();
        
        // Calculate the offset (distance from mouse to the top-left corner of the window)
        dragOffsetX = initialX - chatbotRect.left;
        dragOffsetY = initialY - chatbotRect.top;
        
        // Start dragging
        isDragging = true;
        
        // Add the dragging class to indicate that we're dragging
        chatbotWindow.classList.add('dragging');
        
        // Ensure the window stays visible during drag operations
        chatbotWindow.style.display = 'flex';
    });
    
    // Make the chatbot resizable using the resize handle
    chatbotResizeHandle.addEventListener('mousedown', function(e) {
        // Prevent default behavior
        e.preventDefault();
        
        // Start resizing
        isResizing = true;
        
        // Get initial size and position
        const rect = chatbotWindow.getBoundingClientRect();
        originalWidth = rect.width;
        originalHeight = rect.height;
        originalX = e.clientX;
        originalY = e.clientY;
        
        // Add resizing class
        chatbotWindow.classList.add('resizing');
    });
    
    // Handle mouse move for dragging and resizing
    document.addEventListener('mousemove', function(e) {
        if (isDragging) {
            // Make sure the chatbot window is visible
            if (chatbotWindow.style.display !== 'flex') {
                chatbotWindow.style.display = 'flex';
            }
            
            // Calculate new position
            const newX = e.clientX - dragOffsetX;
            const newY = e.clientY - dragOffsetY;
            
            // Set the new position, ensuring it stays within the viewport
            const maxX = window.innerWidth - chatbotWindow.offsetWidth;
            const maxY = window.innerHeight - chatbotWindow.offsetHeight;
            
            // Make sure we're using absolute positioning during dragging
            chatbotWindow.style.position = 'fixed';
            chatbotWindow.style.right = 'auto'; // Remove the default right positioning
            chatbotWindow.style.bottom = 'auto'; // Remove the default bottom positioning
            
            chatbotWindow.style.left = `${Math.max(0, Math.min(maxX, newX))}px`;
            chatbotWindow.style.top = `${Math.max(0, Math.min(maxY, newY))}px`;
        }
        
        // Handle resizing
        if (isResizing) {
            // Calculate new dimensions
            const deltaX = e.clientX - originalX;
            const deltaY = e.clientY - originalY;
            
            let newWidth = originalWidth + deltaX;
            let newHeight = originalHeight + deltaY;
            
            // Get min/max constraints from CSS or set defaults
            const minWidth = parseInt(getComputedStyle(chatbotWindow).minWidth) || 250;
            const minHeight = parseInt(getComputedStyle(chatbotWindow).minHeight) || 300;
            const maxWidth = window.innerWidth * 0.9;
            const maxHeight = window.innerHeight * 0.8;
            
            // Apply constraints
            newWidth = Math.max(minWidth, Math.min(maxWidth, newWidth));
            newHeight = Math.max(minHeight, Math.min(maxHeight, newHeight));
            
            // Apply new dimensions
            chatbotWindow.style.width = `${newWidth}px`;
            chatbotWindow.style.height = `${newHeight}px`;
            
            // Ensure the window is displayed
            if (chatbotWindow.style.display !== 'flex') {
                chatbotWindow.style.display = 'flex';
            }
        }
    });
    
    // Handle mouse up to stop all operations
    document.addEventListener('mouseup', function() {
        if (isDragging) {
            isDragging = false;
            chatbotWindow.classList.remove('dragging');
            
            // Make sure the window remains visible after dragging
            if (chatbotWindow.style.display !== 'flex') {
                chatbotWindow.style.display = 'flex';
            }
        }
        
        // Handle resizing end
        if (isResizing) {
            isResizing = false;
            chatbotWindow.classList.remove('resizing');
            
            if (chatbotWindow.style.display !== 'flex') {
                chatbotWindow.style.display = 'flex';
            }
            
            // Adjust scrolling after resize is complete
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        }
    });
    
    // Ensure the window doesn't disappear if the mouse leaves the window
    document.addEventListener('mouseleave', function() {
        if (isDragging) {
            isDragging = false;
            chatbotWindow.classList.remove('dragging');
            
            // Make sure the window remains visible
            if (chatbotWindow.style.display !== 'flex') {
                chatbotWindow.style.display = 'flex';
            }
        }
        
        if (isResizing) {
            isResizing = false;
            chatbotWindow.classList.remove('resizing');
            
            // Make sure the window remains visible
            if (chatbotWindow.style.display !== 'flex') {
                chatbotWindow.style.display = 'flex';
            }
        }
    });
    
    // Update chatbot state when window is resized
    window.addEventListener('resize', function() {
        // Only adjust if the chatbot is visible
        if (chatbotWindow.style.display === 'flex') {
            // Get current dimensions
            const width = parseInt(chatbotWindow.style.width) || 350;
            const height = parseInt(chatbotWindow.style.height) || 450;
            
            // Get viewport constraints
            const maxWidth = window.innerWidth * 0.9;
            const maxHeight = window.innerHeight * 0.8;
            
            // Adjust if necessary
            if (width > maxWidth) {
                chatbotWindow.style.width = `${maxWidth}px`;
            }
            
            if (height > maxHeight) {
                chatbotWindow.style.height = `${maxHeight}px`;
            }
            
            // Check if window position is now off-screen
            const rect = chatbotWindow.getBoundingClientRect();
            if (rect.right > window.innerWidth) {
                chatbotWindow.style.left = `${window.innerWidth - rect.width}px`;
            }
            
            if (rect.bottom > window.innerHeight) {
                chatbotWindow.style.top = `${window.innerHeight - rect.height}px`;
            }
        }
    });
    
    // Send message when button is clicked
    chatbotSend.addEventListener('click', sendMessage);
    
    // Send message when Enter key is pressed (but allow Shift+Enter for new lines)
    chatbotInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
        
        // Auto-resize textarea
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
    
    // Function to send a message
    function sendMessage() {
        const message = chatbotInput.value.trim();
        if (!message) return;
        
        // Add user message to chat
        addMessageToChat('user', message);
        
        // Clear input
        chatbotInput.value = '';
        chatbotInput.style.height = 'auto';
        
        // Call API (for now just simulate a response)
        processMessage(message);
    }
    
    // Function to add a message to the chat
    function addMessageToChat(sender, message) {
        const messageElement = document.createElement('div');
        messageElement.className = sender === 'user' ? 'user-message' : 'ai-message';
        
        const messageContent = document.createElement('div');
        messageContent.className = sender === 'user' ? 'user-message-content' : 'ai-message-content';
        
        // Check if this is an error message with HTML content
        if (sender === 'ai' && message.includes('<details>')) {
            messageContent.innerHTML = message;
        } else {
            messageContent.textContent = message;
        }
        
        messageElement.appendChild(messageContent);
        chatbotMessages.appendChild(messageElement);
        
        // Scroll to the bottom
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }
    
    // Process the message
    function processMessage(message) {
        try {
            logDebug("Calling Mistral AI API with message:", message);
            callChatAPI(message);
        } catch (e) {
            logError("Failed to call API:", e);
            displayDetailedError(e);
            // Fallback to placeholder responses if API fails
            fallbackResponse(message);
        }
    }
    
    // Enhanced debug logging
    function logDebug(...args) {
        if (config.debugMode) {
            console.log(`[Chatbot Debug]`, ...args);
        } else {
            console.log(...args);
        }
    }
    
    // Enhanced error logging
    function logError(...args) {
        console.error(`[Chatbot Error]`, ...args);
    }
    
    // Display detailed error in chat
    function displayDetailedError(error, additionalInfo = {}) {
        if (!config.debugMode) {
            // Simple error message for non-debug mode
            addMessageToChat('ai', `Sorry, I encountered a problem: ${error.message}. Enable debug mode for more details.`);
            return;
        }
        
        // Create detailed error message with collapsible sections
        const errorTime = new Date().toISOString();
        const errorStack = error.stack || 'No stack trace available';
        const errorName = error.name || 'Unknown Error';
        const errorCode = error.code || 'No error code';
        
        let networkInfo = '';
        if (additionalInfo.status) {
            networkInfo = `
                <p><strong>Status:</strong> ${additionalInfo.status}</p>
                <p><strong>Status Text:</strong> ${additionalInfo.statusText || 'N/A'}</p>
                <p><strong>URL:</strong> ${additionalInfo.url || 'N/A'}</p>
                <p><strong>Response Type:</strong> ${additionalInfo.responseType || 'N/A'}</p>
            `;
        }
        
        const detailedMessage = `
            <div class="error-container">
                <p>❌ Error: ${error.message}</p>
                <details>
                    <summary>Technical Details (Click to expand)</summary>
                    <div class="error-details">
                        <p><strong>Time:</strong> ${errorTime}</p>
                        <p><strong>Type:</strong> ${errorName}</p>
                        <p><strong>Code:</strong> ${errorCode}</p>
                        ${networkInfo}
                        <details>
                            <summary>Stack Trace</summary>
                            <pre>${errorStack}</pre>
                        </details>
                        <p><em>You can share this information with support to help diagnose the issue.</em></p>
                    </div>
                </details>
            </div>
        `;
        
        addMessageToChat('ai', detailedMessage);
    }
    
    // Fallback response function in case API fails
    function fallbackResponse(message) {
        setTimeout(() => {
            const responses = [
                "Thanks for your message! When an API key is configured, I'll provide a more intelligent response.",
                "I understand you're asking about '" + message + "'. Please check back later when the AI is fully configured.",
                "This is a placeholder response. Soon, a real AI model will process your questions and provide helpful answers."
            ];
            
            const randomResponse = responses[Math.floor(Math.random() * responses.length)];
            addMessageToChat('ai', randomResponse);
        }, 1000);
    }
    
    // Function to call the chat API via our proxy
    async function callChatAPI(message, retryCount = 0) {
        let typingIndicator = null;
        
        try {
            // Add a typing indicator
            typingIndicator = document.createElement('div');
            typingIndicator.className = 'ai-message typing-indicator';
            typingIndicator.innerHTML = '<div class="typing-dots"><span></span><span></span><span></span></div>';
            chatbotMessages.appendChild(typingIndicator);
            
            // Debug log the full URL we're calling
            const fullUrl = config.apiEndpoint;
            logDebug("Making API request to Mistral AI via proxy at URL:", fullUrl);
            
            // Enhanced endpoint logging to help with debugging
            if (config.debugMode) {
                console.log("API URL structure:", {
                    window_location: window.location.toString(),
                    origin: window.location.origin,
                    pathname: window.location.pathname,
                    fullApiUrl: fullUrl
                });
            }
            
            // Diagnostics: Log connection status
            const connectionStatus = navigator.onLine ? 'online' : 'offline';
            logDebug(`Browser connection status: ${connectionStatus}`);
            
            // Start timestamp for performance measurement
            const startTime = performance.now();
            
            // FIXED: Simplified request to only send the message to the proxy
            // The proxy will handle all API-specific formatting
            const response = await fetch(fullUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    message: message
                })
            });
            
            // Calculate request duration
            const requestDuration = performance.now() - startTime;
            logDebug(`Request completed in ${requestDuration.toFixed(2)}ms`);
            
            // Network diagnostics info
            const networkInfo = {
                status: response.status,
                statusText: response.statusText,
                url: response.url,
                responseType: response.headers.get('content-type')
            };
            
            // Check if the response is OK
            if (!response.ok) {
                // If not a JSON response, get the text
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") === -1) {
                    const text = await response.text();
                    logError("Non-JSON response received:", text);
                    
                    // Create a more descriptive error
                    const error = new Error(`Server returned ${response.status}: ${response.statusText}`);
                    error.code = response.status;
                    error.responseText = text;
                    
                    throw error;
                }
                
                // Create a descriptive network error
                const error = new Error(`HTTP Error: ${response.status} ${response.statusText}`);
                error.code = response.status;
                throw error;
            }
            
            // Parse the response
            const data = await response.json();
            logDebug("API response data:", data);
            
            // Remove typing indicator
            if (typingIndicator && typingIndicator.parentNode) {
                chatbotMessages.removeChild(typingIndicator);
                typingIndicator = null;
            }
            
            // Check for errors in response
            if (data.error) {
                const apiError = new Error(`API error: ${data.error.message || JSON.stringify(data.error)}`);
                apiError.code = data.error.code || 'API_ERROR';
                apiError.apiError = data.error;
                throw apiError;
            }
            
            // Extract the assistant's response from Mistral API format
            let assistantMessage;
            if (data.choices && data.choices.length > 0 && data.choices[0].message) {
                assistantMessage = data.choices[0].message.content;
            } else {
                // Handle case where response format is unexpected
                logError("Unexpected API response format:", data);
                const formatError = new Error("Received unexpected response format from API");
                formatError.code = 'UNEXPECTED_FORMAT';
                formatError.data = data;
                throw formatError;
            }
            
            // Add the response to the chat
            addMessageToChat('ai', assistantMessage);
            
        } catch (error) {
            logError('Error calling chat API:', error);
            
            // Remove typing indicator if it exists
            if (typingIndicator && typingIndicator.parentNode) {
                chatbotMessages.removeChild(typingIndicator);
            }
            
            // Try to retry if we haven't exceeded retry attempts
            if (retryCount < config.retryAttempts) {
                logDebug(`Retrying API call (${retryCount + 1}/${config.retryAttempts})...`);
                // Add a brief message indicating a retry
                addMessageToChat('ai', `Network issue detected. Retrying... (${retryCount + 1}/${config.retryAttempts})`);
                
                // Wait before retrying
                await new Promise(resolve => setTimeout(resolve, config.retryDelay));
                return callChatAPI(message, retryCount + 1);
            }
            
            // Collect additional diagnostic information
            const diagnosticInfo = {
                browserInfo: navigator.userAgent,
                timestamp: new Date().toISOString(),
                online: navigator.onLine
            };
            
            // Show detailed error message with diagnostics
            displayDetailedError(error, diagnosticInfo);
        }
    }
    
    // Function to set the API key (can be called from outside)
    window.setChatbotApiKey = function(apiKey) {
        config.apiKey = apiKey;
        logDebug('API key has been set');
    };
    
    // Function to get diagnostic info (can be called from outside)
    window.getChatbotDiagnostics = function() {
        const diagnostics = {
            config: {
                apiEndpoint: config.apiEndpoint,
                model: config.model,
                debugMode: config.debugMode,
                retryAttempts: config.retryAttempts
            },
            browser: {
                userAgent: navigator.userAgent,
                language: navigator.language,
                platform: navigator.platform,
                online: navigator.onLine
            },
            timestamp: new Date().toISOString()
        };
        
        console.log('Chatbot diagnostics:', diagnostics);
        return diagnostics;
    };
});
