<?php

require_once '../settings/db_class.php';
require_once 'user_class.php';

/**
 * Alumni Profile class for handling alumni-specific profile operations
 */
class AlumniProfile extends db_connection
{
    public function __construct()
    {
        parent::db_connect();
    }

    /**
     * Register new alumni (creates user account + alumni profile)
     */
    public function register_alumni($first_name, $last_name, $email, $password, $phone, $major, 
                                   $graduation_year, $current_company, $job_title, $industry, $location, 
                                   $linkedin = null, $bio = null)
    {
        $user = new User();
        
        // First register as user with role 'alumni'
        $result = $user->registerUser($first_name, $last_name, $email, $password, 'alumni', $phone);
        
        if ($result['success']) {
            $user_id = $result['user_id'];
            
            // Parse location (assuming format: "City, Country")
            $location_parts = explode(',', $location);
            $location_city = trim($location_parts[0] ?? '');
            $location_country = trim($location_parts[1] ?? '');
            
            // Create alumni profile
            $profile_data = [
                'university' => 'Ashesi University',
                'major' => $major,
                'graduation_year' => $graduation_year,
                'current_company' => $current_company,
                'job_title' => $job_title,
                'industry' => $industry,
                'location_city' => $location_city,
                'location_country' => $location_country,
                'linkedin_url' => $linkedin
            ];
            
            if ($this->createProfile($user_id, $profile_data)) {
                return $user_id;
            }
        }
        
        return false;
    }

    /**
     * Login alumni
     */
    public function login_alumni($email, $password)
    {
        $user = new User();
        $result = $user->loginUser($email, $password);
        
        if ($result['success'] && $result['user']['user_role'] === 'alumni') {
            $user_data = $result['user'];
            // Get alumni profile data
            $profile = $this->getProfileByUserId($user_data['user_id']);
            if ($profile) {
                return array_merge($user_data, ['alumni_id' => $user_data['user_id']]);
            }
        }
        
        return false;
    }

    /**
     * Get alumni by ID
     */
    public function get_alumni_by_id($alumni_id)
    {
        return $this->getProfileByUserId($alumni_id);
    }

