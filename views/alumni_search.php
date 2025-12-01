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
require_once(dirname(__FILE__).'/../classes/connection_class.php');
require_once(dirname(__FILE__).'/../settings/db_class.php');

$cart = new Cart();
$cart_count = $cart->getCartCount($user_id);

$connection = new Connection();
$db = new db_connection();

// Get filter parameters from URL
$filter_name = isset($_GET['name']) ? trim($_GET['name']) : '';
$filter_major = isset($_GET['major']) ? trim($_GET['major']) : '';
$filter_year = isset($_GET['year']) ? intval($_GET['year']) : 0;
$filter_industry = isset($_GET['industry']) ? trim($_GET['industry']) : '';
$filter_location = isset($_GET['location']) ? trim($_GET['location']) : '';
$filter_type = isset($_GET['type']) ? trim($_GET['type']) : ''; // alumni, student, or all

// Build the query to get all active users (alumni and students)
$conditions = ["u.is_active = 1", "u.user_id != $user_id"];
$params = [];

// User type filter
if ($filter_type === 'alumni') {
    $conditions[] = "u.user_role = 'alumni'";
} elseif ($filter_type === 'student') {
    $conditions[] = "u.user_role = 'student'";
} else {
    $conditions[] = "u.user_role IN ('alumni', 'student')";
}

// Name filter
if (!empty($filter_name)) {
    $safe_name = $db->db->real_escape_string($filter_name);
    $conditions[] = "(u.first_name LIKE '%$safe_name%' OR u.last_name LIKE '%$safe_name%')";
}

// Major filter (from alumni_profiles or student_profiles)
if (!empty($filter_major)) {
    $safe_major = $db->db->real_escape_string($filter_major);
    $conditions[] = "(ap.major LIKE '%$safe_major%' OR sp.major LIKE '%$safe_major%')";
}

// Year filter
if ($filter_year > 0) {
    $conditions[] = "(ap.graduation_year = $filter_year OR sp.expected_graduation = $filter_year)";
}

// Industry filter (alumni only)
if (!empty($filter_industry)) {
    $safe_industry = $db->db->real_escape_string($filter_industry);
    $conditions[] = "ap.industry LIKE '%$safe_industry%'";
}

// Location filter
if (!empty($filter_location)) {
    $safe_location = $db->db->real_escape_string($filter_location);
    $conditions[] = "(ap.location_city LIKE '%$safe_location%' OR ap.location_country LIKE '%$safe_location%')";
}

$sql = "SELECT u.user_id, u.first_name, u.last_name, u.email, u.profile_image, u.user_role, u.bio,
               ap.major as alumni_major, ap.graduation_year, ap.current_company, ap.job_title, 
               ap.industry, ap.location_city, ap.location_country, ap.available_for_mentorship,
               sp.major as student_major, sp.expected_graduation, sp.year_level as current_year, sp.interests as career_interests
        FROM users u
        LEFT JOIN alumni_profiles ap ON u.user_id = ap.user_id
        LEFT JOIN student_profiles sp ON u.user_id = sp.user_id
        WHERE " . implode(" AND ", $conditions) . "
        ORDER BY u.user_role, u.first_name
        LIMIT 100";

$members = $db->db_fetch_all($sql);
if (!$members) $members = [];

$member_count = count($members);

