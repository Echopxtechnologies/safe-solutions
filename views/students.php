<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<!-- Simple Clean Styles -->
<style>
    .panel_s {
        border: 1px solid #d9d9d9;
        margin-bottom: 20px;
    }
    
    .candidates-table {
        font-size: 13px;
    }
    
    .candidates-table thead th {
        background-color: #f9fafb;
        font-weight: 600;
        color: #4a5568;
        text-transform: uppercase;
        font-size: 12px;
        border-bottom: 2px solid #e2e8f0;
        padding: 12px;
    }
    
    .candidates-table tbody td {
        padding: 12px;
        vertical-align: middle;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .candidates-table tbody tr:hover {
        background-color: #f7fafc;
    }
    
    .candidate-name {
        font-weight: 600;
        color: #2d3748;
        font-size: 14px;
        margin-bottom: 4px;
    }
    
    .action-links {
        margin-top: 4px;
    }
    
    .action-links a {
        color: #718096;
        text-decoration: none;
        font-size: 12px;
        margin-right: 8px;
    }
    
    .action-links a:hover {
        color: #2563eb;
        text-decoration: underline;
    }
    
    .action-links .text-danger:hover {
        color: #dc3545;
    }
    
    .action-links .text-success:hover {
        color: #28a745;
    }
    
    .action-links .text-warning:hover {
        color: #ffc107;
    }
    
    .action-separator {
        color: #cbd5e0;
        margin: 0 4px;
    }
    
    .text-muted-light {
        color: #718096;
        font-size: 12px;
    }
    
    .badge-simple {
        display: inline-block;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 3px;
        text-transform: uppercase;
    }
    
    .badge-draft {
        background-color: #e2e8f0;
        color: #4a5568;
    }
    
    .badge-pending {
        background-color: #fef5e7;
        color: #b7791f;
    }
    
    .badge-approved {
        background-color: #d4edda;
        color: #155724;
    }
    
    .badge-locked {
        background-color: #d4edda;
        color: #155724;
    }
    
    .role-badge {
        display: inline-block;
        padding: 4px 8px;
        font-size: 11px;
        border-radius: 3px;
        margin-left: 8px;
        background: #667eea;
        color: white;
    }
    
    /* Change Request Modal Styles */
    .change-request-modal .modal-header {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
    
    .change-request-form .form-group label {
        font-weight: 600;
        color: #495057;
    }
    
    .change-request-badge {
        display: inline-block;
        padding: 3px 8px;
        font-size: 10px;
        border-radius: 3px;
        margin-left: 5px;
    }
    
    .change-request-badge.pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .change-request-badge.approved {
        background: #d4edda;
        color: #155724;
    }
    
    .change-request-badge.rejected {
        background: #f8d7da;
        color: #721c24;
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="no-margin">
                                    <i class="fa fa-users"></i> Candidates Management
                                    <?php if (!is_admin() && is_sls_manager_or_admin()): ?>
                                        <span class="role-badge">
                                            <i class="fa fa-star"></i> SLS Manager
                                        </span>
                                    <?php endif; ?>
                                </h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php if (is_sls_manager_or_admin()): ?>
                                    <a href="<?php echo admin_url('safelegalsolutions/change_requests'); ?>" class="btn btn-warning btn-sm" style="margin-right: 10px;">
                                        <i class="fa fa-exchange"></i> Change Requests
                                        <?php 
                                        // Count pending change requests
                                        $this->db->where('status', 'pending');
                                        $pending_count = $this->db->count_all_results(db_prefix() . 'sls_change_requests');
                                        if ($pending_count > 0): 
                                        ?>
                                            <span class="badge" style="background: #dc3545;"><?php echo $pending_count; ?></span>
                                        <?php endif; ?>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (is_npm() || is_sls_manager_or_admin()): ?>
                                    <a href="<?php echo admin_url('safelegalsolutions/student'); ?>" class="btn btn-primary">
                                        <i class="fa fa-plus"></i> New Candidate
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <hr class="hr-panel-heading" />

                        <!-- Candidates Table -->
                        <div class="table-responsive">
                            <table class="table candidates-table" id="candidates-table">
                                <thead>
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Candidate Name</th>
                                        <th>Contact</th>
                                        <th>Package</th>
                                        <?php if (is_sls_manager_or_admin()): ?>
                                            <th>Branch</th>
                                            <th>Manager</th>
                                        <?php endif; ?>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Get candidates with item/package information
                                    if (is_sls_manager_or_admin()) {
                                        // Admin/Manager sees ALL candidates
                                        $this->db->select($this->db->dbprefix('sls_students') . '.*, ' . 
                                                         $this->db->dbprefix('sls_branches') . '.branch_name, ' .
                                                         $this->db->dbprefix('sls_branch_categories') . '.name as category_name, ' .
                                                         $this->db->dbprefix('staff') . '.firstname as manager_firstname, ' .
                                                         $this->db->dbprefix('staff') . '.lastname as manager_lastname, ' .
                                                         $this->db->dbprefix('sls_items') . '.item_name, ' .
                                                         $this->db->dbprefix('sls_items') . '.total_price as item_total_price');
                                        $this->db->from($this->db->dbprefix('sls_students'));
                                        $this->db->join($this->db->dbprefix('sls_branches'), 
                                                       $this->db->dbprefix('sls_branches') . '.id = ' . $this->db->dbprefix('sls_students') . '.branch_id', 
                                                       'left');
                                        $this->db->join($this->db->dbprefix('sls_branch_categories'), 
                                                       $this->db->dbprefix('sls_branch_categories') . '.id = ' . $this->db->dbprefix('sls_branches') . '.category_id', 
                                                       'left');
                                        $this->db->join($this->db->dbprefix('staff'), 
                                                       $this->db->dbprefix('staff') . '.staffid = ' . $this->db->dbprefix('sls_students') . '.nodal_partner_manager_id', 
                                                       'left');
                                        $this->db->join($this->db->dbprefix('sls_items'), 
                                                       $this->db->dbprefix('sls_items') . '.id = ' . $this->db->dbprefix('sls_students') . '.item_id', 
                                                       'left');
                                        $this->db->order_by($this->db->dbprefix('sls_students') . '.created_at', 'DESC');
                                        $candidates = $this->db->get()->result();
                                    } else {
                                        // NPM sees only THEIR candidates
                                        $staff_id = get_staff_user_id();
                                        $this->db->select($this->db->dbprefix('sls_students') . '.*, ' . 
                                                         $this->db->dbprefix('sls_branches') . '.branch_name, ' .
                                                         $this->db->dbprefix('sls_branch_categories') . '.name as category_name, ' .
                                                         $this->db->dbprefix('sls_items') . '.item_name, ' .
                                                         $this->db->dbprefix('sls_items') . '.total_price as item_total_price');
                                        $this->db->from($this->db->dbprefix('sls_students'));
                                        $this->db->join($this->db->dbprefix('sls_branches'), 
                                                       $this->db->dbprefix('sls_branches') . '.id = ' . $this->db->dbprefix('sls_students') . '.branch_id', 
                                                       'left');
                                        $this->db->join($this->db->dbprefix('sls_branch_categories'), 
                                                       $this->db->dbprefix('sls_branch_categories') . '.id = ' . $this->db->dbprefix('sls_branches') . '.category_id', 
                                                       'left');
                                        $this->db->join($this->db->dbprefix('sls_items'), 
                                                       $this->db->dbprefix('sls_items') . '.id = ' . $this->db->dbprefix('sls_students') . '.item_id', 
                                                       'left');
                                        $this->db->where($this->db->dbprefix('sls_students') . '.nodal_partner_manager_id', $staff_id);
                                        $this->db->order_by($this->db->dbprefix('sls_students') . '.created_at', 'DESC');
                                        $candidates = $this->db->get()->result();
                                    }
                                    
                                    if (!empty($candidates)): 
                                        $serial_number = 1;
                                        foreach ($candidates as $candidate): 
                                            // Check if there are pending change requests for this candidate
                                            $this->db->where('student_id', $candidate->id);
                                            $this->db->where('status', 'pending');
                                            $pending_changes = $this->db->count_all_results(db_prefix() . 'sls_change_requests');
                                    ?>
                                        <tr>
                                            <!-- Serial Number -->
                                            <td><?php echo $serial_number++; ?></td>
                                            
                                            <!-- Candidate Name with Actions Below -->
                                            <td>
                                                <div class="candidate-name">
                                                    <?php echo htmlspecialchars($candidate->student_name); ?>
                                                    
                                                    <!-- Show Pending Change Request Badge -->
                                                    <?php if ($pending_changes > 0): ?>
                                                        <span class="change-request-badge pending">
                                                            <i class="fa fa-clock-o"></i> <?php echo $pending_changes; ?> Pending
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="action-links">
                                                    <?php if ($candidate->is_locked == 1): ?>
                                                        <!-- LOCKED PROFILE: Show "Request Change" for NPM, "View/Edit" for Admin -->
                                                        
                                                        <?php if (is_sls_manager_or_admin()): ?>
                                                            <!-- Admin can always edit -->
                                                            <a href="<?php echo admin_url('safelegalsolutions/student/' . $candidate->id); ?>">
                                                                <i class="fa fa-edit"></i> Edit
                                                            </a>
                                                        <?php else: ?>
                                                            <!-- NPM: Request Change -->
                                                            <a href="javascript:void(0);" 
                                                               onclick="openChangeRequestModal(<?php echo $candidate->id; ?>, '<?php echo htmlspecialchars($candidate->student_name, ENT_QUOTES); ?>');"
                                                               class="text-warning">
                                                                <i class="fa fa-exchange"></i> Request Change
                                                            </a>
                                                            
                                                            <span class="action-separator">|</span>
                                                            
                                                            <a href="<?php echo admin_url('safelegalsolutions/student/' . $candidate->id); ?>">
                                                                <i class="fa fa-eye"></i> View
                                                            </a>
                                                        <?php endif; ?>
                                                        
                                                    <?php else: ?>
                                                        <!-- UNLOCKED PROFILE: Normal Edit -->
                                                        <a href="<?php echo admin_url('safelegalsolutions/student/' . $candidate->id); ?>">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Submit for Review - NPM only, when profile is 100% complete and unlocked -->
                                                    <?php if ($candidate->profile_completion == 100 && $candidate->status == 'draft' && $candidate->is_locked == 0): ?>
                                                        <span class="action-separator">|</span>
                                                        <a href="<?php echo admin_url('safelegalsolutions/submit_for_review/' . $candidate->id); ?>" 
                                                           onclick="return confirm('Submit this candidate profile for admin review?');">
                                                            <i class="fa fa-send"></i> Submit for Review
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Approve Button - Admin/Manager only, for pending reviews -->
                                                    <?php if (is_sls_manager_or_admin() && $candidate->status == 'pending_review'): ?>
                                                        <span class="action-separator">|</span>
                                                        <a href="<?php echo admin_url('safelegalsolutions/approve_student/' . $candidate->id); ?>" 
                                                           class="text-success"
                                                           onclick="return confirm('Approve and lock this candidate profile?');">
                                                            <i class="fa fa-check"></i> Approve
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <!-- View Change Requests -->
                                                    <?php if ($pending_changes > 0 && is_sls_manager_or_admin()): ?>
                                                        <span class="action-separator">|</span>
                                                        <a href="<?php echo admin_url('safelegalsolutions/change_requests?student_id=' . $candidate->id); ?>" 
                                                           class="text-warning">
                                                            <i class="fa fa-exchange"></i> View Requests (<?php echo $pending_changes; ?>)
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Delete Button - NPM can delete unlocked, Admin/Manager can delete all -->
                                                    <?php if ((!is_sls_manager_or_admin() && $candidate->is_locked == 0) || is_sls_manager_or_admin()): ?>
                                                        <span class="action-separator">|</span>
                                                        <a href="<?php echo admin_url('safelegalsolutions/delete_student/' . $candidate->id); ?>" 
                                                           class="text-danger"
                                                           onclick="return confirm('Are you sure you want to delete this candidate?');">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            
                                            <!-- Contact -->
                                            <td>
                                                <div><?php echo htmlspecialchars($candidate->email); ?></div>
                                                <div class="text-muted-light"><?php echo htmlspecialchars($candidate->phone); ?></div>
                                            </td>
                                            
                                            <!-- Package -->
                                            <td>
                                                <?php if (!empty($candidate->item_name)): ?>
                                                    <div><?php echo htmlspecialchars($candidate->item_name); ?></div>
                                                    <div class="text-muted-light">₹<?php echo number_format($candidate->item_total_price, 2); ?></div>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            
                                            <!-- Admin/Manager Only Columns -->
                                            <?php if (is_sls_manager_or_admin()): ?>
                                                <td>
                                                    <?php echo htmlspecialchars($candidate->branch_name); ?>
                                                    <?php if (!empty($candidate->category_name)): ?>
                                                        <br>
                                                        <span class="text-muted-light">
                                                            <?php echo htmlspecialchars($candidate->category_name); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($candidate->manager_firstname . ' ' . $candidate->manager_lastname); ?>
                                                </td>
                                            <?php endif; ?>
                                            
                                            <!-- Status -->
                                            <td>
                                                <?php
                                                $badge_class = 'badge-draft';
                                                $status_text = 'Draft';
                                                
                                                if ($candidate->is_locked == 1) {
                                                    $badge_class = 'badge-locked';
                                                    $status_text = 'Locked';
                                                } elseif ($candidate->status == 'approved') {
                                                    $badge_class = 'badge-approved';
                                                    $status_text = 'Approved';
                                                } elseif ($candidate->status == 'pending_review') {
                                                    $badge_class = 'badge-pending';
                                                    $status_text = 'Pending';
                                                } else {
                                                    $status_text = ucwords(str_replace('_', ' ', $candidate->status));
                                                }
                                                ?>
                                                <span class="badge-simple <?php echo $badge_class; ?>">
                                                    <?php echo $status_text; ?>
                                                </span>
                                                
                                                <!-- Profile Completion -->
                                                <?php if ($candidate->profile_completion < 100): ?>
                                                    <br>
                                                    <small class="text-muted-light">
                                                        <?php echo $candidate->profile_completion; ?>% Complete
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php 
                                        endforeach;
                                    else: 
                                    ?>
                                        <tr>
                                            <td colspan="<?php echo is_sls_manager_or_admin() ? '7' : '5'; ?>" class="text-center">
                                                <div style="padding: 40px 0;">
                                                    <i class="fa fa-users" style="font-size: 48px; color: #ddd; margin-bottom: 15px; display: block;"></i>
                                                    <p class="text-muted" style="font-size: 16px; margin-bottom: 15px;">No candidates found.</p>
                                                    <?php if (is_npm() || is_sls_manager_or_admin()): ?>
                                                        <a href="<?php echo admin_url('safelegalsolutions/student'); ?>" class="btn btn-primary">
                                                            <i class="fa fa-plus"></i> Add Your First Candidate
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Quick Stats Summary -->
                        <?php if (!empty($candidates)): ?>
                        <div class="row mtop20" style="padding-top: 20px; border-top: 1px solid #e2e8f0;">
                            <div class="col-md-12">
                                <div class="text-muted-light" style="font-size: 13px;">
                                    <strong>Quick Summary:</strong>
                                    <?php
                                    $total = count($candidates);
                                    $draft_count = 0;
                                    $pending_count = 0;
                                    $approved_count = 0;
                                    $locked_count = 0;
                                    
                                    foreach ($candidates as $c) {
                                        if ($c->is_locked == 1) {
                                            $locked_count++;
                                        } elseif ($c->status == 'approved') {
                                            $approved_count++;
                                        } elseif ($c->status == 'pending_review') {
                                            $pending_count++;
                                        } else {
                                            $draft_count++;
                                        }
                                    }
                                    ?>
                                    Total: <strong><?php echo $total; ?></strong> candidates
                                    <?php if ($draft_count > 0): ?>
                                        | Draft: <strong><?php echo $draft_count; ?></strong>
                                    <?php endif; ?>
                                    <?php if ($pending_count > 0): ?>
                                        | Pending Review: <strong><?php echo $pending_count; ?></strong>
                                    <?php endif; ?>
                                    <?php if ($approved_count > 0): ?>
                                        | Approved: <strong><?php echo $approved_count; ?></strong>
                                    <?php endif; ?>
                                    <?php if ($locked_count > 0): ?>
                                        | Locked: <strong><?php echo $locked_count; ?></strong>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Change Request Modal -->
<div class="modal fade change-request-modal" id="changeRequestModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php echo form_open(admin_url('safelegalsolutions/submit_change_request')); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">
                        <i class="fa fa-exchange"></i> Request Change for: <span id="modal-student-name"></span>
                    </h4>
                </div>
                
                <div class="modal-body">
                    <!-- Hidden field for student ID -->
                    <input type="hidden" name="student_id" id="modal-student-id">
                    
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> 
                        <strong>Note:</strong> This profile is locked. Please describe the changes you need, and an admin will review your request.
                    </div>
                    
                    <div class="change-request-form">
                        <div class="form-group">
                            <label for="field_name">Field to Change <span class="text-danger">*</span></label>
                            <select name="field_name" id="field_name" class="form-control selectpicker" required data-live-search="true">
                                <option value="">-- Select Field --</option>
                                <option value="student_name">Student Name</option>
                                <option value="email">Email Address</option>
                                <option value="phone">Phone Number</option>
                                <option value="address">Address</option>
                                <option value="date_of_birth">Date of Birth</option>
                                <option value="passport_number">Passport Number</option>
                                <option value="item_id">Package Selection</option>
                                <option value="payment_status">Payment Status</option>
                                <option value="amount_paid">Amount Paid</option>
                                <option value="notes">Notes</option>
                                <option value="other">Other (Specify in reason)</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_value">New Value <span class="text-danger">*</span></label>
                            <textarea name="new_value" id="new_value" class="form-control" rows="3" required
                                      placeholder="Enter the new value you want for this field"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="reason">Reason for Change <span class="text-danger">*</span></label>
                            <textarea name="reason" id="reason" class="form-control" rows="4" required
                                      placeholder="Explain why this change is needed (e.g., 'Passport number updated', 'Name correction required')"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fa fa-paper-plane"></i> Submit Request
                    </button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<!-- Initialize DataTables -->
<script>
$(document).ready(function() {
    <?php if (!empty($candidates)): ?>
    // Initialize DataTables
    var table = $('#candidates-table').DataTable({
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "order": [[0, "asc"]],
        "columnDefs": [
            { "orderable": false, "targets": 0 }
        ],
        "language": {
            "search": "Search candidates:",
            "lengthMenu": "Show _MENU_ candidates per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ candidates",
            "infoEmpty": "No candidates available",
            "infoFiltered": "(filtered from _MAX_ total candidates)",
            "zeroRecords": "No matching candidates found",
            "emptyTable": "No candidates available"
        }
    });
    
    // Re-number rows after sorting/filtering
    table.on('order.dt search.dt', function () {
        table.column(0, {search:'applied', order:'applied'}).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();
    <?php endif; ?>
});

/**
 * Open Change Request Modal
 * @param {int} studentId - The student ID
 * @param {string} studentName - The student's name
 */
function openChangeRequestModal(studentId, studentName) {
    // Set student information in modal
    $('#modal-student-id').val(studentId);
    $('#modal-student-name').text(studentName);
    
    // Reset form fields
    $('#field_name').val('').selectpicker('refresh');
    $('#new_value').val('');
    $('#reason').val('');
    
    // Show modal
    $('#changeRequestModal').modal('show');
}
</script>
<?php init_tail(); ?>