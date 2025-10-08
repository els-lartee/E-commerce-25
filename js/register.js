$(document).ready(function() {
    $('#register-form').submit(function(e) {
        e.preventDefault();

        let name = $('#name').val().trim();
        let email = $('#email').val().trim();
        let password = $('#password').val();
        let phone_number = $('#phone_number').val().trim();
        let country = $('#country').val().trim();
        let city = $('#city').val().trim();
        let role = $('input[name="role"]:checked').val();

        // Field length validations
        if (name.length === 0 || name.length > 100) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Name is required and must be less than 100 characters!',
            });
            return;
        }

        if (email.length === 0 || email.length > 50) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Email is required and must be less than 50 characters!',
            });
            return;
        }

        if (country.length === 0 || country.length > 30) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Country is required and must be less than 30 characters!',
            });
            return;
        }

        if (city.length === 0 || city.length > 30) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'City is required and must be less than 30 characters!',
            });
            return;
        }

        if (phone_number.length === 0 || phone_number.length > 15) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Phone number is required and must be less than 15 characters!',
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

        // Phone number regex validation (digits only)
        let phoneRegex = /^[0-9]+$/;
        if (!phoneRegex.test(phone_number)) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Phone number must contain digits only!',
            });
            return;
        }

        // Password validation
        if (password.length < 6 || !password.match(/[a-z]/) || !password.match(/[A-Z]/) || !password.match(/[0-9]/)) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Password must be at least 6 characters long and contain at least one lowercase letter, one uppercase letter, and one number!',
            });
            return;
        }

        // Disable register button and show loading spinner
        let $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true);
        let originalText = $btn.html();
        $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Registering...');

        // Async check for email uniqueness before submission
        $.ajax({
            url: '../actions/check_email_action.php',
            type: 'POST',
            data: { email: email },
            success: function(response) {
                if (response.exists) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Email already exists. Please use a different email.',
                    });
                    $btn.prop('disabled', false);
                    $btn.html(originalText);
                } else {
                    // Proceed with registration
                    $.ajax({
                        url: '../actions/register_customer_action.php',
                        type: 'POST',
                        data: {
                            name: name,
                            email: email,
                            password: password,
                            country: country,
                            city: city,
                            phone_number: phone_number,
                            role: role
                        },
                        success: function(response) {
                            console.log(response); // Debugging line 
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message,
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = 'login.php';
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
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'An error occurred while checking email! Please try again later.',
                });
                $btn.prop('disabled', false);
                $btn.html(originalText);
            }
        });
    });
});
