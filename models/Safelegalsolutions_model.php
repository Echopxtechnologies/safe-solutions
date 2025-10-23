<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * SafeLegalSolutions Model
 * Handles all database operations for the module
 * Version: 1.2 - With Branch Management Support
 */
class Safelegalsolutions_model extends App_Model
{
    private $table_categories;
    private $table_branches;
    private $table_students;
    private $table_change_requests;
    private $table_items;
    private $table_payments;
private $table_enrollments;

    public function __construct()
    {
        parent::__construct();
        
        $this->table_categories = db_prefix() . 'sls_branch_categories';
        $this->table_branches = db_prefix() . 'sls_branches';
        $this->table_students = db_prefix() . 'sls_students';
        $this->table_change_requests = db_prefix() . 'sls_change_requests';
        $this->table_items = db_prefix() . 'sls_items';
        $this->table_payments = db_prefix() . 'sls_payments'; // NEW
        $this->table_enrollments = db_prefix() . 'sls_package_enrollments'; // NEW
        $this->table_countries = db_prefix() . 'sls_destination_countries';
        $this->table_partner_docs = db_prefix() . 'sls_partner_documents';
    }

    // ==================== BRANCH CATEGORIES ====================

    /**
     * Get all branch categories
     * @param array $where Conditions
     * @return array
     */
    public function get_all_categories($where = [])
    {
        if (!empty($where)) {
            $this->db->where($where);
        }
        
        $this->db->order_by('name', 'ASC');
        return $this->db->get($this->table_categories)->result();
    }

    /**
     * Get single category
     * @param int $id
     * @return object|null
     */
    public function get_category($id)
    {
        return $this->db->get_where($this->table_categories, ['id' => $id])->row();
    }

    // ==================== DESTINATION COUNTRIES ====================

/**
 * Get all destination countries
 * @param array $where Conditions
 * @return array
 */
public function get_all_countries($where = [])
{
    if (!empty($where)) {
        $this->db->where($where);
    }
    
    $this->db->order_by('is_popular', 'DESC');
    $this->db->order_by('display_order', 'ASC');
    $this->db->order_by('country_name', 'ASC');
    return $this->db->get($this->table_countries)->result();
}

/**
 * Get popular countries
 * @return array
 */
public function get_popular_countries()
{
    $this->db->where('is_popular', 1);
    $this->db->where('is_active', 1);
    $this->db->order_by('display_order', 'ASC');
    return $this->db->get($this->table_countries)->result();
}

/**
 * Get country by ID
 * @param int $id
 * @return object|null
 */
public function get_country($id)
{
    return $this->db->get_where($this->table_countries, ['id' => $id])->row();
}

    // ==================== BRANCHES ====================

