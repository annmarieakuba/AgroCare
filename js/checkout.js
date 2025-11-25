// Checkout flow script with Paystack integration (Redirect Flow)
(function () {
    if (!window.CartAPI) {
        console.warn('CartAPI is not available. Ensure cart.js is loaded before checkout.js');
        return;
    }

    const basePath = window.APP_BASE_PATH || '/';

    const CheckoutAPI = {
        async initializePayment(email) {
            const response = await fetch(`${basePath}actions/paystack_init_transaction.php`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email: email })
            });

            const text = await response.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('Invalid JSON response:', text.substring(0, 500));
                throw new Error('Server returned invalid response. Please check console for details.');
            }

            if (data.status !== 'success') {
                throw new Error(data.message || 'Failed to initialize payment');
            }
            return data;
        }
    };

    const CheckoutUI = {
        async init() {
            this.container = document.getElementById('checkoutItemsContainer');
            this.summarySubtotal = document.getElementById('checkoutSubtotal');
            this.summaryCount = document.getElementById('checkoutItemCount');
            this.payButton = document.getElementById('payWithPaystackBtn');
            this.resultContainer = document.getElementById('checkoutResult');
            this.feedback = document.getElementById('checkoutFeedback');
            this.currencyInput = document.getElementById('checkoutCurrency');
            this.paymentMethodInput = document.getElementById('checkoutPaymentMethod');
            this.loadingOverlay = document.getElementById('checkoutLoadingState');

            this.bindEvents();
            await this.loadSummary();
        },
        bindEvents() {
            if (this.payButton) {
                this.payButton.addEventListener('click', async (event) => {
                    event.preventDefault();
                    if (this.payButton.disabled) return;
                    await this.handlePaystackPayment();
                });
            }
        },
        async loadSummary() {
            console.log('Loading cart summary...');
            this.setLoading(true);
            try {
                console.log('Calling CartAPI.fetchCart()...');
                const data = await CartAPI.fetchCart();
                console.log('Cart data received:', data);
                const cart = data?.cart || { items: [], summary: {} };
                console.log('Cart items:', cart.items?.length || 0);
                this.renderSummary(cart);
                if (!cart.items || cart.items.length === 0) {
                    this.disableCheckout('Your cart is empty. Add items before checking out.');
                }
            } catch (error) {
                console.error('Error loading cart summary:', error);
                this.renderError(error.message || 'Unable to load your cart. Please try again later.');
                this.disableCheckout('Unable to load your cart. Please try again later.');
            } finally {
                this.setLoading(false);
            }
        },
        renderSummary(cartData) {
            const items = cartData.items || [];
            const summary = cartData.summary || {};

            if (this.container) {
                if (items.length === 0) {
                    this.container.innerHTML = `
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No items to checkout</h5>
                            <p class="text-muted">Please add products to your cart.</p>
                        </div>
                    `;
                } else {
                    this.container.innerHTML = items.map(item => `
                        <div class="checkout-item border-bottom py-3">
                            <div class="row g-3 align-items-center">
                                <div class="col-3 col-md-2">
                                    ${item.product_image
                                        ? `<img src="${window.APP_BASE_PATH || '/'}${item.product_image}" alt="${item.product_title}" class="img-fluid rounded">`
                                        : `<div class="bg-light text-center rounded py-4">
                                            <i class="fas fa-image fa-2x text-muted"></i>
                                          </div>`
                                    }
                                </div>
                                <div class="col-9 col-md-6">
                                    <h6 class="mb-1">${item.product_title}</h6>
                                    <p class="text-muted small mb-0">Quantity: ${item.qty}</p>
                                </div>
                                <div class="col-12 col-md-4 text-md-end">
                                    <div class="text-muted small">Line Total</div>
                                    <div class="fw-semibold">${CartAPI.formatCurrency(item.line_total)}</div>
                                </div>
                            </div>
                        </div>
                    `).join('');
                }
            }

            if (this.summarySubtotal) {
                this.summarySubtotal.textContent = CartAPI.formatCurrency(summary.subtotal || 0);
            }
            if (this.summaryCount) {
                this.summaryCount.textContent = summary.total_items || 0;
            }
        },
        async handlePaystackPayment() {
            try {
                this.setLoading(true);
                this.togglePayButton(true);

                // Ensure SweetAlert2 is loaded
                if (typeof Swal === 'undefined') {
                    console.error('SweetAlert2 is not loaded!');
                    this.setLoading(false);
                    this.togglePayButton(false);
                    this.showFeedback('SweetAlert2 library is not loaded. Please refresh the page.', 'danger');
                    return;
                }

                console.log('Using SweetAlert2 for email input...');

                // Use SweetAlert for email input - NO FALLBACK TO PROMPT
                const result = await Swal.fire({
                    title: '<i class="fas fa-envelope me-2"></i>Payment Email Required',
                    html: '<p class="mb-3">Please enter your email address for payment processing:</p>',
                    input: 'email',
                    inputLabel: 'Email address',
                    inputPlaceholder: 'Enter your email address',
                    inputValue: '',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-credit-card me-2"></i>Continue to Payment',
                    cancelButtonText: '<i class="fas fa-times me-2"></i>Cancel',
                    confirmButtonColor: '#2d5016',
                    cancelButtonColor: '#6c757d',
                    allowOutsideClick: false,
                    allowEscapeKey: true,
                    inputValidator: (value) => {
                        if (!value) {
                            return 'You need to enter your email address!';
                        }
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(value)) {
                            return 'Please enter a valid email address!';
                        }
                    },
                    customClass: {
                        popup: 'swal2-popup-custom',
                        input: 'swal2-input-custom'
                    }
                });

                const email = result.value;

                if (!email) {
                    this.showFeedback('Payment cancelled.', 'info');
                    this.setLoading(false);
                    this.togglePayButton(false);
                    return;
                }

                // Initialize payment
                this.showFeedback('Initializing payment...', 'info');
                console.log('Initializing payment for email:', email);
                
                const paymentData = await CheckoutAPI.initializePayment(email);
                console.log('Payment initialization response:', paymentData);

                if (!paymentData.authorization_url) {
                    console.error('No authorization URL in response:', paymentData);
                    throw new Error('Invalid payment initialization response. Please try again.');
                }

                // Redirect to Paystack
                this.showFeedback('Redirecting to payment gateway...', 'info');
                console.log('Redirecting to:', paymentData.authorization_url);
                window.location.href = paymentData.authorization_url;
            } catch (error) {
                console.error('Paystack payment error:', error);
                console.error('Error details:', {
                    message: error.message,
                    stack: error.stack
                });
                const errorMsg = error.message || 'Failed to initialize payment. Please check your console for details.';
                this.showFeedback(errorMsg, 'danger');
                this.setLoading(false);
                this.togglePayButton(false);
            }
        },
        renderError(message) {
            if (this.container) {
                this.container.innerHTML = `
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>${message}
                    </div>
                `;
            }
        },
        disableCheckout(message) {
            if (this.payButton) {
                this.payButton.disabled = true;
            }
            this.showFeedback(message, 'warning');
        },
        showFeedback(message, type = 'info') {
            if (!this.feedback || !message) return;
            this.feedback.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
        },
        togglePayButton(isLoading) {
            if (!this.payButton) return;
            this.payButton.disabled = isLoading;
            this.payButton.innerHTML = isLoading
                ? `<span class="spinner-border spinner-border-sm me-2"></span>Processing...`
                : `<i class="fas fa-credit-card me-2"></i>Pay with Paystack`;
        },
        setLoading(isLoading) {
            if (!this.loadingOverlay) return;
            this.loadingOverlay.style.display = isLoading ? 'flex' : 'none';
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        console.log('Checkout page loaded');
        console.log('CartAPI available:', typeof window.CartAPI !== 'undefined');
        
        if (document.getElementById('checkoutItemsContainer')) {
            console.log('Initializing checkout UI...');
            CheckoutUI.init();
        } else {
            console.error('checkoutItemsContainer not found!');
        }
    });
})();
