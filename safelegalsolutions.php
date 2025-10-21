<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Safe Legal
Description: Partner and Candidate Management System with Role-based Access + Client Portal
Version: 2.0 - Fixed Client Area Routing
Author: Your Name
*/
hooks()->add_action('pre_controller', 'disable_language_file_loading');

function disable_language_file_loading()
{
    $CI = &get_instance();
    
    // Override the language loader
    $CI->load->helper('language');
    
    // Replace lang function globally
    if (!function_exists('_l')) {
        function _l($line, $for = '') {
            // Just return the key itself instead of looking for translation
            return ucwords(str_replace('_', ' ', $line));
        }
    }
}

define('SAFELEGALSOLUTIONS_MODULE_NAME', 'safelegalsolutions');
hooks()->add_action('admin_init', 'safelegalsolutions_init_module');

function safelegalsolutions_init_module()
{
    $CI = &get_instance();
    
    // Load helper
    $CI->load->helper(SAFELEGALSOLUTIONS_MODULE_NAME . '/safelegalsolutions');
    
    // Rest of initialization...
}

register_activation_hook(SAFELEGALSOLUTIONS_MODULE_NAME, 'safelegalsolutions_activation_hook');
register_deactivation_hook(SAFELEGALSOLUTIONS_MODULE_NAME, 'safelegalsolutions_deactivation_hook');

// ================================================================
// MODULE ACTIVATION / DEACTIVATION
// ================================================================

function safelegalsolutions_activation_hook()
{
    $CI = &get_instance();
   
    if (!isset($CI->db)) {
        $CI->load->database();
    }
    
    log_activity('SafeLegalSolutions: Activation hook called');
    require_once(__DIR__ . '/install.php');
}

function safelegalsolutions_deactivation_hook()
{
    log_activity('SafeLegalSolutions: Deactivation hook called');
    require_once(__DIR__ . '/uninstall.php');
}

// ================================================================
// HELPER FUNCTIONS
// ================================================================

/**
 * Check if user is Manager or Admin
 * Manager has full SLS module access like Admin
 * 
 * @return bool
 */
function is_sls_manager_or_admin()
{
    return is_admin() || has_permission('safelegalsolutions_manager', '', 'manage');
}

/**
 * Check if user is NPM (Nodal Partner Manager)
 * Simple role check - no permission complexity
 * 
 * @return bool
 */
function is_npm()
{
    if (is_admin()) return false;
    if (is_sls_manager_or_admin()) return false;
    
    $CI = &get_instance();
    $staff_id = get_staff_user_id();
    
    // Get staff role
    $staff = $CI->db->get_where(db_prefix() . 'staff', ['staffid' => $staff_id])->row();
    if (!$staff) return false;
    
    // Get role name
    $role = $CI->db->get_where(db_prefix() . 'roles', ['roleid' => $staff->role])->row();
    if (!$role) return false;
    
    // Check if role is NPM
    return $role->name === 'SLS Nodal Partner Manager';
}

// ================================================================
// CLIENT AREA ROUTING - CRITICAL FIX
// ================================================================

/**
 * Register client area routes for module controllers
 * This fixes the 404 error when accessing /clients/safelegalsolutions_client/*
 * 
 * HOOK: clients_init (runs when client area is loaded)
 * URL PATTERN: /clients/safelegalsolutions_client/{method}/{params}
 */
hooks()->add_action('clients_init', 'safelegalsolutions_register_client_routes');

function safelegalsolutions_register_client_routes()
{
    $CI = &get_instance();
    
    // Get URL segments
    $segment1 = $CI->uri->segment(1); // 'clients'
    $segment2 = $CI->uri->segment(2); // 'safelegalsolutions_client'
    $segment3 = $CI->uri->segment(3); // method name
    
    // Check if we're accessing our client controller
    if ($segment1 === 'clients' && $segment2 === 'safelegalsolutions_client') {
        
        // Build controller path
        $controller_path = module_dir_path(SAFELEGALSOLUTIONS_MODULE_NAME) . 'controllers/Safelegalsolutions_client.php';
        
        // Verify controller file exists
        if (!file_exists($controller_path)) {
            log_activity('SLS Error: Client controller not found at ' . $controller_path);
            show_404();
            return;
        }
        
        // Load the controller file
        require_once($controller_path);
        
        // Get method name (default to 'index')
        $method = $segment3;
        if (empty($method)) {
            $method = 'index';
        }
        
        // Get additional parameters (everything after segment 3)
        $params = array_slice($CI->uri->segment_array(), 3);
        
        // Instantiate the controller
        $controller = new Safelegalsolutions_client();
        
        // Verify method exists
        if (!method_exists($controller, $method)) {
            log_activity('SLS Error: Method "' . $method . '" not found in Safelegalsolutions_client controller');
            show_404();
            return;
        }
        
        // Call the method with parameters
        call_user_func_array(array($controller, $method), $params);
        
        // Display output and stop execution
        $CI->output->_display();
        exit;
    }
}

