<?php
/**
 * Mentorship Booking Page
 * Booking-style interface - Browse mentors and book sessions
 */

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? 'User';
$user_type = $_SESSION['user_type'] ?? 'student';

require_once(dirname(__FILE__).'/../classes/service_class.php');
require_once(dirname(__FILE__).'/../classes/cart_class.php');

$service = new Service();
$cart = new Cart();

// Get only mentorship services
$mentorships = $service->getAllServices('mentorship', null, null, null, null);
$cart_count = $cart->getCartCount($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find a Mentor - Alumni Connect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
    <style>
        body { font-family: 'Inter', sans-serif; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.4s ease-out; }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'includes/navbar.php'; ?>

    <div class="flex">
        <?php include 'includes/sidebar.php'; ?>

        <main class="flex-1 p-6 lg:p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">
                            <i class="fas fa-user-graduate text-primary mr-3"></i>
                            <?php echo $user_type === 'alumni' ? 'Mentorship Hub' : 'Find a Mentor'; ?>
                        </h1>
                        <p class="text-gray-600">
                            <?php echo $user_type === 'alumni' 
                                ? 'Browse fellow mentors or manage your own mentorship offerings' 
                                : 'Book 1-on-1 sessions with experienced alumni mentors'; ?>
                        </p>
                    </div>
                    <?php if ($user_type === 'alumni'): ?>
                        <a href="mentor_dashboard.php" 
                           class="mt-4 md:mt-0 inline-flex items-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-red-900 transition-colors font-medium">
                            <i class="fas fa-chalkboard-teacher mr-2"></i>Go to Mentor Dashboard
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($user_type === 'alumni'): ?>
            <!-- Alumni CTA Banner -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-xl p-6 mb-8 text-white">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="flex items-center space-x-4 mb-4 md:mb-0">
                        <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-hand-holding-heart text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">Become a Mentor</h3>
                            <p class="text-white/80">Share your expertise and help current students succeed</p>
                        </div>
                    </div>
                    <a href="mentor_dashboard.php" class="px-6 py-3 bg-white text-green-700 rounded-lg hover:bg-gray-100 transition-colors font-medium">
                        <i class="fas fa-plus mr-2"></i>Create Mentorship Session
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- How It Works -->
            <div class="bg-gradient-to-r from-primary/10 to-primary/5 rounded-xl p-6 mb-8">
                <h3 class="text-lg font-bold text-gray-900 mb-4">How Mentorship Works</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center flex-shrink-0 font-bold">1</div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Browse Mentors</h4>
                            <p class="text-sm text-gray-600">Find mentors by expertise area</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center flex-shrink-0 font-bold">2</div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Book Session</h4>
                            <p class="text-sm text-gray-600">Select your preferred time</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center flex-shrink-0 font-bold">3</div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Pay Securely</h4>
                            <p class="text-sm text-gray-600">Complete payment via Paystack</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center flex-shrink-0 font-bold">4</div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Meet & Learn</h4>
                            <p class="text-sm text-gray-600">Connect via video call</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mentorship Sessions -->
            <?php if ($mentorships && count($mentorships) > 0): ?>
                <h2 class="text-xl font-bold text-gray-900 mb-4">Available Mentorship Sessions</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($mentorships as $mentor): ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 animate-fadeIn">
                            <div class="p-6">
                                <div class="flex items-start space-x-4">
                                    <!-- Mentor Avatar -->
                                    <div class="flex-shrink-0">
                                        <?php if ($mentor['image_url']): 
                                            $mentor_img_src = (strpos($mentor['image_url'], 'http') === 0) 
                                                ? $mentor['image_url'] 
                                                : '../uploads/services/' . $mentor['image_url'];
                                        ?>
                                            <img src="<?php echo htmlspecialchars($mentor_img_src); ?>" 
                                                 alt="Mentor" class="w-16 h-16 rounded-full object-cover">
                                        <?php else: ?>
                                            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user-tie text-2xl text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Mentor Info -->
                                    <div class="flex-1">
                                        <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 mb-2">
                                            <i class="fas fa-check-circle mr-1"></i>Verified Mentor
                                        </span>
                                        <h3 class="text-lg font-bold text-gray-900">
                                            <?php echo htmlspecialchars($mentor['service_name']); ?>
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            <?php echo htmlspecialchars(substr($mentor['description'], 0, 120)) . '...'; ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Session Details -->
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <div class="flex flex-wrap gap-3 text-sm text-gray-600 mb-4">
                                        <?php if ($mentor['duration']): ?>
                                            <span class="flex items-center bg-gray-100 px-3 py-1 rounded-full">
                                                <i class="fas fa-clock text-primary mr-2"></i>
                                                <?php echo $mentor['duration']; ?> minutes
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($mentor['category']): ?>
                                            <span class="flex items-center bg-gray-100 px-3 py-1 rounded-full">
                                                <i class="fas fa-tag text-primary mr-2"></i>
                                                <?php echo htmlspecialchars($mentor['category']); ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="flex items-center bg-gray-100 px-3 py-1 rounded-full">
                                            <i class="fas fa-video text-primary mr-2"></i>
                                            Virtual Session
                                        </span>
                                    </div>

                                    <!-- Price and Book Button -->
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="text-2xl font-bold text-primary">GHS <?php echo number_format($mentor['price'], 2); ?></span>
                                            <span class="text-sm text-gray-500">/session</span>
                                        </div>
                                        <button onclick="bookSession(<?php echo $mentor['service_id']; ?>, '<?php echo htmlspecialchars($mentor['service_name'], ENT_QUOTES); ?>', <?php echo $mentor['price']; ?>)" 
                                                class="px-6 py-2.5 bg-primary text-white rounded-lg hover:bg-red-900 hover:shadow-md active:scale-95 transition-all duration-200 font-medium">
                                            <i class="fas fa-calendar-check mr-2"></i>Book Session
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- No Mentors Available -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-green-100 to-green-50 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-graduate text-4xl text-green-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Mentors Available</h3>
                    <p class="text-gray-600 mb-6">Check back later for available mentorship sessions</p>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Booking Modal -->
    <div id="bookingModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 overflow-hidden animate-fadeIn">
            <div class="bg-primary text-white p-6">
                <h3 class="text-xl font-bold">Book Mentorship Session</h3>
                <p id="modalSessionName" class="text-white/80 mt-1"></p>
            </div>
            <form id="bookingForm" class="p-6 space-y-4">
                <input type="hidden" id="serviceId" name="service_id">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Date</label>
                    <input type="date" id="sessionDate" name="session_date" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Time</label>
                    <select id="sessionTime" name="session_time" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                        <option value="">Select a time</option>
                        <option value="09:00">9:00 AM</option>
                        <option value="10:00">10:00 AM</option>
                        <option value="11:00">11:00 AM</option>
                        <option value="14:00">2:00 PM</option>
                        <option value="15:00">3:00 PM</option>
                        <option value="16:00">4:00 PM</option>
                        <option value="17:00">5:00 PM</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">What would you like to discuss? (Optional)</label>
                    <textarea id="sessionNotes" name="notes" rows="3" placeholder="E.g., Career transition to tech, resume review..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary"></textarea>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Session Fee</span>
                        <span id="modalPrice" class="text-xl font-bold text-primary"></span>
                    </div>
                </div>

                <div class="flex space-x-3 pt-2">
                    <button type="button" onclick="closeModal()" 
                            class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-3 bg-primary text-white rounded-lg hover:bg-red-900 transition-colors font-medium">
                        <i class="fas fa-credit-card mr-2"></i>Proceed to Pay
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50"></div>

    <script>
        function bookSession(serviceId, sessionName, price) {
            document.getElementById('serviceId').value = serviceId;
            document.getElementById('modalSessionName').textContent = sessionName;
            document.getElementById('modalPrice').textContent = 'GHS ' + price.toFixed(2);
            document.getElementById('bookingModal').classList.remove('hidden');
            document.getElementById('bookingModal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('bookingModal').classList.add('hidden');
            document.getElementById('bookingModal').classList.remove('flex');
        }

        // Close modal when clicking outside
        document.getElementById('bookingModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        // Handle form submission
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const serviceId = document.getElementById('serviceId').value;
            const sessionDate = document.getElementById('sessionDate').value;
            const sessionTime = document.getElementById('sessionTime').value;
            const notes = document.getElementById('sessionNotes').value;

            // Add to cart with special requests
            fetch('../actions/add_to_cart_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `service_id=${serviceId}&quantity=1&selected_date=${sessionDate}&selected_time=${sessionTime}&special_requests=${encodeURIComponent(notes)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    showToast('Session booked! Redirecting to checkout...', 'success');
                    setTimeout(() => window.location.href = 'cart.php', 1500);
                } else {
                    showToast(data.message || 'Failed to book session', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to book session', 'error');
            });
        });

        function showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `px-6 py-3 rounded-lg text-white font-medium shadow-lg animate-fadeIn mb-2 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>${message}`;
            document.getElementById('toast-container').appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }
    </script>
</body>
</html>
