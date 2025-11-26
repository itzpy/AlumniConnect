<?php
require_once(dirname(__FILE__) . '/../classes/alumni_class.php');

// Register new alumni
function register_alumni_ctr($first_name, $last_name, $email, $password, $phone, $major, $graduation_year, 
                             $current_company, $current_position, $industry, $location, $linkedin = null, $bio = null) {
    $alumni = new AlumniProfile();
    return $alumni->register_alumni($first_name, $last_name, $email, $password, $phone, $major, 
                                    $graduation_year, $current_company, $current_position, $industry, $location, $linkedin, $bio);
}

// Login alumni
function login_alumni_ctr($email, $password) {
    $alumni = new AlumniProfile();
    return $alumni->login_alumni($email, $password);
}

// Get alumni by ID
function get_alumni_by_id_ctr($alumni_id) {
    $alumni = new AlumniProfile();
    return $alumni->get_alumni_by_id($alumni_id);
}

// Update alumni profile
function update_alumni_profile_ctr($alumni_id, $first_name, $last_name, $phone, $major, $graduation_year,
                                   $current_company, $current_position, $industry, $location, $bio, 
                                   $linkedin_url, $profile_image = null) {
    $alumni = new Alumni();
    return $alumni->update_profile($alumni_id, $first_name, $last_name, $phone, $major, $graduation_year,
                                   $current_company, $current_position, $industry, $location, $bio, 
                                   $linkedin_url, $profile_image);
}

// Search alumni
function search_alumni_ctr($search_query, $major = '', $graduation_year = '', $industry = '', $location = '') {
    $alumni = new Alumni();
    return $alumni->search_alumni($search_query, $major, $graduation_year, $industry, $location);
}

// Get all alumni
function get_all_alumni_ctr($limit = 20, $offset = 0) {
    $alumni = new Alumni();
    return $alumni->get_all_alumni($limit, $offset);
}

// Get alumni by major
function get_alumni_by_major_ctr($major) {
    $alumni = new Alumni();
    return $alumni->get_alumni_by_major($major);
}

// Get alumni by graduation year
function get_alumni_by_year_ctr($year) {
    $alumni = new Alumni();
    return $alumni->get_alumni_by_graduation_year($year);
}

// Get alumni by industry
function get_alumni_by_industry_ctr($industry) {
    $alumni = new Alumni();
    return $alumni->get_alumni_by_industry($industry);
}

// Update alumni password
function update_alumni_password_ctr($alumni_id, $old_password, $new_password) {
    $alumni = new Alumni();
    return $alumni->update_password($alumni_id, $old_password, $new_password);
}

// Delete alumni
function delete_alumni_ctr($alumni_id) {
    $alumni = new Alumni();
    return $alumni->delete_alumni($alumni_id);
}

// Check if email exists
function check_alumni_email_exists_ctr($email) {
    $alumni = new Alumni();
    $result = $alumni->login_alumni($email, '');
    return $result !== false;
}

// Update last login
function update_alumni_last_login_ctr($alumni_id) {
    $alumni = new Alumni();
    return $alumni->db_query("UPDATE alumni SET last_login = NOW() WHERE alumni_id = '$alumni_id'");
}

// Get alumni count
function get_alumni_count_ctr() {
    $alumni = new Alumni();
    $result = $alumni->db_fetch_one("SELECT COUNT(*) as count FROM alumni");
    return $result['count'] ?? 0;
}
?>