// ================================================================
// CLIENT PORTAL NAVIGATION
// ================================================================

/**
 * Remove "Files" and "Calendar" from client navigation sidebar
 */
hooks()->add_filter('customers_area_navigation', function ($nav) {
    foreach ($nav as $key => $item) {
        $slug = $item['slug'] ?? $key;
        if (in_array($slug, ['files', 'calendar'], true)) {
            unset($nav[$key]);
        }
    }
    return $nav;
});

/**
 * Add SLS Navigation Buttons to Client Area Header
 * ✅ FIXED: Removed Referral Card button, improved styling, reduced button height
 * ✅ FIXED: Hide Company Details, GDPR, and Announcements using CSS + JavaScript
 * Shows only 2 buttons: My Dashboard and My Profile
 * Profile dropdown shows only: Profile and Logout
 */
hooks()->add_action('app_customers_head', 'safelegalsolutions_add_client_navigation');

function safelegalsolutions_add_client_navigation()
{
    // Build correct URLs for client area
    $dashboardUrl = site_url('safelegalsolutions/safelegalsolutions_client/my_dashboard');
    $profileUrl   = site_url('safelegalsolutions/safelegalsolutions_client/my_profile');
    
    echo '
    <style>
      /* ============================================
         Hide Files and Calendar Links
         ============================================ */
      a[href*="clients/files"],
      a[href*="clients/calendar"] {
        display: none !important;
      }
      
      /* ============================================
         ✅ FIXED: Hide Company Details, GDPR, and Announcements
         from Client Profile Dropdown Menu
         ============================================ */
      
      /* Hide by href patterns */
      .customers-top-navbar a[href*="clients/company"],
      .customers-top-navbar a[href*="clients/gdpr"],
      .customers-top-navbar a[href*="clients/announcements"],
      a[href*="clients/company"],
      a[href*="clients/gdpr"],
      a[href*="clients/announcements"] {
        display: none !important;
      }
      
      /* Hide by menu item classes */
      .customers-nav-item-company,
      .customers-nav-item-gdpr,
      .customers-nav-item-announcements,
      li.customers-nav-item-company,
      li.customers-nav-item-gdpr,
      li.customers-nav-item-announcements {
        display: none !important;
      }
      
      /* Hide parent <li> elements containing these links */
      li:has(a[href*="clients/company"]),
      li:has(a[href*="clients/gdpr"]),
      li:has(a[href*="clients/announcements"]) {
        display: none !important;
      }
      
      /* ============================================
         ✅ IMPROVED: SLS Navigation Buttons Styling
         - Reduced height for cleaner look
         - Better spacing and alignment
         - Improved hover effects
         ============================================ */
      
      /* Base button style - REDUCED HEIGHT */
      .navbar .navbar-right .sls-nav-btn {
        color: #fff !important;
        border-radius: 4px;
        padding: 6px 14px;              /* ✅ Reduced from 7px 15px */
        margin-right: 8px;
        margin-top:25px;
        font-weight: 500;
        transition: all 0.25s ease;
        display: inline-flex;           /* ✅ Better alignment */
        align-items: center;            /* ✅ Vertical centering */
        text-decoration: none;
        font-size: 13px;
        line-height: 1.4;               /* ✅ Control line height */
        height: 32px;                   /* ✅ Fixed height for consistency */
        box-sizing: border-box;
      }
      
      .navbar .navbar-right .sls-nav-btn:hover {
        text-decoration: none;
        transform: translateY(-1px);    /* ✅ Subtle lift */
        box-shadow: 0 3px 10px rgba(0,0,0,0.2);  /* ✅ Softer shadow */
      }
      
      /* Dashboard Button - Modern Blue */
      .navbar .navbar-right .sls-dashboard-btn {
        background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);  /* ✅ Gradient */
        border: none;                   /* ✅ Remove border */
      }
      
      .navbar .navbar-right .sls-dashboard-btn:hover {
        background: linear-gradient(135deg, #357ABD 0%, #2868A8 100%);
        box-shadow: 0 4px 12px rgba(74, 144, 226, 0.4);  /* ✅ Colored shadow */
      }
      
      /* Profile Button - Modern Green */
      .navbar .navbar-right .sls-profile-btn {
        background: linear-gradient(135deg, #27AE60 0%, #1E8449 100%);  /* ✅ Gradient */
        border: none;                   /* ✅ Remove border */
      }
      
      .navbar .navbar-right .sls-profile-btn:hover {
        background: linear-gradient(135deg, #1E8449 0%, #186A3B 100%);
        box-shadow: 0 4px 12px rgba(39, 174, 96, 0.4);  /* ✅ Colored shadow */
      }
      
      /* Icon spacing and sizing */
      .navbar .navbar-right .sls-nav-btn i {
        margin-right: 6px;
        font-size: 13px;               /* ✅ Icon size */
      }
      
      /* ✅ Better mobile responsive */
      @media (max-width: 768px) {
        .navbar .navbar-right .sls-nav-btn {
          padding: 5px 10px;
          font-size: 12px;
          margin-right: 5px;
          height: 28px;                 /* ✅ Smaller on mobile */
        }
        
        .navbar .navbar-right .sls-nav-btn i {
          margin-right: 4px;
          font-size: 12px;
        }
      }
      
      @media (max-width: 480px) {
        .navbar .navbar-right .sls-nav-btn {
          padding: 4px 8px;
          font-size: 11px;
          margin-right: 4px;
          height: 26px;                 /* ✅ Even smaller */
        }
        
        .navbar .navbar-right .sls-nav-btn i {
          margin-right: 3px;
          font-size: 11px;
        }
      }
    </style>
    
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        
        // ============================================
        // STEP 1: Remove Company, GDPR, Announcements
        // ============================================
        function removeUnwantedMenuItems() {
          var allLinks = document.querySelectorAll("a");
          
          var urlsToRemove = [
            "clients/company",
            "clients/gdpr",
            "clients/announcements"
          ];
          
          allLinks.forEach(function(link) {
            var href = link.getAttribute("href");
            if (href) {
              urlsToRemove.forEach(function(urlPattern) {
                if (href.indexOf(urlPattern) !== -1) {
                  var parentLi = link.closest("li");
                  if (parentLi) {
                    parentLi.remove();
                    console.log("SLS: Removed menu item - " + urlPattern);
                  }
                }
              });
            }
          });
        }
        
        removeUnwantedMenuItems();
        setTimeout(removeUnwantedMenuItems, 500);
        
        // ============================================
        // STEP 2: Add Navigation Buttons
        // ============================================
        var rightNav = document.querySelector(".customers-top-navbar .navbar-right, .header .navbar-right");
        var hasProfile = document.querySelector("li.customers-nav-item-profile");
        
        if (!rightNav || !hasProfile) {
            console.log("SLS: Navbar or profile not found");
            return;
        }

        if (document.querySelector("li.customers-nav-item-sls-dashboard")) {
            console.log("SLS: Navigation already added");
            return;
        }

        // CREATE DASHBOARD BUTTON
        var liDashboard = document.createElement("li");
        liDashboard.className = "customers-nav-item-sls-dashboard";
        
        var aDashboard = document.createElement("a");
        aDashboard.href = "' . $dashboardUrl . '";
        aDashboard.className = "btn sls-nav-btn sls-dashboard-btn";
        aDashboard.title = "View your registration overview";
        aDashboard.innerHTML = "<i class=\\"fa fa-dashboard\\"></i> My Dashboard";
        
        liDashboard.appendChild(aDashboard);

        // CREATE PROFILE BUTTON
        var liProfile = document.createElement("li");
        liProfile.className = "customers-nav-item-sls-profile";
        
        var aProfile = document.createElement("a");
        aProfile.href = "' . $profileUrl . '";
        aProfile.className = "btn sls-nav-btn sls-profile-btn";
        aProfile.title = "View your complete profile";
        aProfile.innerHTML = "<i class=\\"fa fa-user\\"></i> My Profile";
        
        liProfile.appendChild(aProfile);

        rightNav.prepend(liProfile);
        rightNav.prepend(liDashboard);
        
        console.log("SafeLegalSolutions: Navigation buttons added successfully");
      });
    </script>
    ';
}

