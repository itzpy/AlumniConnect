<?php
/**
 * Jobs Page
 * For students: Browse job opportunities
 * For alumni: Post new jobs (purchase job posting package)
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

// Get job posting packages (for alumni to post jobs)
$job_packages = $service->getAllServices('job_posting', null, null, null, null);
$cart_count = $cart->getCartCount($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Board - Alumni Connect</title>
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
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-briefcase text-primary mr-3"></i>Job Board
                </h1>
                <p class="text-gray-600">
                    <?php if ($user_type === 'alumni'): ?>
                        Post job opportunities and connect with talented Ashesi students
                    <?php else: ?>
                        Discover career opportunities shared by alumni
                    <?php endif; ?>
                </p>
            </div>

            <!-- Tab Navigation -->
            <div class="flex border-b border-gray-200 mb-6">
                <button onclick="showTab('browse')" id="tab-browse" class="px-6 py-3 text-primary border-b-2 border-primary font-medium">
                    <i class="fas fa-search mr-2"></i>Browse Jobs
                </button>
                <?php if ($user_type === 'alumni'): ?>
                    <button onclick="showTab('post')" id="tab-post" class="px-6 py-3 text-gray-500 hover:text-gray-700 font-medium">
                        <i class="fas fa-plus-circle mr-2"></i>Post a Job
                    </button>
                <?php endif; ?>
            </div>

            <!-- Browse Jobs Tab -->
            <div id="content-browse" class="tab-content">
                <!-- Sample Job Listings -->
                <div class="space-y-4">
                    <!-- Sample Job 1 -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow animate-fadeIn">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-laptop-code text-2xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Software Engineering Intern</h3>
                                    <p class="text-primary font-medium">Tech Solutions Ghana</p>
                                    <div class="flex flex-wrap gap-2 mt-2 text-sm text-gray-500">
                                        <span><i class="fas fa-map-marker-alt mr-1"></i>Accra, Ghana</span>
                                        <span><i class="fas fa-clock mr-1"></i>Full-time</span>
                                        <span><i class="fas fa-money-bill mr-1"></i>GHS 2,500/month</span>
                                    </div>
                                    <div class="flex gap-2 mt-3">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Python</span>
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">JavaScript</span>
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">React</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="text-sm text-gray-500">Posted 2 days ago</span>
                                <div class="mt-4">
                                    <button class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-red-900 transition-colors text-sm font-medium">
                                        Apply Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sample Job 2 -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow animate-fadeIn">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-chart-line text-2xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Business Analyst</h3>
                                    <p class="text-primary font-medium">Ecobank Ghana</p>
                                    <div class="flex flex-wrap gap-2 mt-2 text-sm text-gray-500">
                                        <span><i class="fas fa-map-marker-alt mr-1"></i>Accra, Ghana</span>
                                        <span><i class="fas fa-clock mr-1"></i>Full-time</span>
                                        <span><i class="fas fa-money-bill mr-1"></i>GHS 4,000/month</span>
                                    </div>
                                    <div class="flex gap-2 mt-3">
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Excel</span>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">SQL</span>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Power BI</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="text-sm text-gray-500">Posted 5 days ago</span>
                                <div class="mt-4">
                                    <button class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-red-900 transition-colors text-sm font-medium">
                                        Apply Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sample Job 3 -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow animate-fadeIn">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-pen-nib text-2xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Marketing Coordinator</h3>
                                    <p class="text-primary font-medium">Jumia Ghana</p>
                                    <div class="flex flex-wrap gap-2 mt-2 text-sm text-gray-500">
                                        <span><i class="fas fa-map-marker-alt mr-1"></i>Accra, Ghana</span>
                                        <span><i class="fas fa-clock mr-1"></i>Full-time</span>
                                        <span><i class="fas fa-money-bill mr-1"></i>GHS 3,000/month</span>
                                    </div>
                                    <div class="flex gap-2 mt-3">
                                        <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">Marketing</span>
                                        <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">Social Media</span>
                                        <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">Content</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="text-sm text-gray-500">Posted 1 week ago</span>
                                <div class="mt-4">
                                    <button class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-red-900 transition-colors text-sm font-medium">
                                        Apply Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Post a Job Tab (Alumni Only) -->
            <?php if ($user_type === 'alumni'): ?>
            <div id="content-post" class="tab-content hidden">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Post a Job Opportunity</h3>
                    <p class="text-gray-600 mb-6">Select a job posting package to reach qualified Ashesi students</p>

                    <!-- Job Posting Packages -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <?php foreach ($job_packages as $package): ?>
                            <div class="border-2 border-gray-200 rounded-xl p-6 hover:border-primary hover:shadow-lg transition-all duration-300 text-center">
                                <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-bullhorn text-2xl text-white"></i>
                                </div>
                                <h4 class="text-lg font-bold text-gray-900 mb-2">
                                    <?php echo htmlspecialchars($package['service_name']); ?>
                                </h4>
                                <p class="text-sm text-gray-600 mb-4">
                                    <?php echo htmlspecialchars(substr($package['description'], 0, 80)) . '...'; ?>
                                </p>
                                <?php if ($package['duration']): ?>
                                    <p class="text-sm text-gray-500 mb-4">
                                        <i class="fas fa-clock mr-1"></i><?php echo $package['duration']; ?> days visibility
                                    </p>
                                <?php endif; ?>
                                <div class="text-3xl font-bold text-primary mb-4">
                                    GHS <?php echo number_format($package['price'], 2); ?>
                                </div>
                                <button onclick="selectPackage(<?php echo $package['service_id']; ?>)" 
                                        class="w-full px-4 py-3 bg-primary text-white rounded-lg hover:bg-red-900 transition-colors font-medium">
                                    Select Package
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Job Posting Form (shown after selecting package) -->
                <div id="jobFormSection" class="hidden">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Job Details</h3>
                        <form id="jobForm" class="space-y-6">
                            <input type="hidden" id="selectedPackageId" name="package_id">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Job Title *</label>
                                    <input type="text" name="job_title" required placeholder="e.g., Software Engineer"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Company Name *</label>
                                    <input type="text" name="company" required placeholder="Your company name"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                                    <input type="text" name="location" required placeholder="e.g., Accra, Ghana"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Job Type *</label>
                                    <select name="job_type" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                                        <option value="">Select type</option>
                                        <option value="full-time">Full-time</option>
                                        <option value="part-time">Part-time</option>
                                        <option value="contract">Contract</option>
                                        <option value="internship">Internship</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Salary Range</label>
                                <input type="text" name="salary" placeholder="e.g., GHS 3,000 - 5,000/month"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Job Description *</label>
                                <textarea name="description" rows="5" required placeholder="Describe the role, responsibilities, and requirements..."
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Application Email/Link *</label>
                                <input type="text" name="apply_url" required placeholder="How should candidates apply?"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                            </div>

                            <div class="flex space-x-4">
                                <button type="button" onclick="hideJobForm()" 
                                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                                    Back
                                </button>
                                <button type="submit" 
                                        class="flex-1 px-6 py-3 bg-primary text-white rounded-lg hover:bg-red-900 transition-colors font-medium">
                                    <i class="fas fa-credit-card mr-2"></i>Proceed to Payment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50"></div>

    <script>
        function showTab(tab) {
            // Hide all content
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            // Reset all tabs
            document.querySelectorAll('[id^="tab-"]').forEach(el => {
                el.classList.remove('text-primary', 'border-b-2', 'border-primary');
                el.classList.add('text-gray-500');
            });
            
            // Show selected content
            document.getElementById('content-' + tab).classList.remove('hidden');
            // Activate selected tab
            const activeTab = document.getElementById('tab-' + tab);
            activeTab.classList.add('text-primary', 'border-b-2', 'border-primary');
            activeTab.classList.remove('text-gray-500');
        }

        function selectPackage(packageId) {
            document.getElementById('selectedPackageId').value = packageId;
            document.getElementById('jobFormSection').classList.remove('hidden');
            document.getElementById('jobFormSection').scrollIntoView({ behavior: 'smooth' });
        }

        function hideJobForm() {
            document.getElementById('jobFormSection').classList.add('hidden');
        }

        // Handle job form submission
        document.getElementById('jobForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const packageId = document.getElementById('selectedPackageId').value;
            const formData = new FormData(this);
            const notes = `Job: ${formData.get('job_title')} at ${formData.get('company')}, ${formData.get('location')}`;

            // Add package to cart
            fetch('../actions/add_to_cart_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `service_id=${packageId}&quantity=1&special_requests=${encodeURIComponent(notes)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Job posting added! Redirecting to checkout...', 'success');
                    setTimeout(() => window.location.href = 'cart.php', 1500);
                } else {
                    showToast(data.message || 'Failed to add job posting', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to process request', 'error');
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
