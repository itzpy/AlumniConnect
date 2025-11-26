<?php

require_once '../settings/db_class.php';
require_once 'user_class.php';

/**
 * Student Profile class for handling student-specific profile operations
 */
class StudentProfile extends db_connection
{
    public function __construct()
    {
        parent::db_connect();
    }

    /**
     * Register new student (creates user account + student profile)
     */
    public function register_student($first_name, $last_name, $email, $password, $phone, $major, 
                                     $expected_graduation_year, $year_level, $career_interests = null)
    {
        $user = new User();
        
        // First register as user with role 'student'
        $result = $user->registerUser($first_name, $last_name, $email, $password, 'student', $phone);
        
        if ($result['success']) {
            $user_id = $result['user_id'];
            
            // Create student profile
            $profile_data = [
                'university' => 'Ashesi University',
                'major' => $major,
                'expected_graduation' => $expected_graduation_year,
                'year_level' => $year_level,
                'career_goals' => $career_interests
            ];
            
            if ($this->createProfile($user_id, $profile_data)) {
                return $user_id;
            }
        }
        
        return false;
    }

    /**
     * Login student
     */
    public function login_student($email, $password)
    {
        $user = new User();
        $result = $user->loginUser($email, $password);
        
        if ($result['success'] && $result['user']['user_role'] === 'student') {
            $user_data = $result['user'];
            // Get student profile data
            $profile = $this->getProfileByUserId($user_data['user_id']);
            if ($profile) {
                return array_merge($user_data, ['student_id' => $user_data['user_id']]);
            }
        }
        
        return false;
    }

    /**
     * Get student by ID
     */
    public function get_student_by_id($student_id)
    {
        return $this->getProfileByUserId($student_id);
    }

    /**
     * Create student profile
     */
    public function createProfile($user_id, $data)
    {
        $stmt = $this->db->prepare("INSERT INTO student_profiles (user_id, university, major, expected_graduation, year_level, interests, career_goals, gpa, linkedin_url, portfolio_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $university = $data['university'] ?? 'Ashesi University';
        $major = $data['major'];
        $expected_graduation = $data['expected_graduation'];
        $year_level = $data['year_level'];
        $interests = $data['interests'] ?? null;
        $career_goals = $data['career_goals'] ?? null;
        $gpa = $data['gpa'] ?? null;
        $linkedin_url = $data['linkedin_url'] ?? null;
        $portfolio_url = $data['portfolio_url'] ?? null;
        
        $stmt->bind_param("isssssssss", $user_id, $university, $major, $expected_graduation, $year_level, $interests, $career_goals, $gpa, $linkedin_url, $portfolio_url);
        
        return $stmt->execute();
    }

    /**
     * Get student profile by user ID
     */
    public function getProfileByUserId($user_id)
    {
        $stmt = $this->db->prepare("SELECT sp.*, u.first_name, u.last_name, u.email, u.phone, u.profile_image, u.bio FROM student_profiles sp JOIN users u ON sp.user_id = u.user_id WHERE sp.user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Update student profile
     */
    public function updateProfile($user_id, $data)
    {
        $fields = [];
        $values = [];
        $types = "";
        
        $allowed_fields = ['university', 'major', 'expected_graduation', 'year_level', 'interests', 'career_goals', 'gpa', 'linkedin_url', 'portfolio_url'];
        
        foreach ($allowed_fields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
                if ($field === 'expected_graduation') {
                    $types .= "i";
                } elseif ($field === 'gpa') {
                    $types .= "d";
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
        
        $sql = "UPDATE student_profiles SET " . implode(", ", $fields) . " WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }

    /**
     * Search students with filters
     */
    public function searchStudents($filters = [])
    {
        $conditions = ["u.is_active = 1", "u.user_role = 'student'"];
        $params = [];
        $types = "";
        
        if (!empty($filters['major'])) {
            $conditions[] = "sp.major LIKE ?";
            $params[] = "%{$filters['major']}%";
            $types .= "s";
        }
        
        if (!empty($filters['expected_graduation'])) {
            $conditions[] = "sp.expected_graduation = ?";
            $params[] = $filters['expected_graduation'];
            $types .= "i";
        }
        
        if (!empty($filters['year_level'])) {
            $conditions[] = "sp.year_level = ?";
            $params[] = $filters['year_level'];
            $types .= "s";
        }
        
        if (!empty($filters['interests'])) {
            $conditions[] = "sp.interests LIKE ?";
            $params[] = "%{$filters['interests']}%";
            $types .= "s";
        }
        
        if (!empty($filters['name'])) {
            $conditions[] = "(u.first_name LIKE ? OR u.last_name LIKE ?)";
            $params[] = "%{$filters['name']}%";
            $params[] = "%{$filters['name']}%";
            $types .= "ss";
        }
        
        $sql = "SELECT sp.*, u.first_name, u.last_name, u.email, u.profile_image 
                FROM student_profiles sp 
                JOIN users u ON sp.user_id = u.user_id 
                WHERE " . implode(" AND ", $conditions) . " 
                ORDER BY sp.expected_graduation ASC 
                LIMIT 50";
        
        $stmt = $this->db->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get all students
     */
    public function getAllStudents($limit = 50, $offset = 0)
    {
        $stmt = $this->db->prepare("SELECT sp.*, u.first_name, u.last_name, u.profile_image FROM student_profiles sp JOIN users u ON sp.user_id = u.user_id WHERE u.is_active = 1 ORDER BY sp.expected_graduation ASC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get students by graduation year
     */
    public function getStudentsByGraduationYear($year)
    {
        $stmt = $this->db->prepare("SELECT sp.*, u.first_name, u.last_name, u.profile_image FROM student_profiles sp JOIN users u ON sp.user_id = u.user_id WHERE sp.expected_graduation = ? AND u.is_active = 1 ORDER BY u.last_name ASC");
        $stmt->bind_param("i", $year);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get student statistics
     */
    public function getStudentStats()
    {
        $stats = [];
        
        // Total students
        $result = $this->db->query("SELECT COUNT(*) as total FROM student_profiles sp JOIN users u ON sp.user_id = u.user_id WHERE u.is_active = 1");
        $stats['total_students'] = $result->fetch_assoc()['total'];
        
        // Students by year level
        $result = $this->db->query("SELECT year_level, COUNT(*) as count FROM student_profiles GROUP BY year_level ORDER BY FIELD(year_level, 'Freshman', 'Sophomore', 'Junior', 'Senior')");
        $stats['by_year_level'] = $result->fetch_all(MYSQLI_ASSOC);
        
        // Top majors
        $result = $this->db->query("SELECT major, COUNT(*) as count FROM student_profiles GROUP BY major ORDER BY count DESC LIMIT 5");
        $stats['top_majors'] = $result->fetch_all(MYSQLI_ASSOC);
        
        return $stats;
    }
}

?>
