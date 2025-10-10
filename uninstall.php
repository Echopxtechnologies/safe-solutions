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

// Define tables in reverse order (to handle any dependencies)
$tables = [
    $db_prefix . 'sls_change_requests',
    $db_prefix . 'sls_students',
    $db_prefix . 'sls_branches',
    $db_prefix . 'sls_branch_categories',
    $db_prefix . 'sls_items', // ADD THIS LINE
];

// ==================== DROP TABLES ====================
foreach ($tables as $table) {
    try {
        if ($CI->db->table_exists($table)) {
            log_message('info', 'Dropping table: ' . $table);
            
            $CI->db->query("DROP TABLE IF EXISTS `{$table}`");
            
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

// ==================== REMOVE PERMISSIONS ====================
log_message('info', 'Removing staff permissions...');

try {
    // Remove branches permissions
    $CI->db->where('feature', 'safelegalsolutions_branches');
    $deleted1 = $CI->db->delete(db_prefix() . 'staff_permissions');
    
    if ($deleted1) {
        log_message('info', 'SUCCESS: Removed safelegalsolutions_branches permissions');
    }
    
    // Remove students permissions
    $CI->db->where('feature', 'safelegalsolutions_students');
    $deleted2 = $CI->db->delete(db_prefix() . 'staff_permissions');
    
    if ($deleted2) {
        log_message('info', 'SUCCESS: Removed safelegalsolutions_students permissions');
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
    log_message('info', 'All 4 tables removed, permissions cleaned');
    
    if (function_exists('log_activity')) {
        log_activity('SafeLegalSolutions Module Uninstalled Successfully');
    }
} else {
    log_message('error', '========== Uninstallation COMPLETED WITH WARNINGS ==========');
    log_message('error', 'Some tables may still exist');
}

log_message('info', '========== Uninstallation Process Ended ==========');