// ================================================================
// ADMIN/STAFF MENU ITEMS
// ================================================================

/**
 * Initialize module menu items for Admin/Staff
 */
hooks()->add_action('admin_init', 'safelegalsolutions_init_menu_items');

function safelegalsolutions_init_menu_items()
{
    $CI = &get_instance();

    // ============================================
    // ✅ FIXED: Use hardcoded strings instead of _l()
    // ============================================
    
    // Register MANAGER capabilities (full access like admin)
    $manager_capabilities = [
        'capabilities' => [
            'manage' => 'Manage (Full SLS Access)',  // ✅ FIXED - No _l()
        ],
    ];
    
    // Register candidate capabilities (for NPM)
    $npm_capabilities = [
        'capabilities' => [
            'view'   => 'View',      // ✅ FIXED - No _l()
            'create' => 'Create',    // ✅ FIXED - No _l()
            'edit'   => 'Edit',      // ✅ FIXED - No _l()
        ],
    ];
    
    // Register branch capabilities (for Admin/Manager only)
    $admin_capabilities = [
        'capabilities' => [
            'view'   => 'View',      // ✅ FIXED - No _l()
            'create' => 'Create',    // ✅ FIXED - No _l()
            'edit'   => 'Edit',      // ✅ FIXED - No _l()
            'delete' => 'Delete',    // ✅ FIXED - No _l()
        ],
    ];

    // Register all capabilities
    register_staff_capabilities('safelegalsolutions_manager', $manager_capabilities, 'SLS Manager');
    register_staff_capabilities('safelegalsolutions_branches', $admin_capabilities, 'SLS Branches');
    register_staff_capabilities('safelegalsolutions_students', $npm_capabilities, 'SLS Candidates');

    // Show menu to Manager, Admin, or users with candidate permissions
    if (is_sls_manager_or_admin() || is_npm()) {
        
        // Main Menu Item
        $CI->app_menu->add_sidebar_menu_item('safelegalsolutions', [
            'name'     => 'Safe Legal',
            'href'     => admin_url('safelegalsolutions/dashboard'),
            'icon'     => 'fa fa-graduation-cap',
            'position' => 5,
        ]);

        // Dashboard - Everyone
        $CI->app_menu->add_sidebar_children_item('safelegalsolutions', [
            'slug'     => 'safelegalsolutions-dashboard',
            'name'     => 'Dashboard',
            'href'     => admin_url('safelegalsolutions/dashboard'),
            'icon'     => 'fa fa-dashboard',
            'position' => 1,
        ]);

        // My Branch (Admin OR Manager)
        if (is_sls_manager_or_admin()) {
            $CI->app_menu->add_sidebar_children_item('safelegalsolutions', [
                'slug'     => 'safelegalsolutions-my-branch',
                'name'     => 'My Partner',
                'href'     => admin_url('safelegalsolutions/my_branch'),
                'icon'     => 'fa fa-home',
                'position' => 2,
            ]);
        }

        // All Branches (Admin OR Manager)
        if (is_sls_manager_or_admin()) {
            $CI->app_menu->add_sidebar_children_item('safelegalsolutions', [
                'slug'     => 'safelegalsolutions-branches',
                'name'     => 'All Partners',
                'href'     => admin_url('safelegalsolutions/branches'),
                'icon'     => 'fa fa-building',
                'position' => 3,
            ]);
        }

        // Items/Packages (Admin OR Manager)
        if (is_sls_manager_or_admin()) {
            $CI->app_menu->add_sidebar_children_item('safelegalsolutions', [
                'slug'     => 'safelegalsolutions-items',
                'name'     => 'Items / Packages',
                'href'     => admin_url('safelegalsolutions/items'),
                'icon'     => 'fa fa-cubes',
                'position' => 4,
            ]);
        }

        // Candidates - Everyone
        $CI->app_menu->add_sidebar_children_item('safelegalsolutions', [
            'slug'     => 'safelegalsolutions-candidates',
            'name'     => 'Candidates',
            'href'     => admin_url('safelegalsolutions/students'),
            'icon'     => 'fa fa-users',
            'position' => 5,
        ]);
    }
}