    /**
     * Get all branches
     * @param array $where Conditions
     * @return array
     */
    public function get_all_branches($where = [])
    {
        $this->db->select($this->table_branches . '.*, ' . 
                         $this->table_categories . '.name as category_name, ' .
                         $this->table_categories . '.color_code, ' .
                         db_prefix() . 'staff.firstname as manager_firstname, ' .
                         db_prefix() . 'staff.lastname as manager_lastname');
        $this->db->from($this->table_branches);
        $this->db->join($this->table_categories, 
                       $this->table_categories . '.id = ' . $this->table_branches . '.category_id', 
                       'left');
        $this->db->join(db_prefix() . 'staff', 
                       db_prefix() . 'staff.staffid = ' . $this->table_branches . '.nodal_partner_manager_id', 
                       'left');
        
        if (!empty($where)) {
            $this->db->where($where);
        }
        
        $this->db->order_by($this->table_branches . '.is_default', 'DESC');
        $this->db->order_by($this->table_branches . '.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get single branch
     * @param int $id
     * @return object|null
     */
    public function get_branch($id)
    {
        $this->db->select($this->table_branches . '.*, ' . 
                         $this->table_categories . '.name as category_name, ' .
                         $this->table_categories . '.color_code, ' .
                         db_prefix() . 'staff.firstname as manager_firstname, ' .
                         db_prefix() . 'staff.lastname as manager_lastname');
        $this->db->from($this->table_branches);
        $this->db->join($this->table_categories, 
                       $this->table_categories . '.id = ' . $this->table_branches . '.category_id', 
                       'left');
        $this->db->join(db_prefix() . 'staff', 
                       db_prefix() . 'staff.staffid = ' . $this->table_branches . '.nodal_partner_manager_id', 
                       'left');
        $this->db->where($this->table_branches . '.id', $id);
        
        return $this->db->get()->row();
    }

    /**
     * Get branch by assigned manager
     * @param int $staff_id
     * @return object|null
     */
    public function get_branch_by_manager($staff_id)
    {
        $this->db->select($this->table_branches . '.*, ' . 
                         $this->table_categories . '.name as category_name, ' .
                         $this->table_categories . '.color_code');
        $this->db->from($this->table_branches);
        $this->db->join($this->table_categories, 
                       $this->table_categories . '.id = ' . $this->table_branches . '.category_id', 
                       'left');
        $this->db->where($this->table_branches . '.nodal_partner_manager_id', $staff_id);
        $this->db->where($this->table_branches . '.is_active', 1);
        
        return $this->db->get()->row();
    }

    /**
     * Get branch by registration token
     * @param string $token
     * @return object|null
     */
    public function get_branch_by_token($token)
    {
        $this->db->select($this->table_branches . '.*, ' . 
                         $this->table_categories . '.name as category_name, ' .
                         $this->table_categories . '.color_code, ' .
                         db_prefix() . 'staff.firstname as manager_firstname, ' .
                         db_prefix() . 'staff.lastname as manager_lastname');
        $this->db->from($this->table_branches);
        $this->db->join($this->table_categories, 
                       $this->table_categories . '.id = ' . $this->table_branches . '.category_id', 
                       'left');
        $this->db->join(db_prefix() . 'staff', 
                       db_prefix() . 'staff.staffid = ' . $this->table_branches . '.nodal_partner_manager_id', 
                       'left');
        $this->db->where($this->table_branches . '.registration_token', $token);
        
        return $this->db->get()->row();
    }

    /**
     * Get default branch (is_default = 1)
     * @return object|null
     */
    public function get_default_branch()
    {
        $this->db->select($this->table_branches . '.*, ' . 
                         $this->table_categories . '.name as category_name, ' .
                         $this->table_categories . '.color_code, ' .
                         db_prefix() . 'staff.firstname as manager_firstname, ' .
                         db_prefix() . 'staff.lastname as manager_lastname');
        $this->db->from($this->table_branches);
        $this->db->join($this->table_categories, 
                       $this->table_categories . '.id = ' . $this->table_branches . '.category_id', 
                       'left');
        $this->db->join(db_prefix() . 'staff', 
                       db_prefix() . 'staff.staffid = ' . $this->table_branches . '.nodal_partner_manager_id', 
                       'left');
        $this->db->where($this->table_branches . '.is_default', 1);
        $this->db->where($this->table_branches . '.is_active', 1);
        
        return $this->db->get()->row();
    }

    /**
     * Get all active branches for dropdowns (simple - no staff join to avoid ambiguous is_active)
     * @return array
     */
    public function get_all_branches_simple()
    {
        $this->db->select($this->table_branches . '.id, ' .
                         $this->table_branches . '.branch_name, ' .
                         $this->table_branches . '.branch_code, ' .
                         $this->table_branches . '.is_default, ' .
                         $this->table_branches . '.is_active, ' .
                         $this->table_branches . '.nodal_partner_manager_id, ' .
                         $this->table_categories . '.name as category_name, ' .
                         $this->table_categories . '.color_code');
        $this->db->from($this->table_branches);
        $this->db->join($this->table_categories, 
                       $this->table_categories . '.id = ' . $this->table_branches . '.category_id', 
                       'left');
        $this->db->where($this->table_branches . '.is_active', 1);
        $this->db->order_by($this->table_branches . '.is_default', 'DESC');
        $this->db->order_by($this->table_branches . '.branch_name', 'ASC');
        
        return $this->db->get()->result();
    }

    /**
     * Add new branch
     * @param array $data
     * @return int|bool Insert ID or false
     */
    public function add_branch($data)
    {
        // Generate unique branch code if not provided
        if (empty($data['branch_code'])) {
            $data['branch_code'] = 'BR-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        }
        
        $this->db->insert($this->table_branches, $data);
        $insert_id = $this->db->insert_id();
        
        if ($insert_id) {
            log_activity('New Branch Added [ID: ' . $insert_id . ', Name: ' . $data['branch_name'] . ']');
            return $insert_id;
        }
        
        return false;
    }

    /**
     * Update branch
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_branch($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update($this->table_branches, $data);
        
        if ($this->db->affected_rows() > 0) {
            log_activity('Branch Updated [ID: ' . $id . ']');
            return true;
        }
        
        return false;
    }

    /**
     * Delete branch
     * @param int $id
     * @return bool
     */
    public function delete_branch($id)
    {
        // Check if branch has students
        $has_students = $this->db->get_where($this->table_students, ['branch_id' => $id])->num_rows();
        
        if ($has_students > 0) {
            return false; // Cannot delete branch with students
        }
        
        // Prevent deleting default branch
        $branch = $this->get_branch($id);
        if ($branch && isset($branch->is_default) && $branch->is_default == 1) {
            return false; // Cannot delete default branch
        }
        
        $this->db->where('id', $id);
        $this->db->delete($this->table_branches);
        
        if ($this->db->affected_rows() > 0) {
            log_activity('Branch Deleted [ID: ' . $id . ']');
            return true;
        }
        
        return false;
    }

    /**
     * Count branches
     * @param array $where Conditions
     * @return int
     */
    public function count_branches($where = [])
    {
        if (!empty($where)) {
            $this->db->where($where);
        }
        
        return $this->db->count_all_results($this->table_branches);
    }

    /**
     * Get branch earnings (sum of all students in this branch)
     * @param int $branch_id
     * @return float
     */
    public function get_branch_earnings($branch_id)
    {
        $this->db->select_sum('earnings');
        $this->db->where('branch_id', $branch_id);
        $result = $this->db->get($this->table_students)->row();
        
        return $result ? (float)$result->earnings : 0.00;
    }

    /**
     * Get average profile completion for a branch
     * @param int $branch_id
     * @return float
     */
    public function get_branch_avg_completion($branch_id)
    {
        $this->db->select_avg('profile_completion');
        $this->db->where('branch_id', $branch_id);
        $result = $this->db->get($this->table_students)->row();
        
        return $result ? round((float)$result->profile_completion, 2) : 0.00;
    }

    // ==================== STUDENTS ====================

    /**
     * Get students
     * @param array $where Conditions
     * @param int $limit
     * @return array
     */
    public function get_students($where = [], $limit = null)
    {
        $this->db->select($this->table_students . '.*, ' . 
                         $this->table_branches . '.branch_name, ' .
                         $this->table_categories . '.name as category_name, ' .
                         $this->table_countries . '.country_name as destination_country, ' . // ADD THIS
                         db_prefix() . 'staff.firstname as manager_firstname, ' .
                         db_prefix() . 'staff.lastname as manager_lastname');
        $this->db->from($this->table_students);
        $this->db->join($this->table_branches, 
                       $this->table_branches . '.id = ' . $this->table_students . '.branch_id', 
                       'left');
        $this->db->join($this->table_countries, 
                    $this->table_countries . '.id = ' . $this->table_students . '.destination_country_id', 
                    'left');
        $this->db->join($this->table_categories, 
                       $this->table_categories . '.id = ' . $this->table_branches . '.category_id', 
                       'left');
        $this->db->join(db_prefix() . 'staff', 
                       db_prefix() . 'staff.staffid = ' . $this->table_students . '.nodal_partner_manager_id', 
                       'left');
        
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                // Add table prefix to avoid ambiguous column names
                if (in_array($key, ['status', 'is_locked', 'branch_id', 'nodal_partner_manager_id'])) {
                    $this->db->where($this->table_students . '.' . $key, $value);
                } else {
                    $this->db->where($key, $value);
                }
            }
        }
        
        $this->db->order_by($this->table_students . '.created_at', 'DESC');
        
        if ($limit !== null) {
            $this->db->limit($limit);
        }
        
        return $this->db->get()->result();
    }

    /**
     * Get single student
     * @param int $id
     * @return object|null
     */
    public function get_student($id)
    {
        $this->db->select($this->table_students . '.*, ' . 
                         $this->table_branches . '.branch_name, ' .
                         $this->table_categories . '.name as category_name');
        $this->db->from($this->table_students);
        $this->db->join($this->table_branches, 
                       $this->table_branches . '.id = ' . $this->table_students . '.branch_id', 
                       'left');
        $this->db->join($this->table_categories, 
                       $this->table_categories . '.id = ' . $this->table_branches . '.category_id', 
                       'left');
        $this->db->where($this->table_students . '.id', $id);
        
        return $this->db->get()->row();
    }

    /**
     * Get student by email (to check duplicates)
     * @param string $email
     * @return object|null
     */
    public function get_student_by_email($email)
    {
        $this->db->where('email', $email);
        return $this->db->get($this->table_students)->row();
    }

    /**
     * Get recent students
     * @param int $limit
     * @return array
     */
    public function get_recent_students($limit = 10)
    {
        return $this->get_students([], $limit);
    }

 /**
 * Add new student
 * @param array $data
 * @return int|bool Insert ID or false
 */
public function add_student($data)
{
    // ============================================================
    // FILTER: Remove payment table fields (they go to sls_payments)
    // ============================================================
    $payment_table_fields = [
        'payment_method',
        'transaction_reference',
        'payment_date',
        'payment_notes',
        'receipt_number'
    ];
    
    foreach ($payment_table_fields as $field) {
        if (isset($data[$field])) {
            unset($data[$field]);
        }
    }
    
    // ============================================================
    // SET DEFAULT VALUES (FIX FOR EMPTY STATUS)
    // ============================================================
    if (!isset($data['status']) || empty($data['status'])) {
        $data['status'] = 'draft';
    }
    
    if (!isset($data['is_locked']) || $data['is_locked'] === '') {
        $data['is_locked'] = 0;
    }
    
    if (!isset($data['profile_completion']) || $data['profile_completion'] === '') {
        $data['profile_completion'] = $this->calculate_profile_completion($data);
    } else {
        // Recalculate anyway
        $data['profile_completion'] = $this->calculate_profile_completion($data);
    }
    
    // ============================================================
    // INSERT STUDENT
    // ============================================================
    $this->db->insert($this->table_students, $data);
    $insert_id = $this->db->insert_id();
    
    if ($insert_id) {
        log_activity('New Student Added [ID: ' . $insert_id . ', Name: ' . $data['student_name'] . ', Status: ' . $data['status'] . ']');
        return $insert_id;
    }
    
    return false;
}
   /**
 * Update student
 * @param int $id
 * @param array $data
 * @return bool
 */
public function update_student($id, $data)
{
    // ============================================================
    // FILTER: Remove payment table fields (they go to sls_payments)
    // ============================================================
    $payment_table_fields = [
        'payment_method',
        'transaction_reference',
        'payment_date',
        'payment_notes',
        'receipt_number'
    ];
    
    foreach ($payment_table_fields as $field) {
        if (isset($data[$field])) {
            unset($data[$field]);
        }
    }
    
    // Recalculate profile completion if relevant fields updated
    if (isset($data['student_name']) || isset($data['email']) || isset($data['phone'])) {
        $current = $this->get_student($id);
        $merged = array_merge((array)$current, $data);
        $data['profile_completion'] = $this->calculate_profile_completion($merged);
    }
    
    $this->db->where('id', $id);
    $this->db->update($this->table_students, $data);
    
    if ($this->db->affected_rows() > 0) {
        log_activity('Student Updated [ID: ' . $id . ']');
        return true;
    }
    
    return false;
}

    /**
     * Delete student
     * @param int $id
     * @return bool
     */
    public function delete_student($id)
    {
        $this->db->where('id', $id);
        $this->db->delete($this->table_students);
        
        if ($this->db->affected_rows() > 0) {
            log_activity('Student Deleted [ID: ' . $id . ']');
            return true;
        }
        
        return false;
    }

    /**
     * Count students
     * @param array $where Conditions
     * @return int
     */
    public function count_students($where = [])
    {
        $this->db->from($this->table_students);
        
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                // Add table prefix to avoid ambiguous column names
                if (in_array($key, ['status', 'is_locked', 'branch_id', 'nodal_partner_manager_id'])) {
                    $this->db->where($this->table_students . '.' . $key, $value);
                } else {
                    $this->db->where($key, $value);
                }
            }
        }
        
