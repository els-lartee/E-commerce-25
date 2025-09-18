# Customer Registration Implementation Plan

## 1. Rename and update classes/user_class.php to customer_class.php
- [x] Change class name to Customer
- [x] Update createUser to include country, city parameters
- [x] Add edit_customer method (update customer details)
- [x] Add delete_customer method
- [x] Add check_email_exists method for uniqueness

## 2. Rename and update controllers/user_controller.php to customer_controller.php
- [x] Change function names to register_customer_ctr, edit_customer_ctr, delete_customer_ctr, check_email_ctr

## 3. Rename and update actions/register_user_action.php to register_customer_action.php
- [x] Update includes and function calls
- [x] Add handling for country, city in POST data

## 4. Update js/register.js
- [x] Add country, city fields validation
- [x] Add regex for email (valid email format) and phone (digits, length)
- [x] Add async check for email uniqueness before submission
- [x] Add loading spinner on register button
- [x] Validate field lengths (e.g., name 100, email 50, pass 150, contact 15, country/city 30)

## 5. Update login/register.php
- [x] Add country, city input fields
- [x] Ensure no form action (handled by JS)

## 6. Update login/login.php
- [x] No changes needed (simple form as required)

## 7. Update index.php
- [x] Already has menu tray with Register/Login buttons

## Followup steps
- [ ] Test registration with all fields
- [ ] Verify email uniqueness check
- [ ] Ensure redirect to login on success
- [ ] Check database insertion with correct fields