// ================================================================
// ROLE-BASED MENU HIDING
// ================================================================

/**
 * Hide unwanted menu items for NPM and MANAGER using CSS
 * Only System Admin sees all Perfex menus
 * Manager and NPM only see Safe Legal menu
 * ✅ FIXED: Hide "My Profile" and "My Timesheet" from admin profile dropdown
 * Keep only "Edit Profile" and "Logout" for non-admin users
 */
hooks()->add_action('app_admin_head', 'safelegalsolutions_hide_npm_menus_css');

function safelegalsolutions_hide_npm_menus_css()
{
    $CI = &get_instance();
    
    // Only System Admin sees all menus
    if (is_admin()) {
        return; // Admin sees everything
    }
    
    // Check if user is Manager
    $is_manager = has_permission('safelegalsolutions_manager', '', 'manage');
    
    // Check if user is NPM
    $is_npm = is_npm();
    
    // Hide all menus except SLS for both Manager and NPM
    if ($is_manager || $is_npm) {
        echo '<style>
            /* ============================================
               Hide Sidebar Menus (except Safe Legal)
               ============================================ */
            #side-menu > li:not(.menu-item-safelegalsolutions) {
                display: none !important;
            }
            
            /* Hide dashboard in top navigation */
            .nav.navbar-nav li a[href*="/admin/dashboard"] {
                display: none !important;
            }
            
            /* Hide setup in top navigation */
            .nav.navbar-nav li a[href*="/admin/setup"] {
                display: none !important;
            }
            
            /* Ensure Safe Legal menu is visible */
            #side-menu > li.menu-item-safelegalsolutions {
                display: block !important;
            }
            
            /* ============================================
               ✅ NEW: Hide My Profile and My Timesheet
               from Admin Profile Dropdown
               ============================================ */
            
            /* Hide by href patterns */
            .navbar-nav a[href*="/admin/profile"],
            .navbar-nav a[href*="/admin/staff/timesheets"],
            a[href*="/admin/profile"]:not([href*="/admin/profile/edit"]),
            a[href*="/admin/staff/timesheets"] {
                display: none !important;
            }
            
            /* Hide parent <li> elements */
            .navbar-nav li:has(> a[href*="/admin/profile"]:not([href*="edit"])),
            .navbar-nav li:has(> a[href*="/admin/staff/timesheets"]) {
                display: none !important;
            }
            
            /* Hide by common class patterns (if any) */
            .dropdown-menu li a[href$="/profile"],
            .dropdown-menu li a[href*="staff/timesheets"] {
                display: none !important;
            }
        </style>
        
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            
            // ============================================
            // Remove My Profile and My Timesheet from dropdown
            // ============================================
            function removeAdminProfileMenuItems() {
                var allLinks = document.querySelectorAll(".navbar-nav a, .dropdown-menu a");
                
                allLinks.forEach(function(link) {
                    var href = link.getAttribute("href");
                    
                    if (href) {
                        // Remove "My Profile" (but keep "Edit Profile")
                        if (href.indexOf("/admin/profile") !== -1 && href.indexOf("/admin/profile/edit") === -1) {
                            var parentLi = link.closest("li");
                            if (parentLi) {
                                parentLi.remove();
                                console.log("SLS: Removed My Profile menu item");
                            }
                        }
                        
                        // Remove "My Timesheet"
                        if (href.indexOf("/admin/staff/timesheets") !== -1 || href.indexOf("timesheets") !== -1) {
                            var parentLi = link.closest("li");
                            if (parentLi) {
                                parentLi.remove();
                                console.log("SLS: Removed My Timesheet menu item");
                            }
                        }
                    }
                });
            }
            
            // Run immediately
            removeAdminProfileMenuItems();
            
            // Run again after dropdown opens (event delegation)
            setTimeout(removeAdminProfileMenuItems, 500);
            
            // Listen for dropdown clicks to re-run cleanup
            document.addEventListener("click", function(e) {
                if (e.target.closest(".dropdown-toggle") || e.target.closest(".navbar-nav")) {
                    setTimeout(removeAdminProfileMenuItems, 100);
                }
            });
            
            console.log("SLS: Admin profile menu cleanup initialized");
        });
        </script>';
    }
}

