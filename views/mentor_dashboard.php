<?php
/**
 * Mentor Dashboard - For Alumni
 * Manage mentorship availability, view requests, and track sessions
 */

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? 'User';
$user_type = $_SESSION['user_type'] ?? 'student';

// Redirect students to the mentorship booking page
if ($user_type === 'student') {
    header("Location: mentorship.php");
    exit();
}

require_once(dirname(__FILE__).'/../settings/db_class.php');
require_once(dirname(__FILE__).'/../classes/cart_class.php');
require_once(dirname(__FILE__).'/../classes/service_class.php');

$db = new db_connection();
$db->db_connect();
$cart = new Cart();
$service = new Service();

$cart_count = $cart->getCartCount($user_id);

// Get alumni profile for mentorship status
$stmt = $db->db->prepare("SELECT * FROM alumni_profiles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$alumni_profile = $stmt->get_result()->fetch_assoc();

$is_available = $alumni_profile['available_for_mentorship'] ?? 0;

// Handle availability toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_availability'])) {
    $new_status = $is_available ? 0 : 1;
    $stmt = $db->db->prepare("UPDATE alumni_profiles SET available_for_mentorship = ? WHERE user_id = ?");
    $stmt->bind_param("ii", $new_status, $user_id);
    $stmt->execute();
    header("Location: mentor_dashboard.php");
    exit();
}

