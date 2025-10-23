<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * SafeLegalSolutions Client Controller - FIXED VERSION
 * 
 * CORRECT PAYMENT FLOW:
 * 1. Register → Create Student Record (NO client account)
 * 2. Review → Show payment button
 * 3. Purchase → Create temporary client for invoice → Redirect to Razorpay
 * 4. Payment Success → Create REAL client account + Send credentials email
 * 5. Payment Failure → Delete student + temporary client
 * 
 * @package    SafeLegalSolutions
 * @version    5.1 - FIXED
 */
class Safelegalsolutions_client extends ClientsController
{
    // ================================================================
    // CLASS PROPERTIES
    // ================================================================
    
    private $public_methods = ['index', 'register', 'review', 'success', 'purchase', 'payment_success', 'payment_failure'];
    private $log_prefix = 'SafeLegalSolutions Client Controller';

    // ================================================================
    // CONSTRUCTOR & INITIALIZATION
    // ================================================================
    
    public function __construct()
    {
        try {
            parent::__construct();
            
            $this->_load_dependencies();
            $this->_check_authentication();
            $this->_disable_csrf_for_callbacks();
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Constructor Error: ' . $e->getMessage());
            $this->_show_error('System Error', 'Failed to initialize. Please contact support.');
        }
    }
    
    private function _load_dependencies()
    {
        try {
            $this->load->model('safelegalsolutions/safelegalsolutions_client_model');
            $this->load->model('clients_model');
            $this->load->model('invoices_model');
            $this->load->model('payments_model');
            $this->load->library('form_validation');
            $this->load->library('session');
            $this->load->helper('string'); // For random password generation
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Dependency Loading Error: ' . $e->getMessage());
            throw new Exception('Failed to load required dependencies');
        }
    }
    
    private function _check_authentication()
    {
        try {
            $method = $this->router->fetch_method();
            
            if (!in_array($method, $this->public_methods) && !is_client_logged_in()) {
                redirect(site_url('authentication/login'));
            }
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Authentication Check Error: ' . $e->getMessage());
            redirect(site_url('authentication/login'));
        }
    }
    
    /**
     * Disable CSRF for payment callback methods
     */
    private function _disable_csrf_for_callbacks()
    {
        $csrf_exempt_methods = [
            'payment_success',
            'payment_failure',
            'razorpay_webhook'
        ];
        
        if (in_array($this->router->method, $csrf_exempt_methods)) {
            $this->security->csrf_show_error = false;
            $this->config->set_item('csrf_protection', false);
        }
    }

    // ================================================================
    // PUBLIC REGISTRATION METHODS
    // ================================================================
    
    public function index($token = '')
    {
        $this->register($token);
    }
    