        return $this->db->count_all_results();
    }
    
    /**
     * Count students by status (avoiding ambiguous column)
     * @param string $status
     * @return int
     */
    public function count_students_by_status($status)
    {
        $this->db->where('status', $status);
        return $this->db->count_all_results($this->table_students);
    }
    
    /**
     * Count students by locked status
     * @param int $is_locked
     * @return int
     */
    public function count_students_by_locked($is_locked)
    {
        $this->db->where('is_locked', $is_locked);
        return $this->db->count_all_results($this->table_students);
    }
    
    /**
     * Count students by manager
     * @param int $manager_id
     * @return int
     */
    public function count_students_by_manager($manager_id)
    {
        $this->db->where('nodal_partner_manager_id', $manager_id);
        return $this->db->count_all_results($this->table_students);
    }
    
    /**
     * Count students by manager and status
     * @param int $manager_id
     * @param string $status
     * @return int
     */
    public function count_students_by_manager_status($manager_id, $status)
    {
        $this->db->where('nodal_partner_manager_id', $manager_id);
        $this->db->where('status', $status);
        return $this->db->count_all_results($this->table_students);
    }
    
    /**
     * Count students by manager and locked status
     * @param int $manager_id
     * @param int $is_locked
     * @return int
     */
    public function count_students_by_manager_locked($manager_id, $is_locked)
    {
        $this->db->where('nodal_partner_manager_id', $manager_id);
        $this->db->where('is_locked', $is_locked);
        return $this->db->count_all_results($this->table_students);
    }
    
    /**
     * Get students by manager (avoiding joins)
     * @param int $manager_id
     * @param int $limit
     * @return array
     */
    public function get_students_by_manager($manager_id, $limit = null)
    {
        $this->db->select($this->table_students . '.*, ' . 
                         $this->table_branches . '.branch_name, ' .
                         $this->table_categories . '.name as category_name');
        $this->db->from($this->table_students);
        $this->db->join($this->table_branches, 
                       $this->table_branches . '.id = ' . $this->table_students . '.branch_id', 
                       'left');
        $this->db->join($this->table_categories, 
                       $this->table_categories . '.id = ' . $this->table_branches . '.category_id', 
                       'left');
        $this->db->where($this->table_students . '.nodal_partner_manager_id', $manager_id);
        $this->db->order_by($this->table_students . '.created_at', 'DESC');
        
        if ($limit !== null) {
            $this->db->limit($limit);
        }
        
        return $this->db->get()->result();
    }

    /**
     * Calculate profile completion percentage
     * @param array $data Student data
     * @return int Percentage (0-100)
     */
    private function calculate_profile_completion($data)
    {
        $required_fields = [
            'student_name',
            'email',
            'phone',
            'address',
            'date_of_birth',
            'passport_number',

        ];
        
        $filled = 0;
        $total = count($required_fields);
        
        foreach ($required_fields as $field) {
            if (!empty($data[$field])) {
                $filled++;
            }
        }
        
        return round(($filled / $total) * 100);
    }

    /**
     * Generate unique referral code
     * @return string
     */
    public function generate_referral_code()
    {
        do {
            $code = 'REF-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
            $exists = $this->db->get_where($this->table_students, ['referral_code' => $code])->row();
        } while ($exists);
        
        return $code;
    }

    /**
     * Get total earnings (all students)
     * @return float
     */
    public function get_total_earnings()
    {
        $this->db->select_sum('earnings');
        $result = $this->db->get($this->table_students)->row();
        
        return $result ? (float)$result->earnings : 0.00;
    }

    /**
     * Get manager's earnings
     * @param int $staff_id
     * @return float
     */
    public function get_manager_earnings($staff_id)
    {
        $this->db->select_sum('earnings');
        $this->db->where('nodal_partner_manager_id', $staff_id);
        $result = $this->db->get($this->table_students)->row();
        
        return $result ? (float)$result->earnings : 0.00;
    }

    /**
     * Get average profile completion for a manager
     * @param int $staff_id
     * @return float
     */
    public function get_avg_profile_completion($staff_id)
    {
        $this->db->select_avg('profile_completion');
        $this->db->where('nodal_partner_manager_id', $staff_id);
        $result = $this->db->get($this->table_students)->row();
        
        return $result ? round((float)$result->profile_completion, 2) : 0.00;
    }

    // ==================== CHANGE REQUESTS ====================

    /**
     * Get change requests
     * @param array $where Conditions
     * @param int $limit
     * @return array
     */
    public function get_change_requests($where = [], $limit = null)
    {
        $this->db->select($this->table_change_requests . '.*, ' . 
                         $this->table_students . '.student_name, ' .
                         'requester.firstname as requester_firstname, ' .
                         'requester.lastname as requester_lastname, ' .
                         'reviewer.firstname as reviewer_firstname, ' .
                         'reviewer.lastname as reviewer_lastname');
        $this->db->from($this->table_change_requests);
        $this->db->join($this->table_students, 
                       $this->table_students . '.id = ' . $this->table_change_requests . '.student_id', 
                       'left');
        $this->db->join(db_prefix() . 'staff as requester', 
                       'requester.staffid = ' . $this->table_change_requests . '.requested_by', 
                       'left');
        $this->db->join(db_prefix() . 'staff as reviewer', 
                       'reviewer.staffid = ' . $this->table_change_requests . '.reviewed_by', 
                       'left');
        
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                // Prefix table name for status to avoid ambiguity with students table
                if ($key === 'status') {
                    $this->db->where($this->table_change_requests . '.status', $value);
                } else {
                    $this->db->where($key, $value);
                }
            }
        }
        
        $this->db->order_by($this->table_change_requests . '.created_at', 'DESC');
        
        if ($limit !== null) {
            $this->db->limit($limit);
        }
        
        return $this->db->get()->result();
    }

    /**
     * Count change requests (avoiding ambiguous status)
     * @param array $where Conditions
     * @return int
     */
    public function count_change_requests($where = [])
    {
        $this->db->from($this->table_change_requests);
        
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                if ($key === 'status') {
                    $this->db->where($this->table_change_requests . '.status', $value);
                } else {
                    $this->db->where($key, $value);
                }
            }
        }
        
        return $this->db->count_all_results();
    }

    /**
     * Add change request
     * @param array $data
     * @return int|bool Insert ID or false
     */
    public function add_change_request($data)
    {
        $this->db->insert($this->table_change_requests, $data);
        $insert_id = $this->db->insert_id();
        
        if ($insert_id) {
            log_activity('Change Request Created [ID: ' . $insert_id . ']');
            return $insert_id;
        }
        
        return false;
    }


    // ==================== ITEMS/PACKAGES ====================

/**
 * Get all items/packages
 * @param array $where Conditions
 * @return array
 */
public function get_all_items($where = [])
{
    if (!empty($where)) {
        $this->db->where($where);
    }
    
    $this->db->order_by('display_order', 'ASC');
    $this->db->order_by('item_name', 'ASC');
    return $this->db->get($this->table_items)->result();
}

/**
 * Get single item
 * @param int $id
 * @return object|null
 */
public function get_item($id)
{
    return $this->db->get_where($this->table_items, ['id' => $id])->row();
}

/**
 * Get item by code
 * @param string $item_code
 * @return object|null
 */
public function get_item_by_code($item_code)
{
    return $this->db->get_where($this->table_items, ['item_code' => $item_code])->row();
}

/**
 * Get active items for dropdown
 * @return array
 */
public function get_active_items()
{
    $this->db->where('is_active', 1);
    $this->db->order_by('display_order', 'ASC');
    $this->db->order_by('item_name', 'ASC');
    return $this->db->get($this->table_items)->result();
}

/**
 * Add new item
 * @param array $data
 * @return int|bool Insert ID or false
 */
public function add_item($data)
{
    // Calculate GST and total if not provided
    if (isset($data['base_price']) && isset($data['gst_percentage'])) {
        $data['gst_amount'] = ($data['base_price'] * $data['gst_percentage']) / 100;
        $data['total_price'] = $data['base_price'] + $data['gst_amount'];
    }
    
    // Generate unique item code if not provided
    if (empty($data['item_code'])) {
        $data['item_code'] = 'PKG-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
    }
    
    $this->db->insert($this->table_items, $data);
    $insert_id = $this->db->insert_id();
    
    if ($insert_id) {
        log_activity('New Item/Package Added [ID: ' . $insert_id . ', Name: ' . $data['item_name'] . ']');
        return $insert_id;
    }
    
    return false;
}

/**
 * Update item
 * @param int $id
 * @param array $data
 * @return bool
 */
public function update_item($id, $data)
{
    // Recalculate GST and total if pricing fields updated
    if (isset($data['base_price']) && isset($data['gst_percentage'])) {
        $data['gst_amount'] = ($data['base_price'] * $data['gst_percentage']) / 100;
        $data['total_price'] = $data['base_price'] + $data['gst_amount'];
    } elseif (isset($data['base_price']) || isset($data['gst_percentage'])) {
        // If only one field updated, get the other from database
        $current = $this->get_item($id);
        $base_price = isset($data['base_price']) ? $data['base_price'] : $current->base_price;
        $gst_percentage = isset($data['gst_percentage']) ? $data['gst_percentage'] : $current->gst_percentage;
        
        $data['gst_amount'] = ($base_price * $gst_percentage) / 100;
        $data['total_price'] = $base_price + $data['gst_amount'];
    }
    
    $this->db->where('id', $id);
    $this->db->update($this->table_items, $data);
    
    if ($this->db->affected_rows() > 0) {
        log_activity('Item/Package Updated [ID: ' . $id . ']');
        return true;
    }
    
    return false;
}

/**
 * Delete item
 * @param int $id
 * @return bool
 */
public function delete_item($id)
{
    // Check if item is assigned to any students
    $has_students = $this->db->get_where($this->table_students, ['item_id' => $id])->num_rows();
    
    if ($has_students > 0) {
        return false; // Cannot delete item assigned to students
    }
    
    $this->db->where('id', $id);
    $this->db->delete($this->table_items);
    
    if ($this->db->affected_rows() > 0) {
        log_activity('Item/Package Deleted [ID: ' . $id . ']');
        return true;
    }
    
    return false;
}

/**
 * Count items
 * @param array $where Conditions
 * @return int
 */
public function count_items($where = [])
{
    if (!empty($where)) {
        $this->db->where($where);
    }
    
    return $this->db->count_all_results($this->table_items);
}

/**
 * Get students with package/item information
 * @param array $where Conditions
 * @param int $limit
 * @return array
 */
