<?php
require_once(dirname(__FILE__) . '/../classes/job_class.php');

// Post job
function post_job_ctr($posted_by, $user_type, $title, $company, $location, $job_type, $description, $requirements, $application_url) {
    $job = new Job();
    return $job->post_job($posted_by, $user_type, $title, $company, $location, $job_type, $description, $requirements, $application_url);
}

// Get all jobs
function get_all_jobs_ctr($limit = 20, $offset = 0) {
    $job = new Job();
    return $job->get_all_jobs($limit, $offset);
}

// Get job by ID
function get_job_ctr($job_id) {
    $job = new Job();
    return $job->get_job($job_id);
}

// Search jobs
function search_jobs_ctr($keyword, $location = '', $job_type = '') {
    $job = new Job();
    return $job->search_jobs($keyword, $location, $job_type);
}

// Update job
function update_job_ctr($job_id, $title, $company, $location, $job_type, $description, $requirements, $application_url) {
    $job = new Job();
    return $job->update_job($job_id, $title, $company, $location, $job_type, $description, $requirements, $application_url);
}

// Delete job
function delete_job_ctr($job_id, $user_id) {
    $job = new Job();
    return $job->delete_job($job_id, $user_id);
}

// Close job
function close_job_ctr($job_id, $user_id) {
    $job = new Job();
    return $job->close_job($job_id, $user_id);
}

// Get user jobs
function get_user_jobs_ctr($user_id, $user_type) {
    $job = new Job();
    return $job->get_user_jobs($user_id, $user_type);
}
?>