// Get mentorship services created by this alumni
$stmt = $db->db->prepare("SELECT * FROM services WHERE provider_id = ? AND service_type = 'mentorship' ORDER BY date_created DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$my_services = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get mentorship requests/bookings (orders containing this alumni's mentorship services)
$stmt = $db->db->prepare("
    SELECT o.*, oi.*, u.first_name, u.last_name, u.email, u.profile_image
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN users u ON o.user_id = u.user_id
    JOIN services s ON oi.service_id = s.service_id
    WHERE s.provider_id = ? AND s.service_type = 'mentorship'
    ORDER BY o.date_created DESC
    LIMIT 20
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Stats
$stmt = $db->db->prepare("
    SELECT COUNT(DISTINCT o.order_id) as total_bookings, 
           SUM(oi.total_price) as total_earned
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN services s ON oi.service_id = s.service_id
    WHERE s.provider_id = ? AND s.service_type = 'mentorship' AND o.payment_status = 'paid'
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentor Dashboard - Alumni Connect</title>
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
                    }
                }
            }
        }
    </script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50">
    <?php include 'includes/navbar.php'; ?>

    <div class="flex min-h-screen">
        <?php include 'includes/sidebar.php'; ?>

        <main class="flex-1 p-6 lg:p-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        <i class="fas fa-chalkboard-teacher text-primary mr-3"></i>Mentor Dashboard
                    </h1>
                    <p class="text-gray-600">Manage your mentorship offerings and track sessions</p>
                </div>
                
                <!-- Availability Toggle -->
                <form method="POST">
                    <button type="submit" name="toggle_availability" 
                            class="flex items-center space-x-2 px-6 py-3 rounded-lg font-medium transition-all
                            <?php echo $is_available ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">
                        <div class="w-3 h-3 rounded-full <?php echo $is_available ? 'bg-green-500' : 'bg-gray-400'; ?>"></div>
                        <span><?php echo $is_available ? 'Available for Mentorship' : 'Not Available'; ?></span>
                    </button>
                </form>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-purple-100 p-3 rounded-lg">
                            <i class="fas fa-handshake text-2xl text-purple-600"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1"><?php echo $stats['total_bookings'] ?? 0; ?></h3>
                    <p class="text-gray-600 text-sm">Total Sessions Booked</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-green-100 p-3 rounded-lg">
                            <i class="fas fa-coins text-2xl text-green-600"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1">GHS <?php echo number_format($stats['total_earned'] ?? 0, 2); ?></h3>
                    <p class="text-gray-600 text-sm">Total Earned</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-blue-100 p-3 rounded-lg">
                            <i class="fas fa-th-list text-2xl text-blue-600"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1"><?php echo count($my_services); ?></h3>
                    <p class="text-gray-600 text-sm">Active Offerings</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- My Mentorship Services -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-lg font-bold text-gray-900">My Mentorship Offerings</h2>
                            <button onclick="document.getElementById('createServiceModal').classList.remove('hidden')" 
                                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm">
                                <i class="fas fa-plus mr-2"></i>Create New
                            </button>
                        </div>

                        <?php if (!empty($my_services)): ?>
                            <div class="space-y-4">
                                <?php foreach ($my_services as $svc): ?>
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-primary transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="font-semibold text-gray-900"><?php echo htmlspecialchars($svc['service_name']); ?></h3>
                                            <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars(substr($svc['description'] ?? '', 0, 100)); ?>...</p>
                                            <div class="flex items-center space-x-4 mt-3 text-sm">
                                                <span class="text-primary font-semibold">GHS <?php echo number_format($svc['price'], 2); ?></span>
                                                <span class="text-gray-500"><?php echo $svc['duration'] ?? '60'; ?> mins</span>
                                                <span class="px-2 py-1 rounded-full text-xs <?php echo $svc['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'; ?>">
                                                    <?php echo $svc['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button class="text-gray-400 hover:text-primary">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-12">
                                <div class="text-gray-300 text-6xl mb-4">
                                    <i class="fas fa-chalkboard"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">No Mentorship Offerings Yet</h3>
                                <p class="text-gray-500 mb-4">Create your first mentorship session to start helping students</p>
                                <button onclick="document.getElementById('createServiceModal').classList.remove('hidden')" 
                                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                                    Create Your First Session
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Bookings -->
                <div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Recent Bookings</h2>
                        
                        <?php if (!empty($bookings)): ?>
                            <div class="space-y-4">
                                <?php foreach (array_slice($bookings, 0, 5) as $booking): ?>
                                <div class="flex items-center space-x-3 pb-4 border-b border-gray-100 last:border-0">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($booking['first_name'] . '+' . $booking['last_name']); ?>&background=7A1E1E&color=fff" 
                                         class="w-10 h-10 rounded-full">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 truncate">
                                            <?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?>
                                        </p>
                                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($booking['service_name']); ?></p>
                                    </div>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                        <?php echo $booking['fulfillment_status'] === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                        <?php echo ucfirst($booking['fulfillment_status'] ?? 'pending'); ?>
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-calendar-times text-4xl mb-2 text-gray-300"></i>
                                <p>No bookings yet</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tips Card -->
                    <div class="bg-gradient-to-br from-primary to-primary-dark rounded-xl p-6 mt-6 text-white">
                        <h3 class="font-bold mb-2"><i class="fas fa-lightbulb mr-2"></i>Mentor Tips</h3>
                        <ul class="text-sm space-y-2 text-white/90">
                            <li>• Keep your availability status updated</li>
                            <li>• Respond to booking requests within 24 hours</li>
                            <li>• Add detailed descriptions to your sessions</li>
                            <li>• Set competitive but fair pricing</li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Create Service Modal -->
    <div id="createServiceModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50" onclick="document.getElementById('createServiceModal').classList.add('hidden')"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Create Mentorship Session</h3>
                <form action="../actions/create_mentorship_service.php" method="POST">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Session Title</label>
                            <input type="text" name="service_name" required
                                   placeholder="e.g., Career Guidance in Tech"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" rows="3" required
                                      placeholder="Describe what students will learn..."
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Price (GHS)</label>
                                <input type="number" name="price" min="0" step="0.01" required
                                       placeholder="150.00"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Duration (mins)</label>
                                <select name="duration" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                                    <option value="30">30 minutes</option>
                                    <option value="60" selected>60 minutes</option>
                                    <option value="90">90 minutes</option>
                                    <option value="120">120 minutes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="document.getElementById('createServiceModal').classList.add('hidden')"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark">
                            Create Session
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
