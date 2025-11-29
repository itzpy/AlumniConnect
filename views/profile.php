<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login/login.php");
    exit();
}

$user_name = $_SESSION['name'] ?? 'User';
$user_type = $_SESSION['user_type'] ?? 'student';
$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['email'] ?? '';

// Include database class
require_once(dirname(__FILE__).'/../settings/db_class.php');

// Fetch user data from database
$db = new db_connection();

// Get basic user info
$user_data = $db->db_fetch_one("SELECT * FROM users WHERE user_id = '$user_id'");

// Get profile data based on user type
if ($user_type === 'alumni') {
    $profile_data = $db->db_fetch_one("SELECT * FROM alumni_profiles WHERE user_id = '$user_id'");
} else {
    $profile_data = $db->db_fetch_one("SELECT * FROM student_profiles WHERE user_id = '$user_id'");
}

// Merge data for easier access
$profile = array_merge($user_data ?? [], $profile_data ?? []);

// Set defaults if data not found
if (!$user_data) {
    $profile = [
        'first_name' => explode(' ', $user_name)[0] ?? $user_name,
        'last_name' => explode(' ', $user_name)[1] ?? '',
        'email' => $user_email,
        'user_role' => $user_type,
        'bio' => '',
        'phone' => ''
    ];
}

$success_message = '';
$error_message = '';

// Handle profile image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_image'])) {
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        $file = $_FILES['profile_image'];
        
        if (!in_array($file['type'], $allowed_types)) {
            $error_message = "Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.";
        } elseif ($file['size'] > $max_size) {
            $error_message = "File too large. Maximum size is 2MB.";
        } else {
            $upload_dir = dirname(__DIR__) . '/uploads/profiles/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Delete old image if exists
            if (!empty($user_data['profile_image'])) {
                $old_file = $upload_dir . $user_data['profile_image'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
            
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'user_' . $user_id . '_' . time() . '.' . $extension;
            $filepath = $upload_dir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                $db->db_connect();
                $update_sql = "UPDATE users SET profile_image = '" . mysqli_real_escape_string($db->db, $filename) . "' WHERE user_id = '$user_id'";
                if ($db->db_write_query($update_sql)) {
                    $success_message = "Profile picture updated successfully!";
                    $user_data['profile_image'] = $filename;
                    $profile['profile_image'] = $filename;
                } else {
                    $error_message = "Failed to update database.";
                }
            } else {
                $error_message = "Failed to upload image.";
            }
        }
    } else {
        $error_message = "Please select an image to upload.";
    }
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $db->db_connect();
    $bio = mysqli_real_escape_string($db->db, $_POST['bio'] ?? '');
    $phone = mysqli_real_escape_string($db->db, $_POST['phone'] ?? '');
    
    // Update users table
    $update_user = "UPDATE users SET bio = '$bio', phone = '$phone' WHERE user_id = '$user_id'";
    $db->db_write_query($update_user);
    
    // Update profile table based on user type
    if ($user_type === 'alumni') {
        $company = mysqli_real_escape_string($db->db, $_POST['company'] ?? '');
        $position = mysqli_real_escape_string($db->db, $_POST['position'] ?? '');
        $location_city = mysqli_real_escape_string($db->db, $_POST['location'] ?? '');
        $graduation_year = mysqli_real_escape_string($db->db, $_POST['graduation_year'] ?? '');
        
        // Check if profile exists
        $existing = $db->db_fetch_one("SELECT profile_id FROM alumni_profiles WHERE user_id = '$user_id'");
        if ($existing) {
            $update_profile = "UPDATE alumni_profiles SET 
                current_company = '$company',
                job_title = '$position',
                location_city = '$location_city',
                graduation_year = '$graduation_year'
                WHERE user_id = '$user_id'";
        } else {
            $update_profile = "INSERT INTO alumni_profiles (user_id, current_company, job_title, location_city, graduation_year, major) 
                VALUES ('$user_id', '$company', '$position', '$location_city', '$graduation_year', 'Computer Science')";
        }
        $db->db_write_query($update_profile);
    } else {
        $major = mysqli_real_escape_string($db->db, $_POST['major'] ?? '');
        $graduation_year = mysqli_real_escape_string($db->db, $_POST['graduation_year'] ?? '');
        $career_goals = mysqli_real_escape_string($db->db, $_POST['career_goals'] ?? '');
        
        $existing = $db->db_fetch_one("SELECT profile_id FROM student_profiles WHERE user_id = '$user_id'");
        if ($existing) {
            $update_profile = "UPDATE student_profiles SET 
                major = '$major',
                expected_graduation = '$graduation_year',
                career_goals = '$career_goals'
                WHERE user_id = '$user_id'";
        } else {
            $update_profile = "INSERT INTO student_profiles (user_id, major, expected_graduation, career_goals, year_level) 
                VALUES ('$user_id', '$major', '$graduation_year', '$career_goals', 'Junior')";
        }
        $db->db_write_query($update_profile);
    }
    
    $success_message = "Profile updated successfully!";
    
    // Refresh data
    $user_data = $db->db_fetch_one("SELECT * FROM users WHERE user_id = '$user_id'");
    if ($user_type === 'alumni') {
        $profile_data = $db->db_fetch_one("SELECT * FROM alumni_profiles WHERE user_id = '$user_id'");
    } else {
        $profile_data = $db->db_fetch_one("SELECT * FROM student_profiles WHERE user_id = '$user_id'");
    }
    $profile = array_merge($user_data ?? [], $profile_data ?? []);
}

