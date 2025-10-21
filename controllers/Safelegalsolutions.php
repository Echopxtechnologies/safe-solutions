<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * SafeLegalSolutions Controller
 * Handles all module operations for branches, candidates, dashboard, AND public registration
 * Version: 1.6 - With Staff Creation in Branch Form
 */
class Safelegalsolutions extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        
        // Load model
        $this->load->model('safelegalsolutions_model');
        
        // CRITICAL: Allow public access to register method without authentication
        $segment2 = $this->uri->segment(2);
        if ($segment2 === 'register') {
            return;
        }
    }

    /**
     * Default index - redirects to dashboard
     */
    public function index()
    {
        redirect(admin_url('safelegalsolutions/dashboard'));
    }

    /**
     * PUBLIC REGISTRATION METHOD - NO AUTHENTICATION REQUIRED
     */
    public function register($token = '')
    {
        $branch = null;
        
        if (!empty($token)) {
            $branch = $this->safelegalsolutions_model->get_branch_by_token($token);
            
            if (!$branch) {
                $data['error'] = 'Invalid or expired registration link. Please contact your branch manager.';
                $data['title'] = 'Invalid Link';
                $this->load->view('safelegalsolutions/public_error', $data);
                return;
            }
            
            if ($branch->is_active != 1) {
                $data['error'] = 'This registration link is currently inactive. Please contact your branch manager.';
                $data['title'] = 'Link Inactive';
                $this->load->view('safelegalsolutions/public_error', $data);
                return;
            }
        } else {
            $branch = $this->safelegalsolutions_model->get_default_branch();
            
            if (!$branch) {
                $data['error'] = 'No default registration branch available. Please use a specific registration link.';
                $data['title'] = 'Registration Unavailable';
                $this->load->view('safelegalsolutions/public_error', $data);
                return;
            }
            
            $token = $branch->registration_token;
        }
        
        // Handle form submission
        if ($this->input->post()) {
            $post_data = $this->input->post();
            
            $post_data['branch_id'] = $branch->id;
            $post_data['nodal_partner_manager_id'] = $branch->nodal_partner_manager_id;
            $post_data['created_by'] = $branch->nodal_partner_manager_id;
            $post_data['referral_code'] = $this->safelegalsolutions_model->generate_referral_code();
            $post_data['status'] = 'draft';
            
            // Validate required fields
            $required = ['student_name', 'email', 'phone', 'address', 'date_of_birth'];
            $missing = [];
            
            foreach ($required as $field) {
                if (empty($post_data[$field])) {
                    $missing[] = ucwords(str_replace('_', ' ', $field));
                }
            }
            
            if (!empty($missing)) {
                $data['error'] = 'Please fill in all required fields: ' . implode(', ', $missing);
                $data['branch'] = $branch;
                $data['token'] = $token;
                $data['title'] = 'Candidate Registration';
                $data['form_data'] = $post_data;
                $this->load->view('safelegalsolutions/public_register', $data);
                return;
            }
            
            if (!filter_var($post_data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['error'] = 'Please enter a valid email address.';
                $data['branch'] = $branch;
                $data['token'] = $token;
                $data['title'] = 'Candidate Registration';
                $data['form_data'] = $post_data;
                $this->load->view('safelegalsolutions/public_register', $data);
                return;
            }
            
            $existing = $this->safelegalsolutions_model->get_student_by_email($post_data['email']);
            if ($existing) {
                $data['error'] = 'This email address is already registered. Please use a different email.';
                $data['branch'] = $branch;
                $data['token'] = $token;
                $data['title'] = 'Candidate Registration';
                $data['form_data'] = $post_data;
                $this->load->view('safelegalsolutions/public_register', $data);
                return;
            }
            
            $insert_id = $this->safelegalsolutions_model->add_student($post_data);
            
            if ($insert_id) {
                $success_data['success'] = true;
                $success_data['student_name'] = $post_data['student_name'];
                $success_data['branch'] = $branch;
                $success_data['referral_code'] = $post_data['referral_code'];
                $success_data['title'] = 'Registration Successful';
                $this->load->view('safelegalsolutions/public_success', $success_data);
                return;
            } else {
                $data['error'] = 'Failed to register. Please try again or contact support.';
                $data['branch'] = $branch;
                $data['token'] = $token;
                $data['title'] = 'Candidate Registration';
                $data['form_data'] = $post_data;
                $this->load->view('safelegalsolutions/public_register', $data);
                return;
            }
        }
        
        $data['branch'] = $branch;
        $data['token'] = $token;
        $data['title'] = 'Candidate Registration';
        $data['form_data'] = [];
        $this->load->view('safelegalsolutions/public_register', $data);
    }

    /**
     * Dashboard View
     * Shows statistics based on user role
     */
    public function dashboard()
    {
        // Check permission
        if (!is_npm() && !is_sls_manager_or_admin()) {
            access_denied('safelegalsolutions');
        }

        $data['title'] = 'Dashboard';
        $staff_id = get_staff_user_id();

        if (is_sls_manager_or_admin()) {
            // Admin/Manager sees all statistics
            $data['stats'] = [
                'total_branches' => $this->safelegalsolutions_model->count_branches(),
                'total_students' => $this->safelegalsolutions_model->count_students(),
                'pending_reviews' => $this->safelegalsolutions_model->count_students_by_status('pending_review'),
                'locked_profiles' => $this->safelegalsolutions_model->count_students_by_locked(1),
                'total_earnings' => $this->safelegalsolutions_model->get_total_earnings(),
                'change_requests' => $this->safelegalsolutions_model->count_change_requests(['status' => 'pending']),
            ];
            
            $data['recent_students'] = $this->safelegalsolutions_model->get_students_with_packages([], 10);
            $data['recent_change_requests'] = $this->safelegalsolutions_model->get_change_requests(['status' => 'pending'], 10);
            
        } else {
            // Nodal Partner Manager sees only their statistics
            $branch = $this->safelegalsolutions_model->get_branch_by_manager($staff_id);
            
            if (!$branch) {
                set_alert('warning', 'No branch assigned to your account');
            }
            
            $data['branch'] = $branch;
            $data['stats'] = [
                'my_students' => $this->safelegalsolutions_model->count_students_by_manager($staff_id),
                'pending_reviews' => $this->safelegalsolutions_model->count_students_by_manager_status($staff_id, 'pending_review'),
                'locked_profiles' => $this->safelegalsolutions_model->count_students_by_manager_locked($staff_id, 1),
                'my_earnings' => $this->safelegalsolutions_model->get_manager_earnings($staff_id),
                'avg_completion' => $this->safelegalsolutions_model->get_avg_profile_completion($staff_id),
            ];
            
            $data['recent_students'] = $this->safelegalsolutions_model->get_students_with_packages(
                ['nodal_partner_manager_id' => $staff_id], 
                10
            );
        }

        $data['bodyclass'] = 'safelegalsolutions-dashboard';
        $this->load->view('dashboard', $data);
    }

    /**
     * My Branch View (Admin/Manager Only)
     */
    public function my_branch()
    {
        if (!is_sls_manager_or_admin()) {
            access_denied('safelegalsolutions');
        }

        $data['title'] = 'My Branch';
        
        $default_branch = $this->safelegalsolutions_model->get_default_branch();
        
        if (!$default_branch) {
            set_alert('danger', 'No default branch found. Please create a default branch first.');
            redirect(admin_url('safelegalsolutions/branches'));
            return;
        }
        
        $data['branch'] = $default_branch;
        
        $data['stats'] = [
            'total_students' => $this->safelegalsolutions_model->count_students(['branch_id' => $default_branch->id]),
            'pending_reviews' => $this->safelegalsolutions_model->count_students([
                'branch_id' => $default_branch->id,
                'status' => 'pending_review'
            ]),
            'approved_students' => $this->safelegalsolutions_model->count_students([
                'branch_id' => $default_branch->id,
                'status' => 'approved'
            ]),
            'locked_profiles' => $this->safelegalsolutions_model->count_students([
                'branch_id' => $default_branch->id,
                'is_locked' => 1
            ]),
            'total_earnings' => $this->safelegalsolutions_model->get_branch_earnings($default_branch->id),
            'avg_completion' => $this->safelegalsolutions_model->get_branch_avg_completion($default_branch->id),
        ];
        
        $data['recent_students'] = $this->safelegalsolutions_model->get_students_with_packages(
            ['branch_id' => $default_branch->id], 
            10
        );
        
        $data['all_students'] = $this->safelegalsolutions_model->get_students_with_packages(
            ['branch_id' => $default_branch->id]
        );
        
        $data['bodyclass'] = 'safelegalsolutions-my-branch';
        $this->load->view('my_branch', $data);
    }

    /**
     * Branches Management (Admin/Manager Only)
     */
    public function branches()
    {
        if (!is_sls_manager_or_admin()) {
            access_denied('safelegalsolutions');
        }

        $data['title'] = 'Branches';
        $data['bodyclass'] = 'safelegalsolutions-branches';
        
        $this->load->view('branches', $data);
    }

    /**
     * Get branches table data (AJAX - DataTables)
     */
    public function branches_table()
    {
        if (!is_sls_manager_or_admin()) {
            ajax_access_denied();
        }

        $this->app->get_table_data(module_views_path('safelegalsolutions', 'tables/branches_table'));
    }

 /**
 * Add or Edit Branch (with Automatic Staff Creation & Role Assignment)
 * 
 * WORKFLOW:
 * 1. Check if creating new staff (create_new_staff checkbox)
 * 2. Validate staff email doesn't exist
 * 3. Create staff account with generated password
 * 4. Assign to "SLS Nodal Partner Manager" role (auto-created if not exists)
 * 5. Set specific permissions for SafeLegalSolutions module only
 * 6. Send welcome email with credentials
 * 7. Create/Update branch with the new staff as manager
 */
/**
 * Add or Edit Branch (with Automatic Staff Creation & Role Assignment)
 * FIXED VERSION with better error handling
 */
public function branch($id = '')
{
    if (!is_sls_manager_or_admin()) {
        access_denied('safelegalsolutions');
    }

    if ($this->input->post()) {
        $post_data = $this->input->post();
        
        // ============================================================
        // STEP 1: HANDLE STAFF CREATION (if enabled)
        // ============================================================
        if (isset($post_data['create_new_staff']) && $post_data['create_new_staff'] == '1') {
            
            // Validate staff email
            if (empty($post_data['staff_email'])) {
                set_alert('danger', 'Staff email is required when creating new staff');
                redirect(admin_url('safelegalsolutions/branch'));
                return;
            }
            
            // Check if email already exists - FIXED QUERY
            try {
                $this->db->select('staffid');
                $this->db->from(db_prefix() . 'staff');
                $this->db->where('email', $post_data['staff_email']);
                $existing_staff = $this->db->get()->row();
                
                if ($existing_staff) {
                    set_alert('danger', 'Email already exists in the system');
                    redirect(admin_url('safelegalsolutions/branch'));
                    return;
                }
            } catch (Exception $e) {
                log_activity('Branch Creation Error (Email Check): ' . $e->getMessage());
                set_alert('danger', 'Database error. Please check logs.');
                redirect(admin_url('safelegalsolutions/branch'));
                return;
            }
            
            // Validate required staff fields
            if (empty($post_data['staff_firstname']) || empty($post_data['staff_lastname'])) {
                set_alert('danger', 'Staff first name and last name are required');
                redirect(admin_url('safelegalsolutions/branch'));
                return;
            }
            
            // Load staff model
            $this->load->model('staff_model');
            
            // Get or create SLS Nodal Partner Manager role
            $role_id = $this->get_or_create_sls_role();
            
            if (!$role_id) {
                set_alert('danger', 'Failed to create/find SLS role. Please check logs.');
                redirect(admin_url('safelegalsolutions/branch'));
                return;
            }
            
            // Generate secure password
            $password = $this->generate_secure_password(12);
            
            // Prepare staff data
            $staff_data = [
                'email'         => $post_data['staff_email'],
                'firstname'     => $post_data['staff_firstname'],
                'lastname'      => $post_data['staff_lastname'],
                'phonenumber'   => isset($post_data['staff_phonenumber']) ? $post_data['staff_phonenumber'] : '',
                'password'      => $password,
                'active'        => 1,
                'admin'         => 0,
                'role'          => $role_id,
                'is_not_staff'  => 0,
                'hourly_rate'   => 0,
                'default_language' => '',
                'direction'     => '',
                'media_path_slug' => ''
            ];
            
            // Create staff account
            try {
                $new_staff_id = $this->staff_model->add($staff_data);
                
                if (!$new_staff_id) {
                    set_alert('danger', 'Failed to create staff account. Please check logs and try again.');
                    redirect(admin_url('safelegalsolutions/branch'));
                    return;
                }
                
                log_activity('New SLS Partner Staff Created [ID: ' . $new_staff_id . ', Email: ' . $post_data['staff_email'] . ']');
                
            } catch (Exception $e) {
                log_activity('Staff Creation Error: ' . $e->getMessage());
                set_alert('danger', 'Error creating staff account: ' . $e->getMessage());
                redirect(admin_url('safelegalsolutions/branch'));
                return;
            }
            
            // ============================================================
            // STEP 2: SET SPECIFIC PERMISSIONS (SafeLegalSolutions only)
            // ============================================================
            
            try {
                // Delete any existing permissions for this staff - FIXED QUERY
                $this->db->where('staffid', $new_staff_id);
                $this->db->delete(db_prefix() . 'staff_permissions');
                
                // Add SafeLegalSolutions permissions
                $permissions = [
                    [
                        'staffid'    => $new_staff_id,
                        'feature'    => 'safelegalsolutions_students',
                        'capability' => 'view'
                    ],
                    [
                        'staffid'    => $new_staff_id,
                        'feature'    => 'safelegalsolutions_students',
                        'capability' => 'create'
                    ],
                    [
                        'staffid'    => $new_staff_id,
                        'feature'    => 'safelegalsolutions_students',
                        'capability' => 'edit'
                    ]
                ];
                
                $this->db->insert_batch(db_prefix() . 'staff_permissions', $permissions);
                
                log_activity('SLS Permissions Set for Staff ID: ' . $new_staff_id);
                
            } catch (Exception $e) {
                log_activity('Permission Setting Error: ' . $e->getMessage());
                // Don't fail the whole process, just log the error
                // The staff account is created, permissions can be set manually
            }
            
            // ============================================================
            // STEP 3: SEND WELCOME EMAIL WITH CREDENTIALS
            // ============================================================
            try {
                $email_sent = $this->send_staff_credentials_email(
                    $post_data['staff_email'],
                    $password,
                    $post_data['staff_firstname']
                );
                
                log_activity('Staff Credentials Email ' . ($email_sent ? 'Sent' : 'Failed') . ' [Staff ID: ' . $new_staff_id . ']');
                
            } catch (Exception $e) {
                log_activity('Email Send Error: ' . $e->getMessage());
                // Don't fail, just log - email can be resent manually
            }
            
            // Update POST data to use new staff ID as manager
            $post_data['nodal_partner_manager_id'] = $new_staff_id;
        }
        
        // ============================================================
        // STEP 4: VALIDATE & PREPARE BRANCH DATA
        // ============================================================
        
        // Whitelist allowed fields
        $branch_data = [];
        $allowed_fields = [
            'category_id',
            'branch_name',
            'branch_code',
            
            // Contact
            'contact_phone',
            'alternative_phone',
            'website_url',
            
            // Address
            'location',
            'address',
            'address_line2',
            'state',
            'pin_code',
            
            // Business
            'years_in_business',
            'avg_students_per_year',
            'gst_number',
            
            // Partnership
            'services_offered',
            'expected_monthly_referrals',
            'preferred_communication',
            'additional_comments',
            
            // Consent
            'terms_accepted',
            'data_consent',
            'marketing_consent',
            
            // Status
            'nodal_partner_manager_id',
            'is_active',
            'is_default'
        ];
        
        foreach ($allowed_fields as $field) {
            if (isset($post_data[$field]) && $post_data[$field] !== '') {
                $branch_data[$field] = $post_data[$field];
            }
        }
        // Handle primary_destinations (multi-select → JSON)
if (isset($post_data['primary_destinations']) && is_array($post_data['primary_destinations'])) {
    $branch_data['primary_destinations'] = json_encode(array_map('intval', $post_data['primary_destinations']));
} else {
    $branch_data['primary_destinations'] = json_encode([]);
}

// Handle consent metadata
if (!empty($branch_data['terms_accepted']) || !empty($branch_data['data_consent'])) {
    if ($id == '') { // Only on new creation
        $branch_data['consent_date'] = date('Y-m-d H:i:s');
        $branch_data['consent_ip'] = $this->input->ip_address();
    }
}

// Ensure consent checkboxes are stored as 0 or 1
$branch_data['terms_accepted'] = isset($post_data['terms_accepted']) ? 1 : 0;
$branch_data['data_consent'] = isset($post_data['data_consent']) ? 1 : 0;
$branch_data['marketing_consent'] = isset($post_data['marketing_consent']) ? 1 : 0;
        // Validate required fields
        if (empty($branch_data['category_id'])) {
            set_alert('danger', 'Category is required');
            redirect(admin_url('safelegalsolutions/branch'));
            return;
        }
        
        if (empty($branch_data['branch_name'])) {
            set_alert('danger', 'Branch name is required');
            redirect(admin_url('safelegalsolutions/branch'));
            return;
        }
        
        if (empty($branch_data['nodal_partner_manager_id'])) {
            set_alert('danger', 'Nodal Partner Manager is required');
            redirect(admin_url('safelegalsolutions/branch'));
            return;
        }
        
        // ============================================================
        // STEP 5: CREATE/UPDATE BRANCH
        // ============================================================
        
        try {
            if ($id == '') {
                // CREATE NEW BRANCH
                $branch_data['created_by'] = get_staff_user_id();
                $branch_data['registration_token'] = bin2hex(random_bytes(32));
                
                $insert_id = $this->safelegalsolutions_model->add_branch($branch_data);
                
                if ($insert_id) {
                    $success_message = 'Branch created successfully';
                    
                    // Add staff creation success message if applicable
                    if (isset($new_staff_id)) {
                        $success_message .= ' and partner account created for ' . $post_data['staff_firstname'] . ' ' . $post_data['staff_lastname'];
                        if (isset($email_sent) && $email_sent) {
                            $success_message .= '. Login credentials sent to ' . $post_data['staff_email'];
                        } else {
                            $success_message .= '. Please manually send login credentials.';
                        }
                    }
                    
                    set_alert('success', $success_message);
                    redirect(admin_url('safelegalsolutions/branches'));
                } else {
                    set_alert('danger', 'Error creating branch. Please check the logs.');
                    redirect(admin_url('safelegalsolutions/branch'));
                }
            } else {
                // UPDATE EXISTING BRANCH
                $success = $this->safelegalsolutions_model->update_branch($id, $branch_data);
                
                if ($success) {
                    set_alert('success', 'Branch updated successfully');
                    redirect(admin_url('safelegalsolutions/branches'));
                } else {
                    set_alert('danger', 'Error updating branch');
                    redirect(admin_url('safelegalsolutions/branch/' . $id));
                }
            }
        } catch (Exception $e) {
            log_activity('Branch Creation/Update Error: ' . $e->getMessage());
            set_alert('danger', 'Database error: ' . $e->getMessage());
            redirect(admin_url('safelegalsolutions/branch'));
        }
    }

    // ============================================================
    // LOAD FORM VIEW (GET REQUEST)
    // ============================================================
    
    if ($id != '') {
        $data['branch'] = $this->safelegalsolutions_model->get_branch($id);
        if (!$data['branch']) {
            show_404();
        }
    }
    // Get all categories
    $all_categories = $this->safelegalsolutions_model->get_all_categories();

    // Check if user is NPM (Nodal Partner Manager)
    $is_npm = has_permission('safelegalsolutions_students', '', 'view') 
            && !has_permission('customers', '', 'view') 
            && !is_sls_manager_or_admin();

    // NPM: exclude "Safe Legal Solutions" category
    // Admin/Manager: show all categories including "Safe Legal Solutions"
    if ($is_npm) {
        $data['categories'] = array_filter($all_categories, function($cat) {
            return $cat->name !== 'Safe Legal Solutions';
        });
    } else {
        $data['categories'] = $all_categories;
    }
    
    $data['staff_members'] = $this->staff_model->get('', ['active' => 1, 'admin !=' => 1]);
    $data['title'] = $id == '' ? 'Add Branch' : 'Edit Branch';
    
    $this->load->view('branch_form', $data);
}

/**
 * Get or Create SLS Nodal Partner Manager Role
 * FIXED VERSION with better error handling
 */
/**
 * Get or Create SLS Nodal Partner Manager Role
 * FIXED: Auto-assign all necessary permissions
 */
private function get_or_create_sls_role()
{
    $role_name = 'SLS Nodal Partner Manager';
    
    try {
        // Check if role exists
        $this->db->select('roleid');
        $this->db->from(db_prefix() . 'roles');
        $this->db->where('name', $role_name);
        $role = $this->db->get()->row();
        
        if ($role) {
            return $role->roleid;
        }
        
        // ============================================================
        // CREATE ROLE WITH PROPER PERMISSIONS
        // ============================================================
        
        // Define permissions for NPM role
        $permissions = [
            // SLS Candidates - Full Access
            'safelegalsolutions_students' => [
                'view' => 1,
                'create' => 1,
                'edit' => 1
                // NO delete permission
            ],
            
            // SLS Branches - View & Edit Own Branch Only
            'safelegalsolutions_branches' => [
                'view' => 1,
                'edit' => 1
                // NO create/delete permission
            ]
        ];
        
        $role_data = [
            'name' => $role_name,
            'permissions' => serialize($permissions)
        ];
        
        $this->db->insert(db_prefix() . 'roles', $role_data);
        $role_id = $this->db->insert_id();
        
        if ($role_id) {
            log_activity('SLS Nodal Partner Manager Role Created with Permissions [Role ID: ' . $role_id . ']');
            return $role_id;
        }
        
        return false;
        
    } catch (Exception $e) {
        log_activity('Role Creation Error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Generate Secure Random Password
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
 * Send Welcome Email with Staff Credentials
 */
private function send_staff_credentials_email($email, $password, $firstname)
{
    $this->load->library('email');
    
    $company_name = get_option('companyname');
    $admin_url = admin_url();
    
    $subject = 'Welcome to Safe Legal - Your Partner Account Credentials';
    
    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #2563eb; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { background-color: #f9f9f9; padding: 30px; border: 1px solid #ddd; border-radius: 0 0 5px 5px; }
            .credentials { background-color: #fff; padding: 20px; border-left: 4px solid #2563eb; margin: 20px 0; }
            .button { display: inline-block; padding: 12px 30px; background-color: #2563eb; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .warning { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Welcome to Safe Legal</h1>
            </div>
            
            <div class="content">
                <h2>Hello ' . htmlspecialchars($firstname) . ',</h2>
                
                <p>Your partner account has been created as an <strong>SLS Nodal Partner Manager</strong>.</p>
                
                <div class="credentials">
                    <h3>Your Login Credentials:</h3>
                    <p><strong>Admin URL:</strong> <a href="' . $admin_url . '">' . $admin_url . '</a></p>
                    <p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>
                    <p><strong>Password:</strong> <code style="background: #f0f0f0; padding: 5px 10px; border-radius: 3px; font-family: monospace;">' . htmlspecialchars($password) . '</code></p>
                </div>
                
                <div class="warning">
                    <strong>⚠️ Security:</strong> Please change your password after your first login.
                </div>
                
                <center>
                    <a href="' . $admin_url . '" class="button">Login to Admin Panel</a>
                </center>
            </div>
        </div>
    </body>
    </html>
    ';
    
    $this->email->clear();
    $this->email->from(get_option('smtp_email'), $company_name);
    $this->email->to($email);
    $this->email->subject($subject);
    $this->email->message($message);
    $this->email->set_mailtype('html');
    
    try {
        return $this->email->send();
    } catch (Exception $e) {
        log_activity('Email Send Exception: ' . $e->getMessage());
        return false;
    }
}
    /**
     * Delete Branch
     */
    public function delete_branch($id)
    {
        if (!is_sls_manager_or_admin()) {
            ajax_access_denied();
        }

        if (!has_permission('safelegalsolutions_branches', '', 'delete') && !is_sls_manager_or_admin()) {
            access_denied('safelegalsolutions');
        }

        $response = $this->safelegalsolutions_model->delete_branch($id);
        
        if ($response) {
            set_alert('success', 'Branch deleted successfully');
        } else {
            set_alert('danger', 'Error deleting branch. Branch may have candidates assigned or is the default branch.');
        }
        
        redirect(admin_url('safelegalsolutions/branches'));
    }

    /**
     * Candidates Management (URL: /students for compatibility)
     */
    public function students()
    {
        if (!is_npm() && !is_sls_manager_or_admin()) {
            access_denied('safelegalsolutions');
        }

        $data['title'] = 'Candidates';
        $staff_id = get_staff_user_id();
        
        if (!is_sls_manager_or_admin()) {
            $branch = $this->safelegalsolutions_model->get_branch_by_manager($staff_id);
            if (!$branch) {
                set_alert('warning', 'No branch assigned to your account');
            }
            $data['branch'] = $branch;
        }
        
        $data['branches'] = is_sls_manager_or_admin() ? 
            $this->safelegalsolutions_model->get_all_branches_simple() : 
            [$this->safelegalsolutions_model->get_branch_by_manager($staff_id)];
            
        $data['bodyclass'] = 'safelegalsolutions-students';
        
        $this->load->view('students', $data);
    }

    /**
     * Get candidates table data (AJAX - DataTables)
     */
    public function students_table()
    {
        if (!is_npm() && !is_sls_manager_or_admin()) {
            ajax_access_denied();
        }

        $this->app->get_table_data(module_views_path('safelegalsolutions', 'tables/students_table'));
    }

    /**
     * Add or Edit Candidate (URL: /student for compatibility)
     */
    public function student($id = '')
    {
        if (!is_npm() && 
            !is_sls_manager_or_admin()) {
            access_denied('safelegalsolutions');
        }

        $staff_id = get_staff_user_id();

        if ($this->input->post()) {
            $data = $this->input->post();
            
            if ($id != '') {
                $student = $this->safelegalsolutions_model->get_student($id);
                
                if ($student && $student->is_locked == 1 && !is_sls_manager_or_admin()) {
                    set_alert('info', 'Profile is locked. Change request created for admin review.');
                    redirect(admin_url('safelegalsolutions/students'));
                }
            }
            
            if ($id == '') {
    // ============================================================
    // STEP 1: SEPARATE PAYMENT DATA FROM STUDENT DATA
    // ============================================================
    
    // Extract payment fields (for payments table)
    $payment_method = isset($data['payment_method']) ? $data['payment_method'] : 'cash';
    $transaction_reference = isset($data['transaction_reference']) ? $data['transaction_reference'] : '';
    $payment_date = isset($data['payment_date']) ? $data['payment_date'] : date('Y-m-d H:i:s');
    $payment_notes = isset($data['payment_notes']) ? $data['payment_notes'] : '';
    
    // Remove payment fields from student data
    unset($data['payment_method']);
    unset($data['transaction_reference']);
    unset($data['payment_date']);
    unset($data['payment_notes']);
    
    // ============================================================
    // STEP 2: PREPARE STUDENT DATA
    // ============================================================
    
    $data['created_by'] = $staff_id;
    
    if (is_sls_manager_or_admin()) {
        if (!isset($data['branch_id']) || empty($data['branch_id'])) {
            $default_branch = $this->safelegalsolutions_model->get_default_branch();
            
            if ($default_branch) {
                $data['branch_id'] = $default_branch->id;
                $data['nodal_partner_manager_id'] = $default_branch->nodal_partner_manager_id;
            } else {
                set_alert('danger', 'No default branch found. Please create a default branch first.');
                redirect(admin_url('safelegalsolutions/student'));
                return;
            }
        } else {
            $branch = $this->safelegalsolutions_model->get_branch($data['branch_id']);
            if ($branch && $branch->nodal_partner_manager_id) {
                $data['nodal_partner_manager_id'] = $branch->nodal_partner_manager_id;
            } else {
                set_alert('danger', 'Selected branch has no manager assigned');
                redirect(admin_url('safelegalsolutions/student'));
                return;
            }
        }
    } else {
        $manager_branch = $this->safelegalsolutions_model->get_branch_by_manager($staff_id);
        
        if (!$manager_branch) {
            set_alert('danger', 'You do not have a branch assigned. Please contact administrator.');
            redirect(admin_url('safelegalsolutions/students'));
            return;
        }
        
        $data['branch_id'] = $manager_branch->id;
        $data['nodal_partner_manager_id'] = $staff_id;
    }
    
  // ============================================================
    // ENSURE PACKAGE (ITEM) IS PROPERLY SET
    // ============================================================

    // Validate item_id exists and is active
    if (!empty($data['item_id'])) {
        $item = $this->safelegalsolutions_model->get_item($data['item_id']);
        
        if ($item && $item->is_active == 1) {
            // Set total_amount from package price
            $data['total_amount'] = $item->total_price;
            
            log_activity('Package selected [Item ID: ' . $data['item_id'] . ', Package: ' . $item->item_name . ', Price: ' . $item->total_price . ']');
        } else {
            // Invalid or inactive package
            set_alert('danger', 'Selected package is not available. Please select a valid package.');
            redirect(admin_url('safelegalsolutions/student'));
            return;
        }
    } else {
        // No package selected - this is required
        set_alert('danger', 'Please select a package before saving the candidate.');
        redirect(admin_url('safelegalsolutions/student'));
        return;
    }

    // Debug log to verify data before insert
    log_activity('Student data before insert - item_id: ' . (isset($data['item_id']) ? $data['item_id'] : 'NULL') . ', total_amount: ' . (isset($data['total_amount']) ? $data['total_amount'] : '0.00'));

    // Calculate payment percentage
    $amount_paid = isset($data['amount_paid']) ? floatval($data['amount_paid']) : 0;
    $total_amount = isset($data['total_amount']) ? floatval($data['total_amount']) : 0;
    
    if ($total_amount > 0) {
        $data['payment_percentage'] = ($amount_paid / $total_amount) * 100;
    } else {
        $data['payment_percentage'] = 0;
    }
    
    $data['referral_code'] = $this->safelegalsolutions_model->generate_referral_code();
        // Generate unique_id if passport_number is provided
    if (!empty($data['passport_number'])) {
        $data['unique_id'] = $this->safelegalsolutions_model->generate_unique_id($data['passport_number']);
    } else {
        // Generate unique_id even without passport
        $data['unique_id'] = $this->safelegalsolutions_model->generate_unique_id();
    }
    
    // ============================================================
    // STEP 3: INSERT STUDENT (with clean data)
    // ============================================================
    
    $insert_id = $this->safelegalsolutions_model->add_student($data);
    
    if ($insert_id) {
        log_activity('Student Created [ID: ' . $insert_id . ', Name: ' . $data['student_name'] . ']');
        
        // ============================================================
        // STEP 4: CREATE PAYMENT RECORD (if amount > 0)
        // ============================================================
        
        if ($amount_paid > 0) {
            $payment_data = [
                'student_id' => $insert_id,
                'payment_method' => $payment_method,
                'amount' => $amount_paid,
                'payment_date' => $payment_date,
                'transaction_reference' => $transaction_reference,
                'payment_notes' => $payment_notes,
                'payment_status' => 'completed',
                'created_by' => $staff_id
            ];
            
            $payment_id = $this->safelegalsolutions_model->add_payment($payment_data);
            
            if ($payment_id) {
                log_activity('Payment Recorded [Student ID: ' . $insert_id . ', Amount: ' . $amount_paid . ']');
            }
        }
        
        // ============================================================
        // STEP 5: CREATE ENROLLMENT & CLIENT ACCOUNT (if payment complete)
        // ============================================================
        
        $payment_complete = $this->safelegalsolutions_model->is_payment_complete($insert_id);
        
        if ($payment_complete) {
            // Create package enrollment
            $enrollment_id = $this->safelegalsolutions_model->create_package_enrollment($insert_id, [
                'created_by' => $staff_id
            ]);
            
            if ($enrollment_id) {
                log_activity('Package Enrollment Created [Student ID: ' . $insert_id . ', Enrollment ID: ' . $enrollment_id . ']');
            }
            
            // Create client account
            $client_result = $this->safelegalsolutions_model->create_client_account_for_student($insert_id);
            
            if ($client_result['success']) {
                 // ============================================================
            // CREATE INVOICE AFTER CLIENT ACCOUNT CREATION
            // ============================================================
            $invoice_result = $this->safelegalsolutions_model->create_student_invoice($insert_id);
            
            if ($invoice_result['success'] && !$invoice_result['existing']) {
                set_alert('success', 'Candidate added successfully! Client created, credentials sent to ' . $data['email'] . '. Invoice #' . $invoice_result['invoice_number'] . ' created.');
            } else {
                set_alert('success', 'Candidate added successfully! Client portal account created and credentials sent to ' . $data['email']);
            }
    
            } else {
                set_alert('warning', 'Candidate added but client account creation failed: ' . $client_result['message']);
            }
        } else {
            set_alert('success', 'Candidate added successfully');
        }
        
        redirect(admin_url('safelegalsolutions/students'));
    } else {
        set_alert('danger', 'Error adding candidate');
    }
        } else {
    // ============================================================
    // UPDATE EXISTING STUDENT
    // ============================================================
    
    $old_student = $this->safelegalsolutions_model->get_student($id);
    $old_payment_complete = $this->safelegalsolutions_model->is_payment_complete($id);
    
    // Extract payment fields before updating student
    $payment_method = isset($data['payment_method']) ? $data['payment_method'] : 'cash';
    $transaction_reference = isset($data['transaction_reference']) ? $data['transaction_reference'] : '';
    $payment_date = isset($data['payment_date']) ? $data['payment_date'] : date('Y-m-d H:i:s');
    $payment_notes = isset($data['payment_notes']) ? $data['payment_notes'] : '';
    
    // Get amount before removing fields
    $amount_paid = isset($data['amount_paid']) ? floatval($data['amount_paid']) : 0;
    $old_amount = floatval($old_student->amount_paid);
    
    // Remove payment fields from student update
    unset($data['payment_method']);
    unset($data['transaction_reference']);
    unset($data['payment_date']);
    unset($data['payment_notes']);
    
    // Calculate payment percentage
    if (isset($data['amount_paid']) && isset($data['total_amount'])) {
        $total_amount = floatval($data['total_amount']);
        if ($total_amount > 0) {
            $data['payment_percentage'] = ($amount_paid / $total_amount) * 100;
        }
    }
    // Regenerate unique_id if passport_number changed
    if (isset($data['passport_number']) && !empty($data['passport_number'])) {
        $old_student = $this->safelegalsolutions_model->get_student($id);
        
        // Only regenerate if passport changed or unique_id doesn't exist
        if (empty($old_student->unique_id) || $data['passport_number'] != $old_student->passport_number) {
            $data['unique_id'] = $this->safelegalsolutions_model->generate_unique_id($data['passport_number']);
        }
    }
    // Update student record
    $success = $this->safelegalsolutions_model->update_student($id, $data);
    
    if ($success) {
        // ============================================================
        // CREATE PAYMENT RECORD (if new payment added)
        // ============================================================
        
        if ($amount_paid > $old_amount) {
            $new_payment_amount = $amount_paid - $old_amount;
            
            $payment_data = [
                'student_id' => $id,
                'payment_method' => $payment_method,
                'amount' => $new_payment_amount,
                'payment_date' => $payment_date,
                'transaction_reference' => $transaction_reference,
                'payment_notes' => $payment_notes,
                'payment_status' => 'completed',
                'created_by' => get_staff_user_id()
            ];
            
            $payment_id = $this->safelegalsolutions_model->add_payment($payment_data);
            
            if ($payment_id) {
                log_activity('Additional Payment Recorded [Student ID: ' . $id . ', Amount: ' . $new_payment_amount . ']');
            }
        }
        
        // ============================================================
        // CHECK IF PAYMENT JUST BECAME COMPLETE
        // ============================================================
        
        $new_payment_complete = $this->safelegalsolutions_model->is_payment_complete($id);
        
        if (!$old_payment_complete && $new_payment_complete) {
            
            // Create enrollment
            $enrollment_id = $this->safelegalsolutions_model->create_package_enrollment($id, [
                'created_by' => get_staff_user_id()
            ]);
            
            if ($enrollment_id) {
                log_activity('Package Enrollment Created [Student ID: ' . $id . ', Enrollment ID: ' . $enrollment_id . ']');
            }
            
            // Create client account if not exists
            if (empty($old_student->client_id)) {
                $client_result = $this->safelegalsolutions_model->create_client_account_for_student($id);
                
                if ($client_result['success']) {
                    $invoice_result = $this->safelegalsolutions_model->create_student_invoice($id);
            
            if ($invoice_result['success'] && !$invoice_result['existing']) {
                set_alert('success', 'Payment completed! Client created, credentials sent to ' . $old_student->email . '. Invoice #' . $invoice_result['invoice_number'] . ' created and marked as paid.');
            } else {
                set_alert('success', 'Payment completed! Client account created and credentials sent to ' . $old_student->email);
            }
        
                } else {
                    set_alert('warning', 'Payment completed but client account creation failed: ' . $client_result['message']);
                }
            } else {
                $invoice_result = $this->safelegalsolutions_model->create_student_invoice($id);
                if ($invoice_result['success'] && !$invoice_result['existing']) {
            set_alert('success', 'Candidate updated successfully. Payment complete! Invoice #' . $invoice_result['invoice_number'] . ' created.');
        } else {
            set_alert('success', 'Candidate updated successfully. Payment is now complete!');
        }

            }
        } else {
            set_alert('success', 'Candidate updated successfully');
        }
        
        redirect(admin_url('safelegalsolutions/students'));
    } else {
        set_alert('danger', 'Error updating candidate');
        redirect(admin_url('safelegalsolutions/student/' . $id));
    }
       
                if ($success) {
                    set_alert('success', 'Candidate updated successfully');
                    
                    $new_payment_complete = $this->safelegalsolutions_model->is_payment_complete($id);
                    
                    if (!$old_payment_complete && $new_payment_complete) {
                        if (empty($old_student->client_id)) {
                            $client_result = $this->safelegalsolutions_model->create_client_account_for_student($id);
                            
                            if ($client_result['success']) {
                               $invoice_result = $this->safelegalsolutions_model->create_student_invoice($id);
            
            if ($invoice_result['success'] && !$invoice_result['existing']) {
                set_alert('success', 'Payment completed! Client created, credentials sent to ' . $old_student->email . '. Invoice #' . $invoice_result['invoice_number'] . ' created.');
            } else {
                set_alert('success', 'Payment completed! Client account created and credentials sent to ' . $old_student->email);
            }
                            } else {
                                set_alert('warning', 'Payment completed but client account creation failed: ' . $client_result['message']);
                            }
                        }
                    }
                    
                    redirect(admin_url('safelegalsolutions/students'));
                } else {
                    set_alert('danger', 'Error updating candidate');
                }
            }
        }
        
        if ($id != '') {
            $data['student'] = $this->safelegalsolutions_model->get_student($id);
            if (!$data['student']) {
                show_404();
            }
            
            if (!is_sls_manager_or_admin() && $data['student']->nodal_partner_manager_id != $staff_id) {
                access_denied('safelegalsolutions');
            }
        }

        if (is_sls_manager_or_admin()) {
            $data['branches'] = $this->safelegalsolutions_model->get_all_branches_simple();
            $data['default_branch'] = $this->safelegalsolutions_model->get_default_branch();
        } else {
            $branch = $this->safelegalsolutions_model->get_branch_by_manager($staff_id);
            
            if (!$branch) {
                set_alert('danger', 'You do not have a branch assigned. Please contact administrator.');
                redirect(admin_url('safelegalsolutions/students'));
                return;
            }
            
            $data['branches'] = [$branch];
        }
            
        $data['title'] = $id == '' ? 'Add Candidate' : 'Edit Candidate';
        
        $this->load->view('student_form', $data);
    }

    public function success()
    {
        $this->load->view('safelegalsolutions/public_success');
    }

    /**
     * Submit candidate for review
     */
    public function submit_for_review($id)
    {
        $student = $this->safelegalsolutions_model->get_student($id);
        
        if (!$student) {
            show_404();
        }
        
        $staff_id = get_staff_user_id();
        
        if (!is_sls_manager_or_admin() && $student->nodal_partner_manager_id != $staff_id) {
            access_denied('safelegalsolutions');
        }
        
        if ($student->profile_completion < 100) {
            set_alert('warning', 'Profile must be 100% complete before submitting for review');
            redirect(admin_url('safelegalsolutions/students'));
        }
        
        $success = $this->safelegalsolutions_model->update_student($id, [
            'status' => 'pending_review'
        ]);
        
        if ($success) {
            set_alert('success', 'Candidate profile submitted for admin review');
        } else {
            set_alert('danger', 'Error submitting candidate profile');
        }
        
        redirect(admin_url('safelegalsolutions/students'));
    }

    /**
     * Approve and Lock Candidate Profile (Admin/Manager only)
     */
    public function approve_student($id)
    {
        if (!is_sls_manager_or_admin()) {
            access_denied('safelegalsolutions');
        }
        
        $staff_id = get_staff_user_id();
        
        $success = $this->safelegalsolutions_model->update_student($id, [
            'status' => 'approved',
            'is_locked' => 1,
            'locked_at' => date('Y-m-d H:i:s'),
            'locked_by' => $staff_id
        ]);
        
        if ($success) {
            set_alert('success', 'Candidate profile approved and locked');
        } else {
            set_alert('danger', 'Error approving candidate profile');
        }
        
        redirect(admin_url('safelegalsolutions/students'));
    }

    /**
     * Delete Candidate
     */
    public function delete_student($id)
    {
        if (!has_permission('safelegalsolutions_students', '', 'delete') && !is_sls_manager_or_admin()) {
            ajax_access_denied();
        }

        $student = $this->safelegalsolutions_model->get_student($id);
        
        if (!$student) {
            show_404();
        }
        
        if (!is_sls_manager_or_admin()) {
            if ($student->nodal_partner_manager_id != get_staff_user_id() || $student->is_locked == 1) {
                access_denied('safelegalsolutions');
            }
        }

        $response = $this->safelegalsolutions_model->delete_student($id);
        
        if ($response) {
            set_alert('success', 'Candidate deleted successfully');
        } else {
            set_alert('danger', 'Error deleting candidate');
        }
        
        redirect(admin_url('safelegalsolutions/students'));
    }

    /**
     * Items/Packages Management (Admin/Manager Only)
     */
    public function items()
    {
        if (!is_sls_manager_or_admin()) {
            access_denied('safelegalsolutions');
        }

        $data['title'] = 'Items / Packages';
        $data['bodyclass'] = 'safelegalsolutions-items';
        
        $this->load->view('items', $data);
    }

    /**
     * Get items table data (AJAX - DataTables)
     */
    public function items_table()
    {
        if (!is_sls_manager_or_admin()) {
            ajax_access_denied();
        }

        $this->app->get_table_data(module_views_path('safelegalsolutions', 'tables/items_table'));
    }

    /**
     * Add or Edit Item
     */
    public function item($id = '')
    {
        if (!is_sls_manager_or_admin()) {
            access_denied('safelegalsolutions');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            
            if (isset($data['features']) && is_array($data['features'])) {
                $data['features'] = array_filter($data['features'], function($value) {
                    return !empty(trim($value));
                });
                $data['features'] = json_encode(array_values($data['features']));
            } else {
                $data['features'] = json_encode([]);
            }
            
            if ($id == '') {
                $data['created_by'] = get_staff_user_id();
                
                $insert_id = $this->safelegalsolutions_model->add_item($data);
                
                if ($insert_id) {
                    set_alert('success', 'Item/Package added successfully');
                    redirect(admin_url('safelegalsolutions/items'));
                } else {
                    set_alert('danger', 'Error adding item/package');
                }
            } else {
                $success = $this->safelegalsolutions_model->update_item($id, $data);
                
                if ($success) {
                    set_alert('success', 'Item/Package updated successfully');
                    redirect(admin_url('safelegalsolutions/items'));
                } else {
                    set_alert('danger', 'Error updating item/package');
                }
            }
        }

        if ($id != '') {
            $data['item'] = $this->safelegalsolutions_model->get_item($id);
            if (!$data['item']) {
                show_404();
            }
            
            if (!empty($data['item']->features)) {
                $data['item']->features_array = json_decode($data['item']->features, true);
            } else {
                $data['item']->features_array = [];
            }
        }

        $data['title'] = $id == '' ? 'Add Item / Package' : 'Edit Item / Package';
        
        $this->load->view('item_form', $data);
    }

    /**
     * Delete Item
     */
    public function delete_item($id)
    {
        if (!is_sls_manager_or_admin()) {
            ajax_access_denied();
        }

        $response = $this->safelegalsolutions_model->delete_item($id);
        
        if ($response) {
            set_alert('success', 'Item/Package deleted successfully');
        } else {
            set_alert('danger', 'Error deleting item/package. Item may be assigned to candidates.');
        }
        
        redirect(admin_url('safelegalsolutions/items'));
    }

    /**
     * AJAX: Calculate GST and Total Price
     */
    public function calculate_item_price()
    {
        if (!is_sls_manager_or_admin()) {
            ajax_access_denied();
        }
        
        $base_price = $this->input->post('base_price');
        $gst_percentage = $this->input->post('gst_percentage');
        
        $gst_amount = ($base_price * $gst_percentage) / 100;
        $total_price = $base_price + $gst_amount;
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'gst_amount' => number_format($gst_amount, 2, '.', ''),
            'total_price' => number_format($total_price, 2, '.', '')
        ]);
    }


// branch creation
/**
 * AJAX: Create new category
 */
public function create_category_ajax()
{
    if (!is_sls_manager_or_admin()) {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        return;
    }
    
    if ($this->input->post()) {
        $name = trim($this->input->post('name'));
        
        // Validation
        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Category name is required']);
            return;
        }
        
        // Check if category already exists
        $this->db->where('name', $name);
        $existing = $this->db->get(db_prefix() . 'sls_branch_categories')->row();
        
        if ($existing) {
            echo json_encode(['success' => false, 'message' => 'Category already exists']);
            return;
        }
        
        // Insert category
        $data = [
            'name' => $name,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->insert(db_prefix() . 'sls_branch_categories', $data);
        $insert_id = $this->db->insert_id();
        
        if ($insert_id) {
            log_activity('New Category Created via AJAX [ID: ' . $insert_id . ', Name: ' . $name . ']');
            
            echo json_encode([
                'success' => true,
                'message' => 'Category created successfully',
                'category' => [
                    'id' => $insert_id,
                    'name' => $name
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create category']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }
}


/**
 * ============================================================
 * CHANGE REQUEST MANAGEMENT METHODS
 * Add these methods to your Safelegalsolutions.php controller
 * ============================================================
 */

/**
 * Submit Change Request (AJAX)
 * URL: /safelegalsolutions/submit_change_request
 */
public function submit_change_request()
{
    // Check if user is logged in
    if (!is_npm() && !is_sls_manager_or_admin()) {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        return;
    }
    
    if ($this->input->post()) {
        $student_id = $this->input->post('student_id');
        $field_name = $this->input->post('field_name');
        $new_value = $this->input->post('new_value');
        $reason = $this->input->post('reason');
        
        // Validation
        if (empty($student_id) || empty($field_name) || empty($new_value) || empty($reason)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            return;
        }
        
        // Get student record
        $student = $this->safelegalsolutions_model->get_student($student_id);
        
        if (!$student) {
            echo json_encode(['success' => false, 'message' => 'Student not found']);
            return;
        }
        
        // Check if profile is locked
        if ($student->is_locked != 1) {
            echo json_encode(['success' => false, 'message' => 'Profile is not locked. You can edit directly.']);
            return;
        }
        
        // Check permission (NPM can only request for their students)
        $staff_id = get_staff_user_id();
        if (!is_sls_manager_or_admin() && $student->nodal_partner_manager_id != $staff_id) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }
        
        // Get old value
        $old_value = '';
        if ($field_name === 'item_id') {
            $old_value = $student->item_id;
        } else if (property_exists($student, $field_name)) {
            $old_value = $student->{$field_name};
        }
        
        // Insert change request
        $request_data = [
            'student_id' => $student_id,
            'requested_by' => $staff_id,
            'field_name' => $field_name,
            'old_value' => $old_value,
            'new_value' => $new_value,
            'reason' => $reason,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        try {
            $this->db->insert(db_prefix() . 'sls_change_requests', $request_data);
            $request_id = $this->db->insert_id();
            
            if ($request_id) {
                log_activity('Change Request Submitted [Request ID: ' . $request_id . ', Student ID: ' . $student_id . ', Field: ' . $field_name . ']');
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Change request submitted successfully. An admin will review it soon.'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to submit change request']);
            }
        } catch (Exception $e) {
            log_activity('Change Request Submission Error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    }
}

/**
 * Change Requests List View (Admin/Manager Only)
 * URL: /safelegalsolutions/change_requests
 */
public function change_requests()
{
    if (!is_sls_manager_or_admin()) {
        access_denied('safelegalsolutions');
    }
    
    $data['title'] = 'Change Requests';
    
    // Get filter parameter
    $filter_student_id = $this->input->get('student_id');
    $filter_status = $this->input->get('status');
    
    // Build query
    $this->db->select(db_prefix() . 'sls_change_requests.*, ' .
                     db_prefix() . 'sls_students.student_name, ' .
                     db_prefix() . 'sls_students.email, ' .
                     'requester.firstname as requester_firstname, ' .
                     'requester.lastname as requester_lastname, ' .
                     'reviewer.firstname as reviewer_firstname, ' .
                     'reviewer.lastname as reviewer_lastname');
    $this->db->from(db_prefix() . 'sls_change_requests');
    $this->db->join(db_prefix() . 'sls_students', 
                   db_prefix() . 'sls_students.id = ' . db_prefix() . 'sls_change_requests.student_id', 
                   'left');
    $this->db->join(db_prefix() . 'staff as requester', 
                   'requester.staffid = ' . db_prefix() . 'sls_change_requests.requested_by', 
                   'left');
    $this->db->join(db_prefix() . 'staff as reviewer', 
                   'reviewer.staffid = ' . db_prefix() . 'sls_change_requests.reviewed_by', 
                   'left');
    
    // Apply filters
    if ($filter_student_id) {
        $this->db->where(db_prefix() . 'sls_change_requests.student_id', $filter_student_id);
    }
    if ($filter_status) {
        $this->db->where(db_prefix() . 'sls_change_requests.status', $filter_status);
    }
    
    $this->db->order_by(db_prefix() . 'sls_change_requests.created_at', 'DESC');
    $data['change_requests'] = $this->db->get()->result();
    
    // Count by status
    $this->db->where('status', 'pending');
    $data['pending_count'] = $this->db->count_all_results(db_prefix() . 'sls_change_requests');
    
    $this->db->where('status', 'approved');
    $data['approved_count'] = $this->db->count_all_results(db_prefix() . 'sls_change_requests');
    
    $this->db->where('status', 'rejected');
    $data['rejected_count'] = $this->db->count_all_results(db_prefix() . 'sls_change_requests');
    
    $data['bodyclass'] = 'safelegalsolutions-change-requests';
    $this->load->view('change_requests', $data);
}

/**
 * Approve Change Request
 * URL: /safelegalsolutions/approve_change_request/{id}
 */
public function approve_change_request($request_id)
{
    if (!is_sls_manager_or_admin()) {
        ajax_access_denied();
    }
    
    try {
        // Get change request
        $this->db->where('id', $request_id);
        $request = $this->db->get(db_prefix() . 'sls_change_requests')->row();
        
        if (!$request) {
            set_alert('danger', 'Change request not found');
            redirect(admin_url('safelegalsolutions/change_requests'));
            return;
        }
        
        if ($request->status != 'pending') {
            set_alert('warning', 'This change request has already been processed');
            redirect(admin_url('safelegalsolutions/change_requests'));
            return;
        }
        
        // Get student
        $student = $this->safelegalsolutions_model->get_student($request->student_id);
        
        if (!$student) {
            set_alert('danger', 'Student not found');
            redirect(admin_url('safelegalsolutions/change_requests'));
            return;
        }
        
        // Update student field
        $update_data = [];
        
        if ($request->field_name === 'item_id') {
            $update_data['item_id'] = $request->new_value;
        } else {
            $update_data[$request->field_name] = $request->new_value;
        }
        
        // Apply the change
        $this->db->where('id', $request->student_id);
        $this->db->update(db_prefix() . 'sls_students', $update_data);
        
        // Update change request status
        $this->db->where('id', $request_id);
        $this->db->update(db_prefix() . 'sls_change_requests', [
            'status' => 'approved',
            'reviewed_by' => get_staff_user_id(),
            'reviewed_at' => date('Y-m-d H:i:s')
        ]);
        
        log_activity('Change Request Approved [Request ID: ' . $request_id . ', Student ID: ' . $request->student_id . ', Field: ' . $request->field_name . ']');
        
        set_alert('success', 'Change request approved and applied successfully');
        
    } catch (Exception $e) {
        log_activity('Change Request Approval Error: ' . $e->getMessage());
        set_alert('danger', 'Error approving change request: ' . $e->getMessage());
    }
    
    redirect(admin_url('safelegalsolutions/change_requests'));
}

/**
 * Reject Change Request
 * URL: /safelegalsolutions/reject_change_request/{id}
 */
public function reject_change_request($request_id)
{
    if (!is_sls_manager_or_admin()) {
        ajax_access_denied();
    }
    
    if ($this->input->post()) {
        $review_notes = $this->input->post('review_notes');
        
        try {
            // Get change request
            $this->db->where('id', $request_id);
            $request = $this->db->get(db_prefix() . 'sls_change_requests')->row();
            
            if (!$request) {
                echo json_encode(['success' => false, 'message' => 'Change request not found']);
                return;
            }
            
            if ($request->status != 'pending') {
                echo json_encode(['success' => false, 'message' => 'This request has already been processed']);
                return;
            }
            
            // Update change request status
            $this->db->where('id', $request_id);
            $this->db->update(db_prefix() . 'sls_change_requests', [
                'status' => 'rejected',
                'reviewed_by' => get_staff_user_id(),
                'reviewed_at' => date('Y-m-d H:i:s'),
                'review_notes' => $review_notes
            ]);
            
            log_activity('Change Request Rejected [Request ID: ' . $request_id . ', Student ID: ' . $request->student_id . ']');
            
            echo json_encode(['success' => true, 'message' => 'Change request rejected']);
            
        } catch (Exception $e) {
            log_activity('Change Request Rejection Error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }
}

/**
 * Delete Change Request
 * URL: /safelegalsolutions/delete_change_request/{id}
 */
public function delete_change_request($request_id)
{
    if (!is_sls_manager_or_admin()) {
        ajax_access_denied();
    }
    
    try {
        $this->db->where('id', $request_id);
        $this->db->delete(db_prefix() . 'sls_change_requests');
        
        log_activity('Change Request Deleted [Request ID: ' . $request_id . ']');
        
        set_alert('success', 'Change request deleted successfully');
    } catch (Exception $e) {
        log_activity('Change Request Deletion Error: ' . $e->getMessage());
        set_alert('danger', 'Error deleting change request');
    }
    
    redirect(admin_url('safelegalsolutions/change_requests'));
}


// documents
/**
 * Upload partner document (AJAX) - Store as LONGBLOB
 */
public function upload_partner_document()
{
    // NO CSRF check needed - Perfex handles it automatically
    
    if (!is_sls_manager_or_admin()) {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        return;
    }
    
    $branch_id = $this->input->post('branch_id');
    $document_type = $this->input->post('document_type');
    $description = $this->input->post('description');
    
    if (empty($branch_id)) {
        echo json_encode(['success' => false, 'message' => 'Branch ID is required']);
        return;
    }
    
    // Check if file uploaded
    if (empty($_FILES['document']['name'])) {
        echo json_encode(['success' => false, 'message' => 'Please select a file']);
        return;
    }
    
    // Validate file
    $allowed_types = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'xls', 'xlsx'];
    $file_ext = pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION);
    
    if (!in_array(strtolower($file_ext), $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'File type not allowed. Allowed: PDF, DOC, DOCX, JPG, PNG, XLS, XLSX']);
        return;
    }
    
    // Max file size: 10MB
    if ($_FILES['document']['size'] > 10485760) {
        echo json_encode(['success' => false, 'message' => 'File size exceeds 10MB limit']);
        return;
    }
    
    // Read file as binary
    $file_data = file_get_contents($_FILES['document']['tmp_name']);
    
    if ($file_data === false) {
        echo json_encode(['success' => false, 'message' => 'Failed to read file']);
        return;
    }
    
    // Save to database as LONGBLOB
    $doc_data = [
        'branch_id' => $branch_id,
        'file_name' => $_FILES['document']['name'],
        'file_data' => $file_data, // Binary data
        'file_size' => $_FILES['document']['size'],
        'file_type' => $_FILES['document']['type'],
        'document_type' => $document_type,
        'description' => $description,
        'uploaded_by' => get_staff_user_id(),
        'uploaded_at' => date('Y-m-d H:i:s')
    ];
    
    $insert_id = $this->safelegalsolutions_model->add_partner_document($doc_data);
    
    if ($insert_id) {
        echo json_encode([
            'success' => true,
            'message' => 'Document uploaded successfully',
            'document_id' => $insert_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save document']);
    }
}

/**
 * Get partner documents (AJAX)
 */
public function get_partner_documents_ajax($branch_id)
{
    if (!is_sls_manager_or_admin()) {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        return;
    }
    
    $documents = $this->safelegalsolutions_model->get_partner_documents($branch_id);
    
    echo json_encode([
        'success' => true,
        'documents' => $documents
    ]);
}

/**
 * Download partner document (stream binary data)
 */
public function download_partner_document($id)
{
    if (!is_sls_manager_or_admin()) {
        access_denied('safelegalsolutions');
    }
    
    $doc = $this->safelegalsolutions_model->get_partner_document($id);
    
    if (!$doc) {
        show_404();
    }
    
    // Set headers for file download
    header('Content-Type: ' . $doc->file_type);
    header('Content-Disposition: attachment; filename="' . $doc->file_name . '"');
    header('Content-Length: ' . $doc->file_size);
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    
    // Output binary data
    echo $doc->file_data;
    exit;
}

/**
 * Delete partner document (AJAX)
 */
public function delete_partner_document($id)
{
    if (!is_sls_manager_or_admin()) {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        return;
    }
    
    $success = $this->safelegalsolutions_model->delete_partner_document($id);
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Document deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete document']);
    }
}

/**
 * Verify partner document (AJAX)
 */
public function verify_partner_document($id)
{
    if (!is_sls_manager_or_admin()) {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        return;
    }
    
    $success = $this->safelegalsolutions_model->verify_partner_document($id, get_staff_user_id());
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Document verified successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to verify document']);
    }
}
}