// ================================================================
// ROLE-BASED REDIRECTS
// ================================================================

/**
 * Redirect NPM and MANAGER to SLS Dashboard after login
 * Only System Admin goes to default Perfex dashboard
 */
hooks()->add_action('after_user_login', 'safelegalsolutions_redirect_npm_to_dashboard');

function safelegalsolutions_redirect_npm_to_dashboard($staff_id)
{
    // System Admin goes to default dashboard
    if (is_admin()) {
        return;
    }
    
    // Check if user is Manager
    $is_manager = has_permission('safelegalsolutions_manager', '', 'manage');
    
    // Check if user is NPM
    $has_candidate_permission = is_npm();
    $has_no_customer_permission = !has_permission('customers', '', 'view');
    $is_npm = $has_candidate_permission && $has_no_customer_permission && !$is_manager;
    
    // Redirect both Manager and NPM to SLS Dashboard
    if ($is_manager || $is_npm) {
        redirect(admin_url('safelegalsolutions/dashboard'));
    }
}

/**
 * Redirect from homepage to SLS Dashboard (NPM and MANAGER)
 * Only System Admin stays on default dashboard
 */
hooks()->add_action('admin_init', 'safelegalsolutions_redirect_from_homepage', 1);

function safelegalsolutions_redirect_from_homepage()
{
    $CI = &get_instance();
    
    // System Admin stays on default dashboard
    if (is_admin()) {
        return;
    }
    
    $uri_segment_1 = $CI->uri->segment(1);
    $uri_segment_2 = $CI->uri->segment(2);
    
    // Check if on dashboard/homepage
    if ($uri_segment_1 == 'admin' && ($uri_segment_2 == '' || $uri_segment_2 == 'dashboard')) {
        
        // Check if user is Manager
        $is_manager = has_permission('safelegalsolutions_manager', '', 'manage');
        
        // Check if user is NPM
        $has_candidate_permission = is_npm();
        $has_no_customer_permission = !has_permission('customers', '', 'view');
        $is_npm = $has_candidate_permission && $has_no_customer_permission && !$is_manager;
        
        // Redirect both Manager and NPM to SLS Dashboard
        if ($is_manager || $is_npm) {
            redirect(admin_url('safelegalsolutions/dashboard'));
        }
    }
}

