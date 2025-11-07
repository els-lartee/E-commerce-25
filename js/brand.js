document.addEventListener('DOMContentLoaded', () => {
    loadBrands();

    document.getElementById('brandForm').addEventListener('submit', e => {
        e.preventDefault();
        const brand_name = document.getElementById('brand_name').value.trim();
        const cat_id = document.getElementById('cat_id').value;

        if (!brand_name || !cat_id) {
            Swal.fire('Error', 'Please fill all fields.', 'error');
            return;
        }

        fetch('../actions/add_brand_action.php', {
            method: 'POST',
            body: new URLSearchParams({ brand_name, cat_id })
        })
        .then(res => res.text())
        .then(msg => {
            Swal.fire('Success', msg, 'success');
            loadBrands();
            document.getElementById('brandForm').reset();
        })
        .catch(error => {
            Swal.fire('Error', 'Failed to add brand', 'error');
        });
    });
});

function loadBrands() {
    fetch('../actions/fetch_brand_action.php')
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector('#brandTable tbody');
            tbody.innerHTML = '';
            
            // Handle both response formats
            const brands = data.brands || data;
            
            if (!brands || brands.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">No brands found.</td></tr>';
                return;
            }
            
            brands.forEach(row => {
                tbody.innerHTML += `
                    <tr>
                        <td>${row.brand_id}</td>
                        <td contenteditable="true" onblur="updateBrand(${row.brand_id}, this.innerText)">
                            ${row.brand_name}
                        </td>
                        <td>${row.cat_name || 'N/A'}</td>
                        <td>
                            <button class="btn btn-danger btn-sm" onclick="deleteBrand(${row.brand_id})">Delete</button>
                        </td>
                    </tr>`;
            });
        })
        .catch(error => {
            console.error('Error loading brands:', error);
            const tbody = document.querySelector('#brandTable tbody');
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;color:red;">Error loading brands</td></tr>';
        });
}

function updateBrand(brand_id, brand_name) {
    fetch('../actions/update_brand_action.php', {
        method: 'POST',
        body: new URLSearchParams({ brand_id, brand_name })
    })
    .then(res => res.text())
    .then(msg => {
        Swal.fire('Success', msg, 'success');
    })
    .catch(error => {
        Swal.fire('Error', 'Failed to update brand', 'error');
    });
}

function deleteBrand(brand_id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'Delete this brand?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('../actions/delete_brand_action.php', {
                method: 'POST',
                body: new URLSearchParams({ brand_id })
            })
            .then(res => res.text())
            .then(msg => {
                Swal.fire('Deleted!', msg, 'success');
                loadBrands();
            })
            .catch(error => {
                Swal.fire('Error', 'Failed to delete brand', 'error');
            });
        }
    });
}
