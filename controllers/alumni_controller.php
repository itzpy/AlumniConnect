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
    $alumni = new AlumniProfile();
    return $alumni->updateProfile($alumni_id, [
        'major' => $major,
        'graduation_year' => $graduation_year,
        'current_company' => $current_company,
        'job_title' => $current_position,
        'industry' => $industry,
        'location_city' => $location,
        'linkedin_url' => $linkedin_url
    ]);
}

// Search alumni
function search_alumni_ctr($search_query, $major = '', $graduation_year = '', $industry = '', $location = '') {
    $alumni = new AlumniProfile();
    $filters = [
        'name' => $search_query,
        'major' => $major,
        'graduation_year' => $graduation_year,
        'industry' => $industry,
        'location' => $location
    ];
    return $alumni->searchAlumni($filters);
}

// Get all alumni
function get_all_alumni_ctr($limit = 20, $offset = 0) {
    $alumni = new AlumniProfile();
    return $alumni->getAllAlumni($limit, $offset);
}

// Get alumni by major
function get_alumni_by_major_ctr($major) {
    $alumni = new AlumniProfile();
    return $alumni->searchAlumni(['major' => $major]);
}

// Get alumni by graduation year
function get_alumni_by_year_ctr($year) {
    $alumni = new AlumniProfile();
    return $alumni->getAlumniByYearRange($year, $year);
}

// Get alumni by industry
function get_alumni_by_industry_ctr($industry) {
    $alumni = new AlumniProfile();
    return $alumni->searchAlumni(['industry' => $industry]);
}

// Update alumni password
function update_alumni_password_ctr($alumni_id, $old_password, $new_password) {
    require_once(dirname(__FILE__) . '/../classes/user_class.php');
    $user = new User();
    // Verify old password
    $current = $user->getUserById($alumni_id);
    if (!$current || !password_verify($old_password, $current['password'] ?? '')) {
        return false;
    }
    // Update password via updateProfile
    return $user->updateProfile($alumni_id, ['password' => password_hash($new_password, PASSWORD_DEFAULT)]);
}

// Delete alumni
function delete_alumni_ctr($alumni_id) {
    require_once(dirname(__FILE__) . '/../classes/user_class.php');
    $user = new User();
    return $user->deactivateUser($alumni_id);
}

// Check if email exists
function check_alumni_email_exists_ctr($email) {
    require_once(dirname(__FILE__) . '/../classes/user_class.php');
    $user = new User();
    return $user->getUserByEmail($email) !== null;
}

// Update last login
function update_alumni_last_login_ctr($alumni_id) {
    // Last login is automatically updated on login in User class
    return true;
}

// Get alumni count
function get_alumni_count_ctr() {
    $alumni = new AlumniProfile();
    $stats = $alumni->getAlumniStats();
    return $stats['total_alumni'] ?? 0;
}
?>
