/**
 * AI Chatbot Widget JavaScript
 * Handles the chat interface and communication with backend
 */

class ChatbotWidget {
    constructor(options = {}) {
        this.options = {
            position: options.position || 'bottom-right',
            primaryColor: options.primaryColor || '#667eea',
            title: options.title || 'Jewellery Assistant',
            welcomeMessage: options.welcomeMessage || null,
            context: options.context || 'home',
            productInfo: options.productInfo || null,
            apiEndpoint: options.apiEndpoint || 'actions/chat_action.php'
        };
        
        this.isOpen = false;
        this.isMinimized = false;
        this.conversationHistory = [];
        this.isTyping = false;
        
        this.init();
    }
    
    init() {
        this.createWidget();
        this.attachEventListeners();
        this.loadWelcomeMessage();
    }
    
    createWidget() {
        // Create widget container
        const widget = document.createElement('div');
        widget.id = 'chatbot-widget';
        widget.innerHTML = `
            <div class="chatbot-toggle" id="chatbot-toggle">
                <span class="chatbot-icon">💬</span>
                <span class="chatbot-badge" id="chatbot-badge" style="display: none;">1</span>
            </div>
            
            <div class="chatbot-container" id="chatbot-container">
                <div class="chatbot-header">
                    <div class="chatbot-header-info">
                        <span class="chatbot-avatar">💎</span>
                        <div>
                            <div class="chatbot-title">${this.options.title}</div>
                            <div class="chatbot-status">
                                <span class="status-dot"></span>
                                Online
                            </div>
                        </div>
                    </div>
                    <div class="chatbot-header-actions">
                        <button class="chatbot-minimize" id="chatbot-minimize">−</button>
                        <button class="chatbot-close" id="chatbot-close">×</button>
                    </div>
                </div>
                
                <div class="chatbot-messages" id="chatbot-messages">
                    <!-- Messages will be added here -->
                </div>
                
                <div class="chatbot-quick-replies" id="chatbot-quick-replies">
                    <!-- Quick replies will be added here -->
                </div>
                
                <div class="chatbot-input-container">
                    <input type="text" 
                           class="chatbot-input" 
                           id="chatbot-input" 
                           placeholder="Type your message..."
                           autocomplete="off">
                    <button class="chatbot-send" id="chatbot-send">
                        <span>➤</span>
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(widget);
        
        // Add styles
        this.addStyles();
    }
    
    addStyles() {
        const styles = document.createElement('style');
        styles.textContent = `
            #chatbot-widget {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
                position: fixed;
                ${this.options.position === 'bottom-right' ? 'right: 20px;' : 'left: 20px;'}
                bottom: 20px;
                z-index: 99999;
            }
            
            .chatbot-toggle {
                width: 60px;
                height: 60px;
                border-radius: 50%;
                background: linear-gradient(135deg, ${this.options.primaryColor} 0%, #764ba2 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
                transition: transform 0.3s, box-shadow 0.3s;
                position: relative;
            }
            
            .chatbot-toggle:hover {
                transform: scale(1.1);
                box-shadow: 0 6px 25px rgba(102, 126, 234, 0.5);
            }
            
            .chatbot-icon {
                font-size: 28px;
            }
            
            .chatbot-badge {
                position: absolute;
                top: -5px;
                right: -5px;
                background: #ef4444;
                color: white;
                font-size: 12px;
                font-weight: bold;
                width: 22px;
                height: 22px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .chatbot-container {
                display: none;
                flex-direction: column;
                width: 380px;
                height: 520px;
                background: white;
                border-radius: 16px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
                overflow: hidden;
                position: absolute;
                bottom: 80px;
                ${this.options.position === 'bottom-right' ? 'right: 0;' : 'left: 0;'}
            }
            
            .chatbot-container.open {
                display: flex;
                animation: chatbot-slide-up 0.3s ease-out;
            }
            
            .chatbot-container.minimized {
                height: 60px;
            }
            
            @keyframes chatbot-slide-up {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .chatbot-header {
                background: linear-gradient(135deg, ${this.options.primaryColor} 0%, #764ba2 100%);
                color: white;
                padding: 15px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .chatbot-header-info {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            
            .chatbot-avatar {
                font-size: 28px;
            }
            
            .chatbot-title {
                font-weight: 600;
                font-size: 16px;
            }
            
            .chatbot-status {
                font-size: 12px;
                opacity: 0.9;
                display: flex;
                align-items: center;
                gap: 5px;
            }
            
            .status-dot {
                width: 8px;
                height: 8px;
                background: #4ade80;
                border-radius: 50%;
            }
            
            .chatbot-header-actions {
                display: flex;
                gap: 8px;
            }
            
            .chatbot-header-actions button {
                background: rgba(255, 255, 255, 0.2);
                border: none;
                color: white;
                width: 28px;
                height: 28px;
                border-radius: 50%;
                cursor: pointer;
                font-size: 18px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: background 0.2s;
            }
            
            .chatbot-header-actions button:hover {
                background: rgba(255, 255, 255, 0.3);
            }
            
            .chatbot-messages {
                flex: 1;
                overflow-y: auto;
                padding: 15px;
                display: flex;
                flex-direction: column;
                gap: 12px;
                background: #f8fafc;
            }
            
            .chatbot-message {
                max-width: 85%;
                padding: 12px 16px;
                border-radius: 16px;
                font-size: 14px;
                line-height: 1.5;
                animation: message-fade-in 0.3s ease-out;
            }
            
            @keyframes message-fade-in {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .chatbot-message.user {
                background: linear-gradient(135deg, ${this.options.primaryColor} 0%, #764ba2 100%);
                color: white;
                align-self: flex-end;
                border-bottom-right-radius: 4px;
            }
            
            .chatbot-message.bot {
                background: white;
                color: #333;
                align-self: flex-start;
                border-bottom-left-radius: 4px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            }
            
            .chatbot-message.bot .message-content {
                white-space: pre-wrap;
            }
            
            .chatbot-typing {
                display: flex;
                gap: 4px;
                padding: 12px 16px;
                background: white;
                border-radius: 16px;
                align-self: flex-start;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            }
            
            .chatbot-typing span {
                width: 8px;
                height: 8px;
                background: #94a3b8;
                border-radius: 50%;
                animation: typing-bounce 1.4s ease-in-out infinite;
            }
            
            .chatbot-typing span:nth-child(2) {
                animation-delay: 0.2s;
            }
            
            .chatbot-typing span:nth-child(3) {
                animation-delay: 0.4s;
            }
            
            @keyframes typing-bounce {
                0%, 80%, 100% {
                    transform: translateY(0);
                }
                40% {
                    transform: translateY(-6px);
                }
            }
            
            .chatbot-quick-replies {
                padding: 10px 15px;
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                background: #f8fafc;
                border-top: 1px solid #e2e8f0;
                max-height: 100px;
                overflow-y: auto;
            }
            
            .chatbot-quick-replies:empty {
                display: none;
            }
            
            .quick-reply-btn {
                background: white;
                border: 1px solid ${this.options.primaryColor};
                color: ${this.options.primaryColor};
                padding: 8px 14px;
                border-radius: 20px;
                font-size: 13px;
                cursor: pointer;
                transition: all 0.2s;
                white-space: nowrap;
            }
            
            .quick-reply-btn:hover {
                background: ${this.options.primaryColor};
                color: white;
            }
            
            .chatbot-input-container {
                padding: 15px;
                background: white;
                border-top: 1px solid #e2e8f0;
                display: flex;
                gap: 10px;
            }
            
            .chatbot-input {
                flex: 1;
                padding: 12px 16px;
                border: 1px solid #e2e8f0;
                border-radius: 24px;
                font-size: 14px;
                outline: none;
                transition: border-color 0.2s;
            }
            
            .chatbot-input:focus {
                border-color: ${this.options.primaryColor};
            }
            
            .chatbot-send {
                width: 44px;
                height: 44px;
                border: none;
                background: linear-gradient(135deg, ${this.options.primaryColor} 0%, #764ba2 100%);
                color: white;
                border-radius: 50%;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: transform 0.2s, box-shadow 0.2s;
            }
            
            .chatbot-send:hover {
                transform: scale(1.05);
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            }
            
            .chatbot-send:disabled {
                opacity: 0.6;
                cursor: not-allowed;
                transform: none;
            }
            
            .chatbot-send span {
                font-size: 18px;
            }
            
            @media (max-width: 480px) {
                .chatbot-container {
                    width: calc(100vw - 40px);
                    height: 70vh;
                    bottom: 70px;
                    right: -10px;
                    left: -10px;
                    margin: 0 auto;
                }
                
                #chatbot-widget {
                    right: 10px;
                }
            }
        `;
        
        document.head.appendChild(styles);
    }
    
    attachEventListeners() {
        // Toggle button
        document.getElementById('chatbot-toggle').addEventListener('click', () => {
            this.toggle();
        });
        
        // Close button
        document.getElementById('chatbot-close').addEventListener('click', () => {
            this.close();
        });
        
        // Minimize button
        document.getElementById('chatbot-minimize').addEventListener('click', () => {
            this.minimize();
        });
        
        // Send button
        document.getElementById('chatbot-send').addEventListener('click', () => {
            this.sendMessage();
        });
        
        // Input enter key
        document.getElementById('chatbot-input').addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });
    }
    
    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }
    
    open() {
        const container = document.getElementById('chatbot-container');
        container.classList.add('open');
        container.classList.remove('minimized');
        this.isOpen = true;
        this.isMinimized = false;
        document.getElementById('chatbot-badge').style.display = 'none';
        document.getElementById('chatbot-input').focus();
    }
    
    close() {
        const container = document.getElementById('chatbot-container');
        container.classList.remove('open');
        this.isOpen = false;
    }
    
    minimize() {
        const container = document.getElementById('chatbot-container');
        container.classList.toggle('minimized');
        this.isMinimized = !this.isMinimized;
    }
    
    async loadWelcomeMessage() {
        try {
            const response = await fetch(`${this.getBasePath()}${this.options.apiEndpoint}?action=welcome`);
            const data = await response.json();
            
            if (data.status === 'success') {
                this.addMessage(data.message, 'bot');
                if (data.quick_replies) {
                    this.showQuickReplies(data.quick_replies);
                }
            }
        } catch (error) {
            console.error('Error loading welcome message:', error);
            this.addMessage("Hello! 👋 Welcome to our jewellery store. How can I help you today?", 'bot');
        }
    }
    
    getBasePath() {
        // Determine base path based on current location
        const path = window.location.pathname;
        if (path.includes('/view/') || path.includes('/admin/') || path.includes('/login/')) {
            return '../';
        }
        return '';
    }
    
    async sendMessage() {
        const input = document.getElementById('chatbot-input');
        const message = input.value.trim();
        
        if (!message || this.isTyping) return;
        
        // Clear input
        input.value = '';
        
        // Add user message
        this.addMessage(message, 'user');
        
        // Add to history
        this.conversationHistory.push({
            role: 'user',
            content: message
        });
        
        // Show typing indicator
        this.showTyping();
        
        try {
            const formData = new FormData();
            formData.append('action', 'chat');
            formData.append('message', message);
            formData.append('history', JSON.stringify(this.conversationHistory.slice(-10))); // Last 10 messages
            formData.append('context', this.options.context);
            
            if (this.options.productInfo) {
                formData.append('product', JSON.stringify(this.options.productInfo));
            }
            
            const response = await fetch(`${this.getBasePath()}${this.options.apiEndpoint}`, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            // Hide typing indicator
            this.hideTyping();
            
            if (data.status === 'success') {
                this.addMessage(data.message, 'bot');
                
                // Add to history
                this.conversationHistory.push({
                    role: 'assistant',
                    content: data.message
                });
                
                // Show quick replies
                if (data.quick_replies) {
                    this.showQuickReplies(data.quick_replies);
                }
            } else {
                this.addMessage(data.message || "Sorry, I couldn't process your message. Please try again.", 'bot');
            }
        } catch (error) {
            console.error('Chat error:', error);
            this.hideTyping();
            this.addMessage("Sorry, there was an error. Please try again.", 'bot');
        }
    }
    
    addMessage(text, type) {
        const messagesContainer = document.getElementById('chatbot-messages');
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `chatbot-message ${type}`;
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content';
        contentDiv.textContent = text;
        
        messageDiv.appendChild(contentDiv);
        messagesContainer.appendChild(messageDiv);
        
        // Scroll to bottom
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        
        // Show badge if closed
        if (!this.isOpen && type === 'bot') {
            document.getElementById('chatbot-badge').style.display = 'flex';
        }
    }
    
    showTyping() {
        this.isTyping = true;
        const messagesContainer = document.getElementById('chatbot-messages');
        
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chatbot-typing';
        typingDiv.id = 'chatbot-typing-indicator';
        typingDiv.innerHTML = '<span></span><span></span><span></span>';
        
        messagesContainer.appendChild(typingDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    hideTyping() {
        this.isTyping = false;
        const typingIndicator = document.getElementById('chatbot-typing-indicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }
    
    showQuickReplies(replies) {
        const container = document.getElementById('chatbot-quick-replies');
        container.innerHTML = '';
        
        replies.forEach(reply => {
            const btn = document.createElement('button');
            btn.className = 'quick-reply-btn';
            btn.textContent = reply;
            btn.addEventListener('click', () => {
                document.getElementById('chatbot-input').value = reply;
                this.sendMessage();
                container.innerHTML = '';
            });
            container.appendChild(btn);
        });
    }
    
    // Update context (useful when navigating pages)
    setContext(context, productInfo = null) {
        this.options.context = context;
        this.options.productInfo = productInfo;
    }
}

// Auto-initialize if data attribute is present
document.addEventListener('DOMContentLoaded', () => {
    const autoInit = document.querySelector('[data-chatbot-init]');
    if (autoInit) {
        window.chatbot = new ChatbotWidget({
            context: autoInit.dataset.context || 'home',
            productInfo: autoInit.dataset.product ? JSON.parse(autoInit.dataset.product) : null
        });
    }
});