// ================================================================
// ADMIN HEAD COMPONENTS
// ================================================================

/**
 * Load module CSS for admin area
 */
hooks()->add_action('app_admin_head', 'safelegalsolutions_head_components');

function safelegalsolutions_head_components()
{
    $viewuri = $_SERVER['REQUEST_URI'];

    // Only load on SLS pages
    if (!(strpos($viewuri, 'safelegalsolutions') !== false)) {
        return;
    }

    echo '<style>
        /* ============================================
           SLS Dashboard Statistics Boxes
           ============================================ */
        .sls-stat-box {
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .sls-stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .sls-stat-box h3 {
            font-size: 36px;
            font-weight: bold;
            margin: 0 0 10px 0;
        }
        
        .sls-stat-box p {
            font-size: 14px;
            margin: 0;
            opacity: 0.9;
        }
        
        /* ============================================
           SLS Card Component
           ============================================ */
        .sls-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        
        .sls-card h4 {
            margin-top: 0;
            margin-bottom: 15px;
            font-weight: 600;
            color: #333;
        }
        
        /* ============================================
           SLS Progress Bar
           ============================================ */
        .sls-progress-bar {
            background: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
            height: 25px;
        }
        
        .sls-progress-fill {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            line-height: 25px;
            font-weight: bold;
            font-size: 12px;
            transition: width 0.3s ease;
        }
        
        /* ============================================
           SLS Table Styling
           ============================================ */
        .sls-table {
            width: 100%;
            margin-top: 10px;
        }
        
        .sls-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            padding: 12px;
            text-align: left;
        }
        
        .sls-table td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .sls-table tr:hover {
            background-color: #f8f9fa;
        }
    </style>';
}

// ================================================================
// MODULE INSTALLATION CHECK
// ================================================================

/**
 * Check if module tables are installed
 * Auto-install if missing
 */
hooks()->add_action('admin_init', 'safelegalsolutions_check_installation');

function safelegalsolutions_check_installation()
{
    $CI = &get_instance();

    
    // Check if main table exists
    if (!$CI->db->table_exists(db_prefix() . 'sls_branch_categories')) {
        if (file_exists(__DIR__ . '/install.php')) {
            log_activity('SafeLegalSolutions: Running auto-installation');
            require_once(__DIR__ . '/install.php');
        }
    }
}