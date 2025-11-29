<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login/login.php");
    exit();
}

$user_name = $_SESSION['name'] ?? 'User';
$user_id = $_SESSION['user_id'] ?? 0;
$user_type = $_SESSION['user_type'] ?? 'student';

require_once(dirname(__FILE__).'/../classes/cart_class.php');
$cart = new Cart();
$cart_count = $cart->getCartCount($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Alumni - Alumni Connect</title>
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
<body class="bg-gray-50 font-sans">
    <?php include 'includes/navbar.php'; ?>

    <div class="flex">
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 p-6 lg:p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Find Alumni</h1>
                    <p class="text-gray-600">Connect with graduates from your alma mater</p>
                </div>

                <!-- Search and Filters -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search by name</label>
                            <div class="relative">
                                <input type="text" id="searchQuery" placeholder="Search alumni..." 
                                       class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                                <i class="fas fa-search absolute left-3 top-4 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Major</label>
                            <select id="majorFilter" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors bg-white">
                                <option value="">All Majors</option>
                                <option value="Computer Science">Computer Science</option>
                                <option value="Business">Business</option>
                                <option value="Engineering">Engineering</option>
                                <option value="Medicine">Medicine</option>
                                <option value="Arts">Arts</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Graduation Year</label>
                            <select id="yearFilter" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors bg-white">
                                <option value="">All Years</option>
                                <option value="2024">2024</option>
                                <option value="2023">2023</option>
                                <option value="2022">2022</option>
                                <option value="2021">2021</option>
                                <option value="2020">2020</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Industry</label>
                            <select id="industryFilter" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors bg-white">
                                <option value="">All Industries</option>
                                <option value="Technology">Technology</option>
                                <option value="Finance">Finance</option>
                                <option value="Healthcare">Healthcare</option>
                                <option value="Education">Education</option>
                                <option value="Consulting">Consulting</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <input type="text" id="locationFilter" placeholder="City, Country" 
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                        </div>

                        <div class="lg:col-span-2 flex items-end space-x-3">
                            <button onclick="searchAlumni()" class="flex-1 bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-dark transition-colors">
                                <i class="fas fa-search mr-2"></i> Search
                            </button>
                            <button onclick="resetFilters()" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                                <i class="fas fa-redo mr-2"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Results Count -->
                <div class="flex items-center justify-between mb-6">
                    <p class="text-gray-600">Showing <span class="font-semibold">248</span> alumni</p>
                    <div class="flex items-center space-x-2">
                        <label class="text-sm text-gray-600">Sort by:</label>
                        <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary">
                            <option>Most Relevant</option>
                            <option>Name (A-Z)</option>
                            <option>Recent Graduates</option>
                            <option>Location</option>
                        </select>
                    </div>
                </div>

                <!-- Alumni Grid -->
                <div id="alumniResults" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Alumni Card 1 -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                        <div class="flex flex-col items-center text-center">
                            <img src="https://ui-avatars.com/api/?name=Sarah+Johnson&background=2563eb&color=fff" 
                                 alt="Sarah Johnson" class="w-24 h-24 rounded-full mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Sarah Johnson</h3>
                            <p class="text-sm text-gray-600 mb-1">Software Engineer at Google</p>
                            <p class="text-xs text-gray-500 mb-4">Computer Science • Class of 2018</p>
                            
                            <div class="flex items-center space-x-2 mb-4 text-xs text-gray-500">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>San Francisco, CA</span>
                            </div>

                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">React</span>
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">Python</span>
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">AI/ML</span>
                            </div>

                            <div class="flex space-x-2 w-full">
                                <button class="flex-1 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors">
                                    <i class="fas fa-user-plus mr-1"></i> Connect
                                </button>
                                <button class="px-4 py-2 border-2 border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-envelope"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Alumni Card 2 -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                        <div class="flex flex-col items-center text-center">
                            <img src="https://ui-avatars.com/api/?name=Michael+Chen&background=059669&color=fff" 
                                 alt="Michael Chen" class="w-24 h-24 rounded-full mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Michael Chen</h3>
                            <p class="text-sm text-gray-600 mb-1">Product Manager at Microsoft</p>
                            <p class="text-xs text-gray-500 mb-4">Business • Class of 2016</p>
                            
                            <div class="flex items-center space-x-2 mb-4 text-xs text-gray-500">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Seattle, WA</span>
                            </div>

                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">Product</span>
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">Strategy</span>
                            </div>

                            <div class="flex space-x-2 w-full">
                                <button class="flex-1 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors">
                                    <i class="fas fa-user-plus mr-1"></i> Connect
                                </button>
                                <button class="px-4 py-2 border-2 border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-envelope"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Alumni Card 3 -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                        <div class="flex flex-col items-center text-center">
                            <img src="https://ui-avatars.com/api/?name=Emma+Davis&background=dc2626&color=fff" 
                                 alt="Emma Davis" class="w-24 h-24 rounded-full mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Emma Davis</h3>
                            <p class="text-sm text-gray-600 mb-1">Marketing Director at Apple</p>
                            <p class="text-xs text-gray-500 mb-4">Marketing • Class of 2019</p>
                            
                            <div class="flex items-center space-x-2 mb-4 text-xs text-gray-500">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Cupertino, CA</span>
                            </div>

                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">Marketing</span>
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">Branding</span>
                            </div>

                            <div class="flex space-x-2 w-full">
                                <button class="flex-1 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors">
                                    <i class="fas fa-user-plus mr-1"></i> Connect
                                </button>
                                <button class="px-4 py-2 border-2 border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-envelope"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- More cards would be loaded dynamically -->
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-center space-x-2 mt-8">
                    <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="px-4 py-2 bg-primary text-white rounded-lg font-medium">1</button>
                    <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">2</button>
                    <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">3</button>
                    <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function searchAlumni() {
            const query = document.getElementById('searchQuery').value;
            const major = document.getElementById('majorFilter').value;
            const year = document.getElementById('yearFilter').value;
            const industry = document.getElementById('industryFilter').value;
            const location = document.getElementById('locationFilter').value;
            
            console.log('Searching with:', { query, major, year, industry, location });
            // AJAX call would go here
        }

        function resetFilters() {
            document.getElementById('searchQuery').value = '';
            document.getElementById('majorFilter').value = '';
            document.getElementById('yearFilter').value = '';
            document.getElementById('industryFilter').value = '';
            document.getElementById('locationFilter').value = '';
        }
    </script>
</body>
</html>