public function get_students_with_packages($where = [], $limit = null)
{
    $this->db->select($this->table_students . '.*, ' . 
                     $this->table_branches . '.branch_name, ' .
                     $this->table_categories . '.name as category_name, ' .
                     db_prefix() . 'staff.firstname as manager_firstname, ' .
                     db_prefix() . 'staff.lastname as manager_lastname, ' .
                     $this->table_items . '.item_name, ' .
                     $this->table_items . '.total_price as item_total_price, ' .
                     $this->table_items . '.duration_months as item_duration');
    $this->db->from($this->table_students);
    $this->db->join($this->table_branches, 
                   $this->table_branches . '.id = ' . $this->table_students . '.branch_id', 
                   'left');
    $this->db->join($this->table_categories, 
                   $this->table_categories . '.id = ' . $this->table_branches . '.category_id', 
                   'left');
    $this->db->join(db_prefix() . 'staff', 
                   db_prefix() . 'staff.staffid = ' . $this->table_students . '.nodal_partner_manager_id', 
                   'left');
    $this->db->join($this->table_items, 
                   $this->table_items . '.id = ' . $this->table_students . '.item_id', 
                   'left');
    
    if (!empty($where)) {
        foreach ($where as $key => $value) {
            if (in_array($key, ['status', 'is_locked', 'branch_id', 'nodal_partner_manager_id'])) {
                $this->db->where($this->table_students . '.' . $key, $value);
            } else {
                $this->db->where($key, $value);
            }
        }
    }
    
    $this->db->order_by($this->table_students . '.created_at', 'DESC');
    
    if ($limit !== null) {
        $this->db->limit($limit);
    }
    
    return $this->db->get()->result();
}


