<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * SafeLegalSolutions Client Model
 * Handles database operations for public registration
 * 
 * IMPORTANT: Class name MUST have _model suffix to avoid conflict with controller
 * 
 * File: modules/safelegalsolutions/models/Safelegalsolutions_client_model.php
 */
class Safelegalsolutions_client_model extends App_Model
{
    private $table_students = 'tblsls_students';
    private $table_branches = 'tblsls_branches';
    
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get default branch (Head Office)
     * 
     * @return object|null Branch object or null if not found
     */
    public function get_default_branch()
    {
        $this->db->select('
            b.*,
            c.name as category_name,
            CONCAT(s.firstname, " ", s.lastname) as manager_name,
            s.firstname as manager_firstname,
            s.lastname as manager_lastname
        ');
        $this->db->from($this->table_branches . ' b');
        $this->db->join('tblsls_branch_categories c', 'c.id = b.category_id', 'left');
        $this->db->join('tblstaff s', 's.staffid = b.nodal_partner_manager_id', 'left');
        $this->db->where('b.is_default', 1);
        $this->db->where('b.is_active', 1);
        $this->db->limit(1);
        
        return $this->db->get()->row();
    }

    /**
     * Get branch by registration token
     * 
     * @param string $token Registration token
     * @return object|null Branch object or null if not found
     */
    public function get_branch_by_token($token)
    {
        if (empty($token)) {
            return null;
        }
        
        $this->db->select('
            b.*,
            c.name as category_name,
            CONCAT(s.firstname, " ", s.lastname) as manager_name,
            s.firstname as manager_firstname,
            s.lastname as manager_lastname
        ');
        $this->db->from($this->table_branches . ' b');
        $this->db->join('tblsls_branch_categories c', 'c.id = b.category_id', 'left');
        $this->db->join('tblstaff s', 's.staffid = b.nodal_partner_manager_id', 'left');
        $this->db->where('b.registration_token', $token);
        $this->db->limit(1);
        
        return $this->db->get()->row();
    }

    /**
     * Get branch by ID
     * 
     * @param int $branch_id Branch ID
     * @return object|null Branch object or null if not found
     */
    public function get_branch($branch_id)
    {
        if (empty($branch_id)) {
            return null;
        }
        
        $this->db->select('
            b.*,
            c.name as category_name,
            CONCAT(s.firstname, " ", s.lastname) as manager_name,
            s.firstname as manager_firstname,
            s.lastname as manager_lastname
        ');
        $this->db->from($this->table_branches . ' b');
        $this->db->join('tblsls_branch_categories c', 'c.id = b.category_id', 'left');
        $this->db->join('tblstaff s', 's.staffid = b.nodal_partner_manager_id', 'left');
        $this->db->where('b.id', $branch_id);
        $this->db->limit(1);
        
        return $this->db->get()->row();
    }

    /**
     * Get student by email
     * Used for duplicate email check during registration
     * 
     * @param string $email Student email
     * @return object|null Student object or null if not found
     */
    public function get_student_by_email($email)
    {
        if (empty($email)) {
            return null;
        }
        
        $this->db->where('email', trim(strtolower($email)));
        $this->db->limit(1);
        
        return $this->db->get($this->table_students)->row();
    }

    /**
     * Get student by ID
     * 
     * @param int $student_id Student ID
     * @return object|null Student object or null if not found
     */
    public function get_student($student_id)
    {
        if (empty($student_id)) {
            return null;
        }
        
        $this->db->where('id', $student_id);
        $this->db->limit(1);
        
        return $this->db->get($this->table_students)->row();
    }

 /**
 * Add new student
 * 
 * @param array $data Student data
 * @return int|bool Inserted ID on success, false on failure
 */
public function add_student($data)
{
    // ============================================================
    // SET DEFAULT VALUES - CRITICAL FIX FOR NULL STATUS
    // ============================================================
    if (!isset($data['status']) || empty($data['status'])) {
        $data['status'] = 'draft';
    }
    
    if (!isset($data['is_locked']) || $data['is_locked'] === '') {
        $data['is_locked'] = 0;
    }
    
    if (!isset($data['profile_completion']) || $data['profile_completion'] === '') {
        $data['profile_completion'] = 0;
    }
    
    // Add timestamps
    $data['created_at'] = date('Y-m-d H:i:s');
    $data['updated_at'] = date('Y-m-d H:i:s');
    
    // Log for debugging
    log_activity('SafeLegal Client Model - Creating student [Name: ' . $data['student_name'] . ', Email: ' . $data['email'] . ', Status: ' . $data['status'] . ', Locked: ' . $data['is_locked'] . ', Completion: ' . $data['profile_completion'] . ']');
    
    // Insert into database
    $this->db->insert($this->table_students, $data);
    
    if ($this->db->affected_rows() > 0) {
        $insert_id = $this->db->insert_id();
        log_activity('SafeLegal Client Model - âœ" Student created successfully [ID: ' . $insert_id . ', Status: ' . $data['status'] . ']');
        return $insert_id;
    }
    
    log_activity('SafeLegal Client Model - âœ— Failed to create student');
    return false;
}
    /**
     * Generate unique referral code
     * Format: SLS-XXXXXX (6 random alphanumeric characters)
     * 
     * @return string Unique referral code
     */
    public function generate_referral_code()
    {
        $max_attempts = 10;
        $attempts = 0;
        
        do {
            // Generate random 6-character code
            $code = 'SLS-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
            
            // Check if code already exists
            $this->db->where('referral_code', $code);
            $existing = $this->db->get($this->table_students)->row();
            
            if (!$existing) {
                return $code;
            }
            
            $attempts++;
        } while ($attempts < $max_attempts);
        
        // Fallback: use timestamp-based code
        return 'SLS-' . strtoupper(substr(md5(time() . mt_rand()), 0, 6));
    }
/**
     * Generate unique ID based on passport number
     * Format: saflg-XXXXX (5 random digits)
     * 
     * @param string $passport_number Passport number (optional)
     * @return string Generated unique ID
     */
    public function generate_unique_id($passport_number = '')
    {
        $max_attempts = 10;
        $attempts = 0;
        
        do {
            // Generate 5 random digits
            $random_digits = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $unique_id = 'saflg-' . $random_digits;
            
            // Check if exists
            $this->db->where('unique_id', $unique_id);
            $existing = $this->db->get($this->table_students)->row();
            
            if (!$existing) {
                return $unique_id;
            }
            
            $attempts++;
        } while ($attempts < $max_attempts);
        
        // Fallback: use timestamp-based unique ID
        return 'saflg-' . strtoupper(substr(md5(time() . mt_rand()), 0, 5));
    }
    /**
     * Calculate profile completion percentage
     * Based on required fields being filled
     * 
     * @param array $data Student data
     * @return int Profile completion percentage (0-100)
     */
    public function calculate_profile_completion($data)
    {
        // Required fields for registration (basic info)
        $required_fields = [
            'student_name',
            'email',
            'phone',
            'address',
            'date_of_birth',
            'passport_number'
        ];
        
        $filled_count = 0;
        $total_fields = count($required_fields);
        
        foreach ($required_fields as $field) {
            if (isset($data[$field]) && !empty(trim($data[$field]))) {
                $filled_count++;
            }
        }
        
        // Calculate percentage
        if ($total_fields > 0) {
            return (int) round(($filled_count / $total_fields) * 100);
        }
        
        return 0;
    }

    /**
     * Send registration confirmation email (optional)
     * 
     * @param int $student_id Student ID
     * @return bool True on success, false on failure
     */
    public function send_registration_email($student_id)
    {
        // Get student details
        $student = $this->get_student($student_id);
        
        if (!$student) {
            return false;
        }
        
        // Get branch details
        $branch = $this->get_branch($student->branch_id);
        
        if (!$branch) {
            return false;
        }
        
        // Prepare email data
        $to = $student->email;
        $subject = 'Registration Successful - Safe Legal';
        
        $message = '<html><body>';
        $message .= '<h2>Welcome to Safe Legal</h2>';
        $message .= '<p>Dear ' . htmlspecialchars($student->student_name) . ',</p>';
        $message .= '<p>Your registration has been received successfully.</p>';
        $message .= '<p><strong>Registration Details:</strong></p>';
        $message .= '<ul>';
        $message .= '<li>Student Name: ' . htmlspecialchars($student->student_name) . '</li>';
        $message .= '<li>Email: ' . htmlspecialchars($student->email) . '</li>';
        $message .= '<li>Phone: ' . htmlspecialchars($student->phone) . '</li>';
        $message .= '<li>Course Applied: ' . htmlspecialchars($student->course_applied) . '</li>';
        $message .= '<li>Branch: ' . htmlspecialchars($branch->branch_name) . '</li>';
        $message .= '<li>Your Referral Code: <strong>' . htmlspecialchars($student->referral_code) . '</strong></li>';
        $message .= '</ul>';
        $message .= '<p>Our team will review your application and contact you shortly.</p>';
        $message .= '<p>Thank you for choosing Safe Legal</p>';
        $message .= '<p>Best regards,<br>Safe Legal Team</p>';
        $message .= '</body></html>';
        
        // Load email library
        $this->load->library('email');
        
        // Configure email
        $this->email->initialize([
            'protocol'  => 'smtp',
            'smtp_host' => get_option('smtp_host'),
            'smtp_user' => get_option('smtp_username'),
            'smtp_pass' => get_option('smtp_password'),
            'smtp_port' => get_option('smtp_port'),
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'wordwrap'  => TRUE
        ]);
        
        $this->email->from(get_option('smtp_email'), get_option('companyname'));
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($message);
        
        // Send email
        if ($this->email->send()) {
            return true;
        } else {
            // Log error
            log_message('error', 'Failed to send registration email to: ' . $to);
            return false;
        }
    }

    /**
     * Update student record
     * 
     * @param int $student_id Student ID
     * @param array $data Update data
     * @return bool True on success, false on failure
     */
    public function update_student($student_id, $data)
    {
        if (empty($student_id)) {
            return false;
        }
        
        // Add update timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->where('id', $student_id);
        $this->db->update($this->table_students, $data);
        
        return $this->db->affected_rows() > 0;
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
            $data['payment_id'] = 'PAY-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        }
        
        $this->db->insert('tblsls_payments', $data);
        $insert_id = $this->db->insert_id();
        
        if ($insert_id) {
            log_activity('Payment Recorded [ID: ' . $insert_id . ', Student ID: ' . $data['student_id'] . ', Amount: ' . $data['amount'] . ']');
            return $insert_id;
        }
        
        return false;
    }
    /**
     * Create package enrollment when payment is complete
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
        $this->db->where('student_id', $student_id);
        $existing = $this->db->get('tblsls_package_enrollments')->row();
        
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
        
        $this->db->insert('tblsls_package_enrollments', $data);
        $insert_id = $this->db->insert_id();
        
        if ($insert_id) {
            log_activity('Package Enrollment Created [ID: ' . $insert_id . ', Student ID: ' . $student_id . ', Package: ' . $item->item_name . ', Duration: ' . $item->duration_months . ' months, End Date: ' . $end_date . ']');
            return $insert_id;
        }
        
        return false;
    }
    /**
     * Check if referral code exists
     * 
     * @param string $code Referral code
     * @return bool True if exists, false otherwise
     */
    public function referral_code_exists($code)
    {
        $this->db->where('referral_code', $code);
        $count = $this->db->count_all_results($this->table_students);
        
        return $count > 0;
    }

    /**
     * Get student count by branch
     * 
     * @param int $branch_id Branch ID
     * @return int Student count
     */
    public function count_students_by_branch($branch_id)
    {
        $this->db->where('branch_id', $branch_id);
        return $this->db->count_all_results($this->table_students);
    }

    /**
     * Get recent students by branch
     * 
     * @param int $branch_id Branch ID
     * @param int $limit Number of records to return
     * @return array Array of student objects
     */
    public function get_recent_students_by_branch($branch_id, $limit = 10)
    {
        $this->db->select('*');
        $this->db->from($this->table_students);
        $this->db->where('branch_id', $branch_id);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result();
    }

    /**
 * Get all active items/packages for selection
 * 
 * @return array Array of item objects
 */
public function get_active_items()
{
    $this->db->select('*');
    $this->db->from('tblsls_items');
    $this->db->where('is_active', 1);
    $this->db->order_by('display_order', 'ASC');
    $this->db->order_by('item_name', 'ASC');
    
    return $this->db->get()->result();
}

/**
 * Get item by ID
 * 
 * @param int $item_id Item ID
 * @return object|null Item object or null if not found
 */
public function get_item($item_id)
{
    if (empty($item_id)) {
        return null;
    }
    
    $this->db->where('id', $item_id);
    $this->db->where('is_active', 1);
    $this->db->limit(1);
    
    return $this->db->get('tblsls_items')->row();
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
        
        // Get branch info for email
        $branch = $this->get_branch($student->branch_id);
        
        // Generate random secure password
        $password = $this->generate_secure_password();
        $password_hash = app_hash_password($password);
        
        // Prepare client data
        $client_data = [
            'company'           => $student->student_name,
            'vat'               => '',
            'phonenumber'       => $student->phone,
            'country'           => 0,
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
            'lastname'          => '',
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
        $email_sent = $this->send_client_credentials_email($student, $branch, $client_id, $password);
        
        return [
            'success'       => true,
            'message'       => 'Client account created successfully',
            'client_id'     => $client_id,
            'contact_id'    => $contact_id,
            'password'      => $password,
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
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%^&*';
        
        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];
        
        $all_chars = $uppercase . $lowercase . $numbers . $special;
        for ($i = 4; $i < $length; $i++) {
            $password .= $all_chars[random_int(0, strlen($all_chars) - 1)];
        }
        
        return str_shuffle($password);
    }

    /**
     * Send client portal credentials via email
     * 
     * @param object $student Student object
     * @param object $branch Branch object
     * @param int $client_id Client ID
     * @param string $password Plain text password
     * @return bool True if email sent successfully
     */
    private function send_client_credentials_email($student, $branch, $client_id, $password)
    {
        $this->load->library('email');
        
        $company_name = get_option('companyname');
        $portal_url = site_url('authentication/login');
        
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
                    <h1>Welcome to  Safe Legal</h1>
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
                    
                    <p><strong>Course Applied:</strong> ' . htmlspecialchars($student->course_applied) . '</p>
                    <p><strong>Branch:</strong> ' . htmlspecialchars($branch->branch_name) . '</p>
                </div>
                
                <div class="footer">
                    <p>This is an automated message. Please do not reply to this email.</p>
                    <p>&copy; ' . date('Y') . ' ' . htmlspecialchars($company_name) . '. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ';
        
        $this->email->clear();
        $this->email->from(get_option('smtp_email'), $company_name);
        $this->email->to($student->email);
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->set_mailtype('html');
        
        if ($this->email->send()) {
            log_activity('Client Portal Credentials Sent [Email: ' . $student->email . ', Client ID: ' . $client_id . ']');
            return true;
        } else {
            log_activity('Failed to Send Client Portal Credentials [Email: ' . $student->email . ']');
            return false;
        }
    }

    /**
     * Check if payment is complete (100%)
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
        
        if (isset($student->payment_status) && $student->payment_status === 'paid') {
            return true;
        }
        
        if (isset($student->payment_percentage) && $student->payment_percentage >= 100) {
            return true;
        }
        
        if (isset($student->amount_paid) && isset($student->total_amount)) {
            return $student->amount_paid >= $student->total_amount;
        }
        
        return false;
    }


    /**
 * Get student by client_id (for client portal access)
 * 
 * @param int $client_id Client ID from tblclients
 * @return object|null Student object or null if not found
 */
public function get_student_by_client_id($client_id)
{
    if (empty($client_id)) {
        return null;
    }
    
    $this->db->where('client_id', $client_id);
    $this->db->limit(1);
    
    return $this->db->get($this->table_students)->row();
}


// client enrolment id 
/**
 * Get enrollment by student ID
 * 
 * @param int $student_id Student ID
 * @return object|null Enrollment record
 */
public function get_enrollment_by_student($student_id)
{
    $this->db->select('e.*, i.item_name, i.item_code');
    $this->db->from('tblsls_package_enrollments e');
    $this->db->join('tblsls_items i', 'i.id = e.item_id', 'left');
    $this->db->where('e.student_id', $student_id);
    $this->db->order_by('e.created_at', 'DESC');
    $this->db->limit(1);
    
    return $this->db->get()->row();
}


}