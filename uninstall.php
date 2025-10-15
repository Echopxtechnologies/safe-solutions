<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * SafeLegalSolutions Module - Uninstallation Script
 * Removes all database tables and related data
 * 
 * WARNING: This will delete ALL data related to this module
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
log_message('info', '========== SafeLegalSolutions Uninstallation Started ==========');
log_message('info', 'Database: ' . $database_name);
log_message('info', 'Prefix: ' . $db_prefix);

// ==================== DISABLE FOREIGN KEY CHECKS ====================
log_message('info', 'Disabling foreign key checks...');
$CI->db->query("SET FOREIGN_KEY_CHECKS = 0");

// ==================== DROP TABLES IN CORRECT ORDER ====================
// CRITICAL: Drop child tables first (those with foreign keys), then parent tables
$tables = [
    // 1. Drop enrollment and payment records first (child of students and items)
    $db_prefix . 'sls_package_enrollments',  // Has FK to students + items
    $db_prefix . 'sls_payments',             // Has FK to students
    
    // 2. Drop change requests (child of students)
    $db_prefix . 'sls_change_requests',      // Has FK to students
    
    // 3. Drop students (child of branches and items)
    $db_prefix . 'sls_students',             // Has FK to branches + items
    
    // 4. Drop branches (child of categories)
    $db_prefix . 'sls_branches',             // Has FK to categories
    
    // 5. Drop parent tables last
    $db_prefix . 'sls_items',                // No dependencies
    $db_prefix . 'sls_branch_categories',    // No dependencies
];

foreach ($tables as $table) {
    try {
        if ($CI->db->table_exists($table)) {
            log_message('info', 'Dropping table: ' . $table);
            
            // Drop table with CASCADE to remove foreign keys
            $CI->db->query("DROP TABLE IF EXISTS `{$table}` CASCADE");
            
            if (!$CI->db->table_exists($table)) {
                log_message('info', 'SUCCESS: Table ' . $table . ' dropped');
            } else {
                log_message('error', 'ERROR: Table ' . $table . ' still exists after drop');
            }
        } else {
            log_message('info', 'SKIP: Table ' . $table . ' does not exist');
        }
    } catch (Exception $e) {
        log_message('error', 'ERROR dropping ' . $table . ': ' . $e->getMessage());
    }
}

// ==================== RE-ENABLE FOREIGN KEY CHECKS ====================
log_message('info', 'Re-enabling foreign key checks...');
$CI->db->query("SET FOREIGN_KEY_CHECKS = 1");

// ==================== REMOVE PERMISSIONS ====================
log_message('info', 'Removing staff permissions...');

try {
    // Remove all SLS-related permissions
    $features_to_remove = [
        'safelegalsolutions_manager',
        'safelegalsolutions_branches',
        'safelegalsolutions_students'
    ];
    
    foreach ($features_to_remove as $feature) {
        $CI->db->where('feature', $feature);
        $deleted = $CI->db->delete(db_prefix() . 'staff_permissions');
        
        if ($deleted) {
            log_message('info', "SUCCESS: Removed {$feature} permissions");
        }
    }
} catch (Exception $e) {
    log_message('error', 'ERROR removing permissions: ' . $e->getMessage());
}

// ==================== VERIFY UNINSTALLATION ====================
log_message('info', 'Verifying uninstallation...');

$all_removed = true;
foreach ($tables as $table) {
    if ($CI->db->table_exists($table)) {
        log_message('error', "✗ STILL EXISTS: {$table}");
        $all_removed = false;
    } else {
        log_message('info', "✓ REMOVED: {$table}");
    }
}

// ==================== FINAL STATUS ====================
if ($all_removed) {
    log_message('info', '========== Uninstallation COMPLETED SUCCESSFULLY ==========');
    log_message('info', 'All 7 tables removed, permissions cleaned');
    log_message('info', 'Tables removed:');
    log_message('info', '  1. Package Enrollments');
    log_message('info', '  2. Payment Transactions');
    log_message('info', '  3. Change Requests');
    log_message('info', '  4. Students');
    log_message('info', '  5. Branches');
    log_message('info', '  6. Items/Packages');
    log_message('info', '  7. Branch Categories');
    
    if (function_exists('log_activity')) {
        log_activity('SafeLegalSolutions Module Uninstalled Successfully [7 tables removed]');
    }
} else {
    log_message('error', '========== Uninstallation COMPLETED WITH WARNINGS ==========');
    log_message('error', 'Some tables may still exist - check foreign key constraints');
}

log_message('info', '========== Uninstallation Process Ended ==========');