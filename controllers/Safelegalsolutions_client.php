<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * SafeLegalSolutions Client Controller
 * 
 * PURPOSE:
 * - Handles public student registration (no auth required)
 * - Manages authenticated client portal access
 * 
 * ARCHITECTURE:
 * - Extends ClientsController for client portal integration
 * - Mixed authentication: Public methods + Authenticated methods
 * - Session-based multi-step registration workflow
 * 
 * VERSION: 4.0 - Restructured with comprehensive error handling
 * FILE: modules/safelegalsolutions/controllers/Safelegalsolutions_client.php
 * 
 * CHANGELOG v4.0:
 * - Comprehensive restructuring and organization
 * - Added try-catch error handling throughout
 * - Improved logging and error reporting
 * - Better method grouping and documentation
 * - All existing functionality preserved
 * 
 * @package    SafeLegalSolutions
 * @author     Your Company
 * @copyright  2025
 * @version    4.0
 */
class Safelegalsolutions_client extends ClientsController
{
    // ================================================================
    // CLASS PROPERTIES
    // ================================================================
    
    /**
     * @var array Public methods that don't require authentication
     */
    private $public_methods = ['index', 'register', 'review', 'success'];
    
    /**
     * @var string Error log prefix for consistent logging
     */
    private $log_prefix = 'SafeLegalSolutions Client Controller';

    // ================================================================
    // CONSTRUCTOR & INITIALIZATION
    // ================================================================
    
    /**
     * Constructor - Initialize controller and check authentication
     * 
     * WORKFLOW:
     * 1. Call parent constructor
     * 2. Load required models and libraries
     * 3. Check if current method requires authentication
     * 4. Redirect to login if authentication required and user not logged in
     * 
     * @return void
     */
    public function __construct()
    {
        try {
            parent::__construct();
            
            // Load dependencies
            $this->_load_dependencies();
            
            // Check authentication for protected methods
            $this->_check_authentication();
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Constructor Error: ' . $e->getMessage());
            $this->_show_error('System Error', 'Failed to initialize. Please contact support.');
        }
    }
    
    /**
     * Load required models and libraries
     * 
     * @return void
     * @throws Exception If loading fails
     */
    private function _load_dependencies()
    {
        try {
            $this->load->model('safelegalsolutions/safelegalsolutions_client_model');
            $this->load->library('form_validation');
            $this->load->library('session');
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Dependency Loading Error: ' . $e->getMessage());
            throw new Exception('Failed to load required dependencies');
        }
    }
    
    /**
     * Check if authentication is required for current method
     * 
     * @return void
     */
    private function _check_authentication()
    {
        try {
            $method = $this->router->fetch_method();
            
            // If NOT a public method AND user not logged in
            if (!in_array($method, $this->public_methods) && !is_client_logged_in()) {
                redirect(site_url('authentication/login'));
            }
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Authentication Check Error: ' . $e->getMessage());
            redirect(site_url('authentication/login'));
        }
    }

    // ================================================================
    // PUBLIC REGISTRATION METHODS (NO AUTHENTICATION REQUIRED)
    // ================================================================
    
    /**
     * Index - Default landing page
     * 
     * Redirects to registration with optional token
     * 
     * @param string $token Optional branch registration token
     * @return void
     */
    public function index($token = '')
    {
        $this->register($token);
    }
    
    /**
     * STEP 1: Registration Form
     * 
     * WORKFLOW:
     * 1. Validate branch token (or get default branch)
     * 2. Load active packages
     * 3. Display form OR process submission
     * 
     * URL: /safelegalsolutions/safelegalsolutions_client/register/{token}
     * 
     * @param string $token Optional branch registration token
     * @return void
     */
    public function register($token = '')
    {
        try {
            // Get and validate branch
            $branch = $this->_get_and_validate_branch($token);
            
            if (!$branch) {
                return; // Error already handled
            }
            
            // Get available packages
            $items = $this->_get_active_items();
            
            if (!$items) {
                $this->_show_error(
                    'No Packages Available',
                    'No registration packages are currently available. Please contact support.'
                );
                return;
            }
            
            // Handle form submission
            if ($this->input->server('REQUEST_METHOD') === 'POST') {
                $this->_process_registration_step1($branch, $token, $items);
                return;
            }
            
            // Display registration form
            $this->_display_registration_form($branch, $token, $items);
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Register Error: ' . $e->getMessage());
            $this->_show_error(
                'Registration Error',
                'An unexpected error occurred. Please try again or contact support.'
            );
        }
    }
    
