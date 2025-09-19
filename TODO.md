# Customer Login Functionality Implementation

## Tasks to Complete

### 1. Update Customer Class (classes/customer_class.php)
- [x] Add verifyCustomerLogin($email, $password) method to get customer by email and verify password hash

### 2. Update Customer Controller (controllers/customer_controller.php)
- [x] Add login_customer_ctr($email, $password) method to invoke class method and return response

### 3. Create Login Action Script (actions/login_customer_action.php)
- [x] Create new file with session handling, POST data reception, controller invocation, session variable setting, and JSON response

### 4. Create Login JavaScript (js/login.js)
- [x] Create new file with form validation (email regex, password check) and asynchronous submission to login action

### 5. Update Login Form (login/login.php)
- [x] Ensure form has proper id, includes js/login.js, and has placeholders for alert messages

### 6. Update Index Menu (index.php)
- [x] Modify menu to show Logout button when user is logged in (check session variables)
