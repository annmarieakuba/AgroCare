# AgroCare Farm - User Flow Documentation

This document describes the key user journeys and workflows in the AgroCare Farm e-commerce platform.

---

## 1. Customer Registration Flow

### Path: Guest → Registered Customer

**Steps:**
1. User clicks "Register" link in navigation (available on all pages)
2. User is redirected to `login/register.php`
3. User fills out registration form:
   - Full Name
   - Email Address
   - Password
   - Country
   - City
   - Contact Number
4. User clicks "Register" button
5. System validates input:
   - Checks for empty required fields
   - Validates email format
   - Checks if email already exists
6. If validation passes:
   - Password is hashed using bcrypt
   - Customer record is created in database
   - User role is set to 2 (Customer)
   - Success message is displayed
7. User is redirected to login page or automatically logged in

**Alternative Paths:**
- **Email Already Exists**: Error message displayed, user can try different email
- **Validation Failure**: Error messages shown for each invalid field
- **Already Logged In**: User redirected to home page (unless admin creating new account)

**Pages Involved:**
- `login/register.php` - Registration form
- `actions/register_customer_action.php` - Registration processing
- `controllers/customer_controller.php` - Business logic
- `classes/customer_class.php` - Data model

---

## 2. Customer Login Flow

### Path: Guest → Authenticated Customer

**Steps:**
1. User clicks "Login" link in navigation
2. User is redirected to `login/login.php`
3. User enters:
   - Email address
   - Password
4. User clicks "Login" button
5. System validates credentials:
   - Checks if email exists in database
   - Verifies password using `password_verify()`
6. If credentials are valid:
   - Session is created with customer data:
     - `customer_id`
     - `customer_name`
     - `customer_email`
     - `user_role`
     - `customer_country`, `customer_city`, `customer_contact`
   - Guest cart is merged with customer cart (if exists)
   - Success message is displayed
   - User is redirected to home page or previous page
7. If credentials are invalid:
   - Error message displayed
   - User can retry login

**Alternative Paths:**
- **Invalid Email**: "No account found with this email address"
- **Invalid Password**: "Invalid email or password"
- **Already Logged In**: User redirected to home page

**Pages Involved:**
- `login/login.php` - Login form
- `actions/login_customer_action.php` - Login processing
- `controllers/customer_controller.php` - Business logic
- `classes/cart_class.php` - Cart merging logic

---

## 3. Shopping Flow (Authenticated User)

### Path: Browse → Search/Filter → Add to Cart → View Cart → Checkout → Payment → Order Confirmation

**Step 1: Browse Products**
1. User navigates to "All Products" page (`view/all_product.php`)
2. Products are loaded dynamically via AJAX
3. User can browse all available products

**Step 2: Search/Filter Products**
1. User can search by:
   - Keywords (product title, description, keywords)
   - Category (dropdown filter)
   - Brand (dropdown filter)
2. User enters search query and/or selects filters
3. User clicks "Search" button
4. Results are displayed on `view/product_search_result.php`
5. Products matching criteria are shown

**Step 3: Add to Cart**
1. User clicks "Add to Cart" button on product card
2. JavaScript sends AJAX request to `actions/add_to_cart_action.php`
3. System:
   - Validates product exists
   - Checks if item already in cart (updates quantity if exists)
   - Adds item to cart (linked to customer_id)
   - Returns updated cart data
4. Cart badge in navigation updates with item count
5. Success message displayed

**Step 4: View Cart**
1. User clicks "Cart" link in navigation
2. User is redirected to `view/cart.php`
3. Cart items are loaded via AJAX
4. User can:
   - View all items in cart
   - Update quantities (using +/- buttons)
   - Remove items
   - See subtotal
5. User clicks "Proceed to Checkout" button

**Step 5: Checkout**
1. User is redirected to `view/checkout.php`
2. Order summary is displayed:
   - List of items
   - Quantities
   - Prices
   - Subtotal
3. User reviews order
4. User clicks "Pay with Paystack" button

**Step 6: Payment Processing**
1. System initializes Paystack payment:
   - Creates payment reference
   - Calculates total amount
   - Sends request to Paystack API
2. User is redirected to Paystack payment page
3. User enters payment details (card, bank, etc.)
4. User completes payment
5. Paystack redirects to `view/paystack_callback.php`
6. System verifies payment:
   - Calls Paystack API to verify transaction
   - Checks payment amount matches order total
   - Creates order in database
   - Generates invoice number (AGRO-YYYYMMDD-XXXXXX)
   - Records payment
   - Updates order status to "completed"
   - Sends order confirmation email
