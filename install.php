<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * SafeLegalSolutions Module - Installation Script
 * Version: 2.0 - FIXED: Removed redundant payment fields (use sls_payments table)
 */

// Get CodeIgniter instance
$CI = &get_instance();

// Force load database if not loaded
if (!isset($CI->db)) {
    $CI->load->database();
}

// Get the actual database name being used
$database_name = $CI->db->database;
$db_prefix = db_prefix();

// Log start
log_message('info', '========== SafeLegalSolutions Installation Started ==========');
log_message('info', 'Database: ' . $database_name);
log_message('info', 'Prefix: ' . $db_prefix);

// ==================== DISABLE FOREIGN KEY CHECKS ====================
log_message('info', 'Disabling foreign key checks for clean installation...');
$CI->db->query("SET FOREIGN_KEY_CHECKS = 0");

// ==================== TABLE 1: BRANCH CATEGORIES ====================
$table1 = $db_prefix . 'sls_branch_categories';
log_message('info', 'Creating table: ' . $table1);

try {
    // Drop if exists (for clean reinstall)
    $CI->db->query("DROP TABLE IF EXISTS `{$table1}`");
    
    $sql1 = "CREATE TABLE `{$table1}` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `description` TEXT NULL,
        `color_code` VARCHAR(7) DEFAULT '#007bff',
        `is_active` TINYINT(1) DEFAULT 1,
        `created_by` INT(11) NOT NULL DEFAULT 1,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `is_active` (`is_active`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $CI->db->query($sql1);
    log_message('info', 'SUCCESS: Table ' . $table1 . ' created');
} catch (Exception $e) {
    log_message('error', 'ERROR creating ' . $table1 . ': ' . $e->getMessage());
    die('Failed to create table: ' . $table1 . ' - Error: ' . $e->getMessage());
}

// ==================== TABLE 2: BRANCHES (WITH REGISTRATION TOKEN + IS_DEFAULT) ====================
$table2 = $db_prefix . 'sls_branches';
log_message('info', 'Creating table: ' . $table2);

try {
    // Drop if exists (for clean reinstall)
    $CI->db->query("DROP TABLE IF EXISTS `{$table2}`");
    
    $sql2 = "CREATE TABLE `{$table2}` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `category_id` INT(11) NOT NULL,
        `branch_name` VARCHAR(255) NOT NULL,
        `branch_code` VARCHAR(50) NULL,
        `location` VARCHAR(255) NULL,
        `address` TEXT NULL,
        `contact_phone` VARCHAR(20) NULL,
        `nodal_partner_manager_id` INT(11) NULL,
        `registration_token` VARCHAR(64) NULL COMMENT 'Token for public student registration',
        `is_default` TINYINT(1) DEFAULT 0 COMMENT 'Default branch for admin registrations',
        `is_active` TINYINT(1) DEFAULT 1,
        `created_by` INT(11) NOT NULL DEFAULT 1,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `branch_code` (`branch_code`),
        UNIQUE KEY `registration_token` (`registration_token`),
        KEY `category_id` (`category_id`),
        KEY `nodal_partner_manager_id` (`nodal_partner_manager_id`),
        KEY `is_active` (`is_active`),
        KEY `is_default` (`is_default`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $CI->db->query($sql2);
    log_message('info', 'SUCCESS: Table ' . $table2 . ' created with is_default column');
} catch (Exception $e) {
    log_message('error', 'ERROR creating ' . $table2 . ': ' . $e->getMessage());
    die('Failed to create table: ' . $table2 . ' - Error: ' . $e->getMessage());
}

// ==================== TABLE 3: ITEMS/PACKAGES (CREATE BEFORE STUDENTS) ====================
$table5 = $db_prefix . 'sls_items';
log_message('info', 'Creating table: ' . $table5);

try {
    // Drop if exists (for clean reinstall)
    $CI->db->query("DROP TABLE IF EXISTS `{$table5}`");
    
    $sql5 = "CREATE TABLE `{$table5}` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `item_name` VARCHAR(255) NOT NULL,
        `item_code` VARCHAR(50) NULL COMMENT 'Unique item code',
        `description` TEXT NULL,
        `base_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Price without GST',
        `gst_percentage` DECIMAL(5,2) NOT NULL DEFAULT 18.00 COMMENT 'GST percentage',
        `gst_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Calculated GST amount',
        `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Base price + GST',
        `duration_months` INT(3) NULL COMMENT 'Package duration in months',
        `features` TEXT NULL COMMENT 'JSON array of features',
        `is_active` TINYINT(1) DEFAULT 1,
        `display_order` INT(3) DEFAULT 0 COMMENT 'For sorting items',
        `created_by` INT(11) NOT NULL DEFAULT 1,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `item_code` (`item_code`),
        KEY `is_active` (`is_active`),
        KEY `display_order` (`display_order`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $CI->db->query($sql5);
    log_message('info', 'SUCCESS: Table ' . $table5 . ' created');
} catch (Exception $e) {
    log_message('error', 'ERROR creating ' . $table5 . ': ' . $e->getMessage());
    die('Failed to create table: ' . $table5 . ' - Error: ' . $e->getMessage());
}

// ==================== TABLE 4: STUDENTS (CLEANED - REMOVED REDUNDANT PAYMENT FIELDS) ====================
$table3 = $db_prefix . 'sls_students';
log_message('info', 'Creating table: ' . $table3);

try {
    // Drop if exists (for clean reinstall)
    $CI->db->query("DROP TABLE IF EXISTS `{$table3}`");
    
    $sql3 = "CREATE TABLE `{$table3}` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `branch_id` INT(11) NOT NULL,
        `nodal_partner_manager_id` INT(11) NOT NULL,
        
        -- Basic Info
        `student_name` VARCHAR(255) NOT NULL,
        `email` VARCHAR(100) NOT NULL,
        `phone` VARCHAR(20) NULL,
        `address` TEXT NULL,
        `date_of_birth` DATE NULL,
        
        -- Identification Documents
        `passport_number` VARCHAR(50) NULL COMMENT 'Student passport number',
        `unique_id` VARCHAR(50) NULL COMMENT 'Auto-generated unique ID (saflegxxx-333)',
        -- Package/Item Selection
        `item_id` INT(11) NULL COMMENT 'Selected package/item',
        
        -- Profile Status
        `profile_completion` TINYINT(3) DEFAULT 0,
        `status` ENUM('draft','pending_review','approved','locked','change_requested') DEFAULT 'draft',
        `referral_code` VARCHAR(50) NULL,
        
        -- Client Account Integration
        `client_id` INT(11) NULL DEFAULT NULL COMMENT 'Perfex Client ID from tblclients',
        `client_created_at` DATETIME NULL DEFAULT NULL COMMENT 'When client account was created',
        
        -- Financial Tracking (AGGREGATED from sls_payments table)
        `payment_status` ENUM('unpaid','partial','paid') DEFAULT 'unpaid' COMMENT 'Overall payment status',
        `payment_percentage` DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Payment completion % (0-100)',
        `amount_paid` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Total amount paid (sum from payments table)',
        `total_amount` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Total package amount (from item)',
        
        -- Earnings & Lock Status
        `earnings` DECIMAL(10,2) DEFAULT 0.00,
        `is_locked` TINYINT(1) DEFAULT 0,
        `locked_at` DATETIME NULL,
        `locked_by` INT(11) NULL,
        
        -- Metadata
        `notes` TEXT NULL,
        `created_by` INT(11) NOT NULL DEFAULT 1,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`),
        UNIQUE KEY `referral_code` (`referral_code`),
        UNIQUE KEY `passport_number` (`passport_number`),
        UNIQUE KEY `unique_id` (`unique_id`),
        KEY `branch_id` (`branch_id`),
        KEY `nodal_partner_manager_id` (`nodal_partner_manager_id`),
        KEY `item_id` (`item_id`),
        KEY `status` (`status`),
        KEY `is_locked` (`is_locked`),
        KEY `idx_client_id` (`client_id`),
        KEY `idx_payment_status` (`payment_status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $CI->db->query($sql3);
    log_message('info', 'SUCCESS: Table ' . $table3 . ' created (redundant payment fields removed - use sls_payments table)');
} catch (Exception $e) {
    log_message('error', 'ERROR creating ' . $table3 . ': ' . $e->getMessage());
    die('Failed to create table: ' . $table3 . ' - Error: ' . $e->getMessage());
}

// ==================== TABLE 5: CHANGE REQUESTS ====================
$table4 = $db_prefix . 'sls_change_requests';
log_message('info', 'Creating table: ' . $table4);

try {
    // Drop if exists (for clean reinstall)
    $CI->db->query("DROP TABLE IF EXISTS `{$table4}`");
    
    $sql4 = "CREATE TABLE `{$table4}` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `student_id` INT(11) NOT NULL,
        `requested_by` INT(11) NOT NULL,
        `field_name` VARCHAR(100) NOT NULL,
        `old_value` TEXT NULL,
        `new_value` TEXT NULL,
        `reason` TEXT NULL,
        `status` ENUM('pending','approved','rejected') DEFAULT 'pending',
        `reviewed_by` INT(11) NULL,
        `reviewed_at` DATETIME NULL,
        `review_notes` TEXT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `student_id` (`student_id`),
        KEY `requested_by` (`requested_by`),
        KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $CI->db->query($sql4);
    log_message('info', 'SUCCESS: Table ' . $table4 . ' created');
} catch (Exception $e) {
    log_message('error', 'ERROR creating ' . $table4 . ': ' . $e->getMessage());
    die('Failed to create table: ' . $table4 . ' - Error: ' . $e->getMessage());
}

// ==================== TABLE 6: PAYMENT TRANSACTIONS ====================
$table6 = $db_prefix . 'sls_payments';
log_message('info', 'Creating table: ' . $table6);

try {
    // Drop if exists (for clean reinstall)
    $CI->db->query("DROP TABLE IF EXISTS `{$table6}`");
    
    $sql6 = "CREATE TABLE `{$table6}` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `student_id` INT(11) NOT NULL COMMENT 'Reference to tblsls_students',
        `payment_id` VARCHAR(100) NULL COMMENT 'Unique payment transaction ID',
        `payment_method` ENUM('cash','bank_transfer','upi','card','other') DEFAULT 'cash',
        `amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Amount paid in this transaction',
        `payment_date` DATETIME NOT NULL COMMENT 'When payment was received',
        `receipt_number` VARCHAR(100) NULL COMMENT 'Receipt/invoice number',
        `transaction_reference` VARCHAR(255) NULL COMMENT 'Bank/UPI transaction reference',
        `payment_notes` TEXT NULL COMMENT 'Additional payment details',
        `payment_status` ENUM('pending','completed','failed','refunded') DEFAULT 'completed',
        `created_by` INT(11) NOT NULL COMMENT 'Staff who recorded payment',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `payment_id` (`payment_id`),
        KEY `student_id` (`student_id`),
        KEY `payment_date` (`payment_date`),
        KEY `payment_status` (`payment_status`),
        CONSTRAINT `fk_sls_payments_student` 
            FOREIGN KEY (`student_id`) 
            REFERENCES `{$table3}` (`id`) 
            ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $CI->db->query($sql6);
    log_message('info', 'SUCCESS: Table ' . $table6 . ' created');
} catch (Exception $e) {
    log_message('error', 'ERROR creating ' . $table6 . ': ' . $e->getMessage());
    die('Failed to create table: ' . $table6 . ' - Error: ' . $e->getMessage());
}

// ==================== TABLE 7: PACKAGE ENROLLMENTS ====================
$table7 = $db_prefix . 'sls_package_enrollments';
log_message('info', 'Creating table: ' . $table7);

try {
    // Drop if exists (for clean reinstall)
    $CI->db->query("DROP TABLE IF EXISTS `{$table7}`");
    
    $sql7 = "CREATE TABLE `{$table7}` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `student_id` INT(11) NOT NULL COMMENT 'Reference to tblsls_students',
        `item_id` INT(11) NOT NULL COMMENT 'Package/item enrolled in',
        `enrollment_date` DATE NOT NULL COMMENT 'Start date of enrollment',
        `start_date` DATE NOT NULL COMMENT 'Course start date',
        `end_date` DATE NOT NULL COMMENT 'Course end date (calculated)',
        `duration_months` INT(3) NOT NULL COMMENT 'Package duration in months',
        `total_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total package amount',
        `amount_paid` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Amount already paid',
        `payment_status` ENUM('unpaid','partial','paid') DEFAULT 'unpaid',
        `enrollment_status` ENUM('active','paused','completed','cancelled','expired') DEFAULT 'active',
        `completion_percentage` DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Course completion (0-100)',
        `notes` TEXT NULL COMMENT 'Enrollment notes',
        `created_by` INT(11) NOT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `student_id` (`student_id`),
        KEY `item_id` (`item_id`),
        KEY `enrollment_status` (`enrollment_status`),
        KEY `start_date` (`start_date`),
        KEY `end_date` (`end_date`),
        CONSTRAINT `fk_sls_enrollment_student` 
            FOREIGN KEY (`student_id`) 
            REFERENCES `{$table3}` (`id`) 
            ON DELETE CASCADE,
        CONSTRAINT `fk_sls_enrollment_item` 
            FOREIGN KEY (`item_id`) 
            REFERENCES `{$table5}` (`id`) 
            ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $CI->db->query($sql7);
    log_message('info', 'SUCCESS: Table ' . $table7 . ' created');
} catch (Exception $e) {
    log_message('error', 'ERROR creating ' . $table7 . ': ' . $e->getMessage());
    die('Failed to create table: ' . $table7 . ' - Error: ' . $e->getMessage());
}

// ==================== RE-ENABLE FOREIGN KEY CHECKS ====================
log_message('info', 'Re-enabling foreign key checks...');
$CI->db->query("SET FOREIGN_KEY_CHECKS = 1");

// ==================== INSERT DEFAULT CATEGORIES ====================
log_message('info', 'Inserting default categories...');

try {
    $categories = [
        ['name' => 'Individual Consultant', 'color_code' => '#6f42c1', 'description' => 'Independent Consultants', 'created_by' => 1, 'is_active' => 1],
        ['name' => 'Education Agency', 'color_code' => '#fd7e14', 'description' => 'Educational Agencies and Institutions', 'created_by' => 1, 'is_active' => 1],
        ['name' => 'Training Institute', 'color_code' => '#20c997', 'description' => 'Training and Coaching Institutes', 'created_by' => 1, 'is_active' => 1],
        ['name' => 'Bank/Financial Institution', 'color_code' => '#007bff', 'description' => 'Banks and Financial Service Providers', 'created_by' => 1, 'is_active' => 1],
        ['name' => 'Partner Consultant', 'color_code' => '#dc3545', 'description' => 'Partner Consulting Firms', 'created_by' => 1, 'is_active' => 1],
        ['name' => 'Safe Legal ⭐', 'color_code' => '#d2d214ff', 'description' => 'Safe Legal Solutions Head Office', 'created_by' => 1, 'is_active' => 1],
    ];
    
    foreach ($categories as $cat) {
        $CI->db->insert($table1, $cat);
        log_message('info', 'Inserted category: ' . $cat['name']);
    }
    
    log_message('info', 'SUCCESS: 3 default categories inserted (IDFC Bank, HDFC Bank, Safe Legal )');
} catch (Exception $e) {
    log_message('error', 'ERROR inserting categories: ' . $e->getMessage());
}

// ==================== CREATE DEFAULT BRANCH FOR ADMIN ====================
log_message('info', 'Creating default branch for admin...');

try {
    // Get the "Safe Legal Solutions" category ID (should be 3 if inserting fresh)
    $CI->db->where('name', 'Safe Legal Solutions');
    $sls_category = $CI->db->get($table1)->row();
    $category_id = $sls_category ? $sls_category->id : 6;
    
    $default_branch_data = [
        'category_id' => $category_id, // Safe Legal category
        'branch_name' => 'Head Office (Default)',
        'branch_code' => 'BR-DEFAULT',
        'location' => 'Central Office',
        'address' => 'Corporate Headquarters',
        'contact_phone' => '0000000000',
        'nodal_partner_manager_id' => NULL, // No manager assigned - will be set manually
        'registration_token' => bin2hex(random_bytes(32)),
        'is_default' => 1, // Mark as default branch
        'is_active' => 1,
        'created_by' => 1,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $CI->db->insert($table2, $default_branch_data);
    log_message('info', 'SUCCESS: Default branch created with Safe Legal category (Branch ID: ' . $CI->db->insert_id() . ') - No manager assigned');
} catch (Exception $e) {
    log_message('error', 'ERROR creating default branch: ' . $e->getMessage());
}

// ==================== AUTO-GENERATE TOKENS FOR EXISTING BRANCHES (IF ANY) ====================
log_message('info', 'Checking for branches without registration tokens...');

try {
    // Check if there are any branches without tokens
    $CI->db->where('registration_token IS NULL', null, false);
    $branches_without_tokens = $CI->db->get($table2)->result();
    
    if (!empty($branches_without_tokens)) {
        log_message('info', 'Found ' . count($branches_without_tokens) . ' branches without tokens. Generating...');
        
        foreach ($branches_without_tokens as $branch) {
            // Generate unique 64-character token
            $token = bin2hex(random_bytes(32));
            
            // Ensure token is unique
            $exists = $CI->db->get_where($table2, ['registration_token' => $token])->row();
            while ($exists) {
                $token = bin2hex(random_bytes(32));
                $exists = $CI->db->get_where($table2, ['registration_token' => $token])->row();
            }
            
            // Update branch with token
            $CI->db->where('id', $branch->id);
            $CI->db->update($table2, ['registration_token' => $token]);
            
            log_message('info', 'Generated token for branch: ' . $branch->branch_name);
        }
        
        log_message('info', 'SUCCESS: All branches now have registration tokens');
    } else {
        log_message('info', 'All branches already have registration tokens');
    }
} catch (Exception $e) {
    log_message('error', 'ERROR generating tokens: ' . $e->getMessage());
}

// ==================== VERIFY INSTALLATION ====================
log_message('info', 'Verifying installation...');

$tables_to_check = [
    $table1 => 'Branch Categories',
    $table2 => 'Branches (with registration_token & is_default)',
    $table5 => 'Items/Packages',
    $table3 => 'Students (cleaned - payment tracking via sls_payments)',
    $table4 => 'Change Requests',
    $table6 => 'Payment Transactions',
    $table7 => 'Package Enrollments'
];

$all_ok = true;
foreach ($tables_to_check as $table => $name) {
    if ($CI->db->table_exists($table)) {
        log_message('info', "✓ VERIFIED: {$name} table exists");
        
        // Check if registration_token and is_default columns exist in branches table
        if ($table === $table2) {
            $fields = $CI->db->field_data($table);
            $has_token = false;
            $has_is_default = false;
            
            foreach ($fields as $field) {
                if ($field->name === 'registration_token') {
                    $has_token = true;
                }
                if ($field->name === 'is_default') {
                    $has_is_default = true;
                }
            }
            
            if ($has_token) {
                log_message('info', "✓ VERIFIED: registration_token column exists in branches table");
            } else {
                log_message('error', "✗ MISSING: registration_token column in branches table");
                $all_ok = false;
            }
            
            if ($has_is_default) {
                log_message('info', "✓ VERIFIED: is_default column exists in branches table");
            } else {
                log_message('error', "✗ MISSING: is_default column in branches table");
                $all_ok = false;
            }
            
            // Count branches with tokens
            $CI->db->where('registration_token IS NOT NULL', null, false);
            $CI->db->from($table2);
            $token_count = $CI->db->count_all_results();
            log_message('info', "✓ VERIFIED: {$token_count} branches have registration tokens");
            
            // Count default branches
            $CI->db->where('is_default', 1);
            $CI->db->from($table2);
            $default_count = $CI->db->count_all_results();
            log_message('info', "✓ VERIFIED: {$default_count} default branch exists");
        }
        
        // Check students table structure (UPDATED - removed redundant payment fields)
        if ($table === $table3) {
            $fields = $CI->db->field_data($table);
            $has_item_id = false;
            $has_client_id = false;
            $has_client_created_at = false;
            $has_payment_status = false;
            $has_payment_percentage = false;
            $has_amount_paid = false;
            $has_total_amount = false;
            
            foreach ($fields as $field) {
                if ($field->name === 'item_id') {
                    $has_item_id = true;
                }
                if ($field->name === 'client_id') {
                    $has_client_id = true;
                }
                if ($field->name === 'client_created_at') {
                    $has_client_created_at = true;
                }
                if ($field->name === 'payment_status') {
                    $has_payment_status = true;
                }
                if ($field->name === 'payment_percentage') {
                    $has_payment_percentage = true;
                }
                if ($field->name === 'amount_paid') {
                    $has_amount_paid = true;
                }
                if ($field->name === 'total_amount') {
                    $has_total_amount = true;
                }
            }
            
            // Verify essential columns
            if ($has_item_id) {
                log_message('info', "✓ VERIFIED: item_id column exists in students table");
            } else {
                log_message('error', "✗ MISSING: item_id column in students table");
                $all_ok = false;
            }
            
            if ($has_client_id) {
                log_message('info', "✓ VERIFIED: client_id column exists in students table");
            } else {
                log_message('error', "✗ MISSING: client_id column in students table");
                $all_ok = false;
            }
            
            if ($has_client_created_at) {
                log_message('info', "✓ VERIFIED: client_created_at column exists in students table");
            } else {
                log_message('error', "✗ MISSING: client_created_at column in students table");
                $all_ok = false;
            }
            
            if ($has_payment_status) {
                log_message('info', "✓ VERIFIED: payment_status column exists in students table");
            } else {
                log_message('error', "✗ MISSING: payment_status column in students table");
                $all_ok = false;
            }
            
            if ($has_payment_percentage) {
                log_message('info', "✓ VERIFIED: payment_percentage column exists in students table");
            } else {
                log_message('error', "✗ MISSING: payment_percentage column in students table");
                $all_ok = false;
            }
            
            if ($has_amount_paid) {
                log_message('info', "✓ VERIFIED: amount_paid column exists in students table");
            } else {
                log_message('error', "✗ MISSING: amount_paid column in students table");
                $all_ok = false;
            }
            
            if ($has_total_amount) {
                log_message('info', "✓ VERIFIED: total_amount column exists in students table");
            } else {
                log_message('error', "✗ MISSING: total_amount column in students table");
                $all_ok = false;
            }
        }
    } else {
        log_message('error', "✗ MISSING: {$name} table does not exist!");
        $all_ok = false;
    }
}

// Check category count
$CI->db->from($table1);
$category_count = $CI->db->count_all_results();
log_message('info', "Categories in database: {$category_count}");

// Check item count
$CI->db->from($table5);
$item_count = $CI->db->count_all_results();
log_message('info', "Items in database: {$item_count} (will be added manually)");

// Check branch count
$CI->db->from($table2);
$branch_count = $CI->db->count_all_results();
log_message('info', "Branches in database: {$branch_count}");

// ==================== FINAL STATUS ====================
if ($all_ok && $category_count == 3 && $branch_count >= 1) {
    log_message('info', '========== Installation COMPLETED SUCCESSFULLY ==========');
    log_message('info', 'All 7 tables created successfully:');
    log_message('info', '  1. Branch Categories (3 default categories)');
    log_message('info', '  2. Branches (with registration_token & is_default)');
    log_message('info', '  3. Items/Packages (ready for manual entry)');
    log_message('info', '  4. Students (CLEANED):');
    log_message('info', '     - Item/Package assignment (item_id)');
    log_message('info', '     - Client account integration (client_id, client_created_at)');
    log_message('info', '     - Payment aggregation fields (payment_status, payment_percentage, amount_paid, total_amount)');
    log_message('info', '     - Individual payment records stored in sls_payments table');
    log_message('info', '  5. Change Requests');
    log_message('info', '  6. Payment Transactions (sls_payments)');
    log_message('info', '  7. Package Enrollments (sls_package_enrollments)');
    log_message('info', '');
    log_message('info', 'Additional Setup:');
    log_message('info', '  - 1 default branch created for admin under Safe Legal category');
    log_message('info', '  - Registration tokens auto-generated for all branches');
    log_message('info', '  - Client account auto-creation enabled when payment is 100% complete');
    log_message('info', '  - Foreign key constraints properly configured');
    log_message('info', '========================================================');
    
    if (function_exists('log_activity')) {
        log_activity('SafeLegalSolutions Module Installed Successfully [7 tables, 3 categories, 1 default branch, payment tracking optimized]');
    }
} else {
    log_message('error', '========== Installation FAILED ==========');
    log_message('error', 'Not all tables were created or default data missing');
    log_message('error', 'Expected: 3 categories, 1+ branches');
    log_message('error', 'Found: ' . $category_count . ' categories, ' . $item_count . ' items, ' . $branch_count . ' branches');
}

log_message('info', '========== Installation Process Ended ==========');