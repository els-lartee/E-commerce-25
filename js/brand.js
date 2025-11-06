document.addEventListener('DOMContentLoaded', () => {
    loadBrands();

    document.getElementById('brandForm').addEventListener('submit', e => {
        e.preventDefault();
        const brand_name = document.getElementById('brand_name').value.trim();
        const cat_id = document.getElementById('cat_id').value;

        if (!brand_name || !cat_id) {
            alert("Please fill all fields.");
            return;
        }

        fetch('../actions/add_brand_action.php', {
            method: 'POST',
            body: new URLSearchParams({ brand_name, cat_id })
        })
        .then(res => res.text())
        .then(msg => {
            alert(msg);
            loadBrands();
            document.getElementById('brandForm').reset();
        });
    });
});

function loadBrands() {
    fetch('../actions/fetch_brand_action.php')
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector('#brandTable tbody');
            tbody.innerHTML = '';
            data.forEach(row => {
                tbody.innerHTML += `
                    <tr>
                        <td>${row.brand_id}</td>
                        <td contenteditable="true" onblur="updateBrand(${row.brand_id}, this.innerText)">
                            ${row.brand_name}
                        </td>
                        <td>${row.cat_name}</td>
                        <td>
                            <button onclick="deleteBrand(${row.brand_id})">Delete</button>
                        </td>
                    </tr>`;
            });
        });
}

function updateBrand(brand_id, brand_name) {
    fetch('../actions/update_brand_action.php', {
        method: 'POST',
        body: new URLSearchParams({ brand_id, brand_name })
    })
    .then(res => res.text())
    .then(alert);
}

function deleteBrand(brand_id) {
    if (confirm('Are you sure you want to delete this brand?')) {
        fetch('../actions/delete_brand_action.php', {
            method: 'POST',
            body: new URLSearchParams({ brand_id })
        })
        .then(res => res.text())
        .then(msg => {
            alert(msg);
            loadBrands();
        });
    }
}
