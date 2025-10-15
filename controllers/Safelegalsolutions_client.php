<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * SafeLegalSolutions Client Controller
 * PUBLIC ACCESS - No authentication required for registration
 * AUTHENTICATED ACCESS - Client portal methods require login
 * Version: 3.0 - Complete with Payment & Client Creation
 * 
 * File: modules/safelegalsolutions/controllers/Safelegalsolutions_client.php
 * 
 * CHANGELOG v3.0:
 * - Added payment selection in review page
 * - Fixed client account creation workflow
 * - Improved error handling and logging
 * - All existing functionality preserved
 */
class Safelegalsolutions_client extends ClientsController
{
    /**
     * Constructor
     * 
     * Loads required models and libraries
     * Checks authentication for protected methods
     */
    public function __construct()
    {
        parent::__construct();
        
        // Load client-specific model
        $this->load->model('safelegalsolutions/safelegalsolutions_client_model');
        $this->load->library('form_validation');
        $this->load->library('session');
        
        // CRITICAL: Check which methods need authentication
        $method = $this->router->fetch_method();
        
        // Public methods (no login required)
        $public_methods = ['index', 'register', 'review', 'success'];
        
        // If NOT a public method, require login
        if (!in_array($method, $public_methods)) {
            if (!is_client_logged_in()) {
                redirect(site_url('authentication/login'));
            }
        }
    }

    // ================================================================
    // PUBLIC REGISTRATION METHODS - NO AUTHENTICATION REQUIRED
    // ================================================================

    /**
     * Default index method
     * Redirects to register with optional token from URL segment
     * 
     * @param string $token Optional branch token
     * @return void
     */
    public function index($token = '')
    {
        $this->register($token);
    }

    /**
     * STEP 1: Registration form with package selection
     * 
     * URL: /safelegalsolutions/safelegalsolutions_client/register/{token}
     * 
     * @param string $token Optional branch registration token
     * @return void
     */
    public function register($token = '')
    {
        // Get branch information
        if (empty($token)) {
            $branch = $this->safelegalsolutions_client_model->get_default_branch();
            
            if (!$branch) {
                $this->_show_error(
                    'Registration Unavailable',
                    'No registration branch is currently available. Please contact support.'
                );
                return;
            }
        } else {
            $branch = $this->safelegalsolutions_client_model->get_branch_by_token($token);
            
            if (!$branch) {
                $this->_show_error(
                    'Invalid Registration Link',
                    'This registration link is invalid or has expired. Please contact your branch manager for a valid link.'
                );
                return;
            }
            
            if ($branch->is_active != 1) {
                $this->_show_error(
                    'Registration Link Inactive',
                    'This registration link is currently inactive. Please contact your branch manager.'
                );
                return;
            }
        }
        
        // Get all active packages/items
        $items = $this->safelegalsolutions_client_model->get_active_items();
        
        // Handle form submission
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->_process_step1($branch, $token, $items);
            return;
        }
        
        // Display registration form
        $data = [
            'title'      => 'Student Registration - Safe Legal Solutions',
            'branch'     => $branch,
            'token'      => $token,
            'items'      => $items,
            'form_data'  => []
        ];
        
