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
require_once(dirname(__FILE__).'/../classes/alumni_class.php');
require_once(dirname(__FILE__).'/../classes/connection_class.php');
require_once(dirname(__FILE__).'/../settings/db_class.php');

$cart = new Cart();
$cart_count = $cart->getCartCount($user_id);

// Get alumni data
$alumni = new AlumniProfile();
$connection = new Connection();

// Get filter parameters from URL
$filters = [];
if (!empty($_GET['name'])) $filters['name'] = trim($_GET['name']);
if (!empty($_GET['major'])) $filters['major'] = trim($_GET['major']);
if (!empty($_GET['year'])) $filters['graduation_year'] = intval($_GET['year']);
if (!empty($_GET['industry'])) $filters['industry'] = trim($_GET['industry']);
if (!empty($_GET['location'])) $filters['location'] = trim($_GET['location']);

// Search or get all alumni
$alumni_list = $alumni->searchAlumni($filters);
$alumni_count = count($alumni_list);

// Get unique majors, industries, years for filters
$db = new db_connection();
$majors = $db->db_fetch_all("SELECT DISTINCT major FROM alumni_profiles WHERE major IS NOT NULL ORDER BY major");
$industries = $db->db_fetch_all("SELECT DISTINCT industry FROM alumni_profiles WHERE industry IS NOT NULL ORDER BY industry");
$years = $db->db_fetch_all("SELECT DISTINCT graduation_year FROM alumni_profiles WHERE graduation_year IS NOT NULL ORDER BY graduation_year DESC");

// Get user's connection statuses
$user_connections = [];
foreach ($alumni_list as $alum) {
    $status = $connection->getConnectionStatus($user_id, $alum['user_id']);
    $user_connections[$alum['user_id']] = $status;
}
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
                <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search by name</label>
                            <div class="relative">
                                <input type="text" name="name" value="<?php echo htmlspecialchars($_GET['name'] ?? ''); ?>" 
                                       placeholder="Search alumni..." 
                                       class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                                <i class="fas fa-search absolute left-3 top-4 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Major</label>
                            <select name="major" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors bg-white">
                                <option value="">All Majors</option>
                                <?php if ($majors): foreach ($majors as $m): ?>
                                    <option value="<?php echo htmlspecialchars($m['major']); ?>" <?php echo (isset($_GET['major']) && $_GET['major'] == $m['major']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($m['major']); ?>
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Graduation Year</label>
                            <select name="year" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors bg-white">
                                <option value="">All Years</option>
                                <?php if ($years): foreach ($years as $y): ?>
                                    <option value="<?php echo $y['graduation_year']; ?>" <?php echo (isset($_GET['year']) && $_GET['year'] == $y['graduation_year']) ? 'selected' : ''; ?>>
                                        <?php echo $y['graduation_year']; ?>
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Industry</label>
                            <select name="industry" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors bg-white">
                                <option value="">All Industries</option>
                                <?php if ($industries): foreach ($industries as $ind): ?>
                                    <option value="<?php echo htmlspecialchars($ind['industry']); ?>" <?php echo (isset($_GET['industry']) && $_GET['industry'] == $ind['industry']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($ind['industry']); ?>
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <input type="text" name="location" value="<?php echo htmlspecialchars($_GET['location'] ?? ''); ?>" 
                                   placeholder="City, Country" 
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                        </div>

                        <div class="lg:col-span-2 flex items-end space-x-3">
                            <button type="submit" class="flex-1 bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-dark transition-colors">
                                <i class="fas fa-search mr-2"></i> Search
                            </button>
                            <a href="alumni_search.php" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                                <i class="fas fa-redo mr-2"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Results Count -->
                <div class="flex items-center justify-between mb-6">
                    <p class="text-gray-600">Showing <span class="font-semibold"><?php echo $alumni_count; ?></span> alumni</p>
                </div>

                <!-- Alumni Grid -->
                <div id="alumniResults" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if (empty($alumni_list)): ?>
                        <div class="col-span-full text-center py-12">
                            <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">No alumni found</h3>
                            <p class="text-gray-500">Try adjusting your search filters</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($alumni_list as $alum): 
                            $full_name = htmlspecialchars($alum['first_name'] . ' ' . $alum['last_name']);
                            $initials = strtoupper(substr($alum['first_name'], 0, 1) . substr($alum['last_name'], 0, 1));
                            $location = '';
                            if (!empty($alum['location_city'])) {
                                $location = $alum['location_city'];
                                if (!empty($alum['location_country'])) {
                                    $location .= ', ' . $alum['location_country'];
                                }
                            }
                            
                            // Get connection status
                            $conn_status = $user_connections[$alum['user_id']] ?? null;
                            $is_self = ($alum['user_id'] == $user_id);
                        ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                            <div class="flex flex-col items-center text-center">
                                <?php if (!empty($alum['profile_image'])): ?>
                                    <img src="../uploads/profiles/<?php echo htmlspecialchars($alum['profile_image']); ?>" 
                                         alt="<?php echo $full_name; ?>" class="w-24 h-24 rounded-full mb-4 object-cover">
                                <?php else: ?>
                                    <div class="w-24 h-24 rounded-full mb-4 bg-primary flex items-center justify-center text-white text-2xl font-bold">
                                        <?php echo $initials; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <h3 class="text-lg font-bold text-gray-900"><?php echo $full_name; ?></h3>
                                
                                <?php if (!empty($alum['job_title']) || !empty($alum['current_company'])): ?>
                                    <p class="text-sm text-gray-600 mb-1">
                                        <?php 
                                        echo htmlspecialchars($alum['job_title'] ?? '');
                                        if (!empty($alum['current_company'])) {
                                            echo ' at ' . htmlspecialchars($alum['current_company']);
                                        }
                                        ?>
                                    </p>
                                <?php endif; ?>
                                
                                <p class="text-xs text-gray-500 mb-4">
                                    <?php echo htmlspecialchars($alum['major'] ?? ''); ?> 
                                    <?php if (!empty($alum['graduation_year'])): ?>
                                        • Class of <?php echo $alum['graduation_year']; ?>
                                    <?php endif; ?>
                                </p>
                                
                                <?php if ($location): ?>
                                <div class="flex items-center space-x-2 mb-4 text-xs text-gray-500">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($location); ?></span>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($alum['industry'])): ?>
                                <div class="flex flex-wrap gap-2 mb-4">
                                    <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-full"><?php echo htmlspecialchars($alum['industry']); ?></span>
                                    <?php if (!empty($alum['available_for_mentorship']) && $alum['available_for_mentorship'] == 1): ?>
                                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full">
                                            <i class="fas fa-hands-helping mr-1"></i>Mentor
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>

                                <?php if (!$is_self): ?>
                                <div class="flex space-x-2 w-full">
                                    <?php if ($conn_status): ?>
                                        <?php if ($conn_status['status'] == 'accepted'): ?>
                                            <button disabled class="flex-1 bg-green-100 text-green-700 px-4 py-2 rounded-lg text-sm font-medium cursor-default">
                                                <i class="fas fa-check mr-1"></i> Connected
                                            </button>
                                        <?php elseif ($conn_status['status'] == 'pending'): ?>
                                            <?php if ($conn_status['requester_id'] == $user_id): ?>
                                                <button disabled class="flex-1 bg-yellow-100 text-yellow-700 px-4 py-2 rounded-lg text-sm font-medium cursor-default">
                                                    <i class="fas fa-clock mr-1"></i> Request Sent
                                                </button>
                                            <?php else: ?>
                                                <button onclick="handleConnection(<?php echo $conn_status['connection_id']; ?>, 'accept')" 
                                                        class="flex-1 bg-green-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-600 transition-colors">
                                                    <i class="fas fa-check mr-1"></i> Accept
                                                </button>
                                                <button onclick="handleConnection(<?php echo $conn_status['connection_id']; ?>, 'reject')" 
                                                        class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm hover:bg-red-200 transition-colors">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <button onclick="sendConnection(<?php echo $alum['user_id']; ?>, '<?php echo addslashes($full_name); ?>')" 
                                                class="flex-1 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors">
                                            <i class="fas fa-user-plus mr-1"></i> Connect
                                        </button>
                                    <?php endif; ?>
                                    <a href="messages.php?user=<?php echo $alum['user_id']; ?>" 
                                       class="px-4 py-2 border-2 border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                </div>
                                <?php else: ?>
                                <div class="w-full">
                                    <span class="text-sm text-gray-500 italic">This is you</span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

    <!-- Connection Modal -->
    <div id="connectionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Connect with <span id="modalUserName"></span></h3>
            <textarea id="connectionMessage" rows="3" placeholder="Add a note to your connection request (optional)..."
                      class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary mb-4"></textarea>
            <div class="flex space-x-3">
                <button onclick="submitConnection()" class="flex-1 bg-primary text-white px-4 py-3 rounded-lg font-semibold hover:bg-primary-dark transition-colors">
                    Send Request
                </button>
                <button onclick="closeConnectionModal()" class="px-4 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <script>
        let selectedUserId = null;

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `px-6 py-3 rounded-lg text-white font-medium shadow-lg ${type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'}`;
            toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>${message}`;
            document.getElementById('toast-container').appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function sendConnection(userId, userName) {
            selectedUserId = userId;
            document.getElementById('modalUserName').textContent = userName;
            document.getElementById('connectionMessage').value = '';
            document.getElementById('connectionModal').classList.remove('hidden');
            document.getElementById('connectionModal').classList.add('flex');
        }

        function closeConnectionModal() {
            document.getElementById('connectionModal').classList.add('hidden');
            document.getElementById('connectionModal').classList.remove('flex');
            selectedUserId = null;
        }

        function submitConnection() {
            if (!selectedUserId) return;
            
            const message = document.getElementById('connectionMessage').value;
            
            fetch('../actions/send_connection_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    receiver_id: selectedUserId,
                    message: message
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Connection request sent!', 'success');
                    closeConnectionModal();
                    // Reload to update UI
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message || 'Failed to send request', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to send request', 'error');
            });
        }

        function handleConnection(connectionId, action) {
            fetch('../actions/handle_connection_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    connection_id: connectionId,
                    action: action
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message || 'Action failed', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Action failed', 'error');
            });
        }

        // Close modal on outside click
        document.getElementById('connectionModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeConnectionModal();
            }
        });
    </script>
</body>
</html>
