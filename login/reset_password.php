<?php
/**
 * Reset Password Page
 * Allows users to set a new password using a valid token
 */
session_start();

require_once '../settings/db_class.php';

$token = trim($_GET['token'] ?? '');
$message = '';
$message_type = '';
$valid_token = false;
$user_id = null;

if (empty($token)) {
    $message = 'Invalid or missing reset token';
    $message_type = 'error';
} else {
    $db = new db_connection();
    $token_escaped = mysqli_real_escape_string($db->db_conn(), $token);
    
    // Check if token is valid and not expired
    $reset = $db->db_fetch_one("SELECT pr.*, u.email, u.first_name 
                                 FROM password_resets pr 
                                 JOIN users u ON pr.user_id = u.user_id 
                                 WHERE pr.token = '$token_escaped' 
                                 AND pr.expires_at > NOW() 
                                 AND pr.used = 0");
    
    if ($reset) {
        $valid_token = true;
        $user_id = $reset['user_id'];
        $user_email = $reset['email'];
    } else {
        $message = 'This reset link is invalid or has expired. Please request a new one.';
        $message_type = 'error';
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (strlen($password) < 6) {
        $message = 'Password must be at least 6 characters long';
        $message_type = 'error';
    } elseif ($password !== $confirm_password) {
        $message = 'Passwords do not match';
        $message_type = 'error';
    } else {
        $db = new db_connection();
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Update user's password
        $update_result = $db->db_query("UPDATE users SET password = '$hashed_password' WHERE user_id = $user_id");
        
        if ($update_result) {
            // Mark token as used
            $token_escaped = mysqli_real_escape_string($db->db_conn(), $token);
            $db->db_query("UPDATE password_resets SET used = 1 WHERE token = '$token_escaped'");
            
            $message = 'Your password has been reset successfully! You can now login.';
            $message_type = 'success';
            $valid_token = false; // Hide form after success
        } else {
            $message = 'An error occurred. Please try again.';
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - AlumniConnect</title>
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
                    <i class="fas fa-lock text-primary text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Reset Password</h1>
                <p class="text-gray-600">Enter your new password below</p>
            </div>

            <?php if ($message): ?>
                <div class="mb-6 p-4 rounded-lg <?php echo $message_type === 'success' ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-red-50 border border-red-200 text-red-700'; ?>">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                        <span><?php echo htmlspecialchars($message); ?></span>
                    </div>
                </div>
                
                <?php if ($message_type === 'success'): ?>
                    <div class="text-center">
                        <a href="login.php" class="inline-flex items-center justify-center w-full py-3 bg-primary text-white font-semibold rounded-xl hover:bg-primary-dark transition-colors">
                            <i class="fas fa-sign-in-alt mr-2"></i>Go to Login
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($valid_token): ?>
                <form method="POST" class="space-y-5">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </span>
                            <input type="password" id="password" name="password" required minlength="6"
                                   class="w-full pl-11 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                                   placeholder="Enter new password">
                            <button type="button" onclick="togglePassword('password')" 
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye" id="password-icon"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </span>
                            <input type="password" id="confirm_password" name="confirm_password" required minlength="6"
                                   class="w-full pl-11 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                                   placeholder="Confirm new password">
                            <button type="button" onclick="togglePassword('confirm_password')" 
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye" id="confirm_password-icon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full py-3 bg-primary text-white font-semibold rounded-xl hover:bg-primary-dark transition-colors">
                        <i class="fas fa-check mr-2"></i>Reset Password
                    </button>
                </form>
            <?php elseif ($message_type !== 'success'): ?>
                <div class="text-center">
                    <a href="forgot_password.php" class="inline-flex items-center justify-center w-full py-3 bg-primary text-white font-semibold rounded-xl hover:bg-primary-dark transition-colors">
                        <i class="fas fa-redo mr-2"></i>Request New Reset Link
                    </a>
                </div>
            <?php endif; ?>

            <div class="mt-6 text-center">
                <a href="login.php" class="text-primary hover:text-primary-dark transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Login
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