// Get cart count for navbar
require_once(dirname(__FILE__).'/../classes/cart_class.php');
$cart = new Cart();
$cart_count = $cart->getCartCount($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Alumni Connect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
    <style>
        body { font-family: 'Inter', sans-serif; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.3s ease-out; }
    </style>
</head>
<body class="bg-gray-50 font-sans" x-data="{ showEditModal: false }">
    <?php include 'includes/navbar.php'; ?>

    <div class="flex">
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 p-6 lg:p-8">
            <div class="max-w-5xl mx-auto">
                
                <!-- Success/Error Messages -->
                <?php if ($success_message): ?>
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg animate-fadeIn">
                    <i class="fas fa-check-circle mr-2"></i><?php echo $success_message; ?>
                </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg animate-fadeIn">
                    <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error_message; ?>
                </div>
                <?php endif; ?>
                
                <!-- Cover & Profile Header -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                    <!-- Cover Photo -->
                    <div class="h-48 bg-gradient-to-r from-primary-dark to-primary relative">
                        <div class="absolute inset-0 bg-black/10"></div>
                        <div class="absolute bottom-4 left-8 text-white/80 text-sm">
                            <i class="fas fa-user-graduate mr-1"></i>
                            <?php echo ucfirst($user_type); ?> Profile
                        </div>
                    </div>

                    <!-- Profile Info -->
                    <div class="px-8 pb-8">
                        <div class="flex flex-col md:flex-row md:items-end md:justify-between -mt-16">
                            <div class="flex flex-col md:flex-row md:items-end space-y-4 md:space-y-0 md:space-x-6">
                                <!-- Profile Picture -->
                                <div class="relative group">
                                    <?php if (!empty($profile['profile_image'])): ?>
                                        <img src="../uploads/profiles/<?php echo htmlspecialchars($profile['profile_image']); ?>" 
                                             alt="Profile" class="w-32 h-32 rounded-full border-4 border-white shadow-lg object-cover">
                                    <?php else: ?>
                                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode(($profile['first_name'] ?? '') . '+' . ($profile['last_name'] ?? '')); ?>&background=7A1E1E&color=fff&size=150" 
                                             alt="Profile" class="w-32 h-32 rounded-full border-4 border-white shadow-lg">
                                    <?php endif; ?>
                                    <div class="absolute bottom-0 right-0 bg-green-500 w-6 h-6 rounded-full border-2 border-white flex items-center justify-center">
                                        <i class="fas fa-check text-white text-xs"></i>
                                    </div>
                                    <!-- Upload overlay -->
                                    <label for="profile_image_input" class="absolute inset-0 bg-black/50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                        <i class="fas fa-camera text-white text-2xl"></i>
                                    </label>
                                </div>

                                <!-- Name & Title -->
                                <div class="pb-2">
                                    <h1 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? '')); ?></h1>
                                    <p class="text-lg text-gray-600 mb-2">
                                        <?php 
                                        $position = $profile['job_title'] ?? '';
                                        $company = $profile['current_company'] ?? '';
                                        if ($position && $company) {
                                            echo htmlspecialchars($position . ' at ' . $company);
                                        } elseif ($position) {
                                            echo htmlspecialchars($position);
                                        } elseif ($company) {
                                            echo 'Works at ' . htmlspecialchars($company);
                                        } else {
                                            echo ucfirst($user_type) . ' at Ashesi University';
                                        }
                                        ?>
                                    </p>
                                    <div class="flex flex-wrap gap-3 text-sm text-gray-500">
                                        <?php if (!empty($profile['location_city'])): ?>
                                        <span><i class="fas fa-map-marker-alt mr-1"></i><?php echo htmlspecialchars($profile['location_city']); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($profile['graduation_year']) || !empty($profile['expected_graduation'])): ?>
                                        <span><i class="fas fa-graduation-cap mr-1"></i>Class of <?php echo htmlspecialchars($profile['graduation_year'] ?? $profile['expected_graduation'] ?? ''); ?></span>
                                        <?php endif; ?>
                                        <span><i class="fas fa-envelope mr-1"></i><?php echo htmlspecialchars($profile['email'] ?? $user_email); ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex space-x-3 mt-4 md:mt-0">
                                <button @click="showEditModal = true" class="bg-primary text-white px-6 py-2 rounded-lg font-medium hover:bg-primary-dark transition-colors">
                                    <i class="fas fa-edit mr-2"></i>Edit Profile
                                </button>
                                <button onclick="shareProfile()" class="border-2 border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
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
                                <?php if ($user_type === 'alumni'): ?>
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-briefcase text-gray-400 w-5 mt-1"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">Current Position</p>
                                        <p class="font-medium text-gray-900"><?php echo htmlspecialchars($profile['job_title'] ?? 'Not specified'); ?></p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-building text-gray-400 w-5 mt-1"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">Company</p>
                                        <p class="font-medium text-gray-900"><?php echo htmlspecialchars($profile['current_company'] ?? 'Not specified'); ?></p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-map-marker-alt text-gray-400 w-5 mt-1"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">Location</p>
                                        <p class="font-medium text-gray-900"><?php echo htmlspecialchars($profile['location_city'] ?? 'Not specified'); ?></p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-phone text-gray-400 w-5 mt-1"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">Phone</p>
                                        <p class="font-medium text-gray-900"><?php echo htmlspecialchars($profile['phone'] ?? 'Not specified'); ?></p>
                                    </div>
                                </div>
                                <?php else: ?>
                                <!-- Student specific info -->
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-book text-gray-400 w-5 mt-1"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">Major</p>
                                        <p class="font-medium text-gray-900"><?php echo htmlspecialchars($profile['major'] ?? 'Not specified'); ?></p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-graduation-cap text-gray-400 w-5 mt-1"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">Expected Graduation</p>
                                        <p class="font-medium text-gray-900"><?php echo htmlspecialchars($profile['expected_graduation'] ?? 'Not specified'); ?></p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-phone text-gray-400 w-5 mt-1"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">Phone</p>
                                        <p class="font-medium text-gray-900"><?php echo htmlspecialchars($profile['phone'] ?? 'Not specified'); ?></p>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-user-tag text-gray-400 w-5 mt-1"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">Account Type</p>
                                        <p class="font-medium text-gray-900"><?php echo ucfirst($user_type); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Activity</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center p-3 bg-gray-50 rounded-lg">
                                    <p class="text-2xl font-bold text-primary">12</p>
                                    <p class="text-sm text-gray-500">Connections</p>
                                </div>
                                <div class="text-center p-3 bg-gray-50 rounded-lg">
                                    <p class="text-2xl font-bold text-primary">5</p>
                                    <p class="text-sm text-gray-500">Events</p>
                                </div>
                                <div class="text-center p-3 bg-gray-50 rounded-lg">
                                    <p class="text-2xl font-bold text-primary">3</p>
                                    <p class="text-sm text-gray-500">Mentorships</p>
                                </div>
                                <div class="text-center p-3 bg-gray-50 rounded-lg">
                                    <p class="text-2xl font-bold text-primary">8</p>
                                    <p class="text-sm text-gray-500">Messages</p>
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
                                <button @click="showEditModal = true" class="text-primary text-sm font-medium hover:underline">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </button>
                            </div>
                            <?php if (!empty($profile['bio'])): ?>
                            <p class="text-gray-700 leading-relaxed"><?php echo nl2br(htmlspecialchars($profile['bio'])); ?></p>
                            <?php else: ?>
                            <p class="text-gray-400 italic">No bio added yet. Click Edit to add one!</p>
                            <?php endif; ?>
                        </div>

                        <!-- Education Section -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Education</h3>
                            <div class="flex space-x-4">
                                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-graduation-cap text-primary text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900"><?php echo htmlspecialchars($profile['university'] ?? 'Ashesi University'); ?></h4>
                                    <p class="text-gray-600"><?php echo htmlspecialchars($profile['major'] ?? 'Bachelor\'s Degree'); ?></p>
                                    <?php 
                                    $grad_year = $profile['graduation_year'] ?? $profile['expected_graduation'] ?? '';
                                    if (!empty($grad_year)): 
                                    ?>
                                    <p class="text-sm text-gray-500">Class of <?php echo htmlspecialchars($grad_year); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Activity</h3>
                            <div class="space-y-4">
                                <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-calendar text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-900">Registered for <span class="font-medium">Alumni Networking Event</span></p>
                                        <p class="text-xs text-gray-500">2 days ago</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-handshake text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-900">Connected with <span class="font-medium">3 new alumni</span></p>
                                        <p class="text-xs text-gray-500">1 week ago</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-user-plus text-purple-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-900">Joined <span class="font-medium">Alumni Connect</span></p>
                                        <p class="text-xs text-gray-500"><?php echo date('M j, Y', strtotime($user_data['created_at'] ?? 'now')); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Edit Profile Modal -->
    <div x-show="showEditModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
         @click.self="showEditModal = false"
         style="display: none;">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Edit Profile</h3>
                <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form method="POST" class="p-6 space-y-6">
                <input type="hidden" name="update_profile" value="1">
                
                <!-- Profile Picture in Modal -->
                <div class="flex items-center space-x-4">
                    <div class="relative group">
                        <?php if (!empty($profile['profile_image'])): ?>
                            <img src="../uploads/profiles/<?php echo htmlspecialchars($profile['profile_image']); ?>" 
                                 alt="Profile" class="w-20 h-20 rounded-full object-cover border-2 border-gray-200">
                        <?php else: ?>
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode(($profile['first_name'] ?? '') . '+' . ($profile['last_name'] ?? '')); ?>&background=7A1E1E&color=fff&size=80" 
                                 alt="Profile" class="w-20 h-20 rounded-full border-2 border-gray-200">
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="profile_image_input" class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                            <i class="fas fa-camera mr-2"></i>Change Photo
                        </label>
                        <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF up to 2MB</p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                    <textarea name="bio" rows="4" placeholder="Tell us about yourself..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"><?php echo htmlspecialchars($profile['bio'] ?? ''); ?></textarea>
                </div>
                
                <?php if ($user_type === 'alumni'): ?>
                <!-- Alumni specific fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Position/Title</label>
                        <input type="text" name="position" placeholder="e.g., Software Engineer"
                               value="<?php echo htmlspecialchars($profile['job_title'] ?? ''); ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                        <input type="text" name="company" placeholder="e.g., Google"
                               value="<?php echo htmlspecialchars($profile['current_company'] ?? ''); ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                        <input type="text" name="location" placeholder="e.g., Accra, Ghana"
                               value="<?php echo htmlspecialchars($profile['location_city'] ?? ''); ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="text" name="phone" placeholder="e.g., +233 XX XXX XXXX"
                               value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Graduation Year</label>
                    <select name="graduation_year" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                        <option value="">Select year</option>
                        <?php 
                        $current_year = date('Y');
                        for ($year = $current_year; $year >= 2000; $year--): 
                            $selected = ($profile['graduation_year'] ?? '') == $year ? 'selected' : '';
                        ?>
                        <option value="<?php echo $year; ?>" <?php echo $selected; ?>><?php echo $year; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <?php else: ?>
                <!-- Student specific fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Major</label>
                        <input type="text" name="major" placeholder="e.g., Computer Science"
                               value="<?php echo htmlspecialchars($profile['major'] ?? ''); ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expected Graduation</label>
                        <select name="graduation_year" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                            <option value="">Select year</option>
                            <?php 
                            $current_year = date('Y');
                            for ($year = $current_year; $year <= $current_year + 6; $year++): 
                                $selected = ($profile['expected_graduation'] ?? '') == $year ? 'selected' : '';
                            ?>
                            <option value="<?php echo $year; ?>" <?php echo $selected; ?>><?php echo $year; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="text" name="phone" placeholder="e.g., +233 XX XXX XXXX"
                           value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Career Goals</label>
                    <textarea name="career_goals" rows="3" placeholder="What are your career aspirations?"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"><?php echo htmlspecialchars($profile['career_goals'] ?? ''); ?></textarea>
                </div>
                <?php endif; ?>
                
                <div class="flex space-x-4 pt-4 border-t border-gray-200">
                    <button type="button" @click="showEditModal = false" 
                            class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50"></div>

    <!-- Hidden Profile Image Upload Form -->
    <form id="profileImageForm" method="POST" enctype="multipart/form-data" class="hidden">
        <input type="hidden" name="upload_image" value="1">
        <input type="file" name="profile_image" id="profile_image_input" accept="image/*" onchange="document.getElementById('profileImageForm').submit();">
    </form>

    <script>
        function shareProfile() {
            if (navigator.share) {
                navigator.share({
                    title: '<?php echo addslashes($user_data['name'] ?? $user_name); ?> - Alumni Connect',
                    text: 'Check out my profile on Alumni Connect!',
                    url: window.location.href
                });
            } else {
                navigator.clipboard.writeText(window.location.href);
                showToast('Profile link copied to clipboard!', 'success');
            }
        }
        
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
            const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle';
            
            toast.className = `px-6 py-3 rounded-lg text-white font-medium shadow-lg animate-fadeIn mb-2 ${bgColor}`;
            toast.innerHTML = `<i class="fas fa-${icon} mr-2"></i>${message}`;
            document.getElementById('toast-container').appendChild(toast);
            
            setTimeout(() => toast.remove(), 3000);
        }
    </script>
</body>
</html>
