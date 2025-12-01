<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Alumni Connect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#7A1E1E',
                        'primary-dark': '#5a1616',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-primary-dark to-primary min-h-screen py-12 px-4 font-sans">
<?php
// Get preserved form values
$first_name = isset($_GET['first_name']) ? htmlspecialchars($_GET['first_name']) : '';
$last_name = isset($_GET['last_name']) ? htmlspecialchars($_GET['last_name']) : '';
$email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
$phone = isset($_GET['phone']) ? htmlspecialchars($_GET['phone']) : '';
$major = isset($_GET['major']) ? htmlspecialchars($_GET['major']) : '';
$graduation_year = isset($_GET['graduation_year']) ? htmlspecialchars($_GET['graduation_year']) : '';
$user_type = isset($_GET['user_type']) ? htmlspecialchars($_GET['user_type']) : 'alumni';
?>
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden max-w-5xl mx-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary-dark to-primary text-white p-8 text-center">
            <h2 class="text-4xl font-bold mb-2 flex items-center justify-center">
                <i class="fas fa-graduation-cap mr-3"></i>Alumni Connect
            </h2>
            <p class="text-gray-100">Create your account to join our thriving alumni community</p>
        </div>
        
        <div class="p-8 md:p-12">
            <?php if (isset($_GET['error'])): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                    <p class="text-red-700">
                        <?php 
                            if ($_GET['error'] == 'empty') echo 'Please fill in all required fields';
                            elseif ($_GET['error'] == 'password_mismatch') echo 'Passwords do not match';
                            elseif ($_GET['error'] == 'invalid_email') echo 'Invalid email address';
                            elseif ($_GET['error'] == 'registration_failed') echo 'Registration failed. Email may already exist.';
                            else echo 'An error occurred';
                        ?>
                    </p>
                </div>
            <?php endif; ?>
            
            <form action="../actions/register_action.php" method="POST" id="registerForm" class="space-y-8">
                <!-- User Type Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="user-type-card border-2 border-gray-300 rounded-xl p-6 text-center cursor-pointer hover:border-primary hover:bg-gray-50 transition-all active" data-type="alumni">
                        <div class="text-5xl text-primary mb-4"><i class="fas fa-user-graduate"></i></div>
                        <h4 class="text-xl font-semibold mb-2">Alumni</h4>
                        <p class="text-gray-600">I'm a graduate of the university</p>
                    </div>
                    <div class="user-type-card border-2 border-gray-300 rounded-xl p-6 text-center cursor-pointer hover:border-primary hover:bg-gray-50 transition-all" data-type="student">
                        <div class="text-5xl text-primary mb-4"><i class="fas fa-user"></i></div>
                        <h4 class="text-xl font-semibold mb-2">Current Student</h4>
                        <p class="text-gray-600">I'm currently enrolled</p>
                    </div>
                </div>
                
                <input type="hidden" id="userType" name="user_type" value="alumni">
                
                <!-- Personal Information -->
                <div>
                    <h4 class="text-2xl font-semibold text-primary mb-4 flex items-center">
                        <i class="fas fa-user-circle mr-2"></i> Personal Information
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                            <input type="text" id="first_name" name="first_name" required value="<?php echo $first_name; ?>"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                            <input type="text" id="last_name" name="last_name" required value="<?php echo $last_name; ?>"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" id="email" name="email" required value="<?php echo $email; ?>"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo $phone; ?>"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                        </div>
                    </div>
                </div>
                
                <!-- Academic Information -->
                <div>
                    <h4 class="text-2xl font-semibold text-primary mb-4 flex items-center">
                        <i class="fas fa-school mr-2"></i> Academic Details
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="major" class="block text-sm font-medium text-gray-700 mb-2">Major/Field of Study</label>
                            <input type="text" id="major" name="major" required value="<?php echo $major; ?>"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                        </div>
                        <div>
                            <label for="graduation_year" class="block text-sm font-medium text-gray-700 mb-2">Graduation Year</label>
                            <input type="number" id="graduation_year" name="graduation_year" min="1950" max="2030" required value="<?php echo $graduation_year; ?>"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                        </div>
                    </div>
                </div>

                <!-- Student-specific fields -->
                <div id="studentFields" style="display: none;">
                    <h4 class="text-2xl font-semibold text-primary mb-4 flex items-center">
                        <i class="fas fa-user-graduate mr-2"></i> Student Information
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="current_year" class="block text-sm font-medium text-gray-700 mb-2">Current Year Level</label>
                            <select id="current_year" name="current_year"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors bg-white">
                                <option value="">Select Year Level</option>
                                <option value="Freshman">Freshman (Year 1)</option>
                                <option value="Sophomore">Sophomore (Year 2)</option>
                                <option value="Junior">Junior (Year 3)</option>
                                <option value="Senior">Senior (Year 4)</option>
                            </select>
                        </div>
                        <div>
                            <label for="career_interests" class="block text-sm font-medium text-gray-700 mb-2">Career Interests</label>
                            <input type="text" id="career_interests" name="career_interests" placeholder="e.g., Software Development, Data Science"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                        </div>
                    </div>
                </div>
                
                <!-- Alumni-specific fields -->
                <div id="alumniFields">
                    <h4 class="text-2xl font-semibold text-primary mb-4 flex items-center">
                        <i class="fas fa-briefcase mr-2"></i> Professional Information
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="current_company" class="block text-sm font-medium text-gray-700 mb-2">Current Company</label>
                            <input type="text" id="current_company" name="current_company"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                        </div>
                        <div>
                            <label for="current_position" class="block text-sm font-medium text-gray-700 mb-2">Current Position</label>
                            <input type="text" id="current_position" name="current_position"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                        </div>
                        <div>
                            <label for="industry" class="block text-sm font-medium text-gray-700 mb-2">Industry</label>
                            <select id="industry" name="industry"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors bg-white">
                                <option value="">Select Industry</option>
                                <option value="Technology">Technology / Software</option>
                                <option value="Finance">Finance / Banking</option>
                                <option value="Investment Banking">Investment Banking</option>
                                <option value="Fintech">Fintech</option>
                                <option value="Healthcare">Healthcare / Medical</option>
                                <option value="Pharmaceutical">Pharmaceutical</option>
                                <option value="Education">Education / Academia</option>
                                <option value="Engineering">Engineering</option>
                                <option value="Manufacturing">Manufacturing</option>
                                <option value="Energy">Energy / Oil & Gas</option>
                                <option value="Renewable Energy">Renewable Energy</option>
                                <option value="Marketing">Marketing / Advertising</option>
                                <option value="Media">Media / Entertainment</option>
                                <option value="Consulting">Consulting</option>
                                <option value="Management Consulting">Management Consulting</option>
                                <option value="Legal">Legal / Law</option>
                                <option value="Real Estate">Real Estate</option>
                                <option value="Retail">Retail / E-commerce</option>
                                <option value="Hospitality">Hospitality / Tourism</option>
                                <option value="Agriculture">Agriculture / Agribusiness</option>
                                <option value="Telecommunications">Telecommunications</option>
                                <option value="Government">Government / Public Sector</option>
                                <option value="Non-profit">Non-profit / NGO</option>
                                <option value="Entrepreneurship">Entrepreneurship / Startup</option>
                                <option value="Research">Research / Development</option>
                                <option value="Supply Chain">Supply Chain / Logistics</option>
                                <option value="Human Resources">Human Resources</option>
                                <option value="Data Science">Data Science / Analytics</option>
                                <option value="Cybersecurity">Cybersecurity</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <input type="text" id="location" name="location" placeholder="City, Country"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                        </div>
                        <div>
                            <label for="linkedin" class="block text-sm font-medium text-gray-700 mb-2">LinkedIn Profile (Optional)</label>
                            <input type="url" id="linkedin" name="linkedin" placeholder="https://linkedin.com/in/yourprofile"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                        </div>
                        <div class="md:col-span-2">
                            <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Bio (Optional)</label>
                            <textarea id="bio" name="bio" rows="3" placeholder="Tell us about yourself..."
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors"></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Password -->
                <div>
                    <h4 class="text-2xl font-semibold text-primary mb-4 flex items-center">
                        <i class="fas fa-lock mr-2"></i> Security
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="relative">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <input type="password" id="password" name="password" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors pr-12">
                            <i class="fas fa-eye absolute right-4 top-11 text-gray-400 cursor-pointer hover:text-gray-600" id="togglePassword"></i>
                            <small class="text-gray-500 text-sm">Minimum 8 characters</small>
                        </div>
                        <div class="relative">
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors pr-12">
                            <i class="fas fa-eye absolute right-4 top-11 text-gray-400 cursor-pointer hover:text-gray-600" id="toggleConfirmPassword"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Terms -->
                <div>
                    <label class="flex items-start space-x-3 cursor-pointer">
                        <input type="checkbox" name="terms" required class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary mt-1">
                        <span class="text-sm text-gray-700">
                            I agree to the <a href="#" class="text-primary hover:text-primary-dark">Terms of Service</a> and 
                            <a href="#" class="text-primary hover:text-primary-dark">Privacy Policy</a>
                        </span>
                    </label>
                </div>
                
                <button type="submit" class="w-full bg-primary text-white py-4 px-4 rounded-lg font-semibold text-lg hover:bg-primary-dark transition-colors flex items-center justify-center space-x-2">
                    <i class="fas fa-user-plus"></i>
                    <span>Create Account</span>
                </button>
            </form>
            
            <div class="mt-8 pt-8 border-t border-gray-200 text-center">
                <p class="text-gray-600 mb-4">Already have an account?</p>
                <a href="login.php" class="border-2 border-primary text-primary py-3 px-6 rounded-lg font-semibold hover:bg-primary hover:text-white transition-colors inline-flex items-center space-x-2">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Sign In</span>
                </a>
            </div>
            
            <div class="text-center mt-4">
                <a href="../index.php" class="text-sm text-gray-600 hover:text-primary transition-colors">
                    <i class="fas fa-home mr-1"></i> Back to Home
                </a>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/register.js"></script>
    <script>
        // User type selection
        document.querySelectorAll('.user-type-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.user-type-card').forEach(c => {
                    c.classList.remove('active', 'border-primary', 'bg-gray-50');
                    c.classList.add('border-gray-300');
                });
                this.classList.add('active', 'border-primary', 'bg-gray-50');
                this.classList.remove('border-gray-300');
                document.getElementById('userType').value = this.dataset.type;
                
                // Show/hide alumni and student fields
                const alumniFields = document.getElementById('alumniFields');
                const studentFields = document.getElementById('studentFields');
                if (this.dataset.type === 'alumni') {
                    alumniFields.style.display = 'block';
                    studentFields.style.display = 'none';
                } else {
                    alumniFields.style.display = 'none';
                    studentFields.style.display = 'block';
                }
            });
        });
        
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        
        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('confirm_password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
