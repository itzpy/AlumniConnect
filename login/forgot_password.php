<?php
/**
 * Forgot Password Page
 * Allows users to request a password reset
 */
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: ../views/dashboard.php');
    exit;
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../settings/db_class.php';
    
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $message = 'Please enter your email address';
        $message_type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address';
        $message_type = 'error';
    } else {
        $db = new db_connection();
        $email_escaped = mysqli_real_escape_string($db->db_conn(), $email);
        
        // Check if email exists
        $user = $db->db_fetch_one("SELECT user_id, first_name, email FROM users WHERE email = '$email_escaped'");
        
        if ($user) {
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store token in database
            $user_id = $user['user_id'];
            
            // Delete any existing tokens for this user
            $db->db_query("DELETE FROM password_resets WHERE user_id = $user_id");
            
            // Insert new token
            $result = $db->db_query("INSERT INTO password_resets (user_id, token, expires_at) VALUES ($user_id, '$token', '$expires')");
            
            if ($result) {
                // In production, send email here
                // For now, we'll show the reset link (for testing)
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=$token";
                
                // Log for debugging
                error_log("Password reset link for {$user['email']}: $reset_link");
                
                $message = 'If an account with that email exists, a password reset link has been sent. Please check your email.';
                $message_type = 'success';
                
                // Store in session for demo purposes (remove in production)
                $_SESSION['reset_link_demo'] = $reset_link;
            } else {
                $message = 'An error occurred. Please try again.';
                $message_type = 'error';
            }
        } else {
            // Don't reveal if email exists or not (security)
            $message = 'If an account with that email exists, a password reset link has been sent. Please check your email.';
            $message_type = 'success';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - AlumniConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="../index.php" class="inline-flex items-center gap-3">
                <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-white text-xl"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900">AlumniConnect</span>
            </a>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-key text-primary text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Forgot Password?</h1>
                <p class="text-gray-600">Enter your email and we'll send you a reset link</p>
            </div>

            <?php if ($message): ?>
                <div class="mb-6 p-4 rounded-lg <?php echo $message_type === 'success' ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-red-50 border border-red-200 text-red-700'; ?>">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                        <span><?php echo htmlspecialchars($message); ?></span>
                    </div>
                </div>
                
                <?php if (isset($_SESSION['reset_link_demo'])): ?>
                    <!-- Demo only - remove in production -->
                    <div class="mb-6 p-4 rounded-lg bg-blue-50 border border-blue-200 text-blue-700 text-sm">
                        <p class="font-semibold mb-2"><i class="fas fa-info-circle mr-1"></i> Demo Mode:</p>
                        <p class="break-all">
                            <a href="<?php echo $_SESSION['reset_link_demo']; ?>" class="underline hover:no-underline">
                                Click here to reset password
                            </a>
                        </p>
                    </div>
                    <?php unset($_SESSION['reset_link_demo']); ?>
                <?php endif; ?>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </span>
                        <input type="email" id="email" name="email" required
                               class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                               placeholder="Enter your email">
                    </div>
                </div>

                <button type="submit" 
                        class="w-full py-3 bg-primary text-white font-semibold rounded-xl hover:bg-primary-dark transition-colors">
                    <i class="fas fa-paper-plane mr-2"></i>Send Reset Link
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="login.php" class="text-primary hover:text-primary-dark transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Login
                </a>
            </div>
        </div>

        <p class="text-center text-gray-500 text-sm mt-6">
            Don't have an account? <a href="register.php" class="text-primary hover:underline">Sign up</a>
        </p>
    </div>
</body>
</html>
