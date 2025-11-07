$(document).ready(function() {
    // Fetch and display jewellery on page load
    fetchJewellery();

    // Add jewellery form submit
    $('#addJewelleryForm').on('submit', function(e) {
        e.preventDefault();
        const name = $('#jewelleryName').val().trim();
        if (!name) {
            Swal.fire('Error', 'Jewellery name is required.', 'error');
            return;
        }

        $.ajax({
            url: '../actions/add_jewellery_action.php',
            type: 'POST',
            data: { name: name },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('Success', response.message, 'success');
                    $('#addJewelleryForm')[0].reset();
                    fetchJewellery();
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Add jewellery error:', xhr.responseText, status, error);
                Swal.fire('Error', 'An error occurred while adding the jewellery: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
            }
        });
    });

    // Edit jewellery form submit
    $('#editJewelleryForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#editJewelleryId').val();
        const name = $('#editJewelleryName').val().trim();
        if (!name) {
            Swal.fire('Error', 'Jewellery name is required.', 'error');
            return;
        }

        $.ajax({
            url: '../actions/update_jewellery_action.php',
            type: 'POST',
            data: { id: id, name: name },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('Success', response.message, 'success');
                    closeModal('editJewelleryModal');
                    fetchJewellery();
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Update jewellery error:', xhr.responseText, status, error);
                Swal.fire('Error', 'An error occurred while updating the jewellery: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
            }
        });
    });

    // Delete jewellery
    $(document).on('click', '.delete-jewellery', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');

        Swal.fire({
            title: 'Are you sure?',
            text: `Delete jewellery "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../actions/delete_jewellery_action.php',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Deleted!', response.message, 'success');
                            fetchJewellery();
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Delete jewellery error:', xhr.responseText, status, error);
                        Swal.fire('Error', 'An error occurred while deleting the jewellery: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
                    }
                });
            }
        });
    });

    // Edit jewellery button click
    $(document).on('click', '.edit-jewellery', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        $('#editJewelleryId').val(id);
        $('#editJewelleryName').val(name);
        showModal('editJewelleryModal');
    });
});

// Function to fetch and display jewellery
function fetchJewellery() {
    $.ajax({
        url: '../actions/fetch_jewellery_action.php?t=' + Date.now(),
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                const jewellery = response.data;
                let rows = '';
                jewellery.forEach(function(item) {
                    rows += `
                        <tr>
                            <td>${item.id}</td>
                            <td>${item.name}</td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-jewellery" data-id="${item.id}" data-name="${item.name}">Edit</button>
                                <button class="btn btn-sm btn-danger delete-jewellery" data-id="${item.id}" data-name="${item.name}">Delete</button>
                            </td>
                        </tr>
                    `;
                });
                $('#jewelleryTable tbody').html(rows);
            } else {
                $('#jewelleryTable tbody').html('<tr><td colspan="3">No jewellery found.</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Fetch jewellery error:', xhr.responseText, status, error);
            $('#jewelleryTable tbody').html('<tr><td colspan="3">Error loading jewellery: ' + (xhr.responseJSON ? xhr.responseJSON.message : error) + '</td></tr>');
        }
    });
}
