<?php
require_once(dirname(__FILE__) . '/../classes/student_class.php');

// Register new student
function register_student_ctr($first_name, $last_name, $email, $password, $phone, $major, 
                              $expected_graduation_year, $year_level, $career_interests = null) {
    $student = new StudentProfile();
    return $student->register_student($first_name, $last_name, $email, $password, $phone, $major,
                                      $expected_graduation_year, $year_level, $career_interests);
}

// Login student
function login_student_ctr($email, $password) {
    $student = new StudentProfile();
    return $student->login_student($email, $password);
}

// Get student by ID
function get_student_by_id_ctr($student_id) {
    $student = new StudentProfile();
    return $student->get_student_by_id($student_id);
}

// Update student profile
function update_student_profile_ctr($student_id, $first_name, $last_name, $phone, $major,
                                    $expected_graduation_year, $bio, $linkedin_url, $profile_image = null) {
    $student = new StudentProfile();
    return $student->updateProfile($student_id, [
        'major' => $major,
        'expected_graduation' => $expected_graduation_year,
        'linkedin_url' => $linkedin_url
    ]);
}

// Get all students
function get_all_students_ctr($limit = 20, $offset = 0) {
    $student = new StudentProfile();
    return $student->getAllStudents($limit, $offset);
}

// Search students
function search_students_ctr($search_query, $major = '', $year = '') {
    $student = new StudentProfile();
    $filters = [
        'name' => $search_query,
        'major' => $major,
        'expected_graduation' => $year
    ];
    return $student->searchStudents($filters);
}

// Get students by major
function get_students_by_major_ctr($major) {
    $student = new StudentProfile();
    return $student->searchStudents(['major' => $major]);
}

// Update student password
function update_student_password_ctr($student_id, $old_password, $new_password) {
    require_once(dirname(__FILE__) . '/../classes/user_class.php');
    $user = new User();
    // Verify old password
    $current = $user->getUserById($student_id);
    if (!$current || !password_verify($old_password, $current['password'] ?? '')) {
        return false;
    }
    return $user->updateProfile($student_id, ['password' => password_hash($new_password, PASSWORD_DEFAULT)]);
}

// Delete student
function delete_student_ctr($student_id) {
    require_once(dirname(__FILE__) . '/../classes/user_class.php');
    $user = new User();
    return $user->deactivateUser($student_id);
}

// Check if email exists
function check_student_email_exists_ctr($email) {
    require_once(dirname(__FILE__) . '/../classes/user_class.php');
    $user = new User();
    return $user->getUserByEmail($email) !== null;
}

// Update last login
function update_student_last_login_ctr($student_id) {
    // Last login is automatically updated on login in User class
    return true;
}

// Get student count
function get_student_count_ctr() {
    $student = new StudentProfile();
    $stats = $student->getStudentStats();
    return $stats['total_students'] ?? 0;
}
?>
