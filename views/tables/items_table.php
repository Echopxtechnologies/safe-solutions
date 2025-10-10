<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in JSON response

$CI = &get_instance();

$aColumns = [
    'item_name',
    'item_code',
    'base_price',
    'gst_percentage',
    'total_price',
    'duration_months',
    'is_active',
    'id'
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'sls_items';

// Check if table exists
if (!$CI->db->table_exists($sTable)) {
    // Return empty result if table doesn't exist
    $output = [
        'draw' => intval($CI->input->post('draw')),
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => []
    ];
    echo json_encode($output);
    exit;
}

try {
    $result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], []);
    
    $output  = $result['output'];
    $rResult = $result['rResult'];
    
    foreach ($rResult as $aRow) {
        $row = [];
        
        // Item Name
        $row[] = '<strong>' . htmlspecialchars($aRow['item_name']) . '</strong>';
        
        // Item Code
        $row[] = '<span class="label label-default">' . htmlspecialchars($aRow['item_code']) . '</span>';
        
        // Base Price
        $row[] = '<span class="text-success">₹' . number_format($aRow['base_price'], 2) . '</span>';
        
        // GST Percentage
        $row[] = '<span class="label label-info">' . number_format($aRow['gst_percentage'], 2) . '%</span>';
        
        // Total Price (with GST)
        $row[] = '<strong class="text-primary">₹' . number_format($aRow['total_price'], 2) . '</strong>';
        
        // Duration
        if (!empty($aRow['duration_months'])) {
            $row[] = $aRow['duration_months'] . ' months';
        } else {
            $row[] = '<span class="text-muted">-</span>';
        }
        
        // Status
        if ($aRow['is_active'] == 1) {
            $row[] = '<span class="label label-success">Active</span>';
        } else {
            $row[] = '<span class="label label-default">Inactive</span>';
        }
        
        // Options
        $options = '<div class="btn-group">';
        
        // Edit button
        $options .= '<a href="' . admin_url('safelegalsolutions/item/' . $aRow['id']) . '" class="btn btn-default btn-xs" title="Edit">
            <i class="fa fa-pencil"></i>
        </a>';
        
        // Delete button
        $options .= '<a href="' . admin_url('safelegalsolutions/delete_item/' . $aRow['id']) . '" 
            class="btn btn-danger btn-xs _delete" 
            title="Delete"
            onclick="return confirm(\'Are you sure you want to delete this item? This action cannot be undone if item is not assigned to any students.\');">
            <i class="fa fa-trash"></i>
        </a>';
        
        $options .= '</div>';
        
        $row[] = $options;
        
        $output['aaData'][] = $row;
    }
    
} catch (Exception $e) {
    // Log error and return empty result
    log_message('error', 'Items DataTable Error: ' . $e->getMessage());
    
    $output = [
        'draw' => intval($CI->input->post('draw')),
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'Error loading items: ' . $e->getMessage()
    ];
}

// Ensure clean JSON output
ob_clean();
header('Content-Type: application/json');
echo json_encode($output);
exit;