# AgroCare Farm - E-Commerce Platform

A modern, fully functional e-commerce website for AgroCare - a Ghana-based agricultural technology platform that connects smallholder farmers directly with consumers through AI-driven personalized nutrition and sustainable food delivery.

## 🌱 Project Overview

AgroCare Farm is an e-commerce platform designed to:
- Connect farmers directly with consumers
- Provide AI-powered nutrition recommendations
- Offer subscription-based "Curiosity Box" service
- Process secure payments via Paystack
- Support local agriculture in Ghana

## 📚 Documentation

This project includes comprehensive documentation:

- **[SYSTEM_DESIGN.md](SYSTEM_DESIGN.md)** - Complete system design documentation including:
  - Technology stack and architecture
  - Database design and schema
  - Key features and API endpoints
  - Security features
  - Design decisions

- **[USER_FLOWS.md](USER_FLOWS.md)** - Detailed user flow documentation covering:
  - Customer registration and login flows
  - Shopping and checkout processes
  - Guest shopping experience
  - Order management
  - Admin workflows
  - AI chatbot interactions

## 🚀 Features

### Core E-Commerce Features
- ✅ User registration, login, and authentication
- ✅ Product search and filtering
- ✅ Shopping cart management (guest and authenticated)
- ✅ Order processing and invoicing
- ✅ Payment integration (Paystack - Ghana Cedis)
- ✅ Order history and tracking
- ✅ Email order confirmations

### Advanced Features
- ✅ AI Chatbot for nutrition advice
- ✅ Premium membership tiers
- ✅ Curiosity Box subscription service
- ✅ Responsive design (mobile-friendly)
- ✅ Admin product management

## 🛠️ Technology Stack

- **Backend**: PHP 7.4+, MySQL
- **Frontend**: HTML5, CSS3, JavaScript (ES6+), Bootstrap 5.3
- **Payment**: Paystack API
- **Email**: PHPMailer
- **Architecture**: MVC Pattern

## 📁 Project Structure

```
lab1_e/
├── actions/          # API endpoints (AJAX handlers)
├── admin/           # Admin panel pages
├── classes/         # Data models and business logic
├── controllers/     # Request controllers
├── css/            # Stylesheets
├── js/             # JavaScript files
├── view/           # Public-facing pages
├── settings/       # Configuration files
└── db/             # Database schemas
```

## 🔧 Setup Instructions

1. **Database Setup**
   - Import `db/dbforlab.sql` into your MySQL database
   - Run `db/update_checkout_schema.sql` for additional schema updates
   - Update database credentials in `settings/db_cred.php`

2. **Configuration**
   - Configure Paystack API keys in `settings/paystack_config.php`
   - Set up email service in `classes/email_service.php`
   - Ensure `uploads/` directory has write permissions (755 or 777)

3. **Server Requirements**
   - PHP 7.4 or higher
   - MySQL 5.7+ or MariaDB 10.3+
   - Apache or Nginx web server
   - mod_rewrite enabled

## 📖 Getting Started

1. Clone or download the project
2. Set up your web server (XAMPP, WAMP, or similar)
3. Configure database connection
4. Import database schema
5. Configure Paystack and email settings
6. Access the application via your web browser

## 🔐 Default Admin Access

To create an admin account, you can use the admin setup tools or manually set `user_role = 1` in the database for a customer account.

## 📝 Key Pages

- **Home**: `index.php` - Main landing page with featured products
- **Products**: `view/all_product.php` - Product catalog
- **Cart**: `view/cart.php` - Shopping cart
- **Checkout**: `view/checkout.php` - Checkout and payment
- **Order History**: `view/order_history.php` - Customer order history
- **Admin**: `admin/product.php` - Product management (admin only)

## 🎯 Key Features Documentation

For detailed information about:
- **System Architecture**: See [SYSTEM_DESIGN.md](SYSTEM_DESIGN.md)
- **User Workflows**: See [USER_FLOWS.md](USER_FLOWS.md)

## 📧 Support

For questions or issues, please refer to the documentation files or contact the development team.

---

**Version**: 1.0  
**Last Updated**: November 2024  
**License**: Educational Project