// ==================== CLIENT ACCOUNT CREATION ====================

    /**
     * Create client account for student when payment is complete
     * This creates both tblclients and tblcontacts records
     * 
     * @param int $student_id Student ID
     * @param array $payment_data Payment information (optional)
     * @return array Result with success status, client_id, and password
     */
    public function create_client_account_for_student($student_id, $payment_data = [])
    {
        // Get student details
        $student = $this->get_student($student_id);
        
        if (!$student) {
            return [
                'success' => false,
                'message' => 'Student not found'
            ];
        }
        
        // Check if client already exists with this email
        $existing_contact = $this->db->get_where('tblcontacts', [
            'email' => $student->email
        ])->row();
        
        if ($existing_contact) {
            return [
                'success' => false,
                'message' => 'Client account already exists with this email',
                'client_id' => $existing_contact->userid
            ];
        }
        
        // Generate random secure password
        $password = $this->generate_secure_password();
        $password_hash = app_hash_password($password);
        
        // Prepare client data
        $client_data = [
            'company'           => $student->student_name, // Use student name as company
            'vat'               => '', // Can be added later
            'phonenumber'       => $student->phone,
            'country'           => 0, // Default country
            'city'              => '',
            'zip'               => '',
            'state'             => '',
            'address'           => $student->address,
            'website'           => '',
            'datecreated'       => date('Y-m-d H:i:s'),
            'active'            => 1,
            'leadid'            => NULL,
            'billing_street'    => $student->address,
            'billing_city'      => '',
            'billing_state'     => '',
            'billing_zip'       => '',
            'billing_country'   => 0,
            'shipping_street'   => $student->address,
            'shipping_city'     => '',
            'shipping_state'    => '',
            'shipping_zip'      => '',
            'shipping_country'  => 0,
            'longitude'         => NULL,
            'latitude'          => NULL,
            'default_language'  => '',
            'default_currency'  => 0,
            'show_primary_contact' => 1,
            'stripe_id'         => NULL,
            'registration_confirmed' => 1,
            'addedfrom'         => $student->created_by
        ];
        
        // Insert into tblclients
        $this->db->insert('tblclients', $client_data);
        $client_id = $this->db->insert_id();
        
        if (!$client_id) {
            return [
                'success' => false,
                'message' => 'Failed to create client record'
            ];
        }
        
        // Prepare contact data (primary contact)
        $contact_data = [
            'userid'            => $client_id,
            'is_primary'        => 1,
            'firstname'         => $student->student_name,
            'lastname'          => '', // Can be split if needed
            'email'             => $student->email,
            'phonenumber'       => $student->phone,
            'title'             => 'Student',
            'datecreated'       => date('Y-m-d H:i:s'),
            'password'          => $password_hash,
            'new_pass_key'      => NULL,
            'new_pass_key_requested' => NULL,
            'email_verified_at' => date('Y-m-d H:i:s'),
            'last_ip'           => NULL,
            'last_login'        => NULL,
            'last_password_change' => date('Y-m-d H:i:s'),
            'active'            => 1,
            'profile_image'     => NULL,
            'direction'         => NULL,
            'invoice_emails'    => 1,
            'estimate_emails'   => 1,
            'credit_note_emails' => 1,
            'contract_emails'   => 1,
            'task_emails'       => 1,
            'project_emails'    => 1,
            'ticket_emails'     => 1
        ];
        
        // Insert into tblcontacts
        $this->db->insert('tblcontacts', $contact_data);
        $contact_id = $this->db->insert_id();
        
        if (!$contact_id) {
            // Rollback: delete client record
            $this->db->delete('tblclients', ['userid' => $client_id]);
            
            return [
                'success' => false,
                'message' => 'Failed to create contact record'
            ];
        }
        
        // Update student record with client_id
        $this->db->where('id', $student_id);
        $this->db->update($this->table_students, [
            'client_id' => $client_id,
            'client_created_at' => date('Y-m-d H:i:s')
        ]);
        
        // Log activity
        log_activity('Client Account Created for Student [Student ID: ' . $student_id . ', Client ID: ' . $client_id . ', Email: ' . $student->email . ']');
        
        // Send email with credentials
        $email_sent = $this->send_client_credentials_email($student, $client_id, $password);
        
        return [
            'success'       => true,
            'message'       => 'Client account created successfully',
            'client_id'     => $client_id,
            'contact_id'    => $contact_id,
            'password'      => $password, // Return for logging/testing (don't show to user)
            'email_sent'    => $email_sent
        ];
    }

    /**
     * Generate secure random password
     * 
     * @param int $length Password length (default 12)
     * @return string Generated password
     */
    private function generate_secure_password($length = 12)
    {
        // Character sets
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%^&*';
        
        // Ensure at least one character from each set
        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];
        
        // Fill remaining length with random characters from all sets
        $all_chars = $uppercase . $lowercase . $numbers . $special;
        for ($i = 4; $i < $length; $i++) {
            $password .= $all_chars[random_int(0, strlen($all_chars) - 1)];
        }
        
        // Shuffle the password to randomize character positions
        return str_shuffle($password);
    }

    /**
     * Send client portal credentials via email
     * 
     * @param object $student Student object
     * @param int $client_id Client ID
     * @param string $password Plain text password
     * @return bool True if email sent successfully
     */
    private function send_client_credentials_email($student, $client_id, $password)
    {
        // Load email library
        $this->load->library('email');
        
        // Get company info
        $company_name = get_option('companyname');
        $portal_url = site_url('authentication/login');
        
        // Prepare email content
        $subject = 'Your Client Portal Login Credentials - ' . $company_name;
        
        $message = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background-color: #f9f9f9; padding: 30px; border: 1px solid #ddd; border-radius: 0 0 5px 5px; }
                .credentials { background-color: #fff; padding: 20px; border-left: 4px solid #4CAF50; margin: 20px 0; }
                .credentials strong { color: #4CAF50; }
                .button { display: inline-block; padding: 12px 30px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #777; font-size: 12px; }
                .warning { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Welcome to Safe Legal</h1>
                </div>
                
                <div class="content">
                    <h2>Hello ' . htmlspecialchars($student->student_name) . ',</h2>
                    
                    <p>Congratulations! Your registration and payment have been processed successfully. We have created a client portal account for you.</p>
                    
                    <p>You can now access your personal dashboard to:</p>
                    <ul>
                        <li>View your course details and progress</li>
                        <li>Access important documents and resources</li>
                        <li>Track your referral earnings</li>
                        <li>Communicate with our support team</li>
                    </ul>
                    
                    <div class="credentials">
                        <h3>Your Login Credentials:</h3>
                        <p><strong>Portal URL:</strong> <a href="' . $portal_url . '">' . $portal_url . '</a></p>
                        <p><strong>Email/Username:</strong> ' . htmlspecialchars($student->email) . '</p>
                        <p><strong>Password:</strong> <code style="background: #f0f0f0; padding: 5px 10px; border-radius: 3px; font-size: 14px;">' . htmlspecialchars($password) . '</code></p>
                        <p><strong>Referral Code:</strong> ' . htmlspecialchars($student->referral_code) . '</p>
                    </div>
                    
                    <div class="warning">
                        <strong>⚠️ Security Notice:</strong> Please change your password after your first login for security purposes.
                    </div>
                    
                    <center>
                        <a href="' . $portal_url . '" class="button">Login to Your Portal</a>
                    </center>
                    
                    <p style="margin-top: 30px;">If you have any questions or need assistance, please don\'t hesitate to contact us.</p>
                    
                
                    <p><strong>Branch:</strong> ' . htmlspecialchars($student->branch_name) . '</p>
                </div>
                
                <div class="footer">
                    <p>This is an automated message. Please do not reply to this email.</p>
                    <p>&copy; ' . date('Y') . ' ' . htmlspecialchars($company_name) . '. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ';
        
        // Configure email
        $this->email->clear();
        $this->email->from(get_option('smtp_email'), $company_name);
        $this->email->to($student->email);
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->set_mailtype('html');
        
        // Send email
        if ($this->email->send()) {
            log_activity('Client Portal Credentials Sent [Email: ' . $student->email . ', Client ID: ' . $client_id . ']');
            return true;
        } else {
            log_activity('Failed to Send Client Portal Credentials [Email: ' . $student->email . ', Error: ' . $this->email->print_debugger() . ']');
            return false;
        }
    }

    /**
     * Check if payment is complete (100%)
     * This should be called before creating client account
     * 
     * @param int $student_id Student ID
     * @return bool True if payment is 100% complete
     */
    public function is_payment_complete($student_id)
    {
        $student = $this->get_student($student_id);
        
        if (!$student) {
            return false;
        }
        
        // Check if payment_status field exists and is 'paid'
        // OR check if payment_percentage is 100
        // Adjust based on your actual payment tracking implementation
        
        if (isset($student->payment_status) && $student->payment_status === 'paid') {
            return true;
        }
        
        if (isset($student->payment_percentage) && $student->payment_percentage >= 100) {
            return true;
        }
        
        // You can also check against item price if applicable
        if (isset($student->amount_paid) && isset($student->total_amount)) {
            return $student->amount_paid >= $student->total_amount;
        }
        
        return false;
    }


/**
 * Add payment transaction
 * 
 * @param array $data Payment data
 * @return int|bool Payment ID or false
 */
public function add_payment($data)
{
    // Generate unique payment ID if not provided
    if (empty($data['payment_id'])) {
        $data['payment_id'] = 'PAY-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
    }
    
    $this->db->insert($this->table_payments, $data);
    $insert_id = $this->db->insert_id();
    
    if ($insert_id) {
        log_activity('Payment Recorded [ID: ' . $insert_id . ', Student ID: ' . $data['student_id'] . ', Amount: ' . $data['amount'] . ']');
        return $insert_id;
    }
    
    return false;
}

/**
 * Get payments by student
 * 
 * @param int $student_id Student ID
 * @return array Array of payment records
 */
public function get_student_payments($student_id)
{
    $this->db->select($this->table_payments . '.*, ' . 
                     db_prefix() . 'staff.firstname as recorded_by_firstname, ' .
                     db_prefix() . 'staff.lastname as recorded_by_lastname');
    $this->db->from($this->table_payments);
    $this->db->join(db_prefix() . 'staff', 
                   db_prefix() . 'staff.staffid = ' . $this->table_payments . '.created_by', 
                   'left');
    $this->db->where($this->table_payments . '.student_id', $student_id);
    $this->db->order_by($this->table_payments . '.payment_date', 'DESC');
    
    return $this->db->get()->result();
}

/**
 * Get total amount paid by student
 * 
 * @param int $student_id Student ID
 * @return float Total amount paid
 */
public function get_total_paid_by_student($student_id)
{
    $this->db->select_sum('amount');
    $this->db->where('student_id', $student_id);
    $this->db->where('payment_status', 'completed');
    $result = $this->db->get($this->table_payments)->row();
    
    return $result ? (float)$result->amount : 0.00;
}

// ==================== PACKAGE ENROLLMENTS ====================

/**
 * Create package enrollment when payment is complete
 * 
 * AUTOMATICALLY calculates end_date based on start_date + duration
 * 
 * @param int $student_id Student ID
 * @param array $enrollment_data Optional enrollment data
 * @return int|bool Enrollment ID or false
 */
public function create_package_enrollment($student_id, $enrollment_data = [])
{
    // Get student details
    $student = $this->get_student($student_id);
    
    if (!$student || empty($student->item_id)) {
        return false;
    }
    
    // Get package/item details
    $item = $this->get_item($student->item_id);
    
    if (!$item) {
        return false;
    }
    
    // Check if enrollment already exists
    $existing = $this->db->get_where($this->table_enrollments, [
        'student_id' => $student_id
    ])->row();
    
    if ($existing) {
        log_activity('Enrollment already exists for Student ID: ' . $student_id);
        return $existing->id;
    }
    
    // Calculate dates
    $enrollment_date = isset($enrollment_data['enrollment_date']) ? $enrollment_data['enrollment_date'] : date('Y-m-d');
    $start_date = isset($enrollment_data['start_date']) ? $enrollment_data['start_date'] : date('Y-m-d');
    
    // Calculate end_date = start_date + duration_months
    $end_date = date('Y-m-d', strtotime($start_date . ' + ' . $item->duration_months . ' months'));
    
    // Prepare enrollment data
    $data = [
        'student_id'            => $student_id,
        'item_id'               => $student->item_id,
        'enrollment_date'       => $enrollment_date,
        'start_date'            => $start_date,
        'end_date'              => $end_date,
        'duration_months'       => $item->duration_months,
        'total_amount'          => $item->total_price,
        'amount_paid'           => isset($student->amount_paid) ? $student->amount_paid : 0.00,
        'payment_status'        => isset($student->payment_status) ? $student->payment_status : 'unpaid',
        'enrollment_status'     => 'active',
        'completion_percentage' => 0.00,
        'notes'                 => 'Auto-created enrollment for package: ' . $item->item_name,
        'created_by'            => isset($enrollment_data['created_by']) ? $enrollment_data['created_by'] : $student->created_by,
        'created_at'            => date('Y-m-d H:i:s')
    ];
    
    // Merge with any additional enrollment data
    if (!empty($enrollment_data)) {
        $data = array_merge($data, $enrollment_data);
    }
    
    $this->db->insert($this->table_enrollments, $data);
    $insert_id = $this->db->insert_id();
    
    if ($insert_id) {
        log_activity('Package Enrollment Created [ID: ' . $insert_id . ', Student ID: ' . $student_id . ', Package: ' . $item->item_name . ', Duration: ' . $item->duration_months . ' months, End Date: ' . $end_date . ']');
        return $insert_id;
    }
    
    return false;
}

/**
 * Get enrollment by student ID
 * 
 * @param int $student_id Student ID
 * @return object|null Enrollment record
 */
public function get_enrollment_by_student($student_id)
{
    $this->db->select($this->table_enrollments . '.*, ' .
                     $this->table_items . '.item_name, ' .
                     $this->table_items . '.item_code, ' .
                     $this->table_students . '.student_name, ' .
                     $this->table_students . '.email');
    $this->db->from($this->table_enrollments);
    $this->db->join($this->table_items, 
                   $this->table_items . '.id = ' . $this->table_enrollments . '.item_id', 
                   'left');
    $this->db->join($this->table_students, 
                   $this->table_students . '.id = ' . $this->table_enrollments . '.student_id', 
                   'left');
    $this->db->where($this->table_enrollments . '.student_id', $student_id);
    $this->db->order_by($this->table_enrollments . '.created_at', 'DESC');
    $this->db->limit(1);
    
    return $this->db->get()->row();
}

/**
 * Get all enrollments with filters
 * 
 * @param array $where Conditions
 * @param int $limit Limit
 * @return array Array of enrollment records
 */
public function get_enrollments($where = [], $limit = null)
{
    $this->db->select($this->table_enrollments . '.*, ' .
                     $this->table_items . '.item_name, ' .
                     $this->table_items . '.item_code, ' .
                     $this->table_students . '.student_name, ' .
                     $this->table_students . '.email, ' .
                     $this->table_branches . '.branch_name');
    $this->db->from($this->table_enrollments);
    $this->db->join($this->table_items, 
                   $this->table_items . '.id = ' . $this->table_enrollments . '.item_id', 
                   'left');
    $this->db->join($this->table_students, 
                   $this->table_students . '.id = ' . $this->table_enrollments . '.student_id', 
                   'left');
    $this->db->join($this->table_branches, 
                   $this->table_branches . '.id = ' . $this->table_students . '.branch_id', 
                   'left');
    
    if (!empty($where)) {
        foreach ($where as $key => $value) {
            if (strpos($key, '.') !== false) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($this->table_enrollments . '.' . $key, $value);
            }
        }
    }
    
    $this->db->order_by($this->table_enrollments . '.created_at', 'DESC');
    
    if ($limit !== null) {
        $this->db->limit($limit);
    }
    
    return $this->db->get()->result();
}

/**
 * Update enrollment (progress, status, etc.)
 * 
 * @param int $enrollment_id Enrollment ID
 * @param array $data Update data
 * @return bool Success status
 */
public function update_enrollment($enrollment_id, $data)
{
    $this->db->where('id', $enrollment_id);
    $this->db->update($this->table_enrollments, $data);
    
    if ($this->db->affected_rows() > 0) {
        log_activity('Enrollment Updated [ID: ' . $enrollment_id . ']');
        return true;
    }
    
    return false;
}

/**
 * Check if enrollment is expired
 * 
 * @param int $enrollment_id Enrollment ID
 * @return bool True if expired
 */
public function is_enrollment_expired($enrollment_id)
{
    $enrollment = $this->db->get_where($this->table_enrollments, ['id' => $enrollment_id])->row();
    
    if (!$enrollment) {
        return false;
    }
    
    $today = new DateTime();
    $end_date = new DateTime($enrollment->end_date);
    
    return $today > $end_date;
}

/**
 * Get enrollments expiring soon (within X days)
 * 
 * @param int $days Days threshold (default 30)
 * @return array Enrollments expiring soon
 */
public function get_expiring_enrollments($days = 30)
{
    $today = date('Y-m-d');
    $future_date = date('Y-m-d', strtotime('+' . $days . ' days'));
    
    $this->db->select($this->table_enrollments . '.*, ' .
                     $this->table_students . '.student_name, ' .
                     $this->table_students . '.email, ' .
                     $this->table_items . '.item_name');
    $this->db->from($this->table_enrollments);
    $this->db->join($this->table_students, 
                   $this->table_students . '.id = ' . $this->table_enrollments . '.student_id', 
                   'left');
    $this->db->join($this->table_items, 
                   $this->table_items . '.id = ' . $this->table_enrollments . '.item_id', 
                   'left');
    $this->db->where($this->table_enrollments . '.end_date >=', $today);
    $this->db->where($this->table_enrollments . '.end_date <=', $future_date);
    $this->db->where($this->table_enrollments . '.enrollment_status', 'active');
    $this->db->order_by($this->table_enrollments . '.end_date', 'ASC');
    
    return $this->db->get()->result();
}

//passport adn unique id 
/**
 * Generate unique ID based on passport number
 * Format: saflg-XXXXX (5 random digits)
 * @param string $passport_number Passport number
 * @return string Generated unique ID
 */
public function generate_unique_id($passport_number = '')
{
    do {
        // Generate 5 random digits
        $random_digits = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $unique_id = 'saflg-' . $random_digits;
        
        // Check if exists
        $exists = $this->db->get_where($this->table_students, ['unique_id' => $unique_id])->row();
    } while ($exists);
    
    return $unique_id;
}
/**
 * Create invoice WITH line items (proper invoice with items table)
 * This creates invoice + items from student's package
 * 
 * @param int $client_id Client ID from tblclients
 * @param float $amount Invoice amount
 * @param int $student_id Student ID for reference
 * @param string $description Invoice description
 * @return array Result with invoice_id and invoice_number
 */
public function create_simple_invoice($client_id, $amount, $student_id, $description = 'Package Enrollment Payment')
{
    try {
        // Check if invoice already exists for this student
        $existing = $this->db->select('id, number, status')
            ->from(db_prefix() . 'invoices')
            ->where('clientid', $client_id)
            ->like('adminnote', 'Student ID: ' . $student_id)
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get()
            ->row();
        
        if ($existing) {
            log_activity('Invoice already exists for Student ID: ' . $student_id . ' [Invoice #' . $existing->number . ']');
            
            return [
                'success' => true,
                'invoice_id' => $existing->id,
                'invoice_number' => $existing->number,
                'existing' => true
            ];
        }
        
        // Get student for additional info
        $student = $this->get_student($student_id);
        
        if (!$student) {
            return [
                'success' => false,
                'message' => 'Student not found'
            ];
        }
        
        // Get item/package info (REQUIRED for line items)
        if (empty($student->item_id)) {
            return [
                'success' => false,
                'message' => 'No package assigned to student. Cannot create invoice without item.'
            ];
        }
        
        $item = $this->get_item($student->item_id);
        
        if (!$item) {
            return [
                'success' => false,
                'message' => 'Package not found'
            ];
        }
        
        // Get next invoice number
        $next_number = get_option('next_invoice_number');
        if (empty($next_number)) {
            $next_number = 1;
        }
        
        // Get invoice prefix
        $prefix = get_option('invoice_prefix');
        if (empty($prefix)) {
            $prefix = 'INV-';
        }
        
        // Format invoice number
        $invoice_number_formatted = $prefix . str_pad($next_number, 6, '0', STR_PAD_LEFT);
        
        // Generate unique hash for invoice
        $hash = app_generate_hash();
        
        // ============================================================
        // PREPARE INVOICE DATA
        // ============================================================
        
        $invoice_data = [
            'clientid'          => $client_id,
            'number'            => $next_number,
            'prefix'            => $prefix,
            'number_format'     => $next_number,
            'date'              => date('Y-m-d'),
            'duedate'           => date('Y-m-d'), // Due today since already paid
            'currency'          => get_base_currency()->id,
            'subtotal'          => $item->base_price,
            'total'             => $item->total_price,
            'adjustment'        => 0,
            'discount_percent'  => 0,
            'discount_total'    => 0,
            'discount_type'     => '',
            'status'            => 1, // 1 = Unpaid (will be marked paid after payment record)
            'hash'              => $hash,
            'addedfrom'         => $student->created_by ?? get_staff_user_id(),
            'datecreated'       => date('Y-m-d H:i:s'),
            'adminnote'         => 'Student ID: ' . $student_id . "\n" .
                                   'Student Name: ' . $student->student_name . "\n" .
                                   'Email: ' . $student->email . "\n" .
                                   'Package: ' . $item->item_name . "\n" .
                                   'Item Code: ' . $item->item_code . "\n" .
                                   'Auto-generated invoice',
            'clientnote'        => $description . "\n" .
                                   'Package: ' . $item->item_name . "\n" .
                                   'Thank you for your payment!',
            'terms'             => get_option('predefined_terms_invoice'),
            'sale_agent'        => $student->created_by ?? 0,
            'billing_street'    => $student->address ?? '',
            'billing_city'      => '',
            'billing_state'     => '',
            'billing_zip'       => '',
            'billing_country'   => 0,
            'shipping_street'   => $student->address ?? '',
            'shipping_city'     => '',
            'shipping_state'    => '',
            'shipping_zip'      => '',
            'shipping_country'  => 0,
            'include_shipping'  => 0,
            'show_shipping_on_invoice' => 0,
            'show_quantity_as'  => 1,
            'project_id'        => 0,
            'recurring'         => 0,
            'recurring_type'    => NULL,
            'custom_recurring'  => 0,
            'cycles'            => 0,
            'total_cycles'      => 0,
            'is_recurring_from' => NULL,
            'subscription_id'   => 0,
            'cancel_overdue_reminders' => 0,
            'allowed_payment_modes' => serialize(['cash', 'bank_transfer']),
        ];
        
        // Insert invoice
        $this->db->insert(db_prefix() . 'invoices', $invoice_data);
        $invoice_id = $this->db->insert_id();
        
        if (!$invoice_id) {
            return [
                'success' => false,
                'message' => 'Failed to create invoice'
            ];
        }
        
        // ============================================================
        // INSERT LINE ITEMS (Package Base Price + GST)
        // ============================================================
        
        // Line Item 1: Package Base Price
        $item_line_1 = [
            'rel_id' => $invoice_id,
            'rel_type' => 'invoice',
            'description' => $item->item_name,
            'long_description' => !empty($item->description) ? $item->description : 'Package enrollment and access for ' . $item->duration_months . ' months',
            'qty' => 1, // Always 1 as per your requirement
            'rate' => $item->base_price,
            'unit' => 'Package',
            'item_order' => 1
        ];
        
        $this->db->insert(db_prefix() . 'itemable', $item_line_1);
        
        // Line Item 2: GST (if applicable)
        if ($item->gst_amount > 0 && $item->gst_percentage > 0) {
            $item_line_2 = [
                'rel_id' => $invoice_id,
                'rel_type' => 'invoice',
                'description' => 'GST (' . $item->gst_percentage . '%)',
                'long_description' => 'Goods and Services Tax',
                'qty' => 1,
                'rate' => $item->gst_amount,
                'unit' => 'Tax',
                'item_order' => 2
            ];
            
            $this->db->insert(db_prefix() . 'itemable', $item_line_2);
        }
        
        // ============================================================
        // INCREMENT INVOICE NUMBER
        // ============================================================
        
        $next_number_new = (int)$next_number + 1;
        update_option('next_invoice_number', $next_number_new);
        
        log_activity('Invoice Created with Line Items [Invoice ID: ' . $invoice_id . ', Number: ' . $invoice_number_formatted . ', Student ID: ' . $student_id . ', Package: ' . $item->item_name . ', Amount: ₹' . number_format($item->total_price, 2) . ']');
        
        return [
            'success' => true,
            'invoice_id' => $invoice_id,
            'invoice_number' => $invoice_number_formatted,
            'invoice_hash' => $hash,
            'existing' => false
        ];
        
    } catch (Exception $e) {
        log_activity('Invoice Creation Error for Student ID ' . $student_id . ': ' . $e->getMessage());
        
        return [
            'success' => false,
            'message' => 'Error creating invoice: ' . $e->getMessage()
        ];
    }
}
/**
 * Record payment for invoice (marks invoice as paid)
 * Uses existing Perfex payment modes from tblpayment_modes
 * 
 * @param int $invoice_id Invoice ID
 * @param float $amount Payment amount
 * @param string $transaction_id Transaction/reference ID
 * @param string $payment_mode Your payment method (cash, bank_transfer, upi, card, online, other)
 * @param array $payment_data Additional payment data (date, notes, etc.)
 * @return bool Success status
 */
public function record_simple_payment($invoice_id, $amount, $transaction_id = '', $payment_mode = 'cash', $payment_data = [])
{
    try {
        // ============================================================
        // GET PAYMENT MODE ID FROM PERFEX'S tblpayment_modes
        // ============================================================
        
        $payment_mode_id = $this->get_payment_mode_id($payment_mode);
        
        if (!$payment_mode_id) {
            log_activity('Payment mode not found for: ' . $payment_mode . '. Using default.');
            // Fallback to first available payment mode
            $default_mode = $this->db->select('id')
                ->from(db_prefix() . 'payment_modes')
                ->where('active', 1)
                ->order_by('id', 'ASC')
                ->limit(1)
                ->get()
                ->row();
            
            $payment_mode_id = $default_mode ? $default_mode->id : 1;
        }
        
        // ============================================================
        // PREPARE PAYMENT RECORD
        // ============================================================
        
        $payment_record = [
            'invoiceid'     => $invoice_id,
            'amount'        => $amount,
            'paymentmode'   => $payment_mode_id, // This is the ID from tblpayment_modes
            'paymentmethod' => '', // Keep empty for offline payments (used for gateway-specific data)
            'date'          => isset($payment_data['payment_date']) ? date('Y-m-d', strtotime($payment_data['payment_date'])) : date('Y-m-d'),
            'daterecorded'  => date('Y-m-d H:i:s'),
            'note'          => $this->get_payment_note($payment_mode, $payment_data),
            'transactionid' => $transaction_id
        ];
        
        // Insert payment record
        $this->db->insert(db_prefix() . 'invoicepaymentrecords', $payment_record);
        $payment_record_id = $this->db->insert_id();
        
        if (!$payment_record_id) {
            return false;
        }
        
        // Update invoice status to Paid (status = 2)
        $this->db->where('id', $invoice_id);
        $this->db->update(db_prefix() . 'invoices', [
            'status' => 2 // 2 = Paid in Perfex
        ]);
        
        log_activity('Payment Recorded [Invoice ID: ' . $invoice_id . ', Payment ID: ' . $payment_record_id . ', Amount: ₹' . number_format($amount, 2) . ', Method: ' . ucfirst($payment_mode) . ' (Mode ID: ' . $payment_mode_id . ')]');
        
        return true;
        
    } catch (Exception $e) {
        log_activity('Payment Recording Error for Invoice ID ' . $invoice_id . ': ' . $e->getMessage());
        return false;
    }
}

/**
 * Get Perfex payment mode ID by your payment method name
 * Maps your enum values to existing Perfex payment modes
 * 
 * @param string $payment_mode Your payment method (cash, bank_transfer, upi, card, online, other)
 * @return int|null Payment mode ID from tblpayment_modes
 */
private function get_payment_mode_id($payment_mode)
{
    // ============================================================
    // MAP YOUR PAYMENT METHODS TO PERFEX PAYMENT MODE NAMES
    // ============================================================
    
    $payment_mode_mapping = [
        'cash'           => 'Cash',
        'bank_transfer'  => 'Bank Transfer',
        'upi'            => 'UPI',
        'card'           => 'Credit Card', // or 'Debit Card'
        'online'         => 'Online Payment',
        'cheque'         => 'Cheque',
        'other'          => 'Other'
    ];
    
    // Get the Perfex payment mode name
    $perfex_mode_name = isset($payment_mode_mapping[$payment_mode]) 
        ? $payment_mode_mapping[$payment_mode] 
        : 'Cash';
    
    // Query tblpayment_modes to get the ID
    $mode = $this->db->select('id, name')
        ->from(db_prefix() . 'payment_modes')
        ->where('active', 1)
        ->group_start()
            ->like('name', $perfex_mode_name, 'both')
            ->or_where('name', $perfex_mode_name)
        ->group_end()
        ->order_by('id', 'ASC')
        ->limit(1)
        ->get()
        ->row();
    
    if ($mode) {
        return $mode->id;
    }
    
    // ============================================================
    // FALLBACK: If exact match not found, try partial match
    // ============================================================
    
    // For 'card', try to find any card-related payment mode
    if ($payment_mode === 'card') {
        $card_mode = $this->db->select('id')
            ->from(db_prefix() . 'payment_modes')
            ->where('active', 1)
            ->group_start()
                ->like('name', 'card', 'both')
                ->or_like('name', 'credit', 'both')
                ->or_like('name', 'debit', 'both')
            ->group_end()
            ->order_by('id', 'ASC')
            ->limit(1)
            ->get()
            ->row();
        
        if ($card_mode) {
            return $card_mode->id;
        }
    }
    
    // For 'upi', try to find UPI or online payment
    if ($payment_mode === 'upi') {
        $upi_mode = $this->db->select('id')
            ->from(db_prefix() . 'payment_modes')
            ->where('active', 1)
            ->group_start()
                ->like('name', 'upi', 'both')
                ->or_like('name', 'online', 'both')
            ->group_end()
            ->order_by('id', 'ASC')
            ->limit(1)
            ->get()
            ->row();
        
        if ($upi_mode) {
            return $upi_mode->id;
        }
    }
    
    // For 'bank_transfer', try to find bank-related payment mode
    if ($payment_mode === 'bank_transfer') {
        $bank_mode = $this->db->select('id')
            ->from(db_prefix() . 'payment_modes')
            ->where('active', 1)
            ->group_start()
                ->like('name', 'bank', 'both')
                ->or_like('name', 'transfer', 'both')
            ->group_end()
            ->order_by('id', 'ASC')
            ->limit(1)
            ->get()
            ->row();
        
        if ($bank_mode) {
            return $bank_mode->id;
        }
    }
    
    // ============================================================
    // ULTIMATE FALLBACK: Return first active payment mode (usually Cash)
    // ============================================================
    
    $default = $this->db->select('id')
        ->from(db_prefix() . 'payment_modes')
        ->where('active', 1)
        ->order_by('id', 'ASC')
        ->limit(1)
        ->get()
        ->row();
    
    return $default ? $default->id : null;
}

/**
 * Generate payment note based on payment method
 * 
 * @param string $payment_mode Payment method
 * @param array $payment_data Additional payment data
 * @return string Payment note
 */
private function get_payment_note($payment_mode, $payment_data = [])
{
    $note_parts = [];
    
    // Add base note
    $note_parts[] = 'Payment recorded by admin';
    
    // Add payment method specific note
    switch ($payment_mode) {
        case 'cash':
            $note_parts[] = 'Payment Method: Cash';
            break;
        case 'bank_transfer':
            $note_parts[] = 'Payment Method: Bank Transfer';
            break;
        case 'upi':
            $note_parts[] = 'Payment Method: UPI';
            break;
        case 'card':
            $note_parts[] = 'Payment Method: Card Payment';
            break;
        case 'online':
            $note_parts[] = 'Payment Method: Online Payment';
            break;
        case 'cheque':
            $note_parts[] = 'Payment Method: Cheque';
            break;
        case 'other':
            $note_parts[] = 'Payment Method: Other';
            break;
        default:
            $note_parts[] = 'Payment Method: ' . ucfirst(str_replace('_', ' ', $payment_mode));
    }
    
    // Add custom notes if provided
    if (isset($payment_data['payment_notes']) && !empty($payment_data['payment_notes'])) {
        $note_parts[] = $payment_data['payment_notes'];
    }
    
    return implode(' | ', $note_parts);
}

/**
 * Create invoice and record payment in one call
 * This is the main method to use when student payment is complete
 * 
 * @param int $student_id Student ID
 * @return array Result with success status, invoice_id, and invoice_number
 */
public function create_student_invoice($student_id)
{
    $student = $this->get_student($student_id);
    
    if (!$student) {
        return [
            'success' => false,
            'message' => 'Student not found'
        ];
    }
    
    // Validate student has client account
    if (empty($student->client_id)) {
        return [
            'success' => false,
            'message' => 'Student must have a client account. Please create client account first.'
        ];
    }
    
    // Validate payment amount
    if (empty($student->amount_paid) || $student->amount_paid <= 0) {
        return [
            'success' => false,
            'message' => 'No payment amount recorded for this student'
        ];
    }
    
    // Validate student has package/item
    if (empty($student->item_id)) {
        return [
            'success' => false,
            'message' => 'No package assigned to student. Cannot create invoice.'
        ];
    }
    
    // ============================================================
    // STEP 1: CREATE INVOICE WITH LINE ITEMS
    // ============================================================
    
    $invoice_result = $this->create_simple_invoice(
        $student->client_id,
        $student->amount_paid,
        $student_id,
        'Package Enrollment Payment'
    );
    
    if (!$invoice_result['success']) {
        return $invoice_result;
    }
    
    // If invoice already exists, just return it
    if (isset($invoice_result['existing']) && $invoice_result['existing'] === true) {
        return $invoice_result;
    }
    
    // ============================================================
    // STEP 2: GET PAYMENT METHOD FROM LATEST PAYMENT RECORD
    // ============================================================
    
    // Get the most recent payment record for this student
    $this->db->select('payment_method, transaction_reference, payment_date, payment_notes');
    $this->db->from($this->table_payments);
    $this->db->where('student_id', $student_id);
    $this->db->order_by('payment_date', 'DESC');
    $this->db->limit(1);
    $latest_payment = $this->db->get()->row();
    
    $payment_method = 'cash'; // Default
    $transaction_ref = '';
    $payment_date = date('Y-m-d');
    $payment_notes = '';
    
    if ($latest_payment) {
        $payment_method = $latest_payment->payment_method;
        $transaction_ref = $latest_payment->transaction_reference ?? '';
        $payment_date = $latest_payment->payment_date ?? date('Y-m-d');
        $payment_notes = $latest_payment->payment_notes ?? '';
    }
    
    // ============================================================
    // STEP 3: RECORD PAYMENT (MARK INVOICE AS PAID)
    // ============================================================
    
    $payment_recorded = $this->record_simple_payment(
        $invoice_result['invoice_id'],
        $student->amount_paid,
        $transaction_ref,
        $payment_method,
        [
            'payment_date' => $payment_date,
            'payment_notes' => $payment_notes
        ]
    );
    
    if (!$payment_recorded) {
        return [
            'success' => false,
            'message' => 'Invoice created but payment recording failed'
        ];
    }
    
    return [
        'success' => true,
        'message' => 'Invoice created with line items and marked as paid',
        'invoice_id' => $invoice_result['invoice_id'],
        'invoice_number' => $invoice_result['invoice_number'],
        'payment_method' => ucfirst(str_replace('_', ' ', $payment_method)),
        'existing' => false
    ];
}

// ==================== PARTNER DOCUMENTS (LONGBLOB STORAGE) ====================
public function add_document($data)
{
    // Insert with binary data
    $this->db->insert(db_prefix() . 'sls_partner_documents', $data);
    return $this->db->insert_id();
}

public function get_document($id)
{
    return $this->db->where('id', $id)->get(db_prefix() . 'sls_partner_documents')->row();
}

public function get_branch_documents($branch_id)
{
    return $this->db->where('branch_id', $branch_id)
                    ->order_by('uploaded_at', 'DESC')
                    ->get(db_prefix() . 'sls_partner_documents')
                    ->result();
}

public function delete_document($id)
{
    return $this->db->where('id', $id)->delete(db_prefix() . 'sls_partner_documents');
}
/**
 * Get all documents for a branch (without binary data)
 * @param int $branch_id Branch ID
 * @return array
 */
public function get_partner_documents($branch_id)
{
    $this->db->select($this->table_partner_docs . '.id, ' .
                     $this->table_partner_docs . '.branch_id, ' .
                     $this->table_partner_docs . '.file_name, ' .
                     $this->table_partner_docs . '.file_size, ' .
                     $this->table_partner_docs . '.file_type, ' .
                     $this->table_partner_docs . '.document_type, ' .
                     $this->table_partner_docs . '.description, ' .
                     $this->table_partner_docs . '.uploaded_by, ' .
                     $this->table_partner_docs . '.uploaded_at, ' .
                     $this->table_partner_docs . '.is_verified, ' .
                     $this->table_partner_docs . '.verified_by, ' .
                     $this->table_partner_docs . '.verified_at, ' .
                     'uploader.firstname as uploaded_by_firstname, ' .
                     'uploader.lastname as uploaded_by_lastname, ' .
                     'verifier.firstname as verified_by_firstname, ' .
                     'verifier.lastname as verified_by_lastname');
    $this->db->from($this->table_partner_docs);
    $this->db->join(db_prefix() . 'staff as uploader', 
                   'uploader.staffid = ' . $this->table_partner_docs . '.uploaded_by', 
                   'left');
    $this->db->join(db_prefix() . 'staff as verifier', 
                   'verifier.staffid = ' . $this->table_partner_docs . '.verified_by', 
                   'left');
    $this->db->where($this->table_partner_docs . '.branch_id', $branch_id);
    $this->db->order_by($this->table_partner_docs . '.uploaded_at', 'DESC');
    
    return $this->db->get()->result();
}

/**
 * Get single document WITH binary data
 * @param int $id Document ID
 * @return object|null
 */
public function get_partner_document($id)
{
    return $this->db->get_where($this->table_partner_docs, ['id' => $id])->row();
}

/**
 * Add partner document with binary data
 * @param array $data Document data (must include file_data as binary)
 * @return int|bool Insert ID or false
 */
public function add_partner_document($data)
{
    $this->db->insert($this->table_partner_docs, $data);
    $insert_id = $this->db->insert_id();
    
    if ($insert_id) {
        log_activity('Partner Document Uploaded [ID: ' . $insert_id . ', Branch ID: ' . $data['branch_id'] . ', File: ' . $data['file_name'] . ']');
        return $insert_id;
    }
    
    return false;
}

/**
 * Delete partner document
 * @param int $id Document ID
 * @return bool
 */
public function delete_partner_document($id)
{
    $doc = $this->get_partner_document($id);
    
    if (!$doc) {
        return false;
    }
    
    // Delete from database (no file on disk to delete)
    $this->db->where('id', $id);
    $this->db->delete($this->table_partner_docs);
    
    if ($this->db->affected_rows() > 0) {
        log_activity('Partner Document Deleted [ID: ' . $id . ', File: ' . $doc->file_name . ']');
        return true;
    }
    
    return false;
}

/**
 * Verify document
 * @param int $id Document ID
 * @param int $staff_id Verifier staff ID
 * @return bool
 */
public function verify_partner_document($id, $staff_id)
{
    $this->db->where('id', $id);
    $this->db->update($this->table_partner_docs, [
        'is_verified' => 1,
        'verified_by' => $staff_id,
        'verified_at' => date('Y-m-d H:i:s')
    ]);
    
    if ($this->db->affected_rows() > 0) {
        log_activity('Partner Document Verified [ID: ' . $id . ']');
        return true;
    }
    
    return false;
}

// student side upload docuemtn 
/**
 * Upload student document
 */
public function upload_student_document($student_id)
{
    // DEBUG: Log all POST data
    log_message('debug', 'POST data received: ' . print_r($_POST, true));
    log_message('debug', 'FILES data received: ' . print_r($_FILES, true));
    
    // Check if file was uploaded
    if (!isset($_FILES['document_file']) || $_FILES['document_file']['error'] != 0) {
        set_alert('danger', 'File upload failed. Please try again.');
        return false;
    }
    
    $file = $_FILES['document_file'];
    
    // Validate file type
    $allowed_types = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed_types)) {
        set_alert('danger', 'Invalid file type. Allowed: PDF, DOC, DOCX, JPG, PNG');
        return false;
    }
    
    // Validate file size (10MB max)
    if ($file['size'] > 10485760) {
        set_alert('danger', 'File size exceeds 10MB limit');
        return false;
    }
    
    // Read file content as binary
    $file_data = file_get_contents($file['tmp_name']);
    
    // DEBUG: Check what document_type value we're getting
    $document_type_value = $this->input->post('document_type');
    log_message('debug', 'Document type value: ' . var_export($document_type_value, true));
    
    // FIX: Handle NULL or empty document_type
    if (empty($document_type_value)) {
        $document_type_value = 'Other'; // Default value if not provided
        log_message('warning', 'Document type was empty, using default: Other');
    }
    
    // Prepare data for insertion with proper escaping
    $data = [
        'student_id' => (int)$student_id,
        'file_name' => $file['name'],
        'file_data' => $file_data,
        'file_size' => (int)$file['size'],
        'file_type' => $file['type'] ?: NULL,
        'document_type' => $document_type_value ?: NULL,
        'description' => $this->input->post('description') ?: NULL,
        'uploaded_by' => (int)get_staff_user_id(),
        'uploaded_at' => date('Y-m-d H:i:s')
    ];
    
    // DEBUG: Log the data array before insert
    log_message('debug', 'Data prepared for insert: ' . print_r($data, true));
    
    // DEBUG: Check table name
    $table_name = db_prefix() . 'sls_student_documents';
    log_message('debug', 'Table name: ' . $table_name);
    
    // TRY-CATCH for better error handling
    try {
        if ($this->db->insert($table_name, $data)) {
            set_alert('success', 'Document uploaded successfully');
            
            // Log activity
            if (function_exists('log_activity')) {
                log_activity('Student Document Uploaded [Student ID: ' . $student_id . ', File: ' . $file['name'] . ']');
            }
            
            return true;
        } else {
            // Get the actual database error
            $error = $this->db->error();
            log_message('error', 'Database insert failed: ' . print_r($error, true));
            set_alert('danger', 'Database error: ' . $error['message']);
            return false;
        }
    } catch (Exception $e) {
        log_message('error', 'Exception during insert: ' . $e->getMessage());
        set_alert('danger', 'Failed to upload: ' . $e->getMessage());
        return false;
    }
}

public function get_student_documents($student_id)
{
    $this->db->select('d.*, CONCAT(s.firstname, " ", s.lastname) as uploaded_by_name');
    $this->db->from(db_prefix() . 'sls_student_documents d');
    $this->db->join(db_prefix() . 'staff s', 's.staffid = d.uploaded_by', 'left');
    $this->db->where('d.student_id', $student_id);
    $this->db->order_by('d.uploaded_at', 'DESC');
    
    return $this->db->get()->result();
}

public function get_student_document($id, $student_id = null)
{
    $this->db->where('id', $id);
    
    if ($student_id) {
        $this->db->where('student_id', $student_id);
    }
    
    return $this->db->get(db_prefix() . 'sls_student_documents')->row();
}

public function delete_student_document($id)
{
    return $this->db->delete(db_prefix() . 'sls_student_documents', ['id' => $id]);
}

public function verify_student_document($id, $staff_id)
{
    $data = [
        'is_verified' => 1,
        'verified_by' => $staff_id,
        'verified_at' => date('Y-m-d H:i:s')
    ];
    
    return $this->db->update(db_prefix() . 'sls_student_documents', $data, ['id' => $id]);
}

}