    /**
     * Create alumni profile
     */
    public function createProfile($user_id, $data)
    {
        $stmt = $this->db->prepare("INSERT INTO alumni_profiles (user_id, university, major, graduation_year, current_company, job_title, industry, location_city, location_country, linkedin_url, website_url, available_for_mentorship, expertise_areas) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $university = $data['university'] ?? 'Ashesi University';
        $major = $data['major'];
        $graduation_year = $data['graduation_year'];
        $current_company = $data['current_company'] ?? null;
        $job_title = $data['job_title'] ?? null;
        $industry = $data['industry'] ?? null;
        $location_city = $data['location_city'] ?? null;
        $location_country = $data['location_country'] ?? null;
        $linkedin_url = $data['linkedin_url'] ?? null;
        $website_url = $data['website_url'] ?? null;
        $available_for_mentorship = $data['available_for_mentorship'] ?? 0;
        $expertise_areas = $data['expertise_areas'] ?? null;
        
        $stmt->bind_param("issssssssssii", $user_id, $university, $major, $graduation_year, $current_company, $job_title, $industry, $location_city, $location_country, $linkedin_url, $website_url, $available_for_mentorship, $expertise_areas);
        
        return $stmt->execute();
    }

    /**
     * Get alumni profile by user ID
     */
    public function getProfileByUserId($user_id)
    {
        $stmt = $this->db->prepare("SELECT ap.*, u.first_name, u.last_name, u.email, u.phone, u.profile_image, u.bio FROM alumni_profiles ap JOIN users u ON ap.user_id = u.user_id WHERE ap.user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Update alumni profile
     */
    public function updateProfile($user_id, $data)
    {
        $fields = [];
        $values = [];
        $types = "";
        
        $allowed_fields = ['university', 'major', 'graduation_year', 'current_company', 'job_title', 'industry', 'location_city', 'location_country', 'linkedin_url', 'website_url', 'available_for_mentorship', 'expertise_areas'];
        
        foreach ($allowed_fields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
                if ($field === 'graduation_year' || $field === 'available_for_mentorship') {
                    $types .= "i";
                } else {
                    $types .= "s";
                }
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $user_id;
        $types .= "i";
        
        $sql = "UPDATE alumni_profiles SET " . implode(", ", $fields) . " WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }

    /**
     * Search alumni with filters
     */
    public function searchAlumni($filters = [])
    {
        $conditions = ["u.is_active = 1", "u.user_role = 'alumni'"];
        $params = [];
        $types = "";
        
        if (!empty($filters['major'])) {
            $conditions[] = "ap.major LIKE ?";
            $params[] = "%{$filters['major']}%";
            $types .= "s";
        }
        
        if (!empty($filters['graduation_year'])) {
            $conditions[] = "ap.graduation_year = ?";
            $params[] = $filters['graduation_year'];
            $types .= "i";
        }
        
        if (!empty($filters['company'])) {
            $conditions[] = "ap.current_company LIKE ?";
            $params[] = "%{$filters['company']}%";
            $types .= "s";
        }
        
        if (!empty($filters['industry'])) {
            $conditions[] = "ap.industry LIKE ?";
            $params[] = "%{$filters['industry']}%";
            $types .= "s";
        }
        
        if (!empty($filters['location'])) {
            $conditions[] = "(ap.location_city LIKE ? OR ap.location_country LIKE ?)";
            $params[] = "%{$filters['location']}%";
            $params[] = "%{$filters['location']}%";
            $types .= "ss";
        }
        
        if (isset($filters['available_for_mentorship']) && $filters['available_for_mentorship'] == 1) {
            $conditions[] = "ap.available_for_mentorship = 1";
        }
        
        if (!empty($filters['name'])) {
            $conditions[] = "(u.first_name LIKE ? OR u.last_name LIKE ?)";
            $params[] = "%{$filters['name']}%";
            $params[] = "%{$filters['name']}%";
            $types .= "ss";
        }
        
        $sql = "SELECT ap.*, u.first_name, u.last_name, u.email, u.profile_image 
                FROM alumni_profiles ap 
                JOIN users u ON ap.user_id = u.user_id 
                WHERE " . implode(" AND ", $conditions) . " 
                ORDER BY ap.graduation_year DESC 
                LIMIT 50";
        
        $stmt = $this->db->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get all alumni profiles
     */
    public function getAllAlumni($limit = 50, $offset = 0)
    {
        $stmt = $this->db->prepare("SELECT ap.*, u.first_name, u.last_name, u.profile_image FROM alumni_profiles ap JOIN users u ON ap.user_id = u.user_id WHERE u.is_active = 1 ORDER BY ap.graduation_year DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get alumni available for mentorship
     */
    public function getAvailableMentors($limit = 20)
    {
        $stmt = $this->db->prepare("SELECT ap.*, u.first_name, u.last_name, u.profile_image, u.bio FROM alumni_profiles ap JOIN users u ON ap.user_id = u.user_id WHERE ap.available_for_mentorship = 1 AND u.is_active = 1 ORDER BY ap.graduation_year DESC LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get alumni by graduation year range
     */
    public function getAlumniByYearRange($start_year, $end_year)
    {
        $stmt = $this->db->prepare("SELECT ap.*, u.first_name, u.last_name, u.profile_image FROM alumni_profiles ap JOIN users u ON ap.user_id = u.user_id WHERE ap.graduation_year BETWEEN ? AND ? AND u.is_active = 1 ORDER BY ap.graduation_year DESC");
        $stmt->bind_param("ii", $start_year, $end_year);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get alumni statistics
     */
    public function getAlumniStats()
    {
        $stats = [];
        
        // Total alumni
        $result = $this->db->query("SELECT COUNT(*) as total FROM alumni_profiles ap JOIN users u ON ap.user_id = u.user_id WHERE u.is_active = 1");
        $stats['total_alumni'] = $result->fetch_assoc()['total'];
        
        // Available mentors
        $result = $this->db->query("SELECT COUNT(*) as total FROM alumni_profiles WHERE available_for_mentorship = 1");
        $stats['available_mentors'] = $result->fetch_assoc()['total'];
        
        // Top industries
        $result = $this->db->query("SELECT industry, COUNT(*) as count FROM alumni_profiles WHERE industry IS NOT NULL GROUP BY industry ORDER BY count DESC LIMIT 5");
        $stats['top_industries'] = $result->fetch_all(MYSQLI_ASSOC);
        
        // Top companies
        $result = $this->db->query("SELECT current_company, COUNT(*) as count FROM alumni_profiles WHERE current_company IS NOT NULL GROUP BY current_company ORDER BY count DESC LIMIT 5");
        $stats['top_companies'] = $result->fetch_all(MYSQLI_ASSOC);
        
        return $stats;
    }
}

?>
