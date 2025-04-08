/**
 * AI Chatbot JavaScript Functionality
 * Handles the chat UI and prepares for future API integration
 */

document.addEventListener('DOMContentLoaded', function() {
    // Chat Elements
    const chatbotIcon = document.getElementById('chatbot-icon');
    const chatbotWindow = document.getElementById('chatbot-window');
    const chatbotMinimize = document.getElementById('chatbot-minimize');
    const chatbotClose = document.getElementById('chatbot-close');
    const chatbotInput = document.getElementById('chatbot-input');
    const chatbotSend = document.getElementById('chatbot-send');
    const chatbotMessages = document.getElementById('chatbot-messages');
    const chatbotHeader = document.querySelector('.ai-chatbot-header');
    const chatbotResizeHandle = document.getElementById('chatbot-resize-handle');
    
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
    
    // Configuration (for future use)
    const config = {
        apiKey: '', // This will be set later
        apiEndpoint: 'https://api.example.com/chat', // Replace with actual API endpoint
        model: 'gpt-3.5-turbo' // Example model name
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
    
    // Minimize chat window
    chatbotMinimize.addEventListener('click', function() {
        chatbotWindow.style.display = 'none';
        chatbotIcon.style.display = 'flex';
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
        messageContent.textContent = message;
        
        messageElement.appendChild(messageContent);
        chatbotMessages.appendChild(messageElement);
        
        // Scroll to the bottom
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }
    
    // Process the message (will be replaced with actual API call later)
    function processMessage(message) {
        // For now, just simulate a response
        setTimeout(() => {
            const responses = [
                "Thanks for your message! When an API key is configured, I'll provide a more intelligent response.",
                "I understand you're asking about '" + message + "'. Please check back later when the AI is fully configured.",
                "This is a placeholder response. Soon, a real AI model will process your questions and provide helpful answers."
            ];
            
            const randomResponse = responses[Math.floor(Math.random() * responses.length)];
            addMessageToChat('ai', randomResponse);
        }, 1000);
        
        // This is where the API call will go in the future
        // callChatAPI(message);
    }
    
    // Function to call the chat API (for future implementation)
    async function callChatAPI(message) {
        // Check if API key is configured
        if (!config.apiKey) {
            addMessageToChat('ai', "The AI service is not yet configured. Please add an API key.");
            return;
        }
        
        try {
            // Add a typing indicator
            const typingIndicator = document.createElement('div');
            typingIndicator.className = 'ai-message typing-indicator';
            typingIndicator.innerHTML = '<div class="typing-dots"><span></span><span></span><span></span></div>';
            chatbotMessages.appendChild(typingIndicator);
            
            // This is where the actual API call would go
            // const response = await fetch(config.apiEndpoint, {
            //     method: 'POST',
            //     headers: {
            //         'Content-Type': 'application/json',
            //         'Authorization': `Bearer ${config.apiKey}`
            //     },
            //     body: JSON.stringify({
            //         model: config.model,
            //         messages: [
            //             { role: "system", content: "You are a helpful assistant." },
            //             { role: "user", content: message }
            //         ]
            //     })
            // });
            
            // Remove typing indicator
            chatbotMessages.removeChild(typingIndicator);
            
            // For now, just add a placeholder message
            addMessageToChat('ai', "This is where the AI response would appear.");
            
        } catch (error) {
            console.error('Error calling chat API:', error);
            addMessageToChat('ai', "Sorry, there was an error communicating with the AI service.");
        }
    }
    
    // Function to set the API key (can be called from outside)
    window.setChatbotApiKey = function(apiKey) {
        config.apiKey = apiKey;
        console.log('API key has been set');
        
        // You could also store this in localStorage if needed
        // localStorage.setItem('chatbotApiKey', apiKey);
    };
});
