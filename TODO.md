# TODO: Category Management CRUD Implementation

## Steps to Complete:

1. **Create classes/category_class.php**  
   - Extend db_connection.  
   - Implement methods: addCategory($name), getCategories(), getCategoryById($id), updateCategory($id, $name), deleteCategory($id).  
   - Handle uniqueness for name in add/update.

2. **Create controllers/category_controller.php**  
   - Instantiate Category class.  
   - Implement functions: add_category_ctr($name), get_categories_ctr(), get_category_by_id_ctr($id), update_category_ctr($id, $name), delete_category_ctr($id).  
   - Return data or boolean results.

3. **Create actions/fetch_category_action.php**  
   - Require category_controller.php.  
   - Call get_categories_ctr() and return JSON array of categories.

4. **Create actions/add_category_action.php**  
   - Handle POST name.  
   - Call add_category_ctr($name).  
   - Return JSON success/error message.

5. **Create actions/update_category_action.php**  
   - Handle POST id and name.  
   - Call update_category_ctr($id, $name).  
   - Return JSON success/error message.

6. **Create actions/delete_category_action.php**  
   - Handle POST id.  
   - Call delete_category_ctr($id).  
   - Return JSON success/error message.

7. **Create admin/category.php**  
   - Require settings/core.php and check is_logged_in() && is_admin(), else redirect.  
   - HTML structure: Table for categories, add form, edit modal, delete buttons.  
   - Load categories via AJAX on page load.

8. **Create js/category.js**  
   - Fetch and populate table on load.  
   - Handle add: Validate, AJAX to add_action, refresh table.  
   - Handle update: Fill modal, AJAX to update_action, refresh.  
   - Handle delete: Confirm, AJAX to delete_action, refresh.  
   - Use SweetAlert for notifications.

9. **Edit index.php**  
   - Update menu based on login status and role:  
     - Not logged in: Register | Login.  
     - Logged in admin: Logout | Category (link to admin/category.php).  
     - Logged in non-admin: Logout.

10. **Test the implementation**  
    - Login as admin, access category.php, perform CRUD operations.  
    - Verify non-admin can't access.  
    - Test uniqueness, error handling, database updates.  
    - Check AJAX flows and pop-ups.

## Progress:
- [x] Step 1: Create category_class.php  
- [x] Step 2: Create category_controller.php  
- [x] Step 3: Create fetch_category_action.php  
- [x] Step 4: Create add_category_action.php  
- [x] Step 5: Create update_category_action.php  
- [x] Step 6: Create delete_category_action.php  
- [x] Step 7: Create admin/category.php  
- [x] Step 8: Create category.js  
- [x] Step 9: Edit index.php  
- [x] Step 10: Testing  

Last updated: Implementation complete and tested.
