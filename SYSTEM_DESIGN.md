# AgroCare Farm E-Commerce System Design Documentation

## 1. Project Overview

**AgroCare Farm** is a modern, fully functional e-commerce platform designed for agricultural products in Ghana. The system connects smallholder farmers directly with consumers through AI-driven personalized nutrition recommendations and sustainable food delivery. The platform enables customers to browse, search, filter, and purchase fresh agricultural products including vegetables, fruits, fish, pork products, and meal kits.

### Key Objectives
- Provide a seamless online shopping experience for agricultural products
- Support local farmers through direct-to-consumer sales
- Offer AI-powered nutrition recommendations
- Enable subscription-based "Curiosity Box" service
- Process secure payments via Paystack integration
- Deliver order confirmations via email

---

## 2. Technology Stack

### Backend Technologies
- **PHP 7.4+**: Server-side scripting language for business logic
- **MySQL/MariaDB**: Relational database management system
- **PHPMailer**: Email service library for order confirmations
- **Paystack API**: Payment gateway integration for Ghana Cedis (GHS)

### Frontend Technologies
- **HTML5**: Markup language for structure
- **CSS3**: Styling with custom CSS and Bootstrap framework
- **JavaScript (ES6+)**: Client-side interactivity and AJAX requests
- **Bootstrap 5.3**: Responsive UI framework
- **Font Awesome 6.0**: Icon library

### Architecture Pattern
- **MVC (Model-View-Controller)**: Separation of concerns
  - **Models**: `classes/` - Data access and business logic
  - **Views**: `view/`, `admin/`, `login/` - Presentation layer
  - **Controllers**: `controllers/` - Request handling and coordination
  - **Actions**: `actions/` - API endpoints for AJAX requests

---

## 3. System Architecture

### Directory Structure

```
lab1_e/
├── actions/              # API endpoints (AJAX handlers)
│   ├── add_to_cart_action.php
│   ├── login_customer_action.php
│   ├── paystack_verify_payment.php
│   └── ...
├── admin/                # Admin panel pages
│   ├── product.php
│   ├── category.php
│   └── brand.php
├── classes/              # Data models and business logic
│   ├── product_class.php
│   ├── cart_class.php
│   ├── order_class.php
│   ├── customer_class.php
│   ├── paystack_class.php
│   └── email_service.php
├── controllers/          # Request controllers
│   ├── product_controller.php
│   ├── cart_controller.php
│   ├── payment_controller.php
│   └── ...
├── css/                  # Stylesheets
├── js/                   # JavaScript files
├── view/                 # Public-facing pages
│   ├── all_product.php
│   ├── cart.php
│   ├── checkout.php
│   └── order_history.php
├── settings/             # Configuration files
│   ├── db_class.php
│   ├── paystack_config.php
│   └── core.php
└── db/                   # Database schemas
    └── dbforlab.sql
```

### MVC Pattern Implementation

#### Models (Classes)
- **Product**: Manages product CRUD operations, product search
- **Cart**: Handles shopping cart operations (add, update, remove, empty)
- **Order**: Order creation, invoice generation, order details management
- **Customer**: User registration, authentication, profile management
- **PaystackPayment**: Payment transaction initialization and verification
- **EmailService**: Order confirmation email delivery

#### Controllers
- Bridge between actions and classes
- Handle business logic coordination
- Validate input and format responses

#### Views
- HTML templates with embedded PHP
- Bootstrap 5 for responsive design
- JavaScript for dynamic content loading

#### Actions
- RESTful API endpoints
- Return JSON responses
- Handle AJAX requests from frontend

---

## 4. Database Design

### Entity Relationship Overview

The database follows a relational model with the following key entities:

1. **customer**: User accounts (customers and admins)
2. **products**: Product catalog
3. **categories**: Product categories
4. **brands**: Product brands
5. **cart**: Shopping cart items (supports guest and authenticated users)
6. **orders**: Customer orders
7. **orderdetails**: Order line items
8. **payment**: Payment records

### Key Tables

#### customer
- `customer_id` (PK, AUTO_INCREMENT)
- `customer_name` (VARCHAR 100)
- `customer_email` (VARCHAR 50, UNIQUE)
- `customer_pass` (VARCHAR 150) - Hashed passwords
- `customer_country`, `customer_city`, `customer_contact`
- `user_role` (INT) - 1 = Admin, 2 = Customer

#### products
- `product_id` (PK, AUTO_INCREMENT)
- `product_cat` (FK → categories.cat_id)
- `product_brand` (FK → brands.brand_id)
- `product_title` (VARCHAR 200)
- `product_price` (DOUBLE)
- `product_desc` (VARCHAR 500)
- `product_image` (VARCHAR 100)
- `product_keywords` (VARCHAR 100)

#### cart
- `cart_id` (PK, AUTO_INCREMENT)
- `p_id` (FK → products.product_id)
- `ip_add` (VARCHAR 255) - Guest identifier
- `c_id` (FK → customer.customer_id, NULLABLE) - Authenticated user
- `qty` (INT)
- `created_at`, `updated_at` (DATETIME)

#### orders
- `order_id` (PK, AUTO_INCREMENT)
- `customer_id` (FK → customer.customer_id)
- `invoice_no` (VARCHAR 50) - Format: AGRO-YYYYMMDD-XXXXXX
- `order_date` (DATE)
- `order_status` (VARCHAR 100) - pending, completed, cancelled
- `total_amount` (DECIMAL 10,2)

#### orderdetails
- `order_id` (FK → orders.order_id)
- `product_id` (FK → products.product_id)
- `qty` (INT)
- `unit_price` (DECIMAL 10,2) - Price at time of purchase

#### payment
- `pay_id` (PK, AUTO_INCREMENT)
- `amt` (DOUBLE)
- `customer_id` (FK → customer.customer_id)
- `order_id` (FK → orders.order_id)
- `currency` (TEXT) - GHS, NGN, USD
- `payment_method` (VARCHAR 50) - Paystack, Simulated
- `payment_reference` (VARCHAR 100) - Transaction reference
- `payment_date` (DATE)

### Relationships

- **customer** → **orders** (1:N) - One customer can have many orders
- **orders** → **orderdetails** (1:N) - One order contains many line items
- **orderdetails** → **products** (N:1) - Many order items reference one product
- **cart** → **products** (N:1) - Cart items reference products
- **cart** → **customer** (N:1, optional) - Cart can belong to customer or be guest
- **products** → **categories** (N:1) - Products belong to categories
- **products** → **brands** (N:1) - Products belong to brands

### Constraints

- Foreign key constraints with CASCADE on delete/update for referential integrity
- Unique constraint on `customer_email`
- Primary keys on all main tables
- Indexes on foreign keys for performance

---

## 5. Key Features

### 5.1 User Authentication & Authorization

**Implementation**:
- Registration: `actions/register_customer_action.php`
- Login: `actions/login_customer_action.php`
- Logout: `logout.php`
- Session-based authentication
- Password hashing using PHP `password_hash()` and `password_verify()`
- Role-based access control (Admin vs Customer)

**Security Features**:
- SQL injection prevention via prepared statements
- Password hashing (bcrypt)
- Session management
- Email validation
- Input sanitization

### 5.2 Product Management

**Features**:
- Product CRUD operations (Admin only)
- Product search by keywords, category, brand
- Product filtering (category, price range, brand)
- Product image upload and management
- Product categorization and branding

**Implementation**:
- `classes/product_class.php` - Product data model
- `controllers/product_controller.php` - Product business logic
- `actions/product_actions.php` - Product API endpoints
- `admin/product.php` - Admin product management interface

### 5.3 Shopping Cart Management

**Features**:
- Add products to cart
- Update quantities
- Remove items
- Empty cart
- Guest cart support (IP-based)
- Cart persistence for logged-in users
- Automatic cart merge when guest logs in

**Implementation**:
- `classes/cart_class.php` - Cart data model
- `controllers/cart_controller.php` - Cart business logic
- `actions/add_to_cart_action.php`, `update_quantity_action.php`, etc.
- `view/cart.php` - Cart display page

### 5.4 Order Processing & Invoicing

**Features**:
- Order creation from cart
- Invoice number generation (AGRO-YYYYMMDD-XXXXXX format)
- Order status tracking (pending, completed, cancelled)
- Order details with product information
- Order history for customers
- Payment recording

**Implementation**:
- `classes/order_class.php` - Order data model
- `controllers/order_controller.php` - Order business logic
- `actions/process_checkout_action.php` - Checkout processing
- `view/order_history.php` - Customer order history

### 5.5 Payment Integration (Paystack)

**Features**:
- Paystack payment gateway integration
- Support for Ghana Cedis (GHS) currency
- Transaction initialization
- Payment verification
- Payment reference tracking
- Secure payment processing

**Implementation**:
- `classes/paystack_class.php` - Paystack API wrapper
- `controllers/payment_controller.php` - Payment processing
- `actions/paystack_verify_payment.php` - Payment verification
- `settings/paystack_config.php` - Paystack configuration

### 5.6 AI Chatbot (Nutrition Advisor)

**Features**:
- Rule-based AI chatbot for nutrition advice
- Free query limit (3 queries/day for free users)
- Premium upgrade path
- Personalized diet recommendations
- Product recommendations based on goals

**Implementation**:
- `js/ai_chatbot.js` - Chatbot frontend logic
- Floating chat widget on all pages
- Response generation based on keywords and patterns

### 5.7 Email Notifications

**Features**:
- Order confirmation emails
- HTML-formatted email templates
- Plain text fallback
- Product details in email
- Invoice information

**Implementation**:
- `classes/email_service.php` - Email service using PHPMailer
- Integrated into payment verification flow
- Sends emails after successful payment

---

## 6. API Endpoints

### Authentication Endpoints
- `actions/register_customer_action.php` - POST - Register new customer
- `actions/login_customer_action.php` - POST - Customer login
- `logout.php` - GET - Logout and destroy session

