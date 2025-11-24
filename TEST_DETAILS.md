# AgroCare Farm - Test Details & Credentials

This document contains all test credentials, test data, and testing instructions for the AgroCare Farm e-commerce platform.

---

## 🔐 Admin Test Credentials

### Default Admin Account
To create or reset the admin account, run: `quick_admin_setup.php` in your browser.

**Admin Login Details:**
- **Email**: `admin@gmail.com`
- **Password**: `admin123`
- **Role**: Admin (user_role = 1)

**Access Admin Panel:**
1. Login with admin credentials
2. Click on your name in the navigation (top right)
3. You'll see admin menu items:
   - Manage Categories
   - Manage Brands
   - Manage Products

**Alternative Admin Setup:**
You can also manually create an admin by:
1. Registering a regular account
2. Updating the database: `UPDATE customer SET user_role = 1 WHERE customer_email = 'your_email@example.com'`

---

## 💳 Paystack Test Payment Details

### Test API Keys (Already Configured)
The system is currently configured with Paystack **TEST** keys:

- **Public Key**: `pk_test_ca492b9787289153c69d2c9757c7a81babc52639`
- **Secret Key**: `sk_test_c931cd7fde5b564318dc920028a8c3e16409163a`
- **Currency**: GHS (Ghana Cedis - ₵)
- **Environment**: Test Mode

### Test Card Numbers (Paystack Test Cards)

For testing payments, use these Paystack test card numbers:

#### ✅ Successful Payment Cards

**Card Number**: `4084084084084081`
- **CVV**: Any 3 digits (e.g., `408`)
- **Expiry**: Any future date (e.g., `12/25`)
- **PIN**: Any 4 digits (e.g., `0000`)
- **OTP**: `123456` (when prompted)
- **Result**: Payment will be successful

**Card Number**: `5060666666666666666`
- **CVV**: Any 3 digits
- **Expiry**: Any future date
- **PIN**: Any 4 digits
- **OTP**: `123456`
- **Result**: Payment will be successful

#### ❌ Failed Payment Cards

**Card Number**: `5060666666666666667`
- **Result**: Payment will be declined
- **Use Case**: Testing error handling

**Card Number**: `5060666666666666668`
- **Result**: Payment will be declined
- **Use Case**: Testing error handling

#### ⚠️ Insufficient Funds Card

**Card Number**: `5060666666666666669`
- **Result**: Insufficient funds error
- **Use Case**: Testing insufficient funds scenario

### Test Bank Account (Bank Transfer)

For bank transfer testing:
- **Account Number**: Use any test account number
- **Bank**: Select any bank
- **OTP**: `123456`

### Testing Payment Flow

1. **Add products to cart**
2. **Proceed to checkout**
3. **Click "Pay with Paystack"**
4. **On Paystack payment page:**
   - Enter test card number: `4084084084084081`
   - Enter any CVV (e.g., `408`)
   - Enter any future expiry date (e.g., `12/25`)
   - Enter any PIN (e.g., `0000`)
   - Enter OTP: `123456`
5. **Payment will be processed successfully**
6. **You'll be redirected to payment success page**
7. **Order confirmation email will be sent**

---

## 👤 Test Customer Accounts

### Creating Test Customers

You can create test customer accounts through the registration form:

1. Go to: `login/register.php`
2. Fill in the registration form:
   - **Full Name**: Test User
   - **Email**: test@example.com (use any email)
   - **Password**: test123 (or any password)
   - **Country**: Ghana
   - **City**: Accra
   - **Contact**: 0241234567
3. Click "Register"
4. Login with the credentials

### Sample Test Customer Accounts

You can create multiple test accounts for different scenarios:

**Customer 1:**
- Email: `customer1@test.com`
- Password: `test123`
- Role: Customer (user_role = 2)

**Customer 2:**
- Email: `customer2@test.com`
- Password: `test123`
- Role: Customer (user_role = 2)

---

## 🗄️ Database Test Data

### Sample Products

After setting up the database, you can add test products via the admin panel:

1. Login as admin
2. Go to "Manage Products"
3. Click "Add Product"
4. Fill in product details:
   - **Title**: Maize
   - **Price**: 0.80
   - **Category**: Crops
   - **Brand**: AgroCare Farm
   - **Description**: Fresh maize from local farmers
   - **Image**: Upload product image
   - **Keywords**: maize, corn, fresh

### Sample Categories

Default categories (add via admin panel):
- Crops
- Vegetables
- Livestock
- Fruits
- Fish

### Sample Brands

Default brands (add via admin panel):
- AgroCare Farm
- AgroCare

---

## 🧪 Testing Scenarios

### 1. User Registration Test
- **Test**: Register a new customer
- **Expected**: Account created, can login
- **Page**: `login/register.php`

### 2. User Login Test
- **Test**: Login with valid credentials
- **Expected**: Session created, redirected to home
- **Page**: `login/login.php`