    /**
     * STEP 2: Review & Payment Page
     * 
     * WORKFLOW:
     * 1. Validate session data exists
     * 2. Load branch and package info
     * 3. Display review page OR process payment completion
     * 
     * URL: /safelegalsolutions/safelegalsolutions_client/review
     * 
     * @return void
     */
    public function review()
    {
        try {
            // Get registration data from session
            $registration_data = $this->session->userdata('registration_data');
            
            if (empty($registration_data)) {
                redirect('safelegalsolutions/safelegalsolutions_client/register');
                return;
            }
            
            // Validate branch and item
            $branch = $this->_validate_review_branch($registration_data);
            $item = $this->_validate_review_item($registration_data);
            
            if (!$branch || !$item) {
                return; // Error already handled
            }
            
            // Handle payment completion
            if ($this->input->server('REQUEST_METHOD') === 'POST') {
                $this->_complete_registration($registration_data, $branch, $item);
                return;
            }
            
            // Display review page
            $this->_display_review_page($registration_data, $branch, $item);
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Review Error: ' . $e->getMessage());
            $this->_show_error(
                'Review Error',
                'An error occurred while loading the review page. Please try again.'
            );
        }
    }
    
    /**
     * STEP 3: Success Page
     * 
     * WORKFLOW:
     * 1. Validate student ID
     * 2. Load student, branch, and item data
     * 3. Check for client account creation status
     * 4. Display success page with appropriate messages
     * 
     * URL: /safelegalsolutions/safelegalsolutions_client/success/{student_id}
     * 
     * @param int $student_id Student record ID
     * @return void
     */
    public function success($student_id = 0)
    {
        try {
            // Validate student ID
            if (empty($student_id)) {
                redirect('safelegalsolutions/safelegalsolutions_client/register');
                return;
            }
            
            // Get student record
            $student = $this->_get_student_or_redirect($student_id);
            
            if (!$student) {
                return; // Already redirected
            }
            
            // Load related data
            $branch = $this->_get_branch_safe($student->branch_id);
            $item = $this->_get_item_safe($student->item_id);
            
            // Display success page
            $this->_display_success_page($student, $branch, $item);
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Success Page Error: ' . $e->getMessage());
            redirect('safelegalsolutions/safelegalsolutions_client/register');
        }
    }

    // ================================================================
    // REGISTRATION STEP 1: FORM PROCESSING
    // ================================================================
    
    /**
     * Process registration form submission
     * 
     * @param object $branch Branch object
     * @param string $token Branch token
     * @param array $items Available packages
     * @return void
     */
    private function _process_registration_step1($branch, $token, $items)
    {
        try {
            $post_data = $this->input->post();
            
            // Set validation rules
            $this->_set_registration_validation_rules();
            
            // Run validation
            if ($this->form_validation->run() === FALSE) {
                // Validation failed - redisplay form
                $this->_display_registration_form($branch, $token, $items, $post_data);
                return;
            }
            
            // Store in session and redirect to review
            $this->_store_registration_data($post_data, $branch->id, $token);
            redirect('safelegalsolutions/safelegalsolutions_client/review');
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Step 1 Processing Error: ' . $e->getMessage());
            $this->_display_registration_form($branch, $token, $items, $post_data, 
                'An error occurred while processing your registration. Please try again.');
        }
    }
    
    /**
     * Set form validation rules for registration
     * 
     * @return void
     */
    private function _set_registration_validation_rules()
    {
        // Student Name
        $this->form_validation->set_rules('student_name', 'Full Name', [
            'required',
            'trim',
            'max_length[255]',
            'regex_match[/^[a-zA-Z\s]+$/]'
        ], [
            'regex_match' => 'The {field} must contain only letters and spaces.'
        ]);
        
        // Email
        $this->form_validation->set_rules('email', 'Email Address', [
            'required',
            'trim',
            'valid_email',
            'max_length[100]',
            'callback_check_duplicate_email'
        ]);
        
        // Phone
        $this->form_validation->set_rules('phone', 'Phone Number', [
            'required',
            'trim',
            'max_length[20]',
            'regex_match[/^[0-9+\-\s()]+$/]'
        ], [
            'regex_match' => 'The {field} must contain only numbers and valid phone characters.'
        ]);
        
        // Address
        $this->form_validation->set_rules('address', 'Address', [
            'required',
            'trim',
            'min_length[10]'
        ]);
        
        // Date of Birth
        $this->form_validation->set_rules('date_of_birth', 'Date of Birth', [
            'required',
            'callback_validate_age'
        ]);
        
        // Passport Number
        $this->form_validation->set_rules('passport_number', 'Passport Number', [
            'required',
            'trim',
            'min_length[6]',
            'max_length[50]',
            'regex_match[/^[A-Z0-9]+$/]'
        ], [
            'regex_match' => 'The {field} must contain only uppercase letters and numbers.'
        ]);
        
        // Package Selection
        $this->form_validation->set_rules('item_id', 'Package Selection', [
            'required',
            'numeric',
            'callback_validate_item'
        ]);
    }
    
