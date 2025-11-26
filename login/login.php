<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Alumni Connect</title>
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
<body class="bg-gradient-to-br from-primary-dark to-primary min-h-screen flex items-center justify-center p-4 font-sans">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden max-w-4xl w-full flex flex-col md:flex-row">
        <!-- Left Side - Branding -->
        <div class="bg-gradient-to-br from-primary-dark to-primary text-white p-12 md:w-1/2 flex flex-col justify-center">
            <div>
                <h2 class="text-3xl font-bold mb-4 flex items-center">
                    <i class="fas fa-graduation-cap mr-3"></i>Alumni Connect
                </h2>
                <p class="text-gray-100 mb-8 leading-relaxed">
                    Welcome back! Sign in to connect with fellow alumni, discover opportunities, and stay engaged with your alma mater community.
                </p>
                <div class="space-y-3">
                    <h4 class="font-semibold text-lg mb-4">Benefits of Joining:</h4>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-xl"></i>
                        <span>Network with 10,000+ alumni</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-xl"></i>
                        <span>Find mentors and career opportunities</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-xl"></i>
                        <span>Attend exclusive events</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-xl"></i>
                        <span>Give back to students</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="p-12 md:w-1/2">
            <div class="mb-8">
                <h3 class="text-3xl font-bold text-gray-900 mb-2">Sign In</h3>
                <p class="text-gray-600">Enter your credentials to access your account</p>
            </div>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                    <p class="text-red-700">
                        <?php 
                            if ($_GET['error'] == 'empty') echo 'Please fill in all fields';
                            elseif ($_GET['error'] == 'invalid') echo 'Invalid email or password';
                            else echo 'An error occurred';
                        ?>
                    </p>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success']) && $_GET['success'] == 'registered'): ?>
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4">
                    <p class="text-green-700">Registration successful! Please login.</p>
                </div>
            <?php endif; ?>
            
            <form action="../actions/login_action.php" method="POST" class="space-y-6">
                <div>
                    <label for="user_type" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-1"></i> Login as
                    </label>
                    <select id="user_type" name="user_type" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                        <option value="student">Student</option>
                        <option value="alumni">Alumni</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-1"></i> Email Address
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        placeholder="you@example.com"
                        value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors"
                    >
                </div>
                
                <div class="relative">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-1"></i> Password
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        placeholder="Enter your password"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors pr-12"
                    >
                    <i class="fas fa-eye absolute right-4 top-11 text-gray-400 cursor-pointer hover:text-gray-600" id="togglePassword"></i>
                </div>
                
                <div class="flex items-center justify-between">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="text-sm text-gray-700">Remember me</span>
                    </label>
                    <a href="forgot_password.php" class="text-sm text-primary hover:text-primary-dark transition-colors">Forgot Password?</a>
                </div>
                
                <button 
                    type="submit" 
                    class="w-full bg-primary text-white py-3 px-4 rounded-lg font-semibold hover:bg-primary-dark transition-colors flex items-center justify-center space-x-2"
                >
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Sign In</span>
                </button>
            </form>
            
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Don't have an account?</span>
                </div>
            </div>
            
            <a 
                href="register.php" 
                class="w-full border-2 border-primary text-primary py-3 px-4 rounded-lg font-semibold hover:bg-primary hover:text-white transition-colors flex items-center justify-center space-x-2"
            >
                <i class="fas fa-user-plus"></i>
                <span>Create Account</span>
            </a>
            
            <div class="text-center mt-6">
                <a href="../index.php" class="text-sm text-gray-600 hover:text-primary transition-colors">
                    <i class="fas fa-home mr-1"></i> Back to Home
                </a>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/login.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
