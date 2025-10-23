// brand.js - handles brand CRUD via AJAX
(function(){
    const $brandsContainer = $('#brandsContainer');

    function escapeHtml(text) {
        return $('<div>').text(text).html();
    }

    function loadBrands() {
        $.ajax({
            url: '../actions/fetch_brand_action.php',
            method: 'GET',
            dataType: 'json',
            success(resp) {
                if (resp.status !== 'success') {
                    $brandsContainer.html('<div class="alert alert-danger">Failed to load brands</div>');
                    return;
                }
                const brands = resp.brands || [];
                const grouped = {};
                brands.forEach(b => {
                    const cat = b.cat_name || ('Category ' + b.cat_id);
                    if (!grouped[cat]) grouped[cat] = [];
                    grouped[cat].push(b);
                });

                let html = '';
                for (const cat in grouped) {
                    html += `<h5 class="mt-3">${escapeHtml(cat)}</h5>`;
                    html += '<ul class="list-group mb-2">';
                    grouped[cat].forEach(b => {
                        html += `<li class="list-group-item d-flex justify-content-between align-items-center">`;
                        html += `<span>${escapeHtml(b.brand_name)}</span>`;
                        html += `<div>`;
                        html += `<button class="btn btn-sm btn-outline-secondary me-2 editBrandBtn" data-id="${b.brand_id}" data-name="${escapeHtml(b.brand_name)}">Edit</button>`;
                        html += `<button class="btn btn-sm btn-outline-danger deleteBrandBtn" data-id="${b.brand_id}">Delete</button>`;
                        html += `</div>`;
                        html += `</li>`;
                    });
                    html += '</ul>';
                }
                $brandsContainer.html(html);
            },
            error() {
                $brandsContainer.html('<div class="alert alert-danger">Error loading brands</div>');
            }
        });
    }

    // Initial load
    $(document).ready(function(){
        loadBrands();
    });

    // Add brand
    $(document).on('submit', '#addBrandForm', function(e){
        e.preventDefault();
        const brand_name = $('#brand_name').val().trim();
        const cat_id = $('#cat_id').val();
        if (!brand_name || !cat_id) {
            Swal.fire('Error','Brand name and category are required','error');
            return;
        }
        $.post('../actions/add_brand_action.php', {brand_name, cat_id}, function(resp){
            if (resp.status === 'success') {
                Swal.fire('Success', resp.message, 'success');
                $('#brand_name').val('');
                $('#cat_id').val('');
                loadBrands();
            } else {
                Swal.fire('Error', resp.message, 'error');
            }
        }, 'json');
    });

    // Open edit modal
    $(document).on('click', '.editBrandBtn', function(){
        const id = $(this).data('id');
        const name = $(this).data('name');
        $('#edit_brand_id').val(id);
        $('#edit_brand_name').val(name);
        const modal = new bootstrap.Modal(document.getElementById('editBrandModal'));
        modal.show();
    });

    // Update brand
    $(document).on('submit', '#editBrandForm', function(e){
        e.preventDefault();
        const brand_id = $('#edit_brand_id').val();
        const brand_name = $('#edit_brand_name').val().trim();
        if (!brand_name) {
            Swal.fire('Error','Brand name is required','error');
            return;
        }
        $.post('../actions/update_brand_action.php', {brand_id, brand_name}, function(resp){
            if (resp.status === 'success') {
                Swal.fire('Success', resp.message, 'success');
                const modalInstance = bootstrap.Modal.getInstance(document.getElementById('editBrandModal'));
                if (modalInstance) modalInstance.hide();
                loadBrands();
            } else {
                Swal.fire('Error', resp.message, 'error');
            }
        }, 'json');
    });

    // Delete
    $(document).on('click', '.deleteBrandBtn', function(){
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will delete the brand',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it'
        }).then(result => {
            if (result.isConfirmed) {
                $.post('../actions/delete_brand_action.php', {brand_id: id}, function(resp){
                    if (resp.status === 'success') {
                        Swal.fire('Deleted', resp.message, 'success');
                        loadBrands();
                    } else {
                        Swal.fire('Error', resp.message, 'error');
                    }
                }, 'json');
            }
        });
    });

})();