    /**
     * Store validated registration data in session
     * 
     * @param array $post_data Form POST data
     * @param int $branch_id Branch ID
     * @param string $token Branch token
     * @return void
     */
    private function _store_registration_data($post_data, $branch_id, $token)
    {
        $session_data = [
            'branch_id'       => $branch_id,
            'token'           => $token,
            'student_name'    => trim($post_data['student_name']),
            'email'           => trim(strtolower($post_data['email'])),
            'phone'           => trim($post_data['phone']),
            'address'         => trim($post_data['address']),
            'date_of_birth'   => $post_data['date_of_birth'],
            'passport_number' => strtoupper(trim($post_data['passport_number'])),
            'item_id'         => $post_data['item_id']
        ];
        
        $this->session->set_userdata('registration_data', $session_data);
        
        log_activity($this->log_prefix . ' - Registration data stored in session for: ' . $post_data['email']);
    }

    // ================================================================
    // REGISTRATION STEP 2: PAYMENT & COMPLETION
    // ================================================================
    
    /**
     * Complete registration with payment processing
     * 
     * WORKFLOW:
     * 1. Extract and validate payment information
     * 2. Generate unique ID and calculate profile completion
     * 3. Insert student record
     * 4. Record payment transaction (if applicable)
     * 5. Check if payment complete → create client account
     * 6. Redirect to success page
     * 
     * @param array $registration_data Registration form data
     * @param object $branch Branch object
     * @param object $item Package object
     * @return void
     */
    private function _complete_registration($registration_data, $branch, $item)
    {
        try {
            // Get payment information
            $payment_info = $this->_extract_payment_info($item);
            
            // Generate unique ID
            $unique_id = $this->_generate_unique_id($registration_data['passport_number']);
            
            // Calculate profile completion
            $profile_completion = $this->_calculate_profile_completion($registration_data);
            
            // Generate referral code
            $referral_code = $this->_generate_referral_code();
            
            // Prepare student data
            $insert_data = $this->_prepare_student_data(
                $registration_data,
                $branch,
                $item,
                $payment_info,
                $unique_id,
                $profile_completion,
                $referral_code
            );
            
            // Insert student record
            $student_id = $this->_insert_student_record($insert_data);
            
            if (!$student_id) {
                throw new Exception('Failed to create student record');
            }
            
            // Record payment transaction if amount > 0
            if ($payment_info['amount_paid'] > 0) {
                $this->_record_payment_transaction($student_id, $payment_info, $branch);
            }
            
            // Check if payment is complete → create client account
            $this->_handle_client_account_creation($student_id, $branch, $payment_info['payment_percentage']);
            
            // Clear session and redirect to success
            $this->session->unset_userdata('registration_data');
            redirect('safelegalsolutions/safelegalsolutions_client/success/' . $student_id);
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Registration Completion Error: ' . $e->getMessage());
            
            $data = [
                'title'             => 'Review & Payment',
                'registration_data' => $registration_data,
                'branch'            => $branch,
                'item'              => $item,
                'error'             => 'Registration failed. Please try again or contact support.',
                'token'             => $registration_data['token']
            ];
            
            $this->load->view('safelegalsolutions/public_review', $data);
        }
    }
    
    /**
     * Extract payment information from POST data
     * 
     * @param object $item Package object
     * @return array Payment information
     */
    private function _extract_payment_info($item)
    {
        try {
            $payment_status = $this->input->post('payment_status'); // 'paid', 'partial', 'unpaid'
            $amount_paid = 0.00;
            
            // Determine amount based on payment status
            if ($payment_status === 'paid') {
                $amount_paid = $this->input->post('amount_paid');
                if (empty($amount_paid)) {
                    $amount_paid = $item->total_price;
                }
            } elseif ($payment_status === 'partial') {
                $amount_paid = $this->input->post('amount_partial');
                if (empty($amount_paid)) {
                    $amount_paid = 0.00;
                }
            } else {
                $payment_status = 'unpaid';
                $amount_paid = 0.00;
            }
            
            // Ensure numeric
            $amount_paid = (float)$amount_paid;
            
            // Calculate percentage
            $payment_percentage = 0;
            if ($item->total_price > 0) {
                $payment_percentage = ($amount_paid / $item->total_price) * 100;
            }
            
            log_activity($this->log_prefix . ' - Payment Info: Status=' . $payment_status . 
                        ', Amount=' . $amount_paid . ', Percentage=' . $payment_percentage . '%');
            
            return [
                'payment_status'     => $payment_status,
                'amount_paid'        => $amount_paid,
                'payment_percentage' => $payment_percentage
            ];
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Payment Extraction Error: ' . $e->getMessage());
            
            return [
                'payment_status'     => 'unpaid',
                'amount_paid'        => 0.00,
                'payment_percentage' => 0
            ];
        }
    }
    
