$(document).ready(function() {
    // Fetch and display categories on page load
    fetchCategories();

    // Add category form submit
    $('#addCategoryForm').on('submit', function(e) {
        e.preventDefault();
        const name = $('#categoryName').val().trim();
        if (!name) {
            Swal.fire('Error', 'Category name is required.', 'error');
            return;
        }

        $.ajax({
            url: '../actions/add_category_action.php',
            type: 'POST',
            data: { name: name },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('Success', response.message, 'success');
                    $('#addCategoryForm')[0].reset();
                    fetchCategories();
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'An error occurred while adding the category.', 'error');
            }
        });
    });

    // Edit category form submit
    $('#editCategoryForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#editCategoryId').val();
        const name = $('#editCategoryName').val().trim();
        if (!name) {
            Swal.fire('Error', 'Category name is required.', 'error');
            return;
        }

        $.ajax({
            url: '../actions/update_category_action.php',
            type: 'POST',
            data: { id: id, name: name },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('Success', response.message, 'success');
                    $('#editCategoryModal').modal('hide');
                    fetchCategories();
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'An error occurred while updating the category.', 'error');
            }
        });
    });

    // Delete category
    $(document).on('click', '.delete-category', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');

        Swal.fire({
            title: 'Are you sure?',
            text: `Delete category "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../actions/delete_category_action.php',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Deleted!', response.message, 'success');
                            fetchCategories();
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'An error occurred while deleting the category.', 'error');
                    }
                });
            }
        });
    });

    // Edit category button click
    $(document).on('click', '.edit-category', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        $('#editCategoryId').val(id);
        $('#editCategoryName').val(name);
        $('#editCategoryModal').modal('show');
    });
});

// Function to fetch and display categories
function fetchCategories() {
    $.ajax({
        url: '../actions/fetch_category_action.php?t=' + Date.now(),
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                const categories = response.data;
                let rows = '';
                categories.forEach(function(category) {
                    rows += `
                        <tr>
                            <td>${category.cat_id}</td>
                            <td>${category.cat_name}</td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-category" data-id="${category.cat_id}" data-name="${category.cat_name}">Edit</button>
                                <button class="btn btn-sm btn-danger delete-category" data-id="${category.cat_id}" data-name="${category.cat_name}">Delete</button>
                            </td>
                        </tr>
                    `;
                });
                $('#categoriesTable tbody').html(rows);
            } else {
                $('#categoriesTable tbody').html('<tr><td colspan="3">No categories found.</td></tr>');
            }
        },
        error: function() {
            $('#categoriesTable tbody').html('<tr><td colspan="3">Error loading categories.</td></tr>');
        }
    });
}
