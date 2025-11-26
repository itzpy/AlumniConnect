$(document).ready(function() {
    
    // Function to check email uniqueness
    function checkEmailExists(email, callback) {
        $.ajax({
            url: '../actions/check_email_action.php',
            type: 'POST',
            data: { email: email },
            success: function(response) {
                callback(response.exists);
            },
            error: function() {
                callback(false);
            }
        });
    }
    
    $('#register-form').submit(function(e) {
        e.preventDefault();

        const name = $('#name').val().trim();
        const email = $('#email').val().trim();
        const password = $('#password').val();
        const phone_number = $('#phone_number').val().trim();
        const country = $('#country').val().trim();
        const city = $('#city').val().trim();
        const role = $('input[name="role"]:checked').val();

        // Validation patterns
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        const phonePattern = /^[\+]?[0-9\s\-\(\)]{10,15}$/;
        const namePattern = /^[a-zA-Z\s]{2,100}$/;
        const locationPattern = /^[a-zA-Z\s]{2,30}$/;
        
        // Field validation
        if (!name || !email || !password || !phone_number || !country || !city) {
            Swal.fire({
                icon: 'error',
                title: 'Missing Fields',
                text: 'Please fill in all fields!',
            });
            return;
        }
        
        // Name validation (2-100 characters, letters and spaces only)
        if (!namePattern.test(name)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Name',
                text: 'Name must be 2-100 characters and contain only letters and spaces!',
            });
            return;
        }
        
        // Email validation
        if (!emailPattern.test(email) || email.length > 50) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Email',
                text: 'Please enter a valid email address (max 50 characters)!',
            });
            return;
        }
        
        // Password validation
        if (password.length < 6 || !password.match(/[a-z]/) || !password.match(/[A-Z]/) || !password.match(/[0-9]/)) {
            Swal.fire({
                icon: 'error',
                title: 'Weak Password',
                text: 'Password must be at least 6 characters with uppercase, lowercase, and number!',
            });
            return;
        }
        
        // Phone validation
        if (!phonePattern.test(phone_number) || phone_number.length > 15) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Phone',
                text: 'Please enter a valid phone number (10-15 digits)!',
            });
            return;
        }
        
        // Country validation
        if (!locationPattern.test(country)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Country',
                text: 'Country must be 2-30 characters and contain only letters and spaces!',
            });
            return;
        }
        
        // City validation
        if (!locationPattern.test(city)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid City',
                text: 'City must be 2-30 characters and contain only letters and spaces!',
            });
            return;
        }

        // Check email uniqueness before proceeding
        checkEmailExists(email, function(exists) {
            if (exists) {
                Swal.fire({
                    icon: 'error',
                    title: 'Email Exists',
                    text: 'This email is already registered. Please use a different email.',
                });
                return;
            }
            
            // Show loading indicator
            Swal.fire({
                title: 'Registering...',
                text: 'Please wait while we create your account.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Submit form if all validations pass
            $.ajax({
                url: '../actions/register_customer_action.php',
                type: 'POST',
                data: {
                    name: name,
                    email: email,
                    password: password,
                    phone_number: phone_number,
                    country: country,
                    city: city,
                    role: role
                },
                success: function(response) {
                    Swal.close();
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'login.php';
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Registration Failed',
                            text: response.message,
                        });
                    }
                },
                error: function() {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error',
                        text: 'An error occurred! Please try again later.',
                    });
                }
            });
        });
    });
});