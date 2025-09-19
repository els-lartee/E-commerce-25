$(document).ready(function() {
    $('#login-form').submit(function(e) {
        e.preventDefault();

        let email = $('#email').val().trim();
        let password = $('#password').val();

        // Field validations
        if (email.length === 0 || email.length > 50) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Email is required and must be less than 50 characters!',
            });
            return;
        }

        if (password.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Password is required!',
            });
            return;
        }

        // Email regex validation
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please enter a valid email address!',
            });
            return;
        }

        // Disable login button and show loading spinner
        let $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true);
        let originalText = $btn.html();
        $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Logging in...');

        // Submit login form
        $.ajax({
            url: '../actions/login_customer_action.php',
            type: 'POST',
            data: {
                email: email,
                password: password
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '../index.php';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.message,
                    });
                }
                $btn.prop('disabled', false);
                $btn.html(originalText);
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'An error occurred! Please try again later.',
                });
                $btn.prop('disabled', false);
                $btn.html(originalText);
            }
        });
    });
});
