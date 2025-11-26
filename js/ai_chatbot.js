// AI Chatbot Dietitian Widget
(function() {
    'use strict';

    const ChatbotWidget = {
        isOpen: false,
        messageCount: 0,
        maxFreeQueries: 3,

        init() {
            this.createWidget();
            this.attachEventListeners();
        },

        createWidget() {
            const widgetHTML = `
                <div id="aiChatbotContainer" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; font-family: Arial, sans-serif;">
                    <!-- Chat Button -->
                    <button id="chatbotToggle" style="
                        width: 60px;
                        height: 60px;
                        border-radius: 50%;
                        background: linear-gradient(135deg, #2d5016, #4a7c59);
                        color: white;
                        border: none;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 24px;
                        transition: all 0.3s ease;
                    ">
                        <i class="fas fa-robot"></i>
                    </button>

                    <!-- Chat Window -->
                    <div id="chatbotWindow" style="
                        display: none;
                        position: absolute;
                        bottom: 80px;
                        right: 0;
                        width: 350px;
                        height: 500px;
                        background: white;
                        border-radius: 15px;
                        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                        flex-direction: column;
                        overflow: hidden;
                    ">
                        <!-- Header -->
                        <div style="
                            background: linear-gradient(135deg, #2d5016, #4a7c59);
                            color: white;
                            padding: 15px;
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                        ">
                            <div>
                                <h5 style="margin: 0; font-size: 16px;">
                                    <i class="fas fa-robot me-2"></i>AI Dietitian
                                </h5>
                                <small style="opacity: 0.9;">Your personalized nutrition assistant</small>
                            </div>
                            <button id="chatbotClose" style="
                                background: transparent;
                                border: none;
                                color: white;
                                font-size: 20px;
                                cursor: pointer;
                                padding: 5px;
                            ">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <!-- Messages Area -->
                        <div id="chatbotMessages" style="
                            flex: 1;
                            padding: 20px;
                            overflow-y: auto;
                            background: #f8f9fa;
                        ">
                            <div class="chat-message bot-message" style="
                                background: white;
                                padding: 12px 15px;
                                border-radius: 15px;
                                margin-bottom: 15px;
                                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                            ">
                                <p style="margin: 0; color: #333;">
                                    <i class="fas fa-robot me-2" style="color: #2d5016;"></i>
                                    Hello! I'm your AI Dietitian. I can help you:
                                </p>
                                <ul style="margin: 10px 0 0 0; padding-left: 20px; color: #666;">
                                    <li>Plan personalized meals</li>
                                    <li>Track nutrition goals</li>
                                    <li>Find perfect proteins for your needs</li>
                                    <li>Answer nutrition questions</li>
                                </ul>
                                <p style="margin: 10px 0 0 0; color: #666; font-size: 12px;">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Ask me anything about nutrition!
                                </p>
                            </div>
                        </div>

                        <!-- Input Area -->
                        <div style="
                            padding: 15px;
                            background: white;
                            border-top: 1px solid #e0e0e0;
                        ">
                            <div style="display: flex; gap: 10px;">
                                <input type="text" id="chatbotInput" placeholder="Ask about nutrition..." style="
                                    flex: 1;
                                    padding: 10px 15px;
                                    border: 2px solid #e0e0e0;
                                    border-radius: 25px;
                                    outline: none;
                                    font-size: 14px;
                                ">
                                <button id="chatbotSend" style="
                                    width: 40px;
                                    height: 40px;
                                    border-radius: 50%;
                                    background: linear-gradient(135deg, #2d5016, #4a7c59);
                                    color: white;
                                    border: none;
                                    cursor: pointer;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                ">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                            <div id="queryLimitWarning" style="
                                margin-top: 10px;
                                padding: 8px;
                                background: #fff3cd;
                                border-radius: 5px;
                                font-size: 12px;
                                color: #856404;
                                display: none;
                            ">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Free queries limit reached. <a href="premium.php" style="color: #2d5016; font-weight: bold;">Upgrade to Premium</a> for unlimited access.
                            </div>
                        </div>
                    </div>
                </div>
            `;

            const container = document.getElementById('aiChatbotWidget');
            if (container) {
                container.innerHTML = widgetHTML;
            } else {
                const div = document.createElement('div');
                div.innerHTML = widgetHTML;
                document.body.appendChild(div);
            }

            // Add styles
            const style = document.createElement('style');
            style.textContent = `
                #chatbotToggle:hover {
                    transform: scale(1.1);
                    box-shadow: 0 6px 16px rgba(0,0,0,0.4);
                }
                .chat-message {
                    animation: fadeIn 0.3s ease;
                }
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(10px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .user-message {
                    background: linear-gradient(135deg, #2d5016, #4a7c59) !important;
                    color: white !important;
                    margin-left: auto;
                    max-width: 80%;
                }
                .bot-message {
                    background: white;
                    color: #333;
                    max-width: 85%;
                }
                #chatbotMessages::-webkit-scrollbar {
                    width: 6px;
                }
                #chatbotMessages::-webkit-scrollbar-track {
                    background: #f1f1f1;
                }
                #chatbotMessages::-webkit-scrollbar-thumb {
                    background: #2d5016;
                    border-radius: 3px;
                }
            `;
            document.head.appendChild(style);
        },

        attachEventListeners() {
            const toggle = document.getElementById('chatbotToggle');
            const close = document.getElementById('chatbotClose');
            const send = document.getElementById('chatbotSend');
            const input = document.getElementById('chatbotInput');

            if (toggle) {
                toggle.addEventListener('click', () => this.toggleChat());
            }

            if (close) {
                close.addEventListener('click', () => this.closeChat());
            }

            if (send) {
                send.addEventListener('click', () => this.sendMessage());
            }

            if (input) {
                input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.sendMessage();
                    }
                });
            }
        },

        toggleChat() {
            const window = document.getElementById('chatbotWindow');
            if (window) {
                this.isOpen = !this.isOpen;
                window.style.display = this.isOpen ? 'flex' : 'none';
                
                if (this.isOpen) {
                    const input = document.getElementById('chatbotInput');
                    if (input) {
                        setTimeout(() => input.focus(), 100);
                    }
                }
            }
        },

        closeChat() {
            this.isOpen = false;
            const window = document.getElementById('chatbotWindow');
            if (window) {
                window.style.display = 'none';
            }
        },

        sendMessage() {
            const input = document.getElementById('chatbotInput');
            const warning = document.getElementById('queryLimitWarning');
            
            if (!input || !input.value.trim()) return;

            const message = input.value.trim();
            input.value = '';

            // Check query limit for non-premium users
            const isPremium = this.checkPremiumStatus();
            if (!isPremium && this.messageCount >= this.maxFreeQueries) {
                if (warning) {
                    warning.style.display = 'block';
                }
                this.addMessage('I\'ve reached my free query limit. Please upgrade to Premium for unlimited access!', 'bot');
                return;
            }

            this.addMessage(message, 'user');
            this.messageCount++;

            // Simulate AI response (in production, this would call an API)
            setTimeout(() => {
                const response = this.generateResponse(message);
                this.addMessage(response, 'bot');
            }, 1000);
        },

        addMessage(text, type) {
            const messages = document.getElementById('chatbotMessages');
            if (!messages) return;

            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-message ${type}-message`;
            messageDiv.style.cssText = type === 'user' 
                ? 'background: linear-gradient(135deg, #2d5016, #4a7c59); color: white; padding: 12px 15px; border-radius: 15px; margin-bottom: 15px; margin-left: auto; max-width: 80%;'
                : 'background: white; color: #333; padding: 12px 15px; border-radius: 15px; margin-bottom: 15px; max-width: 85%; box-shadow: 0 2px 5px rgba(0,0,0,0.1);';
            
            messageDiv.innerHTML = `<p style="margin: 0;">${this.escapeHtml(text)}</p>`;
            messages.appendChild(messageDiv);
            messages.scrollTop = messages.scrollHeight;
        },

        generateResponse(message) {
            const lowerMessage = message.toLowerCase();
            
            // Simple rule-based responses (in production, use AI/ML API)
            if (lowerMessage.includes('protein') || lowerMessage.includes('meat') || lowerMessage.includes('fish')) {
                return 'Great question! For protein, I recommend our fresh fish and pork products. Fish is excellent for omega-3 fatty acids, while pork provides complete protein. Would you like recommendations based on your dietary goals?';
            }
            
            if (lowerMessage.includes('diet') || lowerMessage.includes('meal') || lowerMessage.includes('nutrition')) {
                return 'I can help you create a personalized meal plan! Our Curiosity Box includes personalized protein portions and recipes. What are your health goals - muscle building, weight management, or balanced nutrition?';
            }
            
            if (lowerMessage.includes('vegetable') || lowerMessage.includes('fruit') || lowerMessage.includes('fresh')) {
                return 'We offer a wide variety of fresh vegetables and fruits from local farmers in Ghana. Our products include maize, cassava, leafy greens, and seasonal fruits. All products are farm-fresh and fair to farmers!';
            }
            
            if (lowerMessage.includes('price') || lowerMessage.includes('cost') || lowerMessage.includes('expensive')) {
                return 'Our products are priced fairly to support both farmers and consumers. The Curiosity Box starts at ₵80/month, and we offer discounts for subscribers. Premium members get 15% off all purchases!';
            }
            
            if (lowerMessage.includes('delivery') || lowerMessage.includes('ship') || lowerMessage.includes('deliver')) {
                return 'We offer fast delivery to your doorstep in Accra and surrounding areas. Premium members get free delivery on all orders, while Curiosity Box subscribers get free delivery on subscription boxes.';
            }
            
            if (lowerMessage.includes('hello') || lowerMessage.includes('hi') || lowerMessage.includes('hey')) {
                return 'Hello! I\'m here to help with your nutrition questions. You can ask me about meal planning, protein recommendations, product information, or anything related to healthy eating!';
            }
            
            // Default response
            return 'That\'s an interesting question! I can help you with meal planning, nutrition advice, product recommendations, and more. Could you provide more details about what you\'d like to know?';
        },

        checkPremiumStatus() {
            // In production, check user's premium status from session/API
            // For now, return false (free user)
            return false;
        },

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => ChatbotWidget.init());
    } else {
        ChatbotWidget.init();
    }

    // Show introduction on first visit
    if (!localStorage.getItem('chatbot_intro_shown')) {
        setTimeout(() => {
            // Check if SweetAlert2 is available
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '<i class="fas fa-robot fa-2x text-primary mb-3"></i>Meet Your AI Dietitian!',
                    html: `
                        <div class="text-center">
                            <p class="lead mb-3">I can help you plan meals, track nutrition, and find the perfect proteins for your goals.</p>
                            <p class="text-muted">Would you like to try it?</p>
                        </div>
                    `,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-comments me-2"></i>Yes, Let\'s Chat!',
                    cancelButtonText: '<i class="fas fa-times me-2"></i>Maybe Later',
                    confirmButtonColor: '#2d5016',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true,
                    allowOutsideClick: true,
                    allowEscapeKey: true,
                    customClass: {
                        popup: 'swal2-popup-custom'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        ChatbotWidget.toggleChat();
                    }
                    localStorage.setItem('chatbot_intro_shown', 'true');
                });
            } else {
                // Fallback if SweetAlert2 is not loaded (shouldn't happen, but just in case)
                const intro = confirm('Meet Your AI Dietitian! I can help you plan meals, track nutrition, and find the perfect proteins for your goals. Would you like to try it?');
                if (intro) {
                    ChatbotWidget.toggleChat();
                }
                localStorage.setItem('chatbot_intro_shown', 'true');
            }
        }, 3000);
    }
})();

