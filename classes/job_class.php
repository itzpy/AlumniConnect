<?php
require_once(dirname(__FILE__) . '/../settings/db_class.php');

class Job extends db_connection {
    
    // Post a job
    public function post_job($posted_by, $user_type, $title, $company, $location, $job_type, $description, $requirements, $application_url) {
        $sql = "INSERT INTO jobs (posted_by, user_type, title, company, location, job_type, description, requirements, application_url, posted_at) 
                VALUES ('$posted_by', '$user_type', '$title', '$company', '$location', '$job_type', '$description', '$requirements', '$application_url', NOW())";
        return $this->db_query($sql);
    }
    
    // Get all jobs
    public function get_all_jobs($limit = 20, $offset = 0) {
        $sql = "SELECT j.*, 
                       COALESCE(a.first_name, s.first_name) as first_name,
                       COALESCE(a.last_name, s.last_name) as last_name,
                       COALESCE(a.profile_image, s.profile_image) as profile_image
                FROM jobs j
                LEFT JOIN alumni a ON j.posted_by = a.alumni_id AND j.user_type = 'alumni'
                LEFT JOIN students s ON j.posted_by = s.student_id AND j.user_type = 'student'
                WHERE j.status = 'active'
                ORDER BY j.posted_at DESC
                LIMIT $limit OFFSET $offset";
        return $this->db_fetch_all($sql);
    }
    
    // Get job by ID
    public function get_job($job_id) {
        $sql = "SELECT j.*, 
                       COALESCE(a.first_name, s.first_name) as first_name,
                       COALESCE(a.last_name, s.last_name) as last_name,
                       COALESCE(a.profile_image, s.profile_image) as profile_image,
                       COALESCE(a.current_company, 'Student') as poster_company
                FROM jobs j
                LEFT JOIN alumni a ON j.posted_by = a.alumni_id AND j.user_type = 'alumni'
                LEFT JOIN students s ON j.posted_by = s.student_id AND j.user_type = 'student'
                WHERE j.job_id = '$job_id'";
        return $this->db_fetch_one($sql);
    }
    
    // Search jobs
    public function search_jobs($keyword, $location = '', $job_type = '') {
        $sql = "SELECT j.*, 
                       COALESCE(a.first_name, s.first_name) as first_name,
                       COALESCE(a.last_name, s.last_name) as last_name
                FROM jobs j
                LEFT JOIN alumni a ON j.posted_by = a.alumni_id AND j.user_type = 'alumni'
                LEFT JOIN students s ON j.posted_by = s.student_id AND j.user_type = 'student'
                WHERE j.status = 'active' 
                  AND (j.title LIKE '%$keyword%' OR j.description LIKE '%$keyword%' OR j.company LIKE '%$keyword%')";
        
        if ($location) {
            $sql .= " AND j.location LIKE '%$location%'";
        }
        if ($job_type) {
            $sql .= " AND j.job_type = '$job_type'";
        }
        
        $sql .= " ORDER BY j.posted_at DESC";
        return $this->db_fetch_all($sql);
    }
    
    // Update job
    public function update_job($job_id, $title, $company, $location, $job_type, $description, $requirements, $application_url) {
        $sql = "UPDATE jobs 
                SET title = '$title', company = '$company', location = '$location', 
                    job_type = '$job_type', description = '$description', 
                    requirements = '$requirements', application_url = '$application_url'
                WHERE job_id = '$job_id'";
        return $this->db_query($sql);
    }
    
    // Delete job
    public function delete_job($job_id, $user_id) {
        $sql = "DELETE FROM jobs WHERE job_id = '$job_id' AND posted_by = '$user_id'";
        return $this->db_query($sql);
    }
    
    // Close job posting
    public function close_job($job_id, $user_id) {
        $sql = "UPDATE jobs SET status = 'closed' WHERE job_id = '$job_id' AND posted_by = '$user_id'";
        return $this->db_query($sql);
    }
    
    // Get jobs by user
    public function get_user_jobs($user_id, $user_type) {
        $sql = "SELECT * FROM jobs 
                WHERE posted_by = '$user_id' AND user_type = '$user_type' 
                ORDER BY posted_at DESC";
        return $this->db_fetch_all($sql);
    }
}
?>
