// products.js - admin product CRUD
(function(){
    const $container = $('#productsContainer');

    function escapeHtml(text) { return $('<div>').text(text).html(); }

    function loadProducts() {
        $.getJSON('../actions/fetch_product_action.php', function(resp){
            if (resp.status !== 'success') {
                $container.html('<div class="alert alert-danger">Failed to load products</div>');
                return;
            }
            const products = resp.products || [];
            if (products.length === 0) {
                $container.html('<div class="alert alert-info">No products yet.</div>');
                return;
            }
            let html = '<div class="table-responsive"><table class="table table-striped">';
            html += '<thead><tr><th>ID</th><th>Title</th><th>Category</th><th>Brand</th><th>Price</th><th>Image</th><th>Actions</th></tr></thead><tbody>';
            products.forEach(p => {
                const img = p.product_image ? `<img src="../${escapeHtml(p.product_image)}" style="max-height:50px">` : '';
                html += `<tr><td>${p.product_id}</td><td>${escapeHtml(p.product_title)}</td><td>${escapeHtml(p.cat_name)}</td><td>${escapeHtml(p.brand_name)}</td><td>${p.product_price}</td><td>${img}</td><td>`;
                html += `<button class="btn btn-sm btn-outline-secondary me-2 editProductBtn" data-json='${JSON.stringify(p)}'>Edit</button>`;
                html += `<button class="btn btn-sm btn-outline-danger deleteProductBtn" data-id="${p.product_id}">Delete</button>`;
                html += `</td></tr>`;
            });
            html += '</tbody></table></div>';
            $container.html(html);
        }).fail(function(){
            $container.html('<div class="alert alert-danger">Error loading products</div>');
        });
    }

    $(document).ready(function(){ loadProducts(); });

    // load categories and brands and populate selects, then load products
    function populateCatsAndBrands(done) {
        // fetch categories
        $.getJSON('../actions/fetch_category_action.php', function(cResp){
            if (cResp.status === 'success') {
                const cats = cResp.categories || [];
                let opts = '<option value="">Select category</option>';
                cats.forEach(c => { opts += `<option value="${c.id}">${escapeHtml(c.name)}</option>`; });
                $('#product_cat, #edit_product_cat').each(function(){ $(this).html(opts); });
            }
        }).always(function(){
            // fetch brands
            $.getJSON('../actions/fetch_brand_action.php', function(bResp){
                console.log('Brand response:', bResp);
                if (bResp.status === 'success') {
                    const brands = bResp.brands || [];
                    console.log('Brands:', brands);
                    let opts = '<option value="">Select brand</option>';
                    brands.forEach(b => { 
                        console.log('Adding brand:', b);
                        opts += `<option value="${b.brand_id}">${escapeHtml(b.brand_name)}</option>`; 
                    });
                    $('#product_brand, #edit_product_brand').each(function(){ $(this).html(opts); });
                }
            }).always(function(){
                if (typeof done === 'function') done();
            });
        });
    }

    // ensure selects are populated before loading products
    $(document).ready(function(){
        populateCatsAndBrands(loadProducts);
    });

    // Add product
    $(document).on('submit', '#addProductForm', function(e){
        e.preventDefault();
        const fd = new FormData(this);
        $.ajax({
            url: '../actions/add_product_action.php',
            method: 'POST',
            data: fd,
            contentType: false,
            processData: false,
            dataType: 'json',
            success(resp){
                if (resp.status === 'success') {
                    Swal.fire('Success', resp.message, 'success');
                    $('#addProductForm')[0].reset();
                    loadProducts();
                } else {
                    Swal.fire('Error', resp.message, 'error');
                }
            },
            error(){ Swal.fire('Error','Request failed','error'); }
        });
    });

    // Open edit modal
    $(document).on('click', '.editProductBtn', function(){
        const p = $(this).data('json');
        $('#edit_product_id').val(p.product_id);
        $('#edit_product_title').val(p.product_title);
        $('#edit_product_price').val(p.product_price);
        $('#edit_product_desc').val(p.product_desc);
        $('#edit_product_keywords').val(p.product_keywords);
        $('#edit_product_cat').val(p.product_cat);
        $('#edit_product_brand').val(p.product_brand);
        const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
        modal.show();
    });

    // Update product
    $(document).on('submit', '#editProductForm', function(e){
        e.preventDefault();
        const fd = new FormData(this);
        $.ajax({
            url: '../actions/update_product_action.php',
            method: 'POST',
            data: fd,
            contentType: false,
            processData: false,
            dataType: 'json',
            success(resp){
                if (resp.status === 'success') {
                    Swal.fire('Success', resp.message, 'success');
                    const modalInstance = bootstrap.Modal.getInstance(document.getElementById('editProductModal'));
                    if (modalInstance) modalInstance.hide();
                    loadProducts();
                } else {
                    Swal.fire('Error', resp.message, 'error');
                }
            },
            error(){ Swal.fire('Error','Request failed','error'); }
        });
    });

    // Delete product
    $(document).on('click', '.deleteProductBtn', function(){
        const id = $(this).data('id');
        Swal.fire({ title:'Confirm', text:'Delete product?', icon:'warning', showCancelButton:true }).then(r => {
            if (r.isConfirmed) {
                $.post('../actions/delete_product_action.php', {product_id: id}, function(resp){
                    if (resp.status === 'success') { Swal.fire('Deleted', resp.message, 'success'); loadProducts(); }
                    else Swal.fire('Error', resp.message, 'error');
                }, 'json');
            }
        });
    });

})();