    /**
     * Generate unique ID from passport number
     * 
     * @param string $passport Passport number
     * @return string Unique ID
     */
    private function _generate_unique_id($passport)
    {
        try {
            $passport = strtoupper(trim($passport));
            
            if (empty($passport)) {
                log_activity($this->log_prefix . ' - WARNING: Empty passport for unique ID generation');
                return 'saflg-' . time();
            }
            
            log_activity($this->log_prefix . ' - Generating unique ID from passport: ' . $passport);
            
            $unique_id = $this->safelegalsolutions_client_model->generate_unique_id($passport);
            
            if (empty($unique_id)) {
                $unique_id = 'saflg-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
                log_activity($this->log_prefix . ' - Fallback unique ID generated: ' . $unique_id);
            } else {
                log_activity($this->log_prefix . ' - Unique ID generated: ' . $unique_id);
            }
            
            return $unique_id;
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Unique ID Generation Error: ' . $e->getMessage());
            return 'saflg-' . time();
        }
    }
    
    /**
     * Calculate profile completion percentage
     * 
     * @param array $registration_data Registration data
     * @return int Profile completion percentage
     */
    private function _calculate_profile_completion($registration_data)
    {
        try {
            return $this->safelegalsolutions_client_model->calculate_profile_completion($registration_data);
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Profile Completion Calculation Error: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Generate unique referral code
     * 
     * @return string Referral code
     */
    private function _generate_referral_code()
    {
        try {
            return $this->safelegalsolutions_client_model->generate_referral_code();
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Referral Code Generation Error: ' . $e->getMessage());
            return 'REF-' . time();
        }
    }
    
    /**
     * Prepare student data for insertion
     * 
     * @param array $registration_data Registration form data
     * @param object $branch Branch object
     * @param object $item Package object
     * @param array $payment_info Payment information
     * @param string $unique_id Unique student ID
     * @param int $profile_completion Profile completion percentage
     * @param string $referral_code Referral code
     * @return array Insert data
     */
    private function _prepare_student_data($registration_data, $branch, $item, $payment_info, 
                                           $unique_id, $profile_completion, $referral_code)
    {
        $notes = sprintf(
            'Package: %s | Price: ₹%s | Payment: %s (%.2f%%)',
            $item->item_name,
            number_format($item->total_price, 2),
            ucfirst($payment_info['payment_status']),
            $payment_info['payment_percentage']
        );
        
        return [
            'branch_id'                  => $branch->id,
            'nodal_partner_manager_id'   => $branch->nodal_partner_manager_id,
            'created_by'                 => $branch->nodal_partner_manager_id,
            'student_name'               => $registration_data['student_name'],
            'email'                      => $registration_data['email'],
            'phone'                      => $registration_data['phone'],
            'address'                    => $registration_data['address'],
            'date_of_birth'              => $registration_data['date_of_birth'],
            'passport_number'            => isset($registration_data['passport_number']) ? 
                                           strtoupper($registration_data['passport_number']) : '',
            'unique_id'                  => $unique_id,
            'item_id'                    => $registration_data['item_id'],
            'payment_status'             => $payment_info['payment_status'],
            'payment_percentage'         => $payment_info['payment_percentage'],
            'amount_paid'                => $payment_info['amount_paid'],
            'total_amount'               => $item->total_price,
            'referral_code'              => $referral_code,
            'status'                     => 'draft',
            'profile_completion'         => $profile_completion,
            'earnings'                   => '0.00',
            'notes'                      => $notes,
            'is_locked'                  => 0
        ];
    }
    
    /**
     * Insert student record into database
     * 
     * @param array $insert_data Student data
     * @return int|false Student ID or false on failure
     */
    private function _insert_student_record($insert_data)
    {
        try {
            $student_id = $this->safelegalsolutions_client_model->add_student($insert_data);
            
            if ($student_id) {
                log_activity($this->log_prefix . ' - Student created [ID: ' . $student_id . 
                           ', Email: ' . $insert_data['email'] . ']');
            } else {
                log_activity($this->log_prefix . ' - Failed to insert student record');
            }
            
            return $student_id;
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Student Insertion Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Record payment transaction
     * 
     * @param int $student_id Student ID
     * @param array $payment_info Payment information
     * @param object $branch Branch object
     * @return void
     */
    private function _record_payment_transaction($student_id, $payment_info, $branch)
    {
        try {
            $payment_method = $this->input->post('payment_method');
            if (empty($payment_method)) {
                $payment_method = 'cash';
            }
            
            $payment_data = [
                'student_id'             => $student_id,
                'payment_method'         => $payment_method,
                'amount'                 => $payment_info['amount_paid'],
                'payment_date'           => date('Y-m-d H:i:s'),
                'transaction_reference'  => $this->input->post('transaction_reference'),
                'payment_notes'          => $this->input->post('payment_notes'),
                'payment_status'         => 'completed',
                'created_by'             => $branch->nodal_partner_manager_id
            ];
            
            $payment_id = $this->safelegalsolutions_client_model->add_payment($payment_data);
            
            if ($payment_id) {
                log_activity($this->log_prefix . ' - Payment recorded [Payment ID: ' . $payment_id . 
                           ', Student ID: ' . $student_id . ', Amount: ' . $payment_info['amount_paid'] . ']');
            }
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Payment Recording Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Handle client account creation if payment is complete
     * 
     * @param int $student_id Student ID
     * @param object $branch Branch object
     * @param float $payment_percentage Payment percentage
     * @return void
     */
    private function _handle_client_account_creation($student_id, $branch, $payment_percentage)
    {
        try {
            $payment_complete = $this->safelegalsolutions_client_model->is_payment_complete($student_id);
            
            log_activity($this->log_prefix . ' - Payment check for Student ID ' . $student_id . 
                        ' = ' . ($payment_complete ? 'COMPLETE' : 'INCOMPLETE'));
            
            if ($payment_complete) {
                // Create package enrollment
                $enrollment_id = $this->safelegalsolutions_client_model->create_package_enrollment(
                    $student_id, 
                    ['created_by' => $branch->nodal_partner_manager_id]
                );
                
                if ($enrollment_id) {
                    log_activity($this->log_prefix . ' - Package enrollment created [Enrollment ID: ' . 
                               $enrollment_id . ', Student ID: ' . $student_id . ']');
                }
                
                // Create client account
                $client_result = $this->safelegalsolutions_client_model->create_client_account_for_student($student_id);
                
                if ($client_result['success']) {
                    log_activity($this->log_prefix . ' - ✓ Client account created [Student ID: ' . 
                               $student_id . ', Client ID: ' . $client_result['client_id'] . ']');
                    
                    $this->session->set_flashdata('client_account_created', true);
                    $this->session->set_flashdata('client_id', $client_result['client_id']);
                    $this->session->set_flashdata('email_sent', $client_result['email_sent']);
                } else {
                    log_activity($this->log_prefix . ' - ✗ Client creation failed [Student ID: ' . 
                               $student_id . ', Error: ' . $client_result['message'] . ']');
                    
                    $this->session->set_flashdata('client_account_created', false);
                    $this->session->set_flashdata('client_error', $client_result['message']);
                }
            } else {
                log_activity($this->log_prefix . ' - Payment incomplete (' . $payment_percentage . 
                           '%), client account NOT created [Student ID: ' . $student_id . ']');
                
                $this->session->set_flashdata('payment_incomplete', true);
                $this->session->set_flashdata('payment_percentage', $payment_percentage);
            }
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Client Account Creation Error: ' . $e->getMessage());
            $this->session->set_flashdata('client_account_created', false);
            $this->session->set_flashdata('client_error', 'Failed to create account: ' . $e->getMessage());
        }
    }

    // ================================================================
    // VALIDATION CALLBACKS
    // ================================================================
    
    /**
     * Validation callback: Check for duplicate email
     * 
     * @param string $email Email address
     * @return bool True if unique, false if duplicate
     */
    public function check_duplicate_email($email)
    {
        try {
            $existing = $this->safelegalsolutions_client_model->get_student_by_email($email);
            
            if ($existing) {
                $this->form_validation->set_message('check_duplicate_email', 
                    'This email address is already registered. Please use a different email or contact support.');
                return false;
            }
            
            return true;
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Email Duplicate Check Error: ' . $e->getMessage());
            $this->form_validation->set_message('check_duplicate_email', 
                'Unable to verify email. Please try again.');
            return false;
        }
    }
    
    /**
     * Validation callback: Validate age (must be 18+)
     * 
     * @param string $date_of_birth Date of birth (YYYY-MM-DD)
     * @return bool True if valid age, false otherwise
     */
    public function validate_age($date_of_birth)
    {
        if (empty($date_of_birth)) {
            $this->form_validation->set_message('validate_age', 'Date of birth is required.');
            return false;
        }
        
        try {
            $dob = new DateTime($date_of_birth);
            $today = new DateTime();
            
            // Check if date is in the future
            if ($dob > $today) {
                $this->form_validation->set_message('validate_age', 'Date of birth cannot be in the future.');
                return false;
            }
            
            // Calculate age
            $age = $today->diff($dob)->y;
            
            // Must be at least 18 years old
            if ($age < 18) {
                $this->form_validation->set_message('validate_age', 
                    'You must be at least 18 years old to register. Current age: ' . $age . ' years.');
                return false;
            }
            
            // Sanity check: not over 100 years old
            if ($age > 100) {
                $this->form_validation->set_message('validate_age', 'Please enter a valid date of birth.');
                return false;
            }
            
            return true;
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Age Validation Error: ' . $e->getMessage());
            $this->form_validation->set_message('validate_age', 'Invalid date format.');
            return false;
        }
    }
    
    /**
     * Validation callback: Validate item exists and is active
     * 
     * @param int $item_id Package ID
     * @return bool True if valid, false otherwise
     */
    public function validate_item($item_id)
    {
        if (empty($item_id)) {
            $this->form_validation->set_message('validate_item', 'Please select a package.');
            return false;
        }
        
        try {
            $item = $this->safelegalsolutions_client_model->get_item($item_id);
            
            if (!$item) {
                $this->form_validation->set_message('validate_item', 'Selected package is not available.');
                return false;
            }
            
            if ($item->is_active != 1) {
                $this->form_validation->set_message('validate_item', 'Selected package is currently inactive.');
                return false;
            }
            
            return true;
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Item Validation Error: ' . $e->getMessage());
            $this->form_validation->set_message('validate_item', 'Unable to validate package selection.');
            return false;
        }
    }

    // ================================================================
    // CLIENT PORTAL METHODS (REQUIRE AUTHENTICATION)
    // ================================================================
    
    /**
     * Client Dashboard
     * 
     * Shows student registration overview with enrollment details
     * 
     * URL: /clients/safelegalsolutions_client/my_dashboard
     * 
     * @return void
     */
    public function my_dashboard()
    {
        try {
            // Verify authentication
            if (!is_client_logged_in()) {
                redirect(site_url('authentication/login'));
                return;
            }
            
            // Get logged-in contact
            $contact = $this->clients_model->get_contact(get_contact_user_id());
            
            if (!$contact) {
                show_404();
                return;
            }
            
            // Get student record
            $student = $this->safelegalsolutions_client_model->get_student_by_client_id($contact->userid);
            
            if (!$student) {
                $this->_show_client_no_record('Dashboard');
                return;
            }
            
            // Load related data
            $branch = $this->_get_branch_safe($student->branch_id);
            $item = $this->_get_item_safe($student->item_id);
            $enrollment = $this->_get_enrollment_safe($student->id);
            
            // Calculate statistics
            $days_since_registration = $this->_calculate_days_since($student->created_at);
            $days_until_end = $this->_calculate_days_until_end($enrollment);
            
            // Prepare dashboard data
            $data = [
                'title'                   => 'My Dashboard',
                'student'                 => $student,
                'branch'                  => $branch,
                'item'                    => $item,
                'enrollment'              => $enrollment,
                'days_since_registration' => $days_since_registration,
                'days_until_end'          => $days_until_end
            ];
            
            $this->data($data);
            $this->view('safelegalsolutions/client/my_dashboard');
            $this->layout();
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Dashboard Error: ' . $e->getMessage());
            $this->_show_client_error('Dashboard Error', 
                'Unable to load dashboard. Please try again or contact support.');
        }
    }
    
    /**
     * Client Profile View
     * 
     * Shows complete student details
     * 
     * URL: /clients/safelegalsolutions_client/my_profile
     * 
     * @return void
     */
    public function my_profile()
    {
        try {
            // Verify authentication
            if (!is_client_logged_in()) {
                redirect(site_url('authentication/login'));
                return;
            }
            
            // Get logged-in contact
            $contact = $this->clients_model->get_contact(get_contact_user_id());
            
            if (!$contact) {
                show_404();
                return;
            }
            
            // Get student record
            $student = $this->safelegalsolutions_client_model->get_student_by_client_id($contact->userid);
            
            if (!$student) {
                $this->_show_client_no_record('My Profile');
                return;
            }
            
            // Load related data
            $branch = $this->_get_branch_safe($student->branch_id);
            $item = $this->_get_item_safe($student->item_id);
            
            // Prepare profile data
            $data = [
                'title'   => 'My Profile',
                'student' => $student,
                'branch'  => $branch,
                'item'    => $item
            ];
            
            $this->data($data);
            $this->view('safelegalsolutions/client/my_profile');
            $this->layout();
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Profile Error: ' . $e->getMessage());
            $this->_show_client_error('Profile Error', 
                'Unable to load profile. Please try again or contact support.');
        }
    }
    
    /**
     * Referral Card View
     * 
     * Shows downloadable referral card with QR code
     * 
     * URL: /clients/safelegalsolutions_client/referral_card
     * 
     * @return void
     */
    public function referral_card()
    {
        try {
            // Verify authentication
            if (!is_client_logged_in()) {
                redirect(site_url('authentication/login'));
                return;
            }
            
            // Get logged-in contact
            $contact = $this->clients_model->get_contact(get_contact_user_id());
            
            if (!$contact) {
                show_404();
                return;
            }
            
            // Get student record
            $student = $this->safelegalsolutions_client_model->get_student_by_client_id($contact->userid);
            
            if (!$student) {
                $this->_show_client_no_record('Referral Card');
                return;
            }
            
            // Load branch data
            $branch = $this->_get_branch_safe($student->branch_id);
            
            // Prepare referral card data
            $data = [
                'title'         => 'My Referral Card',
                'student'       => $student,
                'branch'        => $branch,
                'referral_code' => $student->referral_code
            ];
            
            $this->data($data);
            $this->view('safelegalsolutions/client/referral_card');
            $this->layout();
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Referral Card Error: ' . $e->getMessage());
            $this->_show_client_error('Referral Card Error', 
                'Unable to load referral card. Please try again or contact support.');
        }
    }

    // ================================================================
    // HELPER METHODS - BRANCH & ITEM OPERATIONS
    // ================================================================
    
    /**
     * Get and validate branch by token or default
     * 
     * @param string $token Branch token
     * @return object|false Branch object or false
     */
    private function _get_and_validate_branch($token)
    {
        try {
            if (empty($token)) {
                // Get default branch
                $branch = $this->safelegalsolutions_client_model->get_default_branch();
                
                if (!$branch) {
                    $this->_show_error(
                        'Registration Unavailable',
                        'No registration branch is currently available. Please contact support.'
                    );
                    return false;
                }
            } else {
                // Get branch by token
                $branch = $this->safelegalsolutions_client_model->get_branch_by_token($token);
                
                if (!$branch) {
                    $this->_show_error(
                        'Invalid Registration Link',
                        'This registration link is invalid or has expired. Please contact your branch manager.'
                    );
                    return false;
                }
                
                if ($branch->is_active != 1) {
                    $this->_show_error(
                        'Registration Link Inactive',
                        'This registration link is currently inactive. Please contact your branch manager.'
                    );
                    return false;
                }
            }
            
            return $branch;
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Branch Validation Error: ' . $e->getMessage());
            $this->_show_error('System Error', 'Unable to validate registration link. Please contact support.');
            return false;
        }
    }
    
    /**
     * Get active items/packages
     * 
     * @return array|false Array of items or false
     */
    private function _get_active_items()
    {
        try {
            return $this->safelegalsolutions_client_model->get_active_items();
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Get Active Items Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get branch safely with error handling
     * 
     * @param int $branch_id Branch ID
     * @return object|null Branch object or null
     */
    private function _get_branch_safe($branch_id)
    {
        try {
            return $this->safelegalsolutions_client_model->get_branch($branch_id);
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Get Branch Error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get item safely with error handling
     * 
     * @param int $item_id Item ID
     * @return object|null Item object or null
     */
    private function _get_item_safe($item_id)
    {
        try {
            if (empty($item_id)) {
                return null;
            }
            return $this->safelegalsolutions_client_model->get_item($item_id);
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Get Item Error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get enrollment safely with error handling
     * 
     * @param int $student_id Student ID
     * @return object|null Enrollment object or null
     */
    private function _get_enrollment_safe($student_id)
    {
        try {
            if (empty($student_id)) {
                return null;
            }
            return $this->safelegalsolutions_client_model->get_enrollment_by_student($student_id);
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Get Enrollment Error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Validate branch for review page
     * 
     * @param array $registration_data Registration data
     * @return object|false Branch object or false
     */
    private function _validate_review_branch($registration_data)
    {
        try {
            $branch = $this->safelegalsolutions_client_model->get_branch($registration_data['branch_id']);
            
            if (!$branch) {
                $this->session->unset_userdata('registration_data');
                redirect('safelegalsolutions/safelegalsolutions_client/register');
                return false;
            }
            
            return $branch;
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Review Branch Validation Error: ' . $e->getMessage());
            $this->session->unset_userdata('registration_data');
            redirect('safelegalsolutions/safelegalsolutions_client/register');
            return false;
        }
    }
    
    /**
     * Validate item for review page
     * 
     * @param array $registration_data Registration data
     * @return object|false Item object or false
     */
    private function _validate_review_item($registration_data)
    {
        try {
            $item = $this->safelegalsolutions_client_model->get_item($registration_data['item_id']);
            
            if (!$item) {
                $this->session->unset_userdata('registration_data');
                set_alert('danger', 'Selected package is no longer available.');
                redirect('safelegalsolutions/safelegalsolutions_client/register/' . $registration_data['token']);
                return false;
            }
            
            return $item;
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Review Item Validation Error: ' . $e->getMessage());
            $this->session->unset_userdata('registration_data');
            redirect('safelegalsolutions/safelegalsolutions_client/register/' . $registration_data['token']);
            return false;
        }
    }
    
    /**
     * Get student or redirect on failure
     * 
     * @param int $student_id Student ID
     * @return object|false Student object or false
     */
    private function _get_student_or_redirect($student_id)
    {
        try {
            $student = $this->safelegalsolutions_client_model->get_student($student_id);
            
            if (!$student) {
                redirect('safelegalsolutions/safelegalsolutions_client/register');
                return false;
            }
            
            return $student;
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Get Student Error: ' . $e->getMessage());
            redirect('safelegalsolutions/safelegalsolutions_client/register');
            return false;
        }
    }

    // ================================================================
    // HELPER METHODS - DATE CALCULATIONS
    // ================================================================
    
    /**
     * Calculate days since a given date
     * 
     * @param string $date Date string
     * @return int Number of days
     */
    private function _calculate_days_since($date)
    {
        try {
            $past_date = new DateTime($date);
            $today = new DateTime();
            return $today->diff($past_date)->days;
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Date Calculation Error: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Calculate days until enrollment end date
     * 
     * @param object|null $enrollment Enrollment object
     * @return int|null Days until end (negative if expired)
     */
    private function _calculate_days_until_end($enrollment)
    {
        try {
            if (!$enrollment || empty($enrollment->end_date)) {
                return null;
            }
            
            $end_date = new DateTime($enrollment->end_date);
            $today = new DateTime();
            $days_until_end = $today->diff($end_date)->days;
            
            // Negative if expired
            if ($today > $end_date) {
                $days_until_end = -$days_until_end;
            }
            
            return $days_until_end;
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - End Date Calculation Error: ' . $e->getMessage());
            return null;
        }
    }

    // ================================================================
    // VIEW RENDERING METHODS
    // ================================================================
    
    /**
     * Display registration form
     * 
     * @param object $branch Branch object
     * @param string $token Branch token
     * @param array $items Available packages
     * @param array $form_data Form data (for repopulation)
     * @param string $error Optional error message
     * @return void
     */
    private function _display_registration_form($branch, $token, $items, $form_data = [], $error = '')
    {
        $data = [
            'title'      => 'Student Registration - Safe Legal',
            'branch'     => $branch,
            'token'      => $token,
            'items'      => $items,
            'form_data'  => $form_data,
            'error'      => $error
        ];
        
        $this->load->view('safelegalsolutions/public_register', $data);
    }
    
    /**
     * Display review page
     * 
     * @param array $registration_data Registration data
     * @param object $branch Branch object
     * @param object $item Package object
     * @return void
     */
    private function _display_review_page($registration_data, $branch, $item)
    {
        $data = [
            'title'             => 'Review & Payment',
            'registration_data' => $registration_data,
            'branch'            => $branch,
            'item'              => $item,
            'token'             => $registration_data['token']
        ];
        
        $this->load->view('safelegalsolutions/public_review', $data);
    }
    
    /**
     * Display success page
     * 
     * @param object $student Student object
     * @param object $branch Branch object
     * @param object $item Package object
     * @return void
     */
    private function _display_success_page($student, $branch, $item)
    {
        $data = [
            'title'                   => 'Registration Successful',
            'student_id'              => $student->id,
            'student_name'            => $student->student_name,
            'email'                   => $student->email,
            'referral_code'           => $student->referral_code,
            'branch'                  => $branch,
            'item'                    => $item,
            'payment_status'          => $student->payment_status,
            'payment_percentage'      => isset($student->payment_percentage) ? $student->payment_percentage : 0,
            'client_account_created'  => $this->session->flashdata('client_account_created'),
            'client_id'               => $this->session->flashdata('client_id'),
            'email_sent'              => $this->session->flashdata('email_sent'),
            'client_error'            => $this->session->flashdata('client_error'),
            'payment_incomplete'      => $this->session->flashdata('payment_incomplete'),
            'has_client_account'      => !empty($student->client_id)
        ];
        
        $this->load->view('safelegalsolutions/public_success', $data);
    }
    
    /**
     * Display error page (public)
     * 
     * @param string $title Error title
     * @param string $message Error message
     * @return void
     */
    private function _show_error($title, $message)
    {
        $data = [
            'title'   => $title,
            'error'   => $message
        ];
        
        $this->load->view('safelegalsolutions/public_error', $data);
    }
    
    /**
     * Display "no record" page for client portal
     * 
     * @param string $title Page title
     * @return void
     */
    private function _show_client_no_record($title)
    {
        $data = [
            'title' => $title,
            'error' => 'No registration record found for your account.'
        ];
        
        $this->data($data);
        $this->view('safelegalsolutions/client/no_record');
        $this->layout();
    }
    
    /**
     * Display error page for client portal
     * 
     * @param string $title Error title
     * @param string $message Error message
     * @return void
     */
    private function _show_client_error($title, $message)
    {
        $data = [
            'title' => $title,
            'error' => $message
        ];
        
        $this->data($data);
        $this->view('safelegalsolutions/client/error');
        $this->layout();
    }
}