### 3. Product Search Test
- **Test**: Search for products by keyword
- **Expected**: Relevant products displayed
- **Page**: `view/product_search_result.php`

### 4. Add to Cart Test
- **Test**: Add product to cart (as guest and logged in)
- **Expected**: Item added, cart count updates
- **Page**: `view/all_product.php`

### 5. Cart Management Test
- **Test**: Update quantities, remove items
- **Expected**: Cart updates correctly
- **Page**: `view/cart.php`

### 6. Checkout Test
- **Test**: Proceed to checkout with items in cart
- **Expected**: Order summary displayed
- **Page**: `view/checkout.php`

### 7. Payment Test
- **Test**: Complete payment with test card
- **Expected**: Payment successful, order created, email sent
- **Cards**: Use test card `4084084084084081`
- **Page**: Paystack payment page → `view/payment_success.php`

### 8. Order History Test
- **Test**: View order history after placing order
- **Expected**: Orders displayed with details
- **Page**: `view/order_history.php`

### 9. Admin Product Management Test
- **Test**: Add, edit, delete products
- **Expected**: Products managed successfully
- **Page**: `admin/product.php`
- **Credentials**: admin@gmail.com / admin123

### 10. AI Chatbot Test
- **Test**: Ask nutrition questions
- **Expected**: Responses generated (3 free queries/day)
- **Location**: Floating chat icon (bottom-right on all pages)

---

## 🔧 Setup & Configuration

### Database Setup
1. Import `db/dbforlab.sql`
2. Run `db/update_checkout_schema.sql`
3. Update credentials in `settings/db_cred.php`

### Paystack Configuration
- Test keys are already configured in `settings/paystack_config.php`
- For production, replace with live keys from Paystack dashboard

### Email Configuration
- Email service configured in `classes/email_service.php`
- Currently uses PHP `mail()` function
- For production, configure SMTP settings

### File Uploads
- Ensure `uploads/` directory has write permissions (755 or 777)
- Run `setup_uploads.php` or `fix_uploads.php` to set up

---

## 📝 Test Checklist

### Functional Testing
- [ ] User registration works
- [ ] User login works
- [ ] Product search works
- [ ] Add to cart works (guest and authenticated)
- [ ] Cart management works (update, remove)
- [ ] Checkout process works
- [ ] Payment processing works (test cards)
- [ ] Order creation works
- [ ] Order history displays correctly
- [ ] Email confirmation sent
- [ ] Admin product management works
- [ ] Admin category management works
- [ ] Admin brand management works
- [ ] AI chatbot responds correctly

### UI/UX Testing
- [ ] Responsive design works on mobile
- [ ] Navigation works correctly
- [ ] Forms validate input
- [ ] Error messages display correctly
- [ ] Success messages display correctly
- [ ] Loading states show during AJAX requests

### Security Testing
- [ ] SQL injection prevention (prepared statements)
- [ ] Password hashing works
- [ ] Session management works
- [ ] Admin access restricted
- [ ] File upload validation works

---

## 🐛 Common Test Issues & Solutions

### Issue: Payment not working
**Solution**: 
- Verify Paystack test keys are correct
- Check that callback URL is accessible
- Ensure currency is set to GHS

### Issue: Email not sending
**Solution**:
- Check PHP mail configuration
- For production, configure SMTP in `classes/email_service.php`
- Check server logs for errors

### Issue: Images not uploading
**Solution**:
- Run `fix_uploads.php` to set permissions
- Ensure `uploads/` directory exists and is writable
- Check file size limits in PHP configuration

### Issue: Cannot login as admin
**Solution**:
- Run `quick_admin_setup.php` to create/reset admin
- Verify `user_role = 1` in database
- Check session is working

### Issue: Cart not persisting
**Solution**:
- Check session is enabled
- Verify database connection
- Check cart table exists

---

## 🔗 Useful Test URLs

- **Home**: `http://localhost/lab1_e/index.php`
- **Products**: `http://localhost/lab1_e/view/all_product.php`
- **Cart**: `http://localhost/lab1_e/view/cart.php`
- **Checkout**: `http://localhost/lab1_e/view/checkout.php`
- **Order History**: `http://localhost/lab1_e/view/order_history.php`
- **Login**: `http://localhost/lab1_e/login/login.php`
- **Register**: `http://localhost/lab1_e/login/register.php`
- **Admin Products**: `http://localhost/lab1_e/admin/product.php`
- **Admin Setup**: `http://localhost/lab1_e/quick_admin_setup.php`
- **Fix Uploads**: `http://localhost/lab1_e/fix_uploads.php`

---

## 📞 Paystack Test Support

For Paystack test mode issues:
- **Paystack Test Dashboard**: https://dashboard.paystack.com/#/test
- **Test Mode**: All transactions are simulated, no real money is charged
- **Test Cards**: Use the test card numbers provided above
- **Test Webhooks**: Configure in Paystack dashboard for testing

---

**Document Version**: 1.0  
**Last Updated**: November 2024  
**Environment**: Test/Development