// Get unique majors from both tables
$majors = $db->db_fetch_all("
    SELECT DISTINCT major FROM (
        SELECT major FROM alumni_profiles WHERE major IS NOT NULL AND major != ''
        UNION
        SELECT major FROM student_profiles WHERE major IS NOT NULL AND major != ''
    ) as all_majors ORDER BY major
");

// Get unique industries (alumni only)
$industries = $db->db_fetch_all("SELECT DISTINCT industry FROM alumni_profiles WHERE industry IS NOT NULL AND industry != '' ORDER BY industry");

// Get unique graduation years
$years = $db->db_fetch_all("
    SELECT DISTINCT year FROM (
        SELECT graduation_year as year FROM alumni_profiles WHERE graduation_year IS NOT NULL
        UNION
        SELECT expected_graduation as year FROM student_profiles WHERE expected_graduation IS NOT NULL
    ) as all_years ORDER BY year DESC
");

// Get user's connection statuses
$user_connections = [];
foreach ($members as $member) {
    $status = $connection->getConnectionStatus($user_id, $member['user_id']);
    $user_connections[$member['user_id']] = $status;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Network - Alumni Connect</title>
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
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Network</h1>
                    <p class="text-gray-600">Connect with alumni and students from your university</p>
                </div>

                <!-- Search and Filters -->
                <form id="searchForm" method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search by name</label>
                            <div class="relative">
                                <input type="text" name="name" id="nameSearch" value="<?php echo htmlspecialchars($filter_name); ?>" 
                                       placeholder="Search members..." 
                                       class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                                <i class="fas fa-search absolute left-3 top-4 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                            <select name="type" class="auto-filter w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors bg-white">
                                <option value="">All Members</option>
                                <option value="alumni" <?php echo $filter_type === 'alumni' ? 'selected' : ''; ?>>Alumni</option>
                                <option value="student" <?php echo $filter_type === 'student' ? 'selected' : ''; ?>>Students</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Major</label>
                            <select name="major" class="auto-filter w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors bg-white">
                                <option value="">All Majors</option>
                                <?php if ($majors): foreach ($majors as $m): ?>
                                    <option value="<?php echo htmlspecialchars($m['major']); ?>" <?php echo $filter_major === $m['major'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($m['major']); ?>
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                            <select name="year" class="auto-filter w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors bg-white">
                                <option value="">All Years</option>
                                <?php if ($years): foreach ($years as $y): ?>
                                    <option value="<?php echo $y['year']; ?>" <?php echo $filter_year == $y['year'] ? 'selected' : ''; ?>>
                                        <?php echo $y['year']; ?>
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Industry</label>
                            <select name="industry" class="auto-filter w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors bg-white">
                                <option value="">All Industries</option>
                                <?php if ($industries): foreach ($industries as $ind): ?>
                                    <option value="<?php echo htmlspecialchars($ind['industry']); ?>" <?php echo $filter_industry === $ind['industry'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($ind['industry']); ?>
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Second row with location and reset -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <input type="text" name="location" id="locationSearch" value="<?php echo htmlspecialchars($filter_location); ?>" 
                                   placeholder="City, Country" 
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                        </div>
                        
                        <div class="flex items-end">
                            <a href="alumni_search.php" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                                <i class="fas fa-redo mr-2"></i> Reset Filters
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Results Count -->
                <div class="flex items-center justify-between mb-6">
                    <p class="text-gray-600">Showing <span class="font-semibold"><?php echo $member_count; ?></span> members</p>
                </div>

                <!-- Members Grid -->
                <div id="alumniResults" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if (empty($members)): ?>
                        <div class="col-span-full text-center py-12">
                            <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">No members found</h3>
                            <p class="text-gray-500">Try adjusting your search filters</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($members as $member): 
                            $full_name = htmlspecialchars($member['first_name'] . ' ' . $member['last_name']);
                            $initials = strtoupper(substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1));
                            $is_alumni = $member['user_role'] === 'alumni';
                            
                            // Get major and year based on role
                            $major = $is_alumni ? ($member['alumni_major'] ?? '') : ($member['student_major'] ?? '');
                            $year = $is_alumni ? ($member['graduation_year'] ?? '') : ($member['expected_graduation'] ?? '');
                            $year_label = $is_alumni ? "Class of $year" : ($member['current_year'] ?? "Expected $year");
                            
                            // Location (alumni only)
                            $location = '';
                            if ($is_alumni && !empty($member['location_city'])) {
                                $location = $member['location_city'];
                                if (!empty($member['location_country'])) {
                                    $location .= ', ' . $member['location_country'];
                                }
                            }
                            
                            // Get connection status
                            $conn_status = $user_connections[$member['user_id']] ?? null;
                            $is_self = ($member['user_id'] == $user_id);
                            
                            // Color coding by role
                            $role_color = $is_alumni ? 'primary' : 'blue-600';
                            $role_bg = $is_alumni ? 'bg-primary' : 'bg-blue-600';
                            $role_badge = $is_alumni ? 'Alumni' : 'Student';
                        ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                            <div class="flex flex-col items-center text-center">
                                <!-- Role Badge -->
                                <div class="self-end mb-2">
                                    <span class="px-2 py-1 <?php echo $is_alumni ? 'bg-primary/10 text-primary' : 'bg-blue-100 text-blue-700'; ?> text-xs rounded-full font-medium">
                                        <i class="fas <?php echo $is_alumni ? 'fa-user-graduate' : 'fa-user'; ?> mr-1"></i><?php echo $role_badge; ?>
                                    </span>
                                </div>
                                
                                <?php if (!empty($member['profile_image'])): ?>
                                    <img src="../uploads/profiles/<?php echo htmlspecialchars($member['profile_image']); ?>" 
                                         alt="<?php echo $full_name; ?>" class="w-24 h-24 rounded-full mb-4 object-cover">
                                <?php else: ?>
                                    <div class="w-24 h-24 rounded-full mb-4 <?php echo $role_bg; ?> flex items-center justify-center text-white text-2xl font-bold">
                                        <?php echo $initials; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <h3 class="text-lg font-bold text-gray-900"><?php echo $full_name; ?></h3>
                                
                                <?php if ($is_alumni && (!empty($member['job_title']) || !empty($member['current_company']))): ?>
                                    <p class="text-sm text-gray-600 mb-1">
                                        <?php 
                                        echo htmlspecialchars($member['job_title'] ?? '');
                                        if (!empty($member['current_company'])) {
                                            echo ' at ' . htmlspecialchars($member['current_company']);
                                        }
                                        ?>
                                    </p>
                                <?php elseif (!$is_alumni && !empty($member['career_interests'])): ?>
                                    <p class="text-sm text-gray-600 mb-1">
                                        Interested in: <?php echo htmlspecialchars($member['career_interests']); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <p class="text-xs text-gray-500 mb-4">
                                    <?php echo htmlspecialchars($major); ?> 
                                    <?php if (!empty($year)): ?>
                                        • <?php echo $year_label; ?>
                                    <?php endif; ?>
                                </p>
                                
                                <?php if ($location): ?>
                                <div class="flex items-center space-x-2 mb-4 text-xs text-gray-500">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($location); ?></span>
                                </div>
                                <?php endif; ?>

                                <div class="flex flex-wrap gap-2 mb-4 justify-center">
                                    <?php if ($is_alumni && !empty($member['industry'])): ?>
                                        <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-full"><?php echo htmlspecialchars($member['industry']); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($member['available_for_mentorship']) && $member['available_for_mentorship'] == 1): ?>
                                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full">
                                            <i class="fas fa-hands-helping mr-1"></i>Mentor
                                        </span>
                                    <?php endif; ?>
                                </div>

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
                                        <button onclick="sendConnection(<?php echo $member['user_id']; ?>, '<?php echo addslashes($full_name); ?>')" 
                                                class="flex-1 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors">
                                            <i class="fas fa-user-plus mr-1"></i> Connect
                                        </button>
                                    <?php endif; ?>
                                    <a href="messages.php?user=<?php echo $member['user_id']; ?>" 
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

        // Auto-filter on dropdown change
        document.querySelectorAll('.auto-filter').forEach(select => {
            select.addEventListener('change', function() {
                document.getElementById('searchForm').submit();
            });
        });

        // Debounce for text inputs (name and location search)
        let searchTimeout;
        document.getElementById('nameSearch').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('searchForm').submit();
            }, 500);
        });

        document.getElementById('locationSearch').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('searchForm').submit();
            }, 500);
        });
    </script>
</body>
</html>
