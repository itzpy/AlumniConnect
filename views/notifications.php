<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

$page_title = "Notifications";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Alumni Connect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#7A1E1E',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="dashboard.php" class="text-2xl font-bold text-primary">Alumni Connect</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="dashboard.php" class="text-gray-600 hover:text-primary transition-colors">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>
                    <a href="services.php" class="text-gray-600 hover:text-primary transition-colors">
                        <i class="fas fa-shopping-bag mr-2"></i>Services
                    </a>
                    <a href="cart.php" class="text-gray-600 hover:text-primary transition-colors">
                        <i class="fas fa-shopping-cart mr-2"></i>Cart
                    </a>
                    <a href="../login/logout.php" class="text-gray-600 hover:text-primary transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">
                <i class="fas fa-bell text-primary mr-3"></i>Notifications
            </h1>

            <!-- Welcome Message -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            Welcome, <strong><?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?></strong>! This is your notifications center.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Sample Notifications -->
            <div class="space-y-4">
                <!-- Notification 1 -->
                <div class="border-l-4 border-green-500 bg-green-50 p-4 rounded-r-lg hover:shadow-md transition-shadow">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-sm font-semibold text-gray-800">Welcome to Alumni Connect!</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Your account has been successfully created. Start exploring our premium services and connect with fellow alumni.
                            </p>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="far fa-clock mr-1"></i>Just now
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Notification 2 -->
                <div class="border-l-4 border-blue-500 bg-blue-50 p-4 rounded-r-lg hover:shadow-md transition-shadow">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-shopping-cart text-blue-500 text-xl"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-sm font-semibold text-gray-800">Explore Our Services</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Check out our premium services including job postings, mentorship programs, and exclusive events.
                            </p>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="far fa-clock mr-1"></i>2 minutes ago
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Notification 3 -->
                <div class="border-l-4 border-purple-500 bg-purple-50 p-4 rounded-r-lg hover:shadow-md transition-shadow">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-gift text-purple-500 text-xl"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-sm font-semibold text-gray-800">Special Offer Available</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Get 10% off on your first service purchase. Use code: WELCOME10
                            </p>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="far fa-clock mr-1"></i>1 hour ago
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State (for future when no notifications) -->
            <!-- Uncomment this when implementing dynamic notifications
            <div class="text-center py-12">
                <i class="fas fa-bell-slash text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No notifications yet</h3>
                <p class="text-gray-500">When you have new notifications, they will appear here.</p>
            </div>
            -->

            <!-- Action Buttons -->
            <div class="mt-8 flex justify-between items-center pt-6 border-t">
                <button onclick="markAllAsRead()" class="text-sm text-primary hover:text-red-800 transition-colors">
                    <i class="fas fa-check-double mr-2"></i>Mark all as read
                </button>
                <a href="dashboard.php" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-red-800 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <script>
        // Mark all notifications as read (placeholder function)
        function markAllAsRead() {
            console.log('Marking all notifications as read...');
            // In the future, this would make an AJAX call to update the database
            alert('All notifications marked as read!');
        }
    </script>
</body>
</html>
