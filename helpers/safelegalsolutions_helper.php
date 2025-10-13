<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * SafeLegalSolutions Helper Functions
 * 
 * These functions provide role-based access control for the module
 * 
 * File: modules/safelegalsolutions/helpers/safelegalsolutions_helper.php
 */

if (!function_exists('is_sls_manager_or_admin')) {
    /**
     * Check if current user is SLS Manager or Admin
     * 
     * This function checks:
     * 1. Is the user an admin? (admin = 1)
     * 2. Does the user have the "SLS Nodal Partner Manager" role?
     * 
     * @return bool True if user is admin or SLS manager
     */
    function is_sls_manager_or_admin()
    {
        $CI =& get_instance();
        
        // Check if user is admin
        if (is_admin()) {
            return true;
        }
        
        // Get current staff member
        $staff_id = get_staff_user_id();
        
        if (!$staff_id) {
            return false;
        }
        
        try {
            // Get staff details with proper table prefix
            $CI->db->select(db_prefix() . 'staff.role');
            $CI->db->from(db_prefix() . 'staff');
            $CI->db->where(db_prefix() . 'staff.staffid', $staff_id);
            $staff = $CI->db->get()->row();
            
            if (!$staff || empty($staff->role)) {
                return false;
            }
            
            // Get role name with proper table prefix
            $CI->db->select(db_prefix() . 'roles.name');
            $CI->db->from(db_prefix() . 'roles');
            $CI->db->where(db_prefix() . 'roles.roleid', $staff->role);
            $role = $CI->db->get()->row();
            
            if (!$role) {
                return false;
            }
            
            // Check if role is "SLS Nodal Partner Manager"
            if ($role->name === 'SLS Nodal Partner Manager') {
                return true;
            }
            
        } catch (Exception $e) {
            log_activity('is_sls_manager_or_admin Error: ' . $e->getMessage());
            return false;
        }
        
        return false;
    }
}

if (!function_exists('get_sls_partner_role_id')) {
    /**
     * Get the ID of the SLS Nodal Partner Manager role
     * 
     * @return int|null Role ID or null if not found
     */
    function get_sls_partner_role_id()
    {
        $CI =& get_instance();
        
        try {
            $CI->db->select('roleid');
            $CI->db->from(db_prefix() . 'roles');
            $CI->db->where('name', 'SLS Nodal Partner Manager');
            $role = $CI->db->get()->row();
            
            return $role ? $role->roleid : null;
        } catch (Exception $e) {
            log_activity('get_sls_partner_role_id Error: ' . $e->getMessage());
            return null;
        }
    }
}

if (!function_exists('is_sls_partner')) {
    /**
     * Check if current user is specifically an SLS Partner (not admin)
     * 
     * @return bool True if user is SLS partner (excluding admins)
     */
    function is_sls_partner()
    {
        $CI =& get_instance();
        
        // Not a partner if admin
        if (is_admin()) {
            return false;
        }
        
        $staff_id = get_staff_user_id();
        
        if (!$staff_id) {
            return false;
        }
        
        try {
            $CI->db->select(db_prefix() . 'staff.role');
            $CI->db->from(db_prefix() . 'staff');
            $CI->db->where(db_prefix() . 'staff.staffid', $staff_id);
            $staff = $CI->db->get()->row();
            
            if (!$staff || empty($staff->role)) {
                return false;
            }
            
            $CI->db->select(db_prefix() . 'roles.name');
            $CI->db->from(db_prefix() . 'roles');
            $CI->db->where(db_prefix() . 'roles.roleid', $staff->role);
            $role = $CI->db->get()->row();
            
            return $role && $role->name === 'SLS Nodal Partner Manager';
            
        } catch (Exception $e) {
            log_activity('is_sls_partner Error: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('get_sls_partner_branch')) {
    /**
     * Get the branch assigned to current SLS partner
     * 
     * @return object|null Branch object or null if not found
     */
    function get_sls_partner_branch()
    {
        $CI =& get_instance();
        
        if (!is_sls_partner()) {
            return null;
        }
        
        $staff_id = get_staff_user_id();
        
        if (!$staff_id) {
            return null;
        }
        
        try {
            $CI->db->select('b.*, c.name as category_name');
            $CI->db->from(db_prefix() . 'sls_branches b');
            $CI->db->join(db_prefix() . 'sls_branch_categories c', 'c.id = b.category_id', 'left');
            $CI->db->where('b.nodal_partner_manager_id', $staff_id);
            $CI->db->where('b.is_active', 1);
            
            return $CI->db->get()->row();
            
        } catch (Exception $e) {
            log_activity('get_sls_partner_branch Error: ' . $e->getMessage());
            return null;
        }
    }
}

if (!function_exists('can_access_sls_student')) {
    /**
     * Check if current user can access a specific student record
     * 
     * Admins can access all students
     * Partners can only access students from their branch
     * 
     * @param int $student_id Student ID
     * @return bool True if user can access this student
     */
    function can_access_sls_student($student_id)
    {
        $CI =& get_instance();
        
        // Admins can access everything
        if (is_admin()) {
            return true;
        }
        
        // Must be SLS partner
        if (!is_sls_partner()) {
            return false;
        }
        
        $staff_id = get_staff_user_id();
        
        if (!$staff_id || !$student_id) {
            return false;
        }
        
        try {
            // Check if student belongs to this partner's branch
            $CI->db->select('nodal_partner_manager_id');
            $CI->db->from(db_prefix() . 'sls_students');
            $CI->db->where('id', $student_id);
            $student = $CI->db->get()->row();
            
            if (!$student) {
                return false;
            }
            
            return $student->nodal_partner_manager_id == $staff_id;
            
        } catch (Exception $e) {
            log_activity('can_access_sls_student Error: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('sls_check_staff_permissions')) {
    /**
     * Check if staff has specific SLS module permissions
     * 
     * @param int $staff_id Staff ID
     * @param string $feature Feature name (e.g., 'safelegalsolutions_students')
     * @param string $capability Capability (view, create, edit, delete)
     * @return bool True if staff has permission
     */
    function sls_check_staff_permissions($staff_id, $feature, $capability)
    {
        $CI =& get_instance();
        
        if (!$staff_id || !$feature || !$capability) {
            return false;
        }
        
        try {
            $CI->db->select('id');
            $CI->db->from(db_prefix() . 'staff_permissions');
            $CI->db->where(db_prefix() . 'staff_permissions.staffid', $staff_id);
            $CI->db->where(db_prefix() . 'staff_permissions.feature', $feature);
            $CI->db->like(db_prefix() . 'staff_permissions.capability', $capability);
            
            return $CI->db->count_all_results() > 0;
            
        } catch (Exception $e) {
            log_activity('sls_check_staff_permissions Error: ' . $e->getMessage());
            return false;
        }
    }
}