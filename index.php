<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Alumni Connect - Building Bridges, Creating Futures</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#7A1E1E',
                        'primary-dark': '#5a1616',
                        'primary-light': '#9a2e2e',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans">
    <?php
    session_start();
    $isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    ?>

    <!-- Navigation Bar -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="index.php" class="flex items-center space-x-2 text-xl font-bold text-primary">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Alumni Connect</span>
                </a>
                <ul class="hidden md:flex items-center space-x-8">
                    <?php if ($isLoggedIn): ?>
                        <li><a href="views/dashboard.php" class="text-gray-700 hover:text-primary transition-colors"><i class="fas fa-home mr-1"></i> Dashboard</a></li>
                        <li><a href="views/services.php" class="text-gray-700 hover:text-primary transition-colors"><i class="fas fa-shopping-bag mr-1"></i> Services</a></li>
                        <li><a href="views/alumni_search.php" class="text-gray-700 hover:text-primary transition-colors"><i class="fas fa-search mr-1"></i> Find Alumni</a></li>
                        <li><a href="views/jobs.php" class="text-gray-700 hover:text-primary transition-colors"><i class="fas fa-briefcase mr-1"></i> Jobs</a></li>
                        <li><a href="views/events.php" class="text-gray-700 hover:text-primary transition-colors"><i class="fas fa-calendar mr-1"></i> Events</a></li>
                        <li><a href="views/messages.php" class="text-gray-700 hover:text-primary transition-colors"><i class="fas fa-envelope mr-1"></i> Messages</a></li>
                        <li><a href="views/profile.php" class="text-gray-700 hover:text-primary transition-colors"><i class="fas fa-user mr-1"></i> Profile</a></li>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 1): ?>
                            <li><a href="admin/dashboard.php" class="text-gray-700 hover:text-primary transition-colors"><i class="fas fa-cog mr-1"></i> Admin</a></li>
                        <?php endif; ?>
                        <li><a href="login/logout.php" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors"><i class="fas fa-sign-out-alt mr-1"></i> Logout</a></li>
                    <?php else: ?>
                        <li><a href="#features" class="text-gray-700 hover:text-primary transition-colors">Features</a></li>
                        <li><a href="#about" class="text-gray-700 hover:text-primary transition-colors">About</a></li>
                        <li><a href="#contact" class="text-gray-700 hover:text-primary transition-colors">Contact</a></li>
                        <li><a href="login/login.php" class="border-2 border-primary text-primary px-4 py-2 rounded-lg hover:bg-primary hover:text-white transition-colors">Login</a></li>
                        <li><a href="login/register.php" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-primary-dark to-primary text-white py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-5xl md:text-6xl font-bold mb-6">Building Bridges, Creating Futures</h1>
            <p class="text-xl md:text-2xl mb-8 text-gray-100">Connect with fellow alumni, find mentors, discover opportunities, and give back to your alma mater</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <?php if (!$isLoggedIn): ?>
                    <a href="login/register.php" class="bg-white text-primary px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-colors inline-block">Get Started</a>
                    <a href="#features" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-primary transition-colors inline-block">Learn More</a>
                <?php else: ?>
                    <a href="views/dashboard.php" class="bg-white text-primary px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-colors inline-block">Go to Dashboard</a>
                    <a href="views/alumni_search.php" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-primary transition-colors inline-block">Find Alumni</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 text-center">
                <div>
                    <h3 class="text-5xl font-bold text-primary mb-2">10,000+</h3>
                    <p class="text-gray-600 text-lg">Active Alumni</p>
                </div>
                <div>
                    <h3 class="text-5xl font-bold text-primary mb-2">500+</h3>
                    <p class="text-gray-600 text-lg">Mentorship Connections</p>
                </div>
                <div>
                    <h3 class="text-5xl font-bold text-primary mb-2">1,200+</h3>
                    <p class="text-gray-600 text-lg">Job Opportunities</p>
                </div>
                <div>
                    <h3 class="text-5xl font-bold text-primary mb-2">50+</h3>
                    <p class="text-gray-600 text-lg">Annual Events</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-center text-gray-900 mb-12">Connect, Engage, and Grow</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="text-5xl text-primary mb-4"><i class="fas fa-search"></i></div>
                    <h3 class="text-2xl font-semibold mb-3">Find Alumni</h3>
                    <p class="text-gray-600">Search and connect with alumni by industry, location, graduation year, or interests. Build your professional network.</p>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="text-5xl text-primary mb-4"><i class="fas fa-hands-helping"></i></div>
                    <h3 class="text-2xl font-semibold mb-3">Mentorship Programs</h3>
                    <p class="text-gray-600">Connect with experienced alumni mentors or become a mentor yourself. Guide the next generation of professionals.</p>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="text-5xl text-primary mb-4"><i class="fas fa-briefcase"></i></div>
                    <h3 class="text-2xl font-semibold mb-3">Job Board</h3>
                    <p class="text-gray-600">Access exclusive job opportunities shared by alumni. Post openings and help fellow graduates advance their careers.</p>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="text-5xl text-primary mb-4"><i class="fas fa-calendar-alt"></i></div>
                    <h3 class="text-2xl font-semibold mb-3">Events & Networking</h3>
                    <p class="text-gray-600">Stay updated on alumni events, reunions, and networking opportunities. Never miss a chance to reconnect.</p>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="text-5xl text-primary mb-4"><i class="fas fa-comments"></i></div>
                    <h3 class="text-2xl font-semibold mb-3">Community Feed</h3>
                    <p class="text-gray-600">Share updates, celebrate achievements, and engage with the alumni community through our interactive feed.</p>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="text-5xl text-primary mb-4"><i class="fas fa-graduation-cap"></i></div>
                    <h3 class="text-2xl font-semibold mb-3">Give Back</h3>
                    <p class="text-gray-600">Support current students through scholarships, internships, and career guidance. Make a lasting impact.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-center text-gray-900 mb-8">About Alumni Connect</h2>
            <div class="max-w-4xl mx-auto">
                <p class="text-lg text-gray-700 leading-relaxed mb-6">
                    Alumni Connect is more than just a networking platform—it's a thriving community where graduates 
                    stay connected to their alma mater and each other. Whether you're looking for career opportunities, 
                    seeking mentorship, or wanting to give back to the next generation, Alumni Connect provides the 
                    tools and connections you need to succeed.
                </p>
                <p class="text-lg text-gray-700 leading-relaxed">
                    Our mission is to foster lifelong relationships between alumni, students, and the university, 
                    creating a powerful network that benefits everyone involved.
                </p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-gradient-to-r from-primary-dark to-primary text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold mb-4">Ready to Reconnect?</h2>
            <p class="text-xl mb-8">Join thousands of alumni building meaningful connections</p>
            <?php if (!$isLoggedIn): ?>
                <a href="login/register.php" class="bg-white text-primary px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-colors inline-block">
                    Create Your Account
                </a>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                <div>
                    <h4 class="text-lg font-semibold mb-4"><i class="fas fa-graduation-cap mr-2"></i>Alumni Connect</h4>
                    <p class="text-gray-400">Building bridges between alumni and creating futures together.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#features" class="text-gray-400 hover:text-white transition-colors">Features</a></li>
                        <li><a href="#about" class="text-gray-400 hover:text-white transition-colors">About Us</a></li>
                        <li><a href="views/events.php" class="text-gray-400 hover:text-white transition-colors">Events</a></li>
                        <li><a href="views/jobs.php" class="text-gray-400 hover:text-white transition-colors">Jobs</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Support</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact Us</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Connect With Us</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-facebook mr-2"></i>Facebook</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-twitter mr-2"></i>Twitter</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-linkedin mr-2"></i>LinkedIn</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-instagram mr-2"></i>Instagram</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center">
                <p class="text-gray-400">&copy; 2025 Alumni Connect. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