### Product Endpoints
- `actions/product_actions.php?action=get_all` - GET - Get all products
- `actions/product_actions.php?action=get_single&id={id}` - GET - Get single product
- `actions/product_actions.php?action=search&query={q}&category={cat}` - GET - Search products
- `actions/add_product_action.php` - POST - Add product (Admin)
- `actions/update_product_action.php` - POST - Update product (Admin)
- `actions/delete_product_action.php` - POST - Delete product (Admin)

### Cart Endpoints
- `actions/add_to_cart_action.php` - POST - Add item to cart
- `actions/update_quantity_action.php` - POST - Update cart item quantity
- `actions/remove_from_cart_action.php` - POST - Remove item from cart
- `actions/empty_cart_action.php` - POST - Empty entire cart
- `actions/get_cart_action.php` - GET - Get cart contents

### Order Endpoints
- `actions/process_checkout_action.php` - POST - Process checkout
- `actions/get_customer_orders_action.php` - GET - Get customer orders
- `actions/initialize_payment_action.php` - POST - Initialize Paystack payment
- `actions/paystack_verify_payment.php` - POST - Verify Paystack payment

### Category & Brand Endpoints
- `actions/fetch_category_action.php` - GET - Get all categories
- `actions/fetch_brand_action.php` - GET - Get all brands
- `actions/add_category_action.php` - POST - Add category (Admin)
- `actions/add_brand_action.php` - POST - Add brand (Admin)

---

## 7. Security Features

### 7.1 SQL Injection Prevention
- **Prepared Statements**: All database queries use prepared statements with parameter binding
- **Input Validation**: Server-side validation of all user inputs
- **Type Casting**: Explicit type casting for numeric inputs

### 7.2 Password Security
- **Hashing**: Passwords hashed using PHP `password_hash()` with bcrypt algorithm
- **Verification**: Password verification using `password_verify()`
- **No Plain Text Storage**: Passwords never stored in plain text

### 7.3 Session Management
- **Session-based Authentication**: User sessions managed via PHP sessions
- **Session Security**: Session data stored server-side
- **Logout**: Proper session destruction on logout
- **Role-based Access**: Admin and customer roles enforced

### 7.4 Input Sanitization
- **HTML Escaping**: `htmlspecialchars()` used for output
- **Email Validation**: `filter_var()` for email format validation
- **Trim and Validate**: Input trimming and validation before processing

### 7.5 File Upload Security
- **File Type Validation**: Only image files (JPEG, PNG, GIF, WebP) allowed
- **File Size Limits**: Maximum 5MB file size
- **Secure File Storage**: Files stored in organized directory structure
- **Path Validation**: Relative paths used to prevent directory traversal

### 7.6 Payment Security
- **Paystack Integration**: Secure payment processing via Paystack API
- **Payment Verification**: Server-side payment verification before order completion
- **Transaction References**: Unique transaction references for each payment
- **Amount Validation**: Payment amount verification against order total

---

## 8. Design Decisions

### 8.1 MVC Architecture
**Decision**: Implement MVC pattern for separation of concerns
**Rationale**: 
- Easier maintenance and testing
- Clear separation between data, logic, and presentation
- Scalable structure for future enhancements

### 8.2 Guest Cart Support
**Decision**: Support shopping carts for non-authenticated users
**Rationale**:
- Improve user experience
- Allow browsing before registration
- Automatic cart merge on login

### 8.3 Paystack Integration
**Decision**: Use Paystack for payment processing
**Rationale**:
- Native support for Ghana Cedis (GHS)
- Secure payment gateway
- Easy integration with existing PHP codebase

### 8.4 Email Service
**Decision**: Use PHPMailer for order confirmations
**Rationale**:
- Reliable email delivery
- HTML email support
- Easy configuration

### 8.5 Bootstrap Framework
**Decision**: Use Bootstrap 5.3 for UI
**Rationale**:
- Responsive design out of the box
- Consistent UI components
- Fast development
- Mobile-friendly

---

## 9. Future Enhancements

### Potential Improvements
1. **Advanced AI**: Machine learning-based product recommendations
2. **Inventory Management**: Stock tracking and low stock alerts
3. **Order Tracking**: Real-time order status updates
4. **Reviews & Ratings**: Customer product reviews
5. **Wishlist**: Save products for later
6. **Multi-language Support**: Support for multiple languages
7. **Mobile App**: Native mobile application
8. **Analytics Dashboard**: Sales and customer analytics
9. **Loyalty Program**: Points and rewards system
10. **Subscription Management**: Enhanced Curiosity Box subscription features

---

## 10. Deployment Considerations

### Server Requirements
- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache or Nginx web server
- mod_rewrite enabled (for clean URLs)
- SSL certificate (for secure payments)

### Configuration
- Database credentials in `settings/db_cred.php`
- Paystack API keys in `settings/paystack_config.php`
- Email settings in `classes/email_service.php`
- File upload permissions (755 or 777 for uploads directory)

### Security Checklist
- [ ] Change default database passwords
- [ ] Set secure Paystack API keys
- [ ] Configure email SMTP settings
- [ ] Enable HTTPS/SSL
- [ ] Set proper file permissions
- [ ] Regular database backups
- [ ] Keep PHP and dependencies updated

---

**Document Version**: 1.0  
**Last Updated**: November 2024  
**Author**: AgroCare Development Team

