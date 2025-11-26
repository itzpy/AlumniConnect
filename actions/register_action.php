<?php
require_once('../controllers/alumni_controller.php');
require_once('../controllers/student_controller.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_type = $_POST['user_type'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = trim($_POST['phone']);
    $major = trim($_POST['major']);
    $graduation_year = $_POST['graduation_year'];

    // Build query string for preserving form data
    $form_data = http_build_query([
        'user_type' => $user_type,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'phone' => $phone,
        'major' => $major,
        'graduation_year' => $graduation_year
    ]);

    // Validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        header("Location: ../login/register.php?error=empty&" . $form_data);
        exit();
    }

    if ($password !== $confirm_password) {
        header("Location: ../login/register.php?error=password_mismatch&" . $form_data);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../login/register.php?error=invalid_email&" . $form_data);
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    if ($user_type == 'alumni') {
        $company = isset($_POST['current_company']) ? trim($_POST['current_company']) : '';
        $job_title = isset($_POST['current_position']) ? trim($_POST['current_position']) : '';
        $industry = isset($_POST['industry']) ? trim($_POST['industry']) : '';
        $location = isset($_POST['location']) ? trim($_POST['location']) : '';
        $linkedin = isset($_POST['linkedin']) ? trim($_POST['linkedin']) : '';
        $bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';

        $result = register_alumni_ctr(
            $first_name,
            $last_name,
            $email,
            $hashed_password,
            $phone,
            $major,
            $graduation_year,
            $company,
            $job_title,
            $industry,
            $location,
            $linkedin,
            $bio
        );

        if ($result) {
            header("Location: ../login/login.php?success=registered");
            exit();
        } else {
            header("Location: ../login/register.php?error=registration_failed&" . $form_data);
            exit();
        }
    } else {
        $current_year = isset($_POST['current_year']) ? $_POST['current_year'] : '';
        $career_interests = isset($_POST['career_interests']) ? trim($_POST['career_interests']) : '';

        $result = register_student_ctr(
            $first_name,
            $last_name,
            $email,
            $hashed_password,
            $phone,
            $major,
            $graduation_year,
            $current_year,
            $career_interests
        );

        if ($result) {
            header("Location: ../login/login.php?success=registered");
            exit();
        } else {
            header("Location: ../login/register.php?error=registration_failed&" . $form_data);
            exit();
        }
    }
} else {
    header("Location: ../login/register.php");
    exit();
}
?>