    /**
     * STEP 1: Registration Form
     * Creates student record immediately (WITHOUT client account)
     */
    public function register($token = '')
    {
        try {
            $branch = $this->_get_and_validate_branch($token);
            
            if (!$branch) {
                return;
            }
            
            $items = $this->_get_active_items();
            
            if (!$items) {
                $this->_show_error(
                    'No Packages Available',
                    'No registration packages are currently available. Please contact support.'
                );
                return;
            }
            
            if ($this->input->server('REQUEST_METHOD') === 'POST') {
                $this->_process_registration_step1($branch, $token, $items);
                return;
            }
            $countries = $this->_get_countries();
            $this->_display_registration_form($branch, $token, $items, [], '', $countries);
            
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
     */
    public function review()
    {
        try {
            $registration_data = $this->session->userdata('registration_data');
            
            if (empty($registration_data)) {
                redirect('safelegalsolutions/safelegalsolutions_client/register');
                return;
            }
            
            $branch = $this->_validate_review_branch($registration_data);
            $item = $this->_validate_review_item($registration_data);
            
            if (!$branch || !$item) {
                return;
            }
            
            // Check if student already created
            $student_id = $this->session->userdata('temp_student_id');
            
            if (!$student_id) {
                // Create student record (WITHOUT client account)
                $student_id = $this->_create_temp_student($registration_data, $branch, $item);
                
                if (!$student_id) {
                    $this->_show_error('Registration Error', 'Failed to create student record. Please try again.');
                    return;
                }
                
                $this->session->set_userdata('temp_student_id', $student_id);
            }
            
            $this->_display_review_page($registration_data, $branch, $item, $student_id);
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Review Error: ' . $e->getMessage());
            $this->_show_error(
                'Review Error',
                'An error occurred while loading the review page. Please try again.'
            );
        }
    }
    
    /**
     * Create temporary student record (NO client account yet)
     */
    private function _create_temp_student($registration_data, $branch, $item)
    {
        try {
            $unique_id = $this->_generate_unique_id($registration_data['passport_number']);
            $profile_completion = $this->_calculate_profile_completion($registration_data);
            $referral_code = $this->_generate_referral_code();
            
            $insert_data = [
                'branch_id'                  => $branch->id,
                'nodal_partner_manager_id'   => $branch->nodal_partner_manager_id,
                'created_by'                 => $branch->nodal_partner_manager_id ?? 1,
                'student_name'               => $registration_data['student_name'],
                'email'                      => $registration_data['email'],
                'phone'                      => $registration_data['phone'],
                'address'                    => $registration_data['address'],
                'date_of_birth'              => $registration_data['date_of_birth'],
                'passport_number'            => strtoupper($registration_data['passport_number']),
                 
            // ========== ADD THESE NEW FIELDS ==========
            'passport_expiry_date'       => $registration_data['passport_expiry_date'],
            'destination_country_id'     => $registration_data['destination_country_id'],
            'university_name'            => $registration_data['university_name'],
            'course_program'             => $registration_data['course_program'],
            'city'                       => $registration_data['city'],
            'state'                      => $registration_data['state'],
            'pin_code'                   => $registration_data['pin_code'],
            'emergency_contact_mobile'   => $registration_data['emergency_contact_mobile'],
            'consent_given'              => $registration_data['consent_given'],
            'consent_given_at'           => $registration_data['consent_given_at'],
            // ==========================================
            
                'unique_id'                  => $unique_id,
                'item_id'                    => $registration_data['item_id'],
                'payment_status'             => 'unpaid',
                'payment_percentage'         => 0,
                'amount_paid'                => 0.00,
                'total_amount'               => $item->total_price,
                'referral_code'              => $referral_code,
                'status'                     => 'draft',
                'profile_completion'         => $profile_completion,
                'earnings'                   => '0.00',
                'notes'                      => 'Temporary record - Awaiting payment',
                'is_locked'                  => 0,
                'client_id'                  => NULL, // Will be set after payment success
                'client_created_at'          => NULL
            ];
            
            $student_id = $this->safelegalsolutions_client_model->add_student($insert_data);
            
            if ($student_id) {
                log_activity($this->log_prefix . ' - Temporary student created [ID: ' . $student_id . ', Email: ' . $registration_data['email'] . '] - Awaiting payment');
            }
            
            return $student_id;
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Create Temp Student Error: ' . $e->getMessage());
            return false;
        }
    }
    
    // ================================================================
    // PAYMENT PROCESSING METHODS
    // ================================================================
    
    /**
     * Initiate payment process
     * Creates invoice with temporary client and redirects to Razorpay
     */
    public function purchase()
    {
        try {
            $student_id = $this->session->userdata('temp_student_id');
            
            if (!$student_id) {
                set_alert('danger', 'Invalid session. Please start registration again.');
                redirect('safelegalsolutions/safelegalsolutions_client/register');
                return;
            }
            
            $student = $this->safelegalsolutions_client_model->get_student($student_id);
            
            if (!$student) {
                set_alert('danger', 'Student record not found.');
                redirect('safelegalsolutions/safelegalsolutions_client/register');
                return;
            }
            
            $item = $this->safelegalsolutions_client_model->get_item($student->item_id);
            
            if (!$item) {
                set_alert('danger', 'Package not found.');
                redirect('safelegalsolutions/safelegalsolutions_client/register');
                return;
            }
            
            // Check if payment already processed
            if ($student->payment_status === 'paid' && !empty($student->client_id)) {
                redirect('safelegalsolutions/safelegalsolutions_client/success/' . $student_id);
                return;
            }
            
            // FREE PACKAGE: Direct success
            if ($item->total_price <= 0) {
                $this->_handle_free_package($student_id, $student, $item);
                return;
            }
            
            // PAID PACKAGE: Create invoice and redirect to Razorpay
            $this->_process_paid_package($student, $item);
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Purchase Error: ' . $e->getMessage());
            set_alert('danger', 'Payment processing failed. Please try again or contact support.');
            redirect('safelegalsolutions/safelegalsolutions_client/register');
        }
    }
    
    /**
     * Handle free package enrollment
     */
    private function _handle_free_package($student_id, $student, $item)
    {
        try {
            $this->db->trans_start();
            
            $free_payment_id = 'FREE_' . time() . '_' . $student_id;
            
            // Update student payment status
            $this->db->where('id', $student_id);
            $this->db->update(db_prefix() . 'sls_students', [
                'payment_status' => 'paid',
                'payment_percentage' => 100,
                'amount_paid' => 0.00,
                'status' => 'draft'
            ]);
            
            // Record free payment transaction
            $this->db->insert(db_prefix() . 'sls_payments', [
                'student_id' => $student_id,
                'payment_id' => $free_payment_id,
                'payment_method' => 'other',
                'amount' => 0.00,
                'payment_date' => date('Y-m-d H:i:s'),
                'payment_notes' => 'Free package enrollment',
                'payment_status' => 'completed',
                'receipt_number' => 'FREE-' . $student_id,
                'created_by' => $student->created_by,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            // Create REAL client account with credentials
            $client_result = $this->_create_real_client_account($student_id, $student);
            
            if (!$client_result['success']) {
                throw new Exception('Failed to create client account: ' . $client_result['message']);
            }
            
            // Create package enrollment
            $enrollment_result = $this->_create_package_enrollment($student_id, $student, $item);
            
            if (!$enrollment_result) {
                throw new Exception('Failed to create package enrollment');
            }
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Transaction failed');
            }
            
            log_activity($this->log_prefix . ' - ✓ Free package enrollment completed [Student ID: ' . $student_id . ', Client ID: ' . $client_result['client_id'] . ']');
            
            $this->session->set_flashdata('client_account_created', true);
            $this->session->set_flashdata('client_id', $client_result['client_id']);
            $this->session->set_flashdata('email_sent', $client_result['email_sent']);
            $this->session->set_flashdata('credentials_sent', true);
            
            redirect('safelegalsolutions/safelegalsolutions_client/success/' . $student_id);
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_activity($this->log_prefix . ' - Free Package Error: ' . $e->getMessage());
            set_alert('danger', 'Enrollment failed: ' . $e->getMessage());
            redirect('safelegalsolutions/safelegalsolutions_client/register');
        }
    }
    
    /**
     * Process paid package with Razorpay
     */
    private function _process_paid_package($student, $item)
    {
        try {
            // Get or create TEMPORARY client for invoice (NOT for portal access)
            $temp_client_id = $this->_get_or_create_temp_client($student);
            
            if (!$temp_client_id) {
                throw new Exception('Failed to create temporary client for invoice');
            }
            
            // Check for existing unpaid invoice
            $existing_invoice = $this->db->select('id, status, hash')
                ->from(db_prefix() . 'invoices')
                ->where('clientid', $temp_client_id)
                ->like('adminnote', 'Student ID: ' . $student->id)
                ->where('status !=', 2) // Not paid
                ->order_by('id', 'DESC')
                ->limit(1)
                ->get()
                ->row();
            
            if ($existing_invoice) {
                $invoice_id = $existing_invoice->id;
                $invoice = $this->invoices_model->get($invoice_id);
                
                log_activity($this->log_prefix . ' - Reusing existing invoice [Invoice: ' . $invoice_id . ', Student: ' . $student->id . ']');
            } else {
                // Create new invoice
                $invoice_data = [
                    'clientid'      => $temp_client_id,
                    'date'          => date('Y-m-d'),
                    'duedate'       => date('Y-m-d', strtotime('+7 days')),
                    'currency'      => get_base_currency()->id,
                    'subtotal'      => $item->base_price,
                    'total'         => $item->total_price,
                    'status'        => 1, // unpaid
                    'allowed_payment_modes' => ['razorpay'],
                    'clientnote'    => 'Invoice for Package: ' . $item->item_name,
                    'adminnote'     => 'Student ID: ' . $student->id . ', Package ID: ' . $item->id . ', Email: ' . $student->email,
                    'newitems'      => [
                        [
                            'description' => 'Package Enrollment: ' . $item->item_name,
                            'long_description' => $item->description ?? 'Package enrollment and access',
                            'qty'         => 1,
                            'rate'        => $item->base_price,
                            'unit'        => '',
                            'order'       => 1,
                        ]
                    ]
                ];
                
                // Add GST as separate line item if applicable
                if ($item->gst_amount > 0) {
                    $invoice_data['newitems'][] = [
                        'description' => 'GST (' . $item->gst_percentage . '%)',
                        'long_description' => 'Goods and Services Tax',
                        'qty'         => 1,
                        'rate'        => $item->gst_amount,
                        'unit'        => '',
                        'order'       => 2,
                    ];
                }
                
                $invoice_id = $this->invoices_model->add($invoice_data);
                
                if (!$invoice_id) {
                    throw new Exception('Failed to create invoice');
                }
                
                $invoice = $this->invoices_model->get($invoice_id);
                
                log_activity($this->log_prefix . ' - Invoice created [Invoice: ' . $invoice_id . ', Student: ' . $student->id . ', Amount: ₹' . $item->total_price . ']');
            }
            
            if (!$invoice) {
                throw new Exception('Invoice not found');
            }
            
            // Load Razorpay gateway and process payment
            $this->load->library('razor_pay_gateway');
            
            $payment_data = [
                'invoice'   => $invoice,
                'invoiceid' => $invoice_id,
                'amount'    => $invoice->total,
                'hash'      => $invoice->hash,
            ];
            
            log_activity($this->log_prefix . ' - Initiating Razorpay payment [Invoice: ' . $invoice_id . ', Student: ' . $student->id . ', Amount: ₹' . $invoice->total . ']');
            
            // This will redirect to Razorpay payment page
            $this->razor_pay_gateway->process_payment($payment_data);
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Paid Package Error: ' . $e->getMessage());
            set_alert('danger', 'Payment processing failed: ' . $e->getMessage());
            redirect('safelegalsolutions/safelegalsolutions_client/register');
        }
    }
    
    /**
     * Get or create TEMPORARY client for invoice ONLY (not for portal access)
     * This client will remain inactive until payment is successful
     */
    private function _get_or_create_temp_client($student)
    {
        try {
            // Check if temporary client already exists
            if (!empty($student->client_id)) {
                $existing_client = $this->db->get_where('tblclients', ['userid' => $student->client_id])->row();
                if ($existing_client) {
                    return $student->client_id;
                }
            }
            
            // Create temporary client for invoice ONLY
            $client_data = [
                'company'           => $student->student_name . ' (Temporary)',
                'phonenumber'       => $student->phone,
                'address'           => $student->address,
                'datecreated'       => date('Y-m-d H:i:s'),
                'active'            => 0, // INACTIVE until payment
                'show_primary_contact' => 1,
                'registration_confirmed' => 0,
                'addedfrom'         => $student->created_by ?? 1
            ];
            
            $this->db->insert('tblclients', $client_data);
            $temp_client_id = $this->db->insert_id();
            
            if (!$temp_client_id) {
                return false;
            }
            
            // Create contact WITHOUT password (will be set after payment)
            $contact_data = [
                'userid'            => $temp_client_id,
                'is_primary'        => 1,
                'firstname'         => $student->student_name,
                'lastname'          => '',
                'email'             => $student->email,
                'phonenumber'       => $student->phone,
                'title'             => 'Student',
                'datecreated'       => date('Y-m-d H:i:s'),
                'password'          => '', // NO PASSWORD YET
                'active'            => 0, // INACTIVE
                'invoice_emails'    => 1,
                'estimate_emails'   => 0,
                'credit_note_emails' => 0,
                'contract_emails'   => 0,
                'task_emails'       => 0,
                'project_emails'    => 0,
                'ticket_emails'     => 0
            ];
            
            $this->db->insert('tblcontacts', $contact_data);
            
            // Link temporary client to student
            $this->db->where('id', $student->id);
            $this->db->update(db_prefix() . 'sls_students', [
                'client_id' => $temp_client_id
            ]);
            
            log_activity($this->log_prefix . ' - Temporary client created for invoice [Temp Client ID: ' . $temp_client_id . ', Student ID: ' . $student->id . '] - NO LOGIN ACCESS YET');
            
            return $temp_client_id;
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Create Temp Client Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * PAYMENT SUCCESS CALLBACK
     * This is where we create the REAL client account with login credentials
     */
    public function payment_success($invoice_id = null)
    {
        try {
            $student_id = $this->session->userdata('temp_student_id');
            
            if (!$invoice_id || !is_numeric($invoice_id)) {
                set_alert('warning', 'Invalid payment reference.');
                redirect('safelegalsolutions/safelegalsolutions_client/register');
                return;
            }
            
            $invoice = $this->invoices_model->get($invoice_id);
            
            if (!$invoice) {
                set_alert('warning', 'Invoice not found.');
                redirect('safelegalsolutions/safelegalsolutions_client/register');
                return;
            }
            
            // Extract student ID from adminnote
            if (!$student_id) {
                $student_id = $this->_extract_student_id_from_admin_note($invoice->adminnote);
            }
            
            if (!$student_id) {
                set_alert('warning', 'Student information not found.');
                redirect('safelegalsolutions/safelegalsolutions_client/register');
                return;
            }
            
            $student = $this->safelegalsolutions_client_model->get_student($student_id);
            
            if (!$student) {
                set_alert('warning', 'Student record not found.');
                redirect('safelegalsolutions/safelegalsolutions_client/register');
                return;
            }
            
            // Get item details
            $item = $this->safelegalsolutions_client_model->get_item($student->item_id);
            
            if (!$item) {
                set_alert('warning', 'Package not found.');
                redirect('safelegalsolutions/safelegalsolutions_client/register');
                return;
            }
            
            // Check for payment error
            if ($this->input->get('payment_error')) {
                $this->_handle_payment_failure($student_id, $student, 'Payment verification failed');
                return;
            }
            
            // Get payment transaction ID
            $payment_id = $this->input->get('razorpay_payment_id');
            
            // Verify invoice is paid
            if ($invoice->status != 2) { // 2 = paid
                $this->_handle_payment_failure($student_id, $student, 'Payment not completed');
                return;
            }
            
            // Get payment details from invoice payments
            if (!$payment_id) {
                $payments = $this->payments_model->get_invoice_payments($invoice_id);
                
                if (empty($payments)) {
                    $this->_handle_payment_failure($student_id, $student, 'Payment record not found');
                    return;
                }
                
                $latest_payment = end($payments);
                $payment_id = $latest_payment['transactionid'];
            }
            
            log_activity($this->log_prefix . ' - Payment verified [Transaction: ' . $payment_id . ', Invoice: ' . $invoice_id . ', Student: ' . $student_id . ']');
            
            // START TRANSACTION
            $this->db->trans_start();
            
            // Update student payment status
            $this->db->where('id', $student_id);
            $this->db->update(db_prefix() . 'sls_students', [
                'payment_status' => 'paid',
                'payment_percentage' => 100,
                'amount_paid' => $invoice->total,
                'status' => 'draft'
            ]);
            
            // Record payment in SLS payment table
            $this->db->insert(db_prefix() . 'sls_payments', [
                'student_id' => $student_id,
                'payment_id' => $payment_id,
                'payment_method' => 'other', // Razorpay
                'amount' => $invoice->total,
                'payment_date' => date('Y-m-d H:i:s'),
                'payment_notes' => 'Razorpay payment successful',
                'payment_status' => 'completed',
                'receipt_number' => 'INV-' . $invoice->id, // Invoice number as receipt
                'created_by' => $student->created_by ?? 1,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            // Create REAL client account with login credentials
            $client_result = $this->_create_real_client_account($student_id, $student);
            
            if (!$client_result['success']) {
                throw new Exception('Failed to create client account: ' . $client_result['message']);
            }
            
            // Create package enrollment
            $enrollment_result = $this->_create_package_enrollment($student_id, $student, $item);
            
            if (!$enrollment_result) {
                throw new Exception('Failed to create package enrollment');
            }
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Transaction failed');
            }
            
            log_activity($this->log_prefix . ' - ✓ Payment successful, client account created [Student ID: ' . $student_id . ', Client ID: ' . $client_result['client_id'] . ', Transaction: ' . $payment_id . ']');
            
            $this->session->set_flashdata('client_account_created', true);
            $this->session->set_flashdata('client_id', $client_result['client_id']);
            $this->session->set_flashdata('email_sent', $client_result['email_sent']);
            $this->session->set_flashdata('credentials_sent', true);
            $this->session->set_flashdata('payment_id', $payment_id);
            
            // Clear session
            $this->session->unset_userdata(['temp_student_id', 'registration_data']);
            
            redirect('safelegalsolutions/safelegalsolutions_client/success/' . $student_id);
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_activity($this->log_prefix . ' - Payment Success Handler Error: ' . $e->getMessage());
            
            if (!empty($student_id)) {
                $this->_handle_payment_failure($student_id, $student ?? null, 'System error: ' . $e->getMessage());
            } else {
                set_alert('danger', 'Payment processing error. Please contact support.');
                redirect('safelegalsolutions/safelegalsolutions_client/register');
            }
        }
    }
    
    /**
     * Create REAL client account with login credentials
     * Activates the temporary client or creates new one with password
     */
    private function _create_real_client_account($student_id, $student)
    {
        try {
            $this->load->helper('string');
            
            // Generate random password
            $password = random_string('alnum', 12);
            $hashed_password = app_hash_password($password);
            
            $real_client_id = null;
            $is_new_client = false;
            
            // Check if temporary client exists
            if (!empty($student->client_id)) {
                $temp_client = $this->db->get_where('tblclients', ['userid' => $student->client_id])->row();
                
                if ($temp_client) {
                    // Activate the temporary client
                    $this->db->where('userid', $student->client_id);
                    $this->db->update('tblclients', [
                        'active' => 1,
                        'company' => $student->student_name, // Remove "(Temporary)"
                        'registration_confirmed' => 1
                    ]);
                    
                    // Update contact with password and activate
                    $this->db->where('userid', $student->client_id);
                    $this->db->where('is_primary', 1);
                    $this->db->update('tblcontacts', [
                        'password' => $hashed_password,
                        'active' => 1,
                        'email_verified_at' => date('Y-m-d H:i:s'),
                        'last_password_change' => date('Y-m-d H:i:s')
                    ]);
                    
                    $real_client_id = $student->client_id;
                    
                    log_activity($this->log_prefix . ' - Activated temporary client [Client ID: ' . $real_client_id . ', Student ID: ' . $student_id . ']');
                } else {
                    // Temporary client doesn't exist, create new
                    $is_new_client = true;
                }
            } else {
                // No client_id, create new
                $is_new_client = true;
            }
            
            // Create new client if needed
            if ($is_new_client) {
                // Create client
                $client_data = [
                    'company'           => $student->student_name,
                    'phonenumber'       => $student->phone,
                    'address'           => $student->address,
                    'datecreated'       => date('Y-m-d H:i:s'),
                    'active'            => 1, // ACTIVE
                    'show_primary_contact' => 1,
                    'registration_confirmed' => 1,
                    'addedfrom'         => $student->created_by ?? 1
                ];
                
                $this->db->insert('tblclients', $client_data);
                $real_client_id = $this->db->insert_id();
                
                if (!$real_client_id) {
                    return [
                        'success' => false,
                        'message' => 'Failed to create client record'
                    ];
                }
                
                // Create contact with password
                $contact_data = [
                    'userid'            => $real_client_id,
                    'is_primary'        => 1,
                    'firstname'         => $student->student_name,
                    'lastname'          => '',
                    'email'             => $student->email,
                    'phonenumber'       => $student->phone,
                    'title'             => 'Student',
                    'datecreated'       => date('Y-m-d H:i:s'),
                    'password'          => $hashed_password,
                    'active'            => 1, // ACTIVE
                    'email_verified_at' => date('Y-m-d H:i:s'),
                    'last_password_change' => date('Y-m-d H:i:s'),
                    'invoice_emails'    => 1,
                    'estimate_emails'   => 0,
                    'credit_note_emails' => 0,
                    'contract_emails'   => 0,
                    'task_emails'       => 0,
                    'project_emails'    => 0,
                    'ticket_emails'     => 1
                ];
                
                $this->db->insert('tblcontacts', $contact_data);
                
                log_activity($this->log_prefix . ' - Created new client account [Client ID: ' . $real_client_id . ', Student ID: ' . $student_id . ']');
            }
            
            // Update student record with client_id
            $this->db->where('id', $student_id);
            $this->db->update(db_prefix() . 'sls_students', [
                'client_id' => $real_client_id,
                'client_created_at' => date('Y-m-d H:i:s')
            ]);
            
            // Send credentials email
            $email_sent = $this->_send_credentials_email($student, $password, $real_client_id);
            
            return [
                'success' => true,
                'client_id' => $real_client_id,
                'email_sent' => $email_sent,
                'message' => 'Client account created successfully'
            ];
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Create Real Client Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Send login credentials email to customer
     */
 private function _send_credentials_email($student, $password, $real_client_id)
{
    try {
        $this->load->library('email');
        
        $company_name = get_option('companyname');
        $login_url = site_url('authentication/login');
        
        $subject = 'Your Login Credentials - Safe Legal Solutions';
        
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
                    <h1>Welcome to Safe Legal Solutions</h1>
                </div>
                
                <div class="content">
                    <h2>Hello ' . htmlspecialchars($student->student_name) . ',</h2>
                    
                    <p>Congratulations! Your registration and payment have been processed successfully. We have created a client portal account for you.</p>
                    
                    <p>You can now access your personal dashboard to:</p>
                    <ul>
                        <li>View your package details and progress</li>
                        <li>Access important documents and resources</li>
                        <li>Track your referral earnings</li>
                        <li>Communicate with our support team</li>
                    </ul>
                    
                    <div class="credentials">
                        <h3>Your Login Credentials:</h3>
                        <p><strong>Portal URL:</strong> <a href="' . $login_url . '">' . $login_url . '</a></p>
                        <p><strong>Email/Username:</strong> ' . htmlspecialchars($student->email) . '</p>
                        <p><strong>Password:</strong> <code style="background: #f0f0f0; padding: 5px 10px; border-radius: 3px; font-size: 14px;">' . htmlspecialchars($password) . '</code></p>
                        <p><strong>Student ID:</strong> ' . htmlspecialchars($student->unique_id) . '</p>
                        <p><strong>Referral Code:</strong> ' . htmlspecialchars($student->referral_code) . '</p>
                    </div>
                    
                    <div class="warning">
                        <strong>⚠️ Security Notice:</strong> Please change your password after your first login for security purposes.
                    </div>
                    
                    <center>
                        <a href="' . $login_url . '" class="button">Login to Your Portal</a>
                    </center>
                    
                    <p style="margin-top: 30px;">If you have any questions or need assistance, please don\'t hesitate to contact us.</p>
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
        
        $sent = $this->email->send();
        
        if ($sent) {
            log_activity($this->log_prefix . ' - ✓ Credentials email sent [Email: ' . $student->email . ', Client ID: ' . $real_client_id . ']');
        } else {
            log_activity($this->log_prefix . ' - ✗ Failed to send credentials email [Email: ' . $student->email . ', Client ID: ' . $real_client_id . ']');
        }
        
        return $sent;
        
    } catch (Exception $e) {
        log_activity($this->log_prefix . ' - Send Email Error: ' . $e->getMessage());
        return false;
    }
}
    /**
     * Create package enrollment record
     */
    private function _create_package_enrollment($student_id, $student, $item)
    {
        try {
            // Calculate enrollment dates
            $enrollment_date = date('Y-m-d');
            $start_date = date('Y-m-d');
            
            // Calculate end date based on package duration
            $duration_months = $item->duration_months ?? 12;
            $end_date = date('Y-m-d', strtotime('+' . $duration_months . ' months'));
            
            // Check if enrollment already exists
            $existing = $this->db->get_where(db_prefix() . 'sls_package_enrollments', [
                'student_id' => $student_id
            ])->row();
            
            if ($existing) {
                log_activity($this->log_prefix . ' - Package enrollment already exists [Student ID: ' . $student_id . ', Enrollment ID: ' . $existing->id . ']');
                return $existing->id;
            }
            
            // Create enrollment
            $enrollment_data = [
                'student_id' => $student_id,
                'item_id' => $item->id,
                'enrollment_date' => $enrollment_date,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'duration_months' => $duration_months,
                'total_amount' => $item->total_price,
                'amount_paid' => $student->amount_paid,
                'payment_status' => 'paid',
                'enrollment_status' => 'active',
                'completion_percentage' => 0,
                'notes' => 'Package enrollment created after successful payment',
                'created_by' => $student->created_by ?? 1,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $this->db->insert(db_prefix() . 'sls_package_enrollments', $enrollment_data);
            $enrollment_id = $this->db->insert_id();
            
            if ($enrollment_id) {
                log_activity($this->log_prefix . ' - ✓ Package enrollment created [Enrollment ID: ' . $enrollment_id . ', Student ID: ' . $student_id . ', Package: ' . $item->item_name . ']');
            }
            
            return $enrollment_id;
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Create Enrollment Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * PAYMENT FAILURE CALLBACK
     * Delete student record and temporary client
     */
    public function payment_failure()
    {
        try {
            $student_id = $this->session->userdata('temp_student_id');
            
            if (!$student_id) {
                set_alert('warning', 'Session expired. Please start registration again.');
                redirect('safelegalsolutions/safelegalsolutions_client/register');
                return;
            }
            
            $student = $this->safelegalsolutions_client_model->get_student($student_id);
            
            if ($student) {
                $this->_handle_payment_failure($student_id, $student, 'Payment cancelled by user');
            }
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Payment Failure Handler Error: ' . $e->getMessage());
            set_alert('danger', 'An error occurred. Please try again or contact support.');
            redirect('safelegalsolutions/safelegalsolutions_client/register');
        }
    }
    
    /**
     * Handle payment failure - Delete student AND temporary client
     */
    private function _handle_payment_failure($student_id, $student, $reason)
    {
        try {
            $this->db->trans_start();
            
            log_activity($this->log_prefix . ' - ✗ Payment failed [Student ID: ' . $student_id . ', Reason: ' . $reason . ']');
            
            // Delete temporary client if exists
            if ($student && !empty($student->client_id)) {
                // Check if this is truly a temporary client (inactive)
                $client = $this->db->get_where('tblclients', ['userid' => $student->client_id])->row();
                
                if ($client && $client->active == 0) {
                    // Delete contacts
                    $this->db->delete('tblcontacts', ['userid' => $student->client_id]);
                    
                    // Delete client
                    $this->db->delete('tblclients', ['userid' => $student->client_id]);
                    
                    log_activity($this->log_prefix . ' - Deleted temporary client [Client ID: ' . $student->client_id . ']');
                }
            }
            
            // Delete student record
            $this->db->delete(db_prefix() . 'sls_students', ['id' => $student_id]);
            
            log_activity($this->log_prefix . ' - Deleted student record [Student ID: ' . $student_id . ']');
            
            $this->db->trans_complete();
            
            // Clear session
            $this->session->unset_userdata(['temp_student_id', 'registration_data']);
            
            set_alert('warning', 'Payment was not successful. Your registration has been cancelled. Please try again.');
            redirect('safelegalsolutions/safelegalsolutions_client/register');
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_activity($this->log_prefix . ' - Handle Payment Failure Error: ' . $e->getMessage());
            set_alert('danger', 'An error occurred. Please contact support with Student ID: ' . $student_id);
            redirect('safelegalsolutions/safelegalsolutions_client/register');
        }
    }
    
    /**
     * Extract student ID from invoice admin note
     */
    private function _extract_student_id_from_admin_note($note)
    {
        if (preg_match('/Student ID: (\d+)/', $note, $matches)) {
            return (int)$matches[1];
        }
        return null;
    }
    
    // ================================================================
    // STEP 3: SUCCESS PAGE
    // ================================================================
    
    public function success($student_id = 0)
    {
        try {
            if (empty($student_id)) {
                redirect('safelegalsolutions/safelegalsolutions_client/register');
                return;
            }
            
            $student = $this->_get_student_or_redirect($student_id);
            
            if (!$student) {
                return;
            }
            
            $branch = $this->_get_branch_safe($student->branch_id);
            $item = $this->_get_item_safe($student->item_id);
            
            $this->_display_success_page($student, $branch, $item);
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Success Page Error: ' . $e->getMessage());
            redirect('safelegalsolutions/safelegalsolutions_client/register');
        }
    }

    // ================================================================
    // REGISTRATION STEP 1: FORM PROCESSING
    // ================================================================
    
    private function _process_registration_step1($branch, $token, $items)
    {
        try {
            $post_data = $this->input->post();
            
            $this->_set_registration_validation_rules();
            
            if ($this->form_validation->run() === FALSE) {
                $this->_display_registration_form($branch, $token, $items, $post_data);
                return;
            }
            
            $this->_store_registration_data($post_data, $branch->id, $token);
            redirect('safelegalsolutions/safelegalsolutions_client/review');
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Step 1 Processing Error: ' . $e->getMessage());
            $this->_display_registration_form($branch, $token, $items, $post_data, 
                'An error occurred while processing your registration. Please try again.');
        }
    }
    
    private function _set_registration_validation_rules()
    {
        $this->form_validation->set_rules('student_name', 'Full Name', [
            'required',
            'trim',
            'max_length[255]',
            'regex_match[/^[a-zA-Z\s]+$/]'
        ], [
            'regex_match' => 'The {field} must contain only letters and spaces.'
        ]);
        
        $this->form_validation->set_rules('email', 'Email Address', [
            'required',
            'trim',
            'valid_email',
            'max_length[100]',
            'callback_check_duplicate_email'
        ]);
        
        $this->form_validation->set_rules('phone', 'Phone Number', [
            'required',
            'trim',
            'max_length[20]',
            'regex_match[/^[0-9+\-\s()]+$/]'
        ], [
            'regex_match' => 'The {field} must contain only numbers and valid phone characters.'
        ]);
        
        $this->form_validation->set_rules('address', 'Address', [
            'required',
            'trim',
            'min_length[10]'
        ]);
        
        $this->form_validation->set_rules('date_of_birth', 'Date of Birth', [
            'required',
            'callback_validate_age'
        ]);
        
        $this->form_validation->set_rules('passport_number', 'Passport Number', [
            'required',
            'trim',
            'min_length[6]',
            'max_length[50]',
            'regex_match[/^[A-Z0-9]+$/]'
        ], [
            'regex_match' => 'The {field} must contain only uppercase letters and numbers.'
        ]);

        // ========== ADD THESE NEW RULES ==========

$this->form_validation->set_rules('passport_expiry_date', 'Passport Expiry Date', [
    'required',
    'callback_validate_future_date'
]);

$this->form_validation->set_rules('destination_country_id', 'Destination Country', [
    'required',
    'numeric'
]);

$this->form_validation->set_rules('university_name', 'University/Institution Name', [
    'required',
    'trim',
    'max_length[255]'
]);

$this->form_validation->set_rules('course_program', 'Course/Program', [
    'required',
    'trim',
    'max_length[255]'
]);

$this->form_validation->set_rules('city', 'City', [
    'required',
    'trim',
    'max_length[100]'
]);

$this->form_validation->set_rules('state', 'State/Province', [
    'required',
    'trim',
    'max_length[100]'
]);

$this->form_validation->set_rules('pin_code', 'PIN Code', [
    'required',
    'trim',
    'max_length[20]',
    'regex_match[/^[0-9]{5,10}$/]'
], [
    'regex_match' => 'The {field} must be 5-10 digits.'
]);

$this->form_validation->set_rules('emergency_contact_mobile', 'Emergency Contact Mobile', [
    'required',
    'trim',
    'max_length[20]',
    'regex_match[/^[0-9+\-\s()]+$/]'
], [
    'regex_match' => 'The {field} must contain only numbers and valid phone characters.'
]);

$this->form_validation->set_rules('consent_given', 'Data Processing Consent', [
    'required'
]);
        
        $this->form_validation->set_rules('item_id', 'Package Selection', [
            'required',
            'numeric',
            'callback_validate_item'
        ]);
    }
    
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
            // ========== ADD THESE NEW FIELDS ==========
            'passport_expiry_date'       => $post_data['passport_expiry_date'],
            'destination_country_id'     => $post_data['destination_country_id'],
            'university_name'            => trim($post_data['university_name']),
            'course_program'             => trim($post_data['course_program']),
            'city'                       => trim($post_data['city']),
            'state'                      => trim($post_data['state']),
            'pin_code'                   => trim($post_data['pin_code']),
            'emergency_contact_mobile'   => trim($post_data['emergency_contact_mobile']),
            'consent_given'              => $post_data['consent_given'] == '1' ? 1 : 0,
            'consent_given_at'           => $post_data['consent_given'] == '1' ? date('Y-m-d H:i:s') : null,
            // ==========================================
            'item_id'         => $post_data['item_id'],
            'status'          => 'draft'
        ];
        
        $this->session->set_userdata('registration_data', $session_data);
        
        log_activity($this->log_prefix . ' - Registration data stored in session for: ' . $post_data['email']);
    }

    // ================================================================
    // VALIDATION CALLBACKS
    // ================================================================
    /**
 * Validate future date (passport expiry)
 * @param string $date Date string
 * @return bool
 */
public function validate_future_date($date)
{
    if (empty($date)) {
        $this->form_validation->set_message('validate_future_date', 'The {field} is required.');
        return false;
    }
    
    try {
        $expiry_date = new DateTime($date);
        $today = new DateTime();
        
        if ($expiry_date <= $today) {
            $this->form_validation->set_message('validate_future_date', 
                'The {field} must be a future date. Your passport appears to be expired.');
            return false;
        }
        
        return true;
        
    } catch (Exception $e) {
        log_activity($this->log_prefix . ' - Future Date Validation Error: ' . $e->getMessage());
        $this->form_validation->set_message('validate_future_date', 'Invalid date format.');
        return false;
    }
}
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
    
    public function validate_age($date_of_birth)
    {
        if (empty($date_of_birth)) {
            $this->form_validation->set_message('validate_age', 'Date of birth is required.');
            return false;
        }
        
        try {
            $dob = new DateTime($date_of_birth);
            $today = new DateTime();
            
            if ($dob > $today) {
                $this->form_validation->set_message('validate_age', 'Date of birth cannot be in the future.');
                return false;
            }
            
            $age = $today->diff($dob)->y;
            
            if ($age < 18) {
                $this->form_validation->set_message('validate_age', 
                    'You must be at least 18 years old to register. Current age: ' . $age . ' years.');
                return false;
            }
            
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
    // CLIENT PORTAL METHODS
    // ================================================================
    
    public function my_dashboard()
    {
        try {
            if (!is_client_logged_in()) {
                redirect(site_url('authentication/login'));
                return;
            }
            
            $contact = $this->clients_model->get_contact(get_contact_user_id());
            
            if (!$contact) {
                show_404();
                return;
            }
            
            $student = $this->safelegalsolutions_client_model->get_student_by_client_id($contact->userid);
            
            if (!$student) {
                $this->_show_client_no_record('Dashboard');
                return;
            }
            
            $branch = $this->_get_branch_safe($student->branch_id);
            $item = $this->_get_item_safe($student->item_id);
            $enrollment = $this->_get_enrollment_safe($student->id);
            
            $days_since_registration = $this->_calculate_days_since($student->created_at);
            $days_until_end = $this->_calculate_days_until_end($enrollment);
            
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
    
  public function my_profile()
{
    try {
        // Ensure client is logged in
        if (!is_client_logged_in()) {
            redirect('authentication/login');
            return;
        }
        
        $client_id = get_client_user_id();
        
        // Get student record linked to this client
        $student = $this->safelegalsolutions_client_model->get_student_by_client_id($client_id);
        
        if (!$student) {
            $this->_show_client_no_record('My Profile');
            return;
        }
        
        // Get the item/package details
        $item = null;
        if (!empty($student->item_id)) {
            $item = $this->safelegalsolutions_client_model->get_item($student->item_id);
        }
        
        // Get countries for destination dropdown
        $countries = $this->_get_countries();
        
        // Prepare data for view
        $data = [
            'title'     => 'My Profile',
            'student'   => $student,
            'item'      => $item,
            'countries' => $countries
        ];
        
        // Use Perfex's standard client layout with EXISTING VIEW
        $this->data($data);
        $this->view('safelegalsolutions/client/my_profile');  // Uses your existing my_profile.php
        $this->layout();
        
    } catch (Exception $e) {
        log_activity($this->log_prefix . ' - My Profile Error: ' . $e->getMessage());
        $this->_show_client_error(
            'Profile Error',
            'Failed to load your profile. Please try again or contact support.'
        );
    }
}

/**
 * Update Profile - Handle form submission
 * Validates, checks lock status, updates student record, recalculates profile completion
 */
public function update_profile()
{
    try {
        // Ensure client is logged in
        if (!is_client_logged_in()) {
            set_alert('danger', 'You must be logged in to update your profile.');
            redirect('authentication/login');
            return;
        }
        
        // Check if it's a POST request
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            redirect('safelegalsolutions/safelegalsolutions_client/my_profile');
            return;
        }
        
        $client_id = get_client_user_id();
        
        // Get student record
        $student = $this->safelegalsolutions_client_model->get_student_by_client_id($client_id);
        
        if (!$student) {
            set_alert('danger', 'Student record not found.');
            redirect('clients');
            return;
        }
        
        // CRITICAL: Check if profile is locked
        if ($student->is_locked == 1) {
            set_alert('danger', 'Your profile is locked and cannot be edited. Please contact support for assistance.');
            redirect('safelegalsolutions/safelegalsolutions_client/my_profile');
            return;
        }
        
        // Validate form inputs
        $validation_result = $this->_validate_profile_update();
        
        if (!$validation_result) {
            // Validation failed, redirect back with errors
            redirect('safelegalsolutions/safelegalsolutions_client/my_profile');
            return;
        }
        
        // Prepare update data (EXCLUDE email and payment fields)
        $update_data = $this->_prepare_profile_update_data();
        
        // Update student record
        $updated = $this->safelegalsolutions_client_model->update_student($student->id, $update_data);
        
        if ($updated) {
            // Recalculate profile completion
            $this->_recalculate_profile_completion($student->id);
            
            // Log activity
            log_activity(sprintf(
                'Student Profile Updated [ID: %d, Name: %s] by client [ID: %d]',
                $student->id,
                $student->student_name,
                $client_id
            ));
            
            set_alert('success', 'Your profile has been updated successfully!');
        } else {
            set_alert('warning', 'No changes were made to your profile.');
        }
        
        redirect('safelegalsolutions/safelegalsolutions_client/my_profile');
        
    } catch (Exception $e) {
        log_activity($this->log_prefix . ' - Update Profile Error: ' . $e->getMessage());
        set_alert('danger', 'An error occurred while updating your profile. Please try again.');
        redirect('safelegalsolutions/safelegalsolutions_client/my_profile');
    }
}

/**
 * Validate Profile Update Form
 * @return bool
 */
private function _validate_profile_update()
{
    try {
        // Required fields validation
        $this->form_validation->set_rules('student_name', 'Full Name', 'required|max_length[255]');
        $this->form_validation->set_rules('phone', 'Phone Number', 'required|max_length[20]');
        $this->form_validation->set_rules('date_of_birth', 'Date of Birth', 'required');
        $this->form_validation->set_rules('address', 'Address', 'required');
        $this->form_validation->set_rules('passport_number', 'Passport Number', 'required|max_length[50]');
        
        // Optional fields with validation
        $this->form_validation->set_rules('whatsapp_number', 'WhatsApp Number', 'max_length[20]');
        $this->form_validation->set_rules('skype_teams_id', 'Skype/Teams ID', 'max_length[100]');
        $this->form_validation->set_rules('aadhar_number', 'Aadhar Number', 'max_length[20]');
        $this->form_validation->set_rules('pan_number', 'PAN Number', 'max_length[20]');
        
        // Email validation (even though readonly, validate if submitted)
        $this->form_validation->set_rules('email', 'Email', 'valid_email');
        
        if ($this->form_validation->run() === FALSE) {
            return false;
        }
        
        return true;
        
    } catch (Exception $e) {
        log_activity($this->log_prefix . ' - Validation Error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Prepare profile update data from POST
 * EXCLUDES: email, payment fields, client_id, and other admin-only fields
 * @return array
 */
private function _prepare_profile_update_data()
{
    $data = [];
    
    // ========== PERSONAL INFORMATION ==========
    $data['student_name'] = $this->input->post('student_name', true);
    $data['phone'] = $this->input->post('phone', true);
    $data['whatsapp_number'] = $this->input->post('whatsapp_number', true);
    $data['date_of_birth'] = $this->input->post('date_of_birth', true);
    $data['gender'] = $this->input->post('gender', true);
    $data['address'] = $this->input->post('address', true);
    $data['address_line2'] = $this->input->post('address_line2', true);
    $data['city'] = $this->input->post('city', true);
    $data['state'] = $this->input->post('state', true);
    $data['pin_code'] = $this->input->post('pin_code', true);
    $data['nationality'] = $this->input->post('nationality', true);
    $data['place_of_birth'] = $this->input->post('place_of_birth', true);
    $data['religion'] = $this->input->post('religion', true);
    $data['languages_spoken'] = $this->input->post('languages_spoken', true);
    $data['skype_teams_id'] = $this->input->post('skype_teams_id', true);
    
    // ========== PASSPORT & ID ==========
    $data['passport_number'] = strtoupper($this->input->post('passport_number', true));
    $data['passport_expiry_date'] = $this->input->post('passport_expiry_date', true);
    $data['aadhar_number'] = $this->input->post('aadhar_number', true);
    $data['pan_number'] = strtoupper($this->input->post('pan_number', true));
    
    // ========== EMERGENCY CONTACTS ==========
    // Primary Guardian
    $data['primary_contact_name'] = $this->input->post('primary_contact_name', true);
    $data['primary_contact_relationship'] = $this->input->post('primary_contact_relationship', true);
    $data['primary_contact_mobile'] = $this->input->post('primary_contact_mobile', true);
    $data['primary_contact_email'] = $this->input->post('primary_contact_email', true);
    $data['primary_contact_occupation'] = $this->input->post('primary_contact_occupation', true);
    $data['primary_contact_annual_income'] = $this->input->post('primary_contact_annual_income', true);
    
    // Secondary Guardian
    $data['secondary_contact_name'] = $this->input->post('secondary_contact_name', true);
    $data['secondary_contact_relationship'] = $this->input->post('secondary_contact_relationship', true);
    $data['secondary_contact_mobile'] = $this->input->post('secondary_contact_mobile', true);
    $data['secondary_contact_email'] = $this->input->post('secondary_contact_email', true);
    $data['secondary_contact_occupation'] = $this->input->post('secondary_contact_occupation', true);
    
    // Emergency Contact
    $data['emergency_contact_name'] = $this->input->post('emergency_contact_name', true);
    $data['emergency_contact_relationship'] = $this->input->post('emergency_contact_relationship', true);
    $data['emergency_contact_mobile'] = $this->input->post('emergency_contact_mobile', true);
    $data['emergency_contact_address'] = $this->input->post('emergency_contact_address', true);
    
    // ========== EDUCATION BACKGROUND ==========
    $data['highest_qualification'] = $this->input->post('highest_qualification', true);
    $data['institution_name'] = $this->input->post('institution_name', true);
    $data['year_of_completion'] = $this->input->post('year_of_completion', true);
    $data['percentage_cgpa'] = $this->input->post('percentage_cgpa', true);
    
    // ========== DESTINATION DETAILS ==========
    $data['destination_country_id'] = $this->input->post('destination_country_id', true);
    $data['university_name'] = $this->input->post('university_name', true);
    $data['course_program'] = $this->input->post('course_program', true);
    $data['city_destination'] = $this->input->post('city_destination', true);
    $data['state_destination'] = $this->input->post('state_destination', true);
    $data['course_duration'] = $this->input->post('course_duration', true);
    $data['start_date'] = $this->input->post('start_date', true);
    $data['expected_completion_date'] = $this->input->post('expected_completion_date', true);
    $data['student_id_number'] = $this->input->post('student_id_number', true);
    
    // ========== VISA INFORMATION ==========
    $data['visa_type'] = $this->input->post('visa_type', true);
    $data['visa_number'] = $this->input->post('visa_number', true);
    $data['visa_issue_date'] = $this->input->post('visa_issue_date', true);
    $data['visa_expiry_date'] = $this->input->post('visa_expiry_date', true);
    $data['visa_status'] = $this->input->post('visa_status', true);
    
    // ========== ACCOMMODATION ==========
    $data['accommodation_type'] = $this->input->post('accommodation_type', true);
    $data['accommodation_address'] = $this->input->post('accommodation_address', true);
    $data['accommodation_city'] = $this->input->post('accommodation_city', true);
    $data['accommodation_state'] = $this->input->post('accommodation_state', true);
    $data['accommodation_zip'] = $this->input->post('accommodation_zip', true);
    $data['accommodation_country'] = $this->input->post('accommodation_country', true);
    $data['local_phone_number'] = $this->input->post('local_phone_number', true);
    $data['landlord_name'] = $this->input->post('landlord_name', true);
    $data['landlord_contact'] = $this->input->post('landlord_contact', true);
    
    // Local Emergency Contacts
    $data['local_contact1_name'] = $this->input->post('local_contact1_name', true);
    $data['local_contact1_relationship'] = $this->input->post('local_contact1_relationship', true);
    $data['local_contact1_phone'] = $this->input->post('local_contact1_phone', true);
    $data['local_contact1_email'] = $this->input->post('local_contact1_email', true);
    
    $data['local_contact2_name'] = $this->input->post('local_contact2_name', true);
    $data['local_contact2_relationship'] = $this->input->post('local_contact2_relationship', true);
    $data['local_contact2_phone'] = $this->input->post('local_contact2_phone', true);
    $data['local_contact2_email'] = $this->input->post('local_contact2_email', true);
    

    // ========== ✅ FINANCIAL INFORMATION (NOW EDITABLE) ==========
    $data['total_course_fee'] = $this->input->post('total_course_fee', true);
    $data['funding_source'] = $this->input->post('funding_source', true);
    $data['loan_bank_name'] = $this->input->post('loan_bank_name', true);
    $data['loan_amount'] = $this->input->post('loan_amount', true);
    $data['loan_account_number'] = $this->input->post('loan_account_number', true);
    $data['loan_officer_contact'] = $this->input->post('loan_officer_contact', true);
    // ========== HEALTH & INSURANCE ==========
    $data['health_insurance_provider'] = $this->input->post('health_insurance_provider', true);
    $data['health_insurance_policy'] = $this->input->post('health_insurance_policy', true);
    $data['health_insurance_coverage'] = $this->input->post('health_insurance_coverage', true);
    $data['other_insurance'] = $this->input->post('other_insurance', true);
    $data['blood_group'] = $this->input->post('blood_group', true);
    $data['allergies_medical_conditions'] = $this->input->post('allergies_medical_conditions', true);
    $data['regular_medications'] = $this->input->post('regular_medications', true);
    $data['emergency_medical_contact'] = $this->input->post('emergency_medical_contact', true);
    
    // ========== ADDITIONAL INFORMATION ==========
    $data['previous_legal_issues'] = $this->input->post('previous_legal_issues', true);
    $data['legal_issues_details'] = $this->input->post('legal_issues_details', true);
    $data['special_dietary_requirements'] = $this->input->post('special_dietary_requirements', true);
    $data['other_special_needs'] = $this->input->post('other_special_needs', true);
    
    // Referral Information
    $data['referral_source'] = $this->input->post('referral_source', true);
    $data['referrer_name'] = $this->input->post('referrer_name', true);
    $data['referrer_organization'] = $this->input->post('referrer_organization', true);
    $data['referrer_contact'] = $this->input->post('referrer_contact', true);
    
    // Consent
    $data['consent_given'] = $this->input->post('consent_given') ? 1 : 0;
    if ($data['consent_given'] == 1 && empty($this->input->post('consent_given_at_original'))) {
        $data['consent_given_at'] = date('Y-m-d H:i:s');
    }
    
    // Clean up empty values
    foreach ($data as $key => $value) {
        if ($value === '' || $value === null) {
            $data[$key] = null;
        }
    }
    
    return $data;
}

/**
 * Recalculate profile completion percentage
 * @param int $student_id
 */
private function _recalculate_profile_completion($student_id)
{
    try {
        // Get fresh student data
        $student = $this->safelegalsolutions_client_model->get_student($student_id);
        
        if (!$student) {
            return;
        }
        
        // Convert student object to array for calculation
        $student_data = (array) $student;
        
        // Calculate profile completion
        $profile_completion = $this->safelegalsolutions_client_model->calculate_profile_completion($student_data);
        
        // Update profile completion in database
        $this->safelegalsolutions_client_model->update_student($student_id, [
            'profile_completion' => $profile_completion
        ]);
        
        log_activity(sprintf(
            'Profile completion recalculated for student [ID: %d]: %d%%',
            $student_id,
            $profile_completion
        ));
        
    } catch (Exception $e) {
        log_activity($this->log_prefix . ' - Recalculate Profile Completion Error: ' . $e->getMessage());
    }
}

// ================================================================
// END OF NEW METHODS TO ADD

    public function referral_card()
    {
        try {
            if (!is_client_logged_in()) {
                redirect(site_url('authentication/login'));
                return;
            }
            
            $contact = $this->clients_model->get_contact(get_contact_user_id());
            
            if (!$contact) {
                show_404();
                return;
            }
            
            $student = $this->safelegalsolutions_client_model->get_student_by_client_id($contact->userid);
            
            if (!$student) {
                $this->_show_client_no_record('Referral Card');
                return;
            }
            
            $branch = $this->_get_branch_safe($student->branch_id);
            
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
    // HELPER METHODS
    // ================================================================
    
    private function _get_and_validate_branch($token)
    {
        try {
            if (empty($token)) {
                $branch = $this->safelegalsolutions_client_model->get_default_branch();
                
                if (!$branch) {
                    $this->_show_error(
                        'Registration Unavailable',
                        'No registration branch is currently available. Please contact support.'
                    );
                    return false;
                }
            } else {
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
    
    private function _get_active_items()
    {
        try {
            return $this->safelegalsolutions_client_model->get_active_items();
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Get Active Items Error: ' . $e->getMessage());
            return false;
        }
    }
    
    private function _get_branch_safe($branch_id)
    {
        try {
            return $this->safelegalsolutions_client_model->get_branch($branch_id);
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Get Branch Error: ' . $e->getMessage());
            return null;
        }
    }
    
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
    
    private function _get_enrollment_safe($student_id)
    {
        try {
            if (empty($student_id)) {
                return null;
            }
            
            $enrollment = $this->db->get_where(db_prefix() . 'sls_package_enrollments', [
                'student_id' => $student_id
            ])->row();
            
            return $enrollment;
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Get Enrollment Error: ' . $e->getMessage());
            return null;
        }
    }
    
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
    
    private function _calculate_days_until_end($enrollment)
    {
        try {
            if (!$enrollment || empty($enrollment->end_date)) {
                return null;
            }
            
            $end_date = new DateTime($enrollment->end_date);
            $today = new DateTime();
            $days_until_end = $today->diff($end_date)->days;
            
            if ($today > $end_date) {
                $days_until_end = -$days_until_end;
            }
            
            return $days_until_end;
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - End Date Calculation Error: ' . $e->getMessage());
            return null;
        }
    }
    
    private function _generate_unique_id($passport_number)
    {
        try {
            $passport = strtoupper(trim($passport_number));
            
            if (empty($passport)) {
                log_activity($this->log_prefix . ' - WARNING: Empty passport for unique ID generation');
                return 'saflg-' . time();
            }
            
            $unique_id = $this->safelegalsolutions_client_model->generate_unique_id($passport);
            
            if (empty($unique_id)) {
                $unique_id = 'saflg-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            }
            
            return $unique_id;
            
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Unique ID Generation Error: ' . $e->getMessage());
            return 'saflg-' . time();
        }
    }
    
    private function _calculate_profile_completion($registration_data)
    {
        try {
            return $this->safelegalsolutions_client_model->calculate_profile_completion($registration_data);
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Profile Completion Calculation Error: ' . $e->getMessage());
            return 0;
        }
    }
    
    private function _generate_referral_code()
    {
        try {
            return $this->safelegalsolutions_client_model->generate_referral_code();
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Referral Code Generation Error: ' . $e->getMessage());
            return 'REF-' . time();
        }
    }

    // ================================================================
    // VIEW RENDERING METHODS
    // ================================================================
    
    private function _display_registration_form($branch, $token, $items, $form_data = [], $error = '', $countries = [])
    {
        $data = [
            'title'      => 'Student Registration - Safe Legal',
            'branch'     => $branch,
            'token'      => $token,
            'items'      => $items,
            'form_data'  => $form_data,
            'error'      => $error,
            'countries'  => $countries
        ];
        
        $this->load->view('safelegalsolutions/public_register', $data);
    }
    
    private function _display_review_page($registration_data, $branch, $item, $student_id)
    {
        $data = [
            'title'             => 'Review & Payment',
            'registration_data' => $registration_data,
            'branch'            => $branch,
            'item'              => $item,
            'student_id'        => $student_id,
            'token'             => $registration_data['token']
        ];
        
        $this->load->view('safelegalsolutions/public_review', $data);
    }
    
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
            'payment_percentage'      => $student->payment_percentage ?? 0,
            'client_account_created'  => $this->session->flashdata('client_account_created'),
            'client_id'               => $this->session->flashdata('client_id'),
            'email_sent'              => $this->session->flashdata('email_sent'),
            'credentials_sent'        => $this->session->flashdata('credentials_sent'),
            'client_error'            => $this->session->flashdata('client_error'),
            'payment_id'              => $this->session->flashdata('payment_id'),
            'has_client_account'      => !empty($student->client_id)
        ];
        
        $this->load->view('safelegalsolutions/public_success', $data);
    }
    
    private function _show_error($title, $message)
    {
        $data = [
            'title'   => $title,
            'error'   => $message
        ];
        
        $this->load->view('safelegalsolutions/public_error', $data);
    }
    
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
    private function _get_countries()
    {
        try {
            return $this->safelegalsolutions_client_model->get_all_countries(['is_active' => 1]);
        } catch (Exception $e) {
            log_activity($this->log_prefix . ' - Get Countries Error: ' . $e->getMessage());
            return [];
        }
    }


// docuemnt uplaod 
/**
 * Get my documents (AJAX) - For clients
 */
public function get_my_documents_ajax()
{
    $student = $this->safelegalsolutions_client_model->get_student_by_client(get_client_user_id());
    
    if (!$student) {
        echo json_encode(['success' => false, 'message' => 'Student profile not found']);
        return;
    }
    
    $documents = $this->safelegalsolutions_client_model->get_student_documents($student->id);
    
    echo json_encode([
        'success' => true,
        'documents' => $documents
    ]);
}

/**
 * Download my document - For clients
 */
public function download_my_document($id)
{
    $student = $this->safelegalsolutions_client_model->get_student_by_client(get_client_user_id());
    
    if (!$student) {
        access_denied('Documents');
    }
    
    // Verify document belongs to this student
    $doc = $this->safelegalsolutions_client_model->get_student_document($id, $student->id);
    
    if (!$doc) {
        show_404();
    }
    
    header('Content-Type: ' . $doc->file_type);
    header('Content-Disposition: attachment; filename="' . $doc->file_name . '"');
    header('Content-Length: ' . $doc->file_size);
    
    echo $doc->file_data;
    exit;
}
}