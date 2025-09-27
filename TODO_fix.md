# TODO: Fix Category CRUD Per-Admin Isolation

## Steps to Complete:

1. **Edit setup.php**  
   - Drop existing UNIQUE on cat_name.  
   - Add composite UNIQUE KEY unique_cat_user (cat_name, user_id).  
   - Run the script to update DB schema.

2. **Edit classes/category_class.php**  
   - Update constructor if needed.  
   - Modify addCategory($name, $user_id): Check uniqueness with user_id, insert with user_id.  
   - Modify getCategories($user_id): Add WHERE user_id = ? ORDER BY cat_name ASC.  
   - Modify getCategoryById($id, $user_id): Add AND user_id = ? to WHERE.  
   - Modify updateCategory($id, $name, $user_id): Check uniqueness excluding current ID with user_id, UPDATE with WHERE cat_id = ? AND user_id = ?.  
   - Modify deleteCategory($id, $user_id): DELETE WHERE cat_id = ? AND user_id = ?.

3. **Edit controllers/category_controller.php**  
   - Update add_category_ctr($name, $user_id): Pass $user_id to $category->addCategory($name, $user_id).  
   - Update get_categories_ctr($user_id): Pass to getCategories($user_id).  
   - Update get_category_by_id_ctr($id, $user_id): Pass to getCategoryById($id, $user_id).  
   - Update update_category_ctr($id, $name, $user_id): Pass to updateCategory($id, $name, $user_id).  
   - Update delete_category_ctr($id, $user_id): Pass to deleteCategory($id, $user_id).

4. **Edit actions/fetch_category_action.php**  
   - Add session_start(); require_once '../settings/core.php';.  
   - Add if (!is_logged_in() || !is_admin()) { error response; exit; }.  
   - $user_id = get_user_id();.  
   - Call get_categories_ctr($user_id); return JSON.

5. **Edit actions/add_category_action.php**  
   - Add $user_id = get_user_id(); after admin check.  
   - Call add_category_ctr($name, $user_id);.

6. **Edit actions/update_category_action.php**  
   - Add $user_id = get_user_id(); after admin check.  
   - Call update_category_ctr($id, $name, $user_id);.

7. **Edit actions/delete_category_action.php**  
   - Add $user_id = get_user_id(); after admin check.  
   - Call delete_category_ctr($id, $user_id);.

8. **Run setup.php**  
   - Execute to apply DB changes.

9. **Test the implementation**  
   - Login as admin, access admin/category.php, add/update/delete categories.  
   - Verify per-admin isolation (if multiple admins, names unique per admin).  
   - Check non-admin access denied, error handling, AJAX/SweetAlert.

## Progress:
- [ ] Step 1: Edit setup.php  
- [ ] Step 2: Edit category_class.php  
- [ ] Step 3: Edit category_controller.php  
- [ ] Step 4: Edit fetch_category_action.php  
- [ ] Step 5: Edit add_category_action.php  
- [ ] Step 6: Edit update_category_action.php  
- [ ] Step 7: Edit delete_category_action.php  
- [ ] Step 8: Run setup.php  
- [ ] Step 9: Testing  

Last updated: Starting bug fix implementation.
