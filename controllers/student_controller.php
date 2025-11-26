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
    return $student->update_profile($student_id, $first_name, $last_name, $phone, $major,
                                    $expected_graduation_year, $bio, $linkedin_url, $profile_image);
}

// Get all students
function get_all_students_ctr($limit = 20, $offset = 0) {
    $student = new Student();
    return $student->get_all_students($limit, $offset);
}

// Search students
function search_students_ctr($search_query, $major = '', $year = '') {
    $student = new Student();
    return $student->search_students($search_query, $major, $year);
}

// Get students by major
function get_students_by_major_ctr($major) {
    $student = new Student();
    return $student->get_students_by_major($major);
}

// Update student password
function update_student_password_ctr($student_id, $old_password, $new_password) {
    $student = new Student();
    return $student->update_password($student_id, $old_password, $new_password);
}

// Delete student
function delete_student_ctr($student_id) {
    $student = new Student();
    return $student->delete_student($student_id);
}

// Check if email exists
function check_student_email_exists_ctr($email) {
    $student = new Student();
    $result = $student->login_student($email, '');
    return $result !== false;
}

// Update last login
function update_student_last_login_ctr($student_id) {
    $student = new Student();
    return $student->db_query("UPDATE students SET last_login = NOW() WHERE student_id = '$student_id'");
}

// Get student count
function get_student_count_ctr() {
    $student = new Student();
    $result = $student->db_fetch_one("SELECT COUNT(*) as count FROM students");
    return $result['count'] ?? 0;
}
?>