7. User is redirected to `view/payment_success.php`

**Step 7: Order Confirmation**
1. User sees payment success page
2. Order details displayed:
   - Order ID
   - Invoice Number
   - Payment Reference
   - Total Amount
   - Number of Items
3. User receives email confirmation with order details
4. User can:
   - Continue shopping
   - View order history

**Pages Involved:**
- `view/all_product.php` - Product listing
- `view/product_search_result.php` - Search results
- `view/cart.php` - Shopping cart
- `view/checkout.php` - Checkout page
- `actions/add_to_cart_action.php` - Add to cart
- `actions/initialize_payment_action.php` - Payment initialization
- `actions/paystack_verify_payment.php` - Payment verification
- `view/payment_success.php` - Order confirmation
- `classes/email_service.php` - Email sending

---

## 4. Guest Shopping Flow

### Path: Browse → Add to Cart (Guest) → Login/Register → Cart Merge → Checkout

**Step 1: Browse as Guest**
1. User browses products without logging in
2. User can search and filter products
3. User clicks "Add to Cart" on a product

**Step 2: Add to Cart (Guest)**
1. System creates guest cart using:
   - IP address or session ID as identifier
   - `c_id` set to NULL
2. Item is added to cart
3. Cart badge updates

**Step 3: View Cart (Guest)**
1. User views cart
2. System prompts user to login for checkout
3. User clicks "Login" or "Register"

**Step 4: Login/Register**
1. User completes login or registration
2. System automatically merges guest cart with customer cart:
   - Items from guest cart are transferred to customer cart
   - Duplicate items have quantities combined
   - Guest cart items are removed
3. User is redirected back to cart or checkout

**Step 4: Checkout (Now Authenticated)**
1. User proceeds with checkout as authenticated customer
2. Rest of flow follows authenticated shopping flow

**Pages Involved:**
- `view/all_product.php` - Product listing
- `view/cart.php` - Cart (with login prompt)
- `login/login.php` or `login/register.php` - Authentication
- `classes/cart_class.php` - Cart merging logic

---

## 5. Order Management Flow

### Path: View Orders → View Order Details → Track Status

**Step 1: View Order History**
1. User clicks "My Orders" in user dropdown menu
2. User is redirected to `view/order_history.php`
3. System fetches all orders for the customer
4. Orders are displayed in chronological order (newest first)
5. Each order card shows:
   - Order ID
   - Invoice Number
   - Order Date
   - Order Status (Completed, Pending, Cancelled)
   - Total Amount
   - Number of Items
   - Preview of first 3 items

**Step 2: View Order Details**
1. User clicks "View Details" button on an order card
2. Modal opens with full order details:
   - Complete order information
   - All items with images
   - Quantities and prices
   - Payment information (method, reference)
   - Total amount
3. User can close modal

**Step 3: Track Order Status**
1. Order status is displayed on order card:
   - **Completed**: Order has been paid and processed
   - **Pending**: Order is awaiting payment
   - **Cancelled**: Order has been cancelled
2. Status is updated automatically during payment processing

**Pages Involved:**
- `view/order_history.php` - Order history page
- `actions/get_customer_orders_action.php` - Fetch orders
- `js/order_history.js` - Frontend logic

---

## 6. Admin Flow

### Path: Admin Login → Manage Products/Categories/Brands

**Step 1: Admin Login**
1. Admin user logs in with admin credentials (user_role = 1)
2. Admin sees additional menu items in dropdown

**Step 2: Access Admin Panel**
1. Admin clicks on admin menu items:
   - "Manage Categories" → `admin/category.php`
   - "Manage Brands" → `admin/brand.php`
   - "Manage Products" → `admin/product.php`

**Step 3: Manage Products**
1. Admin views product list
2. Admin can:
   - **Add Product**: Click "Add Product" button
     - Fill form: title, price, category, brand, description, image, keywords
     - Upload product image
     - Submit form
   - **Edit Product**: Click "Edit" on product row
     - Modify product details
     - Update or keep existing image
     - Save changes
   - **Delete Product**: Click "Delete" on product row
     - Confirm deletion
     - Product removed from database

**Step 4: Manage Categories**
1. Admin views category list
2. Admin can add, edit, or delete categories

**Step 5: Manage Brands**
1. Admin views brand list
2. Admin can add, edit, or delete brands

**Pages Involved:**
- `admin/product.php` - Product management
- `admin/category.php` - Category management
- `admin/brand.php` - Brand management
- `actions/add_product_action.php`, `update_product_action.php`, etc.

---

## 7. AI Chatbot Flow

### Path: Open Chat → Ask Question → Get Recommendation → Premium Upgrade (if needed)

**Step 1: Open Chat**
1. User sees floating chat icon (bottom-right corner) on any page
2. User clicks chat icon
3. Chat window opens
4. First-time users see introduction popup:
   - "Meet Your AI Dietitian!"
   - Explains chatbot capabilities

**Step 2: Ask Question**
1. User types nutrition-related question in chat input
2. Examples:
   - "What foods are high in protein?"
   - "I want to build muscle, what should I eat?"
   - "What's a good meal plan for weight loss?"
3. User clicks send or presses Enter

**Step 3: Get Recommendation**
1. System checks user's query count:
   - Free users: 3 queries per day
   - Premium users: Unlimited queries
2. If within limit:
   - Chatbot analyzes question using keyword matching
   - Generates response with:
     - Nutrition advice
     - Product recommendations
     - Links to relevant products
3. Response is displayed in chat
4. Query count is incremented

**Step 4: Premium Upgrade (if needed)**
1. If user exceeds free query limit:
   - Message displayed: "You've reached your daily limit"
   - Option to upgrade to Premium shown
   - Link to `premium.php` provided
2. User can:
   - Upgrade to Premium membership
   - Wait until next day for free queries to reset

**Pages Involved:**
- `js/ai_chatbot.js` - Chatbot logic (present on all pages)
- `premium.php` - Premium membership page

---

## 8. Error Handling Flows

### Common Error Scenarios

**1. Payment Failure**
- **Scenario**: Payment verification fails
- **Flow**: 
  1. User redirected to checkout with error message
  2. Order is not created
  3. Cart remains intact
  4. User can retry payment

**2. Cart Empty on Checkout**
- **Scenario**: User tries to checkout with empty cart
- **Flow**:
  1. Error message displayed
  2. User redirected to cart or product page

**3. Product Out of Stock (Future)**
- **Scenario**: Product unavailable
- **Flow**: Currently not implemented, but would show "Out of Stock" message

**4. Session Expired**
- **Scenario**: User session expires during checkout
- **Flow**:
  1. User redirected to login page
  2. Cart preserved (guest cart or customer cart)
  3. User can continue after login

**5. Network Error**
- **Scenario**: AJAX request fails
- **Flow**:
  1. Error message displayed to user
  2. User can retry action
  3. Loading states shown during requests

---

## 9. User Flow Diagrams (Text Representation)

### Complete Shopping Journey

```
[Home Page]
    ↓
[Browse Products] → [Search/Filter] → [View Product Details]
    ↓                                              ↓
[Add to Cart] ← ← ← ← ← ← ← ← ← ← ← ← ← ← ← ← ← ←
    ↓
[View Cart] → [Update Quantities] → [Remove Items]
    ↓
[Proceed to Checkout]
    ↓
[Review Order] → [Pay with Paystack]
    ↓
[Payment Processing]
    ↓
[Payment Verification]
    ↓
[Order Confirmation] → [Email Sent]
    ↓
[View Order History]
```

### Guest to Customer Conversion

```
[Guest Browsing]
    ↓
[Add Items to Guest Cart]
    ↓
[View Cart] → [Prompted to Login]
    ↓
[Login/Register]
    ↓
[Cart Automatically Merged]
    ↓
[Continue Shopping as Customer]
```

---

## 10. Key Decision Points

### User Decision Points

1. **Registration vs. Login**: User chooses to create account or use existing
2. **Guest vs. Authenticated**: User can shop as guest or create account
3. **Payment Method**: Currently only Paystack, but extensible
4. **Product Selection**: User chooses products based on search/filter results
5. **Cart Management**: User decides quantities and items to purchase
6. **Premium Upgrade**: User chooses to upgrade for unlimited AI queries

### System Decision Points

1. **Cart Merge**: System automatically merges guest cart on login
2. **Payment Verification**: System verifies payment before order creation
3. **Query Limit**: System enforces free user query limits
4. **Role-based Access**: System shows/hides admin features based on role
5. **Error Handling**: System provides appropriate error messages and recovery paths

---

**Document Version**: 1.0  
**Last Updated**: November 2024  
**Author**: AgroCare Development Team

