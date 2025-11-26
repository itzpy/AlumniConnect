<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login/login.php");
    exit();
}

$user_name = $_SESSION['name'] ?? 'User';
$user_type = $_SESSION['user_type'] ?? 'student';
$user_id = $_SESSION['user_id'];

// Fetch user data (this would normally come from database)
// For now, using sample data
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Alumni Connect</title>
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
            <div class="max-w-5xl mx-auto">
                <!-- Cover & Profile Header -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                    <!-- Cover Photo -->
                    <div class="h-48 bg-gradient-to-r from-primary-dark to-primary relative">
                        <button class="absolute top-4 right-4 bg-white text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 transition-colors">
                            <i class="fas fa-camera mr-2"></i>Edit Cover
                        </button>
                    </div>

                    <!-- Profile Info -->
                    <div class="px-8 pb-8">
                        <div class="flex flex-col md:flex-row md:items-end md:justify-between -mt-16">
                            <div class="flex flex-col md:flex-row md:items-end space-y-4 md:space-y-0 md:space-x-6">
                                <!-- Profile Picture -->
                                <div class="relative">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=7A1E1E&color=fff&size=150" 
                                         alt="Profile" class="w-32 h-32 rounded-full border-4 border-white shadow-lg">
                                    <button class="absolute bottom-0 right-0 bg-primary text-white w-10 h-10 rounded-full shadow-lg hover:bg-primary-dark transition-colors">
                                        <i class="fas fa-camera"></i>
                                    </button>
                                </div>

                                <!-- Name & Title -->
                                <div class="pb-2">
                                    <h1 class="text-3xl font-bold text-gray-900"><?php echo $user_name; ?></h1>
                                    <p class="text-lg text-gray-600 mb-2">Software Engineer at Google</p>
                                    <div class="flex flex-wrap gap-3 text-sm text-gray-500">
                                        <span><i class="fas fa-map-marker-alt mr-1"></i> San Francisco, CA</span>
                                        <span><i class="fas fa-graduation-cap mr-1"></i> Computer Science '18</span>
                                        <span><i class="fas fa-envelope mr-1"></i> <?php echo $_SESSION['email']; ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex space-x-3 mt-4 md:mt-0">
                                <button onclick="toggleEditMode()" id="editBtn" class="bg-primary text-white px-6 py-2 rounded-lg font-medium hover:bg-primary-dark transition-colors">
                                    <i class="fas fa-edit mr-2"></i>Edit Profile
                                </button>
                                <button class="border-2 border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-share-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Content -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- About Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">About</h3>
                            <div class="space-y-3">
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-briefcase text-gray-400 w-5 mt-1"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">Current Position</p>
                                        <p class="font-medium text-gray-900">Software Engineer</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-building text-gray-400 w-5 mt-1"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">Company</p>
                                        <p class="font-medium text-gray-900">Google</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-industry text-gray-400 w-5 mt-1"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">Industry</p>
                                        <p class="font-medium text-gray-900">Technology</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-phone text-gray-400 w-5 mt-1"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">Phone</p>
                                        <p class="font-medium text-gray-900">+1 (555) 123-4567</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Skills Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Skills</h3>
                                <button class="text-primary text-sm font-medium hover:underline">
                                    <i class="fas fa-plus mr-1"></i>Add
                                </button>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full">React</span>
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full">Python</span>
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full">Node.js</span>
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full">AI/ML</span>
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full">AWS</span>
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full">Docker</span>
                            </div>
                        </div>

                        <!-- Connections Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Connections</h3>
                                <a href="connections.php" class="text-primary text-sm font-medium hover:underline">See all</a>
                            </div>
                            <p class="text-2xl font-bold text-primary mb-4">248</p>
                            <div class="flex -space-x-2 mb-3">
                                <img src="https://ui-avatars.com/api/?name=John+Doe&background=random" class="w-10 h-10 rounded-full border-2 border-white" alt="">
                                <img src="https://ui-avatars.com/api/?name=Jane+Smith&background=random" class="w-10 h-10 rounded-full border-2 border-white" alt="">
                                <img src="https://ui-avatars.com/api/?name=Mike+Johnson&background=random" class="w-10 h-10 rounded-full border-2 border-white" alt="">
                                <img src="https://ui-avatars.com/api/?name=Sarah+Williams&background=random" class="w-10 h-10 rounded-full border-2 border-white" alt="">
                                <div class="w-10 h-10 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center text-sm font-medium text-gray-600">
                                    +244
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Bio Section -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Bio</h3>
                                <button class="text-primary text-sm font-medium hover:underline">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </button>
                            </div>
                            <p class="text-gray-700 leading-relaxed">
                                Passionate software engineer with 6 years of experience in building scalable web applications. 
                                Currently working at Google on cutting-edge AI/ML projects. Love mentoring students and giving 
                                back to the community. Always eager to connect with fellow alumni and share experiences.
                            </p>
                        </div>

                        <!-- Experience Section -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Experience</h3>
                                <button class="text-primary text-sm font-medium hover:underline">
                                    <i class="fas fa-plus mr-1"></i>Add
                                </button>
                            </div>
                            
                            <div class="space-y-6">
                                <div class="flex space-x-4">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-building text-blue-600 text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900">Software Engineer</h4>
                                        <p class="text-gray-600">Google</p>
                                        <p class="text-sm text-gray-500">2020 - Present • 4 years</p>
                                        <p class="text-sm text-gray-700 mt-2">
                                            Working on AI/ML infrastructure, building scalable systems for machine learning models.
                                        </p>
                                    </div>
                                </div>

                                <div class="flex space-x-4">
                                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-building text-purple-600 text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900">Junior Developer</h4>
                                        <p class="text-gray-600">Tech Startup Inc.</p>
                                        <p class="text-sm text-gray-500">2018 - 2020 • 2 years</p>
                                        <p class="text-sm text-gray-700 mt-2">
                                            Built responsive web applications using React and Node.js.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Education Section -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Education</h3>
                            
                            <div class="flex space-x-4">
                                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-graduation-cap text-primary text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">Bachelor of Science in Computer Science</h4>
                                    <p class="text-gray-600">Your University Name</p>
                                    <p class="text-sm text-gray-500">Class of 2018</p>
                                    <p class="text-sm text-gray-700 mt-2">
                                        Graduated with honors. Focus on Software Engineering and AI.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleEditMode() {
            alert('Edit mode would open a form to update profile information');
        }
    </script>
</body>
</html>