        $this->load->view('safelegalsolutions/public_register', $data);
    }

    /**
     * Process STEP 1: Validate form and redirect to review
     * 
     * @param object $branch Branch object
     * @param string $token Branch token
     * @param array $items Available packages
     * @return void
     */
    private function _process_step1($branch, $token, $items)
    {
        $post_data = $this->input->post();
        
        // ============================================================
        // VALIDATION RULES
        // ============================================================
        
        // Student Name: Required, letters and spaces only
        $this->form_validation->set_rules('student_name', 'Full Name', [
            'required',
            'trim',
            'max_length[255]',
            'regex_match[/^[a-zA-Z\s]+$/]'
        ], [
            'regex_match' => 'The {field} must contain only letters and spaces.'
        ]);
        
        // Email: Required, valid format, check for duplicates
        $this->form_validation->set_rules('email', 'Email Address', [
            'required',
            'trim',
            'valid_email',
            'max_length[100]',
            'callback_check_duplicate_email'
        ]);
        
        // Phone: Required, numbers and phone characters only
        $this->form_validation->set_rules('phone', 'Phone Number', [
            'required',
            'trim',
            'max_length[20]',
            'regex_match[/^[0-9+\-\s()]+$/]'
        ], [
            'regex_match' => 'The {field} must contain only numbers and valid phone characters.'
        ]);
        
        // Address: Required, minimum 10 characters
        $this->form_validation->set_rules('address', 'Address', [
            'required',
            'trim',
            'min_length[10]'
        ]);
        
        // Date of Birth: Required, must be 18+
        $this->form_validation->set_rules('date_of_birth', 'Date of Birth', [
            'required',
            'callback_validate_age'
        ]);
        // Passport Number: Required, alphanumeric, minimum 6 characters
        $this->form_validation->set_rules('passport_number', 'Passport Number', [
            'required',
            'trim',
            'min_length[6]',
            'max_length[50]',
            'regex_match[/^[A-Z0-9]+$/]'
        ], [
            'regex_match' => 'The {field} must contain only uppercase letters and numbers.'
        ]);
        // Package Selection: Required, must exist and be active
        $this->form_validation->set_rules('item_id', 'Package Selection', [
            'required',
            'numeric',
            'callback_validate_item'
        ]);
        
        // ============================================================
        // RUN VALIDATION
        // ============================================================
        
        if ($this->form_validation->run() === FALSE) {
            // Validation failed - redisplay form with errors
            $data = [
                'title'      => 'Student Registration - Safe Legal Solutions',
                'branch'     => $branch,
                'token'      => $token,
                'items'      => $items,
                'form_data'  => $post_data
            ];
            
            $this->load->view('safelegalsolutions/public_register', $data);
            return;
        }
        
        // ============================================================
        // VALIDATION PASSED - STORE IN SESSION
        // ============================================================
        
        $this->session->set_userdata('registration_data', [
            'branch_id'      => $branch->id,
            'token'          => $token,
            'student_name'   => trim($post_data['student_name']),
            'email'          => trim(strtolower($post_data['email'])),
            'phone'          => trim($post_data['phone']),
            'address'        => trim($post_data['address']),
            'date_of_birth'  => $post_data['date_of_birth'],
            'passport_number' => strtoupper(trim($post_data['passport_number'])),
            'item_id'        => $post_data['item_id']
        ]);
        
        // Redirect to review page
        redirect('safelegalsolutions/safelegalsolutions_client/review');
    }

    /**
     * STEP 2: Review & Payment Page
     * 
     * URL: /safelegalsolutions/safelegalsolutions_client/review
     * 
     * Shows registration summary and payment options
     * 
     * @return void
     */
    public function review()
    {
        // Get registration data from session
        $registration_data = $this->session->userdata('registration_data');
        
        if (empty($registration_data)) {
            redirect('safelegalsolutions/safelegalsolutions_client/register');
            return;
        }
        
        // Get branch info
        $branch = $this->safelegalsolutions_client_model->get_branch($registration_data['branch_id']);
        
        if (!$branch) {
            $this->session->unset_userdata('registration_data');
            redirect('safelegalsolutions/safelegalsolutions_client/register');
            return;
        }
        
        // Get selected package/item
        $item = $this->safelegalsolutions_client_model->get_item($registration_data['item_id']);
        
        if (!$item) {
            $this->session->unset_userdata('registration_data');
            set_alert('danger', 'Selected package is no longer available.');
            redirect('safelegalsolutions/safelegalsolutions_client/register/' . $registration_data['token']);
            return;
        }
        
        // Handle payment completion (form submission)
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->_complete_registration($registration_data, $branch, $item);
            return;
        }
        
        // Display review page
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
     * Complete registration after payment review
     * 
     * WORKFLOW:
     * 1. Get payment information from form
     * 2. Calculate payment percentage
     * 3. Insert student record
     * 4. Check if payment is 100% complete
     * 5. If complete, create client account + send email
     * 6. Redirect to success page
     * 
     * @param array $registration_data Registration form data
     * @param object $branch Branch object
     * @param object $item Package/item object
     * @return void
     */
    private function _complete_registration($registration_data, $branch, $item)
    {

        // ============================================================
        // PREPARE STUDENT DATA
        // ============================================================

        // Debug: Check if passport_number exists in registration_data
        if (isset($registration_data['passport_number']) && !empty($registration_data['passport_number'])) {
            log_activity('Public Registration - Passport found in session: ' . $registration_data['passport_number']);
        } else {
            log_activity('Public Registration - WARNING: Passport NOT found in session data');
        }

        // Generate unique_id from passport number
        $passport = isset($registration_data['passport_number']) ? strtoupper(trim($registration_data['passport_number'])) : '';
        $unique_id = '';

        try {
            $unique_id = $this->safelegalsolutions_client_model->generate_unique_id($passport);
            log_activity('Public Registration - Unique ID generated: ' . $unique_id);
        } catch (Exception $e) {
            log_activity('Public Registration - ERROR generating unique_id: ' . $e->getMessage());
            // Fallback: generate manually
            $unique_id = 'saflg-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            log_activity('Public Registration - Fallback unique_id: ' . $unique_id);
        }

        // Validate unique_id was generated
        if (empty($unique_id)) {
            log_activity('Public Registration - CRITICAL: unique_id is EMPTY after generation!');
            $unique_id = 'saflg-' . time(); // Emergency fallback
        }
        // Calculate profile completion percentage
        $profile_completion = $this->safelegalsolutions_client_model->calculate_profile_completion($registration_data);
        
        // Generate unique referral code
        $referral_code = $this->safelegalsolutions_client_model->generate_referral_code();
        
        // ============================================================
        // GET PAYMENT INFORMATION FROM FORM
        // ============================================================
        
        $payment_status = $this->input->post('payment_status'); // 'paid', 'partial', or 'unpaid'
        $amount_paid = 0.00;
        
        // Determine amount based on payment status
        if ($payment_status === 'paid') {
            // Full payment
            $amount_paid = $this->input->post('amount_paid');
            if (empty($amount_paid)) {
                $amount_paid = $item->total_price; // Default to full amount
            }
        } elseif ($payment_status === 'partial') {
            // Partial payment
            $amount_paid = $this->input->post('amount_partial');
            if (empty($amount_paid)) {
                $amount_paid = 0.00;
            }
        } else {
            // Unpaid
            $payment_status = 'unpaid';
            $amount_paid = 0.00;
        }
        
        // Ensure amount is numeric
        $amount_paid = (float)$amount_paid;
        
        // Calculate payment percentage
        $payment_percentage = 0;
        if ($item->total_price > 0) {
            $payment_percentage = ($amount_paid / $item->total_price) * 100;
        }
        
        // Log payment info for debugging
        log_activity('Public Registration - Payment Info: Status=' . $payment_status . ', Amount=' . $amount_paid . ', Percentage=' . $payment_percentage . '%');
        
        // ============================================================
        // PREPARE STUDENT DATA
        // ============================================================
        
        $insert_data = [
            'branch_id'                  => $branch->id,
            'nodal_partner_manager_id'   => $branch->nodal_partner_manager_id,
            'created_by'                 => $branch->nodal_partner_manager_id,
            'student_name'               => $registration_data['student_name'],
            'email'                      => $registration_data['email'],
            'phone'                      => $registration_data['phone'],
            'address'                    => $registration_data['address'],
            'date_of_birth'              => $registration_data['date_of_birth'],
            'passport_number'            => isset($registration_data['passport_number']) ? strtoupper($registration_data['passport_number']) : '', // ✅ ADDED
            'unique_id'                  => $unique_id,
            'item_id'                    => $registration_data['item_id'],
            'payment_status'             => $payment_status,
            'payment_percentage'         => $payment_percentage,
            'amount_paid'                => $amount_paid,
            'total_amount'               => $item->total_price,
            'referral_code'              => $referral_code,
            'status'                     => 'draft',
            'profile_completion'         => $profile_completion,
            'earnings'                   => '0.00',
            'notes'                      => 'Package: ' . $item->item_name . ' | Price: ₹' . number_format($item->total_price, 2) . ' | Payment: ' . ucfirst($payment_status) . ' (' . number_format($payment_percentage, 2) . '%)',
            'is_locked'                  => 0
        ];
        
        // ============================================================
        // INSERT STUDENT RECORD
        // ============================================================
        
        $student_id = $this->safelegalsolutions_client_model->add_student($insert_data);
        
        if ($student_id) {
            log_activity('Public Registration: Student created [ID: ' . $student_id . ', Email: ' . $registration_data['email'] . ', Payment: ' . $payment_status . ' (' . $payment_percentage . '%)]');
             // ============================================================
            // RECORD PAYMENT TRANSACTION (if amount > 0)
            // ============================================================
            
            if ($amount_paid > 0) {
                $payment_method = $this->input->post('payment_method');
                if (empty($payment_method)) {
                    $payment_method = 'cash';
                }
                
                $transaction_reference = $this->input->post('transaction_reference');
                $payment_notes = $this->input->post('payment_notes');
                
                $payment_data = [
                    'student_id'             => $student_id,
                    'payment_method'         => $payment_method,
                    'amount'                 => $amount_paid,
                    'payment_date'           => date('Y-m-d H:i:s'),
                    'transaction_reference'  => $transaction_reference,
                    'payment_notes'          => $payment_notes,
                    'payment_status'         => 'completed',
                    'created_by'             => $branch->nodal_partner_manager_id
                ];
                
                $payment_id = $this->safelegalsolutions_client_model->add_payment($payment_data);
                
                if ($payment_id) {
                    log_activity('Public Registration: Payment recorded [Payment ID: ' . $payment_id . ', Student ID: ' . $student_id . ', Amount: ' . $amount_paid . ']');
                }
            }
            // ============================================================
            // AUTO-CREATE CLIENT ACCOUNT IF PAYMENT IS 100% COMPLETE
            // ============================================================
            
            $payment_complete = $this->safelegalsolutions_client_model->is_payment_complete($student_id);
            
            log_activity('Public Registration: Payment check result for Student ID ' . $student_id . ' = ' . ($payment_complete ? 'COMPLETE' : 'INCOMPLETE'));
            
            if ($payment_complete) {
                log_activity('Public Registration: Payment complete (' . $payment_percentage . '%), creating client account [Student ID: ' . $student_id . ']');
                
                // ============================================================
                // CREATE PACKAGE ENROLLMENT
                // ============================================================
                
                $enrollment_id = $this->safelegalsolutions_client_model->create_package_enrollment($student_id, [
                    'created_by' => $branch->nodal_partner_manager_id
                ]);
                
                if ($enrollment_id) {
                    log_activity('Public Registration: Package enrollment created [Enrollment ID: ' . $enrollment_id . ', Student ID: ' . $student_id . ']');
                }
                
                // Create client account + send email
                $client_result = $this->safelegalsolutions_client_model->create_client_account_for_student($student_id);    
                
                if ($client_result['success']) {
                    log_activity('Public Registration: ✓ Client account created successfully [Student ID: ' . $student_id . ', Client ID: ' . $client_result['client_id'] . ']');
                    
                    $this->session->set_flashdata('client_account_created', true);
                    $this->session->set_flashdata('client_id', $client_result['client_id']);
                    $this->session->set_flashdata('email_sent', $client_result['email_sent']);
                } else {
                    log_activity('Public Registration: ✗ Client creation failed [Student ID: ' . $student_id . ', Error: ' . $client_result['message'] . ']');
                    
                    $this->session->set_flashdata('client_account_created', false);
                    $this->session->set_flashdata('client_error', $client_result['message']);
                }
            } else {
                log_activity('Public Registration: Payment incomplete (' . $payment_percentage . '%), client account NOT created [Student ID: ' . $student_id . ']');
                $this->session->set_flashdata('payment_incomplete', true);
                $this->session->set_flashdata('payment_percentage', $payment_percentage);
            }
            
            // Clear registration session
            $this->session->unset_userdata('registration_data');
            
            // Redirect to success page
            redirect('safelegalsolutions/safelegalsolutions_client/success/' . $student_id);
            
        } else {
            // ============================================================
            // REGISTRATION FAILED
            // ============================================================
            
            log_activity('Public Registration: Failed to insert student record');
            
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
     * Success page (PUBLIC)
     * 
     * Shows registration success with client account info if created
     * 
     * URL: /safelegalsolutions/safelegalsolutions_client/success/{student_id}
     * 
     * @param int $student_id Student record ID
     * @return void
     */
    public function success($student_id = 0)
    {
        if (empty($student_id)) {
            redirect('safelegalsolutions/safelegalsolutions_client/register');
            return;
        }
        
        // Get student record
        $student = $this->safelegalsolutions_client_model->get_student($student_id);
        
        if (!$student) {
            redirect('safelegalsolutions/safelegalsolutions_client/register');
            return;
        }
        
        // Get branch and item info
        $branch = $this->safelegalsolutions_client_model->get_branch($student->branch_id);
        $item = $this->safelegalsolutions_client_model->get_item($student->item_id);
        
        // Check if client account was created
        $client_account_created = $this->session->flashdata('client_account_created');
        $client_id = $this->session->flashdata('client_id');
        $email_sent = $this->session->flashdata('email_sent');
        $client_error = $this->session->flashdata('client_error');
        $payment_incomplete = $this->session->flashdata('payment_incomplete');
        $payment_percentage = $this->session->flashdata('payment_percentage');
        
        // Check if student has client_id
        $has_client_account = !empty($student->client_id);
        
        // Prepare success page data
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
            'client_account_created'  => $client_account_created,
            'client_id'               => $client_id,
            'email_sent'              => $email_sent,
            'client_error'            => $client_error,
            'payment_incomplete'      => $payment_incomplete,
            'has_client_account'      => $has_client_account
        ];
        
        $this->load->view('safelegalsolutions/public_success', $data);
    }

    // ================================================================
    // VALIDATION CALLBACKS FOR PUBLIC REGISTRATION
    // ================================================================

    /**
     * Validation callback: Check duplicate email
     * 
     * Ensures email is not already registered in the system
     * 
     * @param string $email Email address to check
     * @return bool True if unique, false if duplicate
     */
    public function check_duplicate_email($email)
    {
        $existing = $this->safelegalsolutions_client_model->get_student_by_email($email);
        
        if ($existing) {
            $this->form_validation->set_message('check_duplicate_email', 
                'This email address is already registered. Please use a different email or contact support if this is your email.');
            return false;
        }
        
        return true;
    }

    /**
     * Validation callback: Validate age (must be 18+)
     * 
     * Ensures applicant is at least 18 years old
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
            $this->form_validation->set_message('validate_age', 'Invalid date format.');
            return false;
        }
    }

    /**
     * Validation callback: Validate item exists and is active
     * 
     * Ensures selected package is available
     * 
     * @param int $item_id Package/item ID
     * @return bool True if valid, false otherwise
     */
    public function validate_item($item_id)
    {
        if (empty($item_id)) {
            $this->form_validation->set_message('validate_item', 'Please select a package.');
            return false;
        }
        
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
    }

    /**
     * Display error page
     * 
     * @param string $title Error page title
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

    // ================================================================
    // CLIENT PORTAL - AUTHENTICATED METHODS
    // These methods require the student to be logged in as a client
    // ================================================================

    /**
     * Client Dashboard - Shows student's registration overview
     * 
     * URL: /clients/safelegalsolutions_client/my_dashboard
     * 
     * REQUIRES: Client login + linked student record
     * 
     * @return void
     */
    public function my_dashboard()
{
    // Double-check authentication (redundant but safe)
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
    
    // Get student record linked to this client
    $student = $this->safelegalsolutions_client_model->get_student_by_client_id($contact->userid);
    
    if (!$student) {
        // No student record found
        $data['title'] = 'Dashboard';
        $data['error'] = 'No registration record found for your account.';
        $this->data($data);
        $this->view('safelegalsolutions/client/no_record');
        $this->layout();
        return;
    }
    
    // Get branch information
    $branch = $this->safelegalsolutions_client_model->get_branch($student->branch_id);
    
    // Get package/item information
    $item = null;
    if (!empty($student->item_id)) {
        $item = $this->safelegalsolutions_client_model->get_item($student->item_id);
    }
    
    // ✅ NEW: Get enrollment information (course start/end dates)
    $enrollment = null;
    if ($student->id) {
        $enrollment = $this->safelegalsolutions_client_model->get_enrollment_by_student($student->id);
    }
    
    // Calculate days since registration
    $registration_date = new DateTime($student->created_at);
    $today = new DateTime();
    $days_since_registration = $today->diff($registration_date)->days;
    
    // ✅ NEW: Calculate days until course ends (if enrollment exists)
    $days_until_end = null;
    if ($enrollment && !empty($enrollment->end_date)) {
        $end_date = new DateTime($enrollment->end_date);
        $days_until_end = $today->diff($end_date)->days;
        
        // Check if course already ended
        if ($today > $end_date) {
            $days_until_end = -$days_until_end; // Negative means expired
        }
    }
    
    // Prepare dashboard data
    $data['title'] = 'My Dashboard';
    $data['student'] = $student;
    $data['branch'] = $branch;
    $data['item'] = $item;
    $data['enrollment'] = $enrollment; // ✅ NEW
    $data['days_since_registration'] = $days_since_registration;
    $data['days_until_end'] = $days_until_end; // ✅ NEW
    
    // Load dashboard view
    $this->data($data);
    $this->view('safelegalsolutions/client/my_dashboard');
    $this->layout();
}

    /**
     * Client Profile View - Shows complete student details
     * 
     * URL: /clients/safelegalsolutions_client/my_profile
     * 
     * REQUIRES: Client login + linked student record
     * 
     * @return void
     */
    public function my_profile()
    {
        // Double-check authentication
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
        
        // Get student record linked to this client
        $student = $this->safelegalsolutions_client_model->get_student_by_client_id($contact->userid);
        
        if (!$student) {
            // No student record found
            $data['title'] = 'My Profile';
            $data['error'] = 'No registration record found for your account.';
            $this->data($data);
            $this->view('safelegalsolutions/client/no_record');
            $this->layout();
            return;
        }
        
        // Get branch information
        $branch = $this->safelegalsolutions_client_model->get_branch($student->branch_id);
        
        // Get package/item information
        $item = null;
        if (!empty($student->item_id)) {
            $item = $this->safelegalsolutions_client_model->get_item($student->item_id);
        }
        
        // Prepare profile data
        $data['title'] = 'My Profile';
        $data['student'] = $student;
        $data['branch'] = $branch;
        $data['item'] = $item;
        
        // Load profile view
        $this->data($data);
        $this->view('safelegalsolutions/client/my_profile');
        $this->layout();
    }

    /**
     * View/Download Referral Card
     * 
     * URL: /clients/safelegalsolutions_client/referral_card
     * 
     * REQUIRES: Client login + linked student record
     * 
     * @return void
     */
    public function referral_card()
    {
        // Check authentication
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
            // No student record found
            $data['title'] = 'Referral Card';
            $data['error'] = 'No registration record found for your account.';
            $this->data($data);
            $this->view('safelegalsolutions/client/no_record');
            $this->layout();
            return;
        }
        
        // Get branch information (for contact details on card)
        $branch = $this->safelegalsolutions_client_model->get_branch($student->branch_id);
        
        // Prepare referral card data
        $data['title'] = 'My Referral Card';
        $data['student'] = $student;
        $data['branch'] = $branch;
        $data['referral_code'] = $student->referral_code;
        
        // Load referral card view
        $this->data($data);
        $this->view('safelegalsolutions/client/referral_card');
        $this->layout();
    }
}