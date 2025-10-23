$(document).ready(function() {
    loadCategories();
    loadBrands();

    // Add Brand Form Submit
    $('#addBrandForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        $.ajax({
            url: '../actions/add_brand_action.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const data = JSON.parse(response);
                if (data.status === 'success') {
                    Swal.fire('Success', data.message, 'success');
                    $('#addBrandForm')[0].reset();
                    loadBrands();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to add brand.', 'error');
            }
        });
    });

    // Edit Brand Form Submit
    $('#editBrandForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        $.ajax({
            url: '../actions/update_brand_action.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const data = JSON.parse(response);
                if (data.status === 'success') {
                    Swal.fire('Success', data.message, 'success');
                    $('#editBrandModal').modal('hide');
                    loadBrands();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to update brand.', 'error');
            }
        });
    });

    // Delete Brand
    $(document).on('click', '.delete-brand', function() {
        const brandId = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../actions/delete_brand_action.php',
                    type: 'POST',
                    data: { id: brandId },
                    success: function(response) {
                        const data = JSON.parse(response);
                        if (data.status === 'success') {
                            Swal.fire('Deleted!', data.message, 'success');
                            loadBrands();
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to delete brand.', 'error');
                    }
                });
            }
        });
    });

    // Edit Brand Button
    $(document).on('click', '.edit-brand', function() {
        const brandId = $(this).data('id');
        const brandName = $(this).data('name');
        const brandCategory = $(this).data('category');
        $('#editBrandId').val(brandId);
        $('#editBrandName').val(brandName);
        $('#editBrandCategory').val(brandCategory);
        $('#editBrandModal').modal('show');
    });
});

function loadCategories() {
    $.ajax({
        url: '../actions/fetch_categories_action.php',
        type: 'GET',
        success: function(response) {
            const data = JSON.parse(response);
            if (data.status === 'success') {
                const select = $('#brandCategory');
                select.empty();
                select.append('<option value="">Select Category</option>');
                data.data.forEach(function(category) {
                    select.append(`<option value="${category.cat_id}">${category.cat_name}</option>`);
                });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Failed to load categories.', 'error');
        }
    });
}

function loadBrands() {
    $.ajax({
        url: '../actions/fetch_brand_action.php',
        type: 'GET',
        success: function(response) {
            const data = JSON.parse(response);
            if (data.status === 'success') {
                const tbody = $('#brandsTable tbody');
                tbody.empty();
                let currentCategory = '';
                data.data.forEach(function(brand) {
                    if (brand.cat_name !== currentCategory) {
                        tbody.append(`<tr><td colspan="4" class="table-secondary fw-bold">${brand.cat_name}</td></tr>`);
                        currentCategory = brand.cat_name;
                    }
                    tbody.append(`
                        <tr>
                            <td>${brand.brand_id}</td>
                            <td>${brand.brand_name}</td>
                            <td>${brand.cat_name}</td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-brand" data-id="${brand.brand_id}" data-name="${brand.brand_name}" data-category="${brand.cat_name}">Edit</button>
                                <button class="btn btn-sm btn-danger delete-brand" data-id="${brand.brand_id}">Delete</button>
                            </td>
                        </tr>
                    `);
                });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Failed to load brands.', 'error');
        }
    });
}
