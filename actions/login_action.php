<?php
require_once('../controllers/alumni_controller.php');
require_once('../controllers/student_controller.php');
require_once('../classes/user_class.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $user_type = isset($_POST['user_type']) ? $_POST['user_type'] : 'student';

    if (empty($email) || empty($password)) {
        header("Location: ../login/login.php?error=empty&email=" . urlencode($email));
        exit();
    }

    // Try to login based on user type
    if ($user_type == 'admin') {
        // Admin login using User class directly
        $user = new User();
        $result = $user->loginUser($email, $password);
        
        if ($result['success'] && $result['user']['user_role'] === 'admin') {
            session_start();
            $_SESSION['user_id'] = $result['user']['user_id'];
            $_SESSION['email'] = $result['user']['email'];
            $_SESSION['name'] = $result['user']['first_name'] . ' ' . $result['user']['last_name'];
            $_SESSION['user_type'] = 'admin';
            $_SESSION['logged_in'] = true;
            
            header("Location: ../views/dashboard.php");
            exit();
        }
    } elseif ($user_type == 'alumni') {
        $alumni = login_alumni_ctr($email, $password);
        
        if ($alumni) {
            session_start();
            $_SESSION['user_id'] = $alumni['alumni_id'];
            $_SESSION['email'] = $alumni['email'];
            $_SESSION['name'] = $alumni['first_name'] . ' ' . $alumni['last_name'];
            $_SESSION['user_type'] = 'alumni';
            $_SESSION['logged_in'] = true;
            
            header("Location: ../views/dashboard.php");
            exit();
        }
    } else {
        $student = login_student_ctr($email, $password);
        
        if ($student) {
            session_start();
            $_SESSION['user_id'] = $student['student_id'];
            $_SESSION['email'] = $student['email'];
            $_SESSION['name'] = $student['first_name'] . ' ' . $student['last_name'];
            $_SESSION['user_type'] = 'student';
            $_SESSION['logged_in'] = true;
            
            header("Location: ../views/dashboard.php");
            exit();
        }
    }
    
    // Login failed
    header("Location: ../login/login.php?error=invalid&email=" . urlencode($email));
    exit();
} else {
    header("Location: ../login/login.php");
    exit();
}
?>
