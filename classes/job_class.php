<?php
require_once(dirname(__FILE__) . '/../settings/db_class.php');

class Job extends db_connection {
    
    // Post a job
    public function post_job($posted_by, $company_name, $job_title, $job_description, $job_location, $job_type, $salary_range = null, $application_url = null, $application_deadline = null) {
        if (!$this->db_connect()) {
            return false;
        }
        
        $posted_by = intval($posted_by);
        $company_name = mysqli_real_escape_string($this->db, $company_name);
        $job_title = mysqli_real_escape_string($this->db, $job_title);
        $job_description = mysqli_real_escape_string($this->db, $job_description);
        $job_location = mysqli_real_escape_string($this->db, $job_location);
        $job_type = mysqli_real_escape_string($this->db, $job_type);
        $salary_range = $salary_range ? "'" . mysqli_real_escape_string($this->db, $salary_range) . "'" : "NULL";
        $application_url = $application_url ? "'" . mysqli_real_escape_string($this->db, $application_url) . "'" : "NULL";
        $application_deadline = $application_deadline ? "'" . mysqli_real_escape_string($this->db, $application_deadline) . "'" : "NULL";
        
        $sql = "INSERT INTO job_opportunities (posted_by, company_name, job_title, job_description, job_location, job_type, salary_range, application_url, application_deadline) 
                VALUES ($posted_by, '$company_name', '$job_title', '$job_description', '$job_location', '$job_type', $salary_range, $application_url, $application_deadline)";
        
        if (mysqli_query($this->db, $sql)) {
            return mysqli_insert_id($this->db);
        }
        return false;
    }
    
    // Get all jobs
    public function get_all_jobs($limit = 20, $offset = 0) {
        $limit = intval($limit);
        $offset = intval($offset);
        
        $sql = "SELECT j.*, u.first_name, u.last_name, u.profile_image
                FROM job_opportunities j
                JOIN users u ON j.posted_by = u.user_id
                WHERE j.is_active = 1
                ORDER BY j.date_posted DESC
                LIMIT $limit OFFSET $offset";
        return $this->db_fetch_all($sql);
    }
    
    // Get job by ID
    public function get_job($job_id) {
        $job_id = intval($job_id);
        $sql = "SELECT j.*, u.first_name, u.last_name, u.profile_image, u.email,
                       ap.current_company, ap.job_title as poster_title
                FROM job_opportunities j
                JOIN users u ON j.posted_by = u.user_id
                LEFT JOIN alumni_profiles ap ON j.posted_by = ap.user_id
                WHERE j.job_id = $job_id";
        return $this->db_fetch_one($sql);
    }
    
    // Search jobs
    public function search_jobs($keyword = '', $location = '', $job_type = '') {
        $conditions = ["j.is_active = 1"];
        
        if (!empty($keyword)) {
            $keyword = $this->db->real_escape_string($keyword);
            $conditions[] = "(j.job_title LIKE '%$keyword%' OR j.job_description LIKE '%$keyword%' OR j.company_name LIKE '%$keyword%')";
        }
        
        if (!empty($location)) {
            $location = $this->db->real_escape_string($location);
            $conditions[] = "j.job_location LIKE '%$location%'";
        }
        
        if (!empty($job_type)) {
            $job_type = $this->db->real_escape_string($job_type);
            $conditions[] = "j.job_type = '$job_type'";
        }
        
        $sql = "SELECT j.*, u.first_name, u.last_name, u.profile_image
                FROM job_opportunities j
                JOIN users u ON j.posted_by = u.user_id
                WHERE " . implode(" AND ", $conditions) . "
                ORDER BY j.date_posted DESC";
        return $this->db_fetch_all($sql);
    }
    
    // Update job
    public function update_job($job_id, $user_id, $data) {
        if (!$this->db_connect()) {
            return false;
        }
        
        $job_id = intval($job_id);
        $user_id = intval($user_id);
        
        $fields = [];
        $allowed = ['company_name', 'job_title', 'job_description', 'job_location', 'job_type', 'salary_range', 'application_url', 'application_deadline'];
        
        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $value = mysqli_real_escape_string($this->db, $data[$field]);
                $fields[] = "$field = '$value'";
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $sql = "UPDATE job_opportunities SET " . implode(", ", $fields) . " WHERE job_id = $job_id AND posted_by = $user_id";
        return mysqli_query($this->db, $sql);
    }
    
    // Delete job
    public function delete_job($job_id, $user_id) {
        $job_id = intval($job_id);
        $user_id = intval($user_id);
        $sql = "UPDATE job_opportunities SET is_active = 0 WHERE job_id = $job_id AND posted_by = $user_id";
        return $this->db_query($sql);
    }
    
    // Close job posting
    public function close_job($job_id, $user_id) {
        return $this->delete_job($job_id, $user_id);
    }
    
    // Get jobs by user
    public function get_user_jobs($user_id) {
        $user_id = intval($user_id);
        $sql = "SELECT * FROM job_opportunities 
                WHERE posted_by = $user_id 
                ORDER BY date_posted DESC";
        return $this->db_fetch_all($sql);
    }
    
    // Get active job count
    public function get_active_job_count() {
        $result = $this->db_fetch_one("SELECT COUNT(*) as count FROM job_opportunities WHERE is_active = 1");
        return $result ? $result['count'] : 0;
    }
}
?>
