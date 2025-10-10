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
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="no-margin">
                                    <i class="fa fa-users"></i> Candidates Management
                                    <?php if (!is_admin() && is_sls_manager_or_admin()): ?>
                                        <span class="role-badge">
                                            <i class="fa fa-star"></i> SLS Manager
                                        </span>
                                    <?php endif; ?>
                                </h4>
                            </div>
                            <div class="col-md-4 text-right">
                                <?php if (has_permission('safelegalsolutions_students', '', 'create') || is_sls_manager_or_admin()): ?>
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
                                    // Get candidates with item/package information (DB table still sls_students)
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
                                        $serial_number = 1; // Initialize serial number counter
                                        foreach ($candidates as $candidate): 
                                    ?>
                                        <tr>
                                            <!-- Serial Number -->
                                            <td><?php echo $serial_number++; ?></td>
                                            
                                            <!-- Candidate Name with Actions Below -->
                                            <td>
                                                <div class="candidate-name">
                                                    <?php echo htmlspecialchars($candidate->student_name); ?>
                                                </div>
                                                <div class="action-links">
                                                    <!-- Edit Link - Everyone can edit -->
                                                    <a href="<?php echo admin_url('safelegalsolutions/student/' . $candidate->id); ?>">
                                                        Edit
                                                    </a>
                                                    
                                                    <!-- Submit for Review - NPM only, when profile is 100% complete -->
                                                    <?php if ($candidate->profile_completion == 100 && $candidate->status == 'draft' && $candidate->is_locked == 0): ?>
                                                        <span class="action-separator">|</span>
                                                        <a href="<?php echo admin_url('safelegalsolutions/submit_for_review/' . $candidate->id); ?>" 
                                                           onclick="return confirm('Submit this candidate profile for admin review?');">
                                                            Submit for Review
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Approve Button - Admin/Manager only, for pending reviews -->
                                                    <?php if (is_sls_manager_or_admin() && $candidate->status == 'pending_review'): ?>
                                                        <span class="action-separator">|</span>
                                                        <a href="<?php echo admin_url('safelegalsolutions/approve_student/' . $candidate->id); ?>" 
                                                           class="text-success"
                                                           onclick="return confirm('Approve and lock this candidate profile?');">
                                                            Approve
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Delete Button - NPM can delete unlocked, Admin/Manager can delete all -->
                                                    <?php if ((!is_sls_manager_or_admin() && $candidate->is_locked == 0) || is_sls_manager_or_admin()): ?>
                                                        <span class="action-separator">|</span>
                                                        <a href="<?php echo admin_url('safelegalsolutions/delete_student/' . $candidate->id); ?>" 
                                                           class="text-danger"
                                                           onclick="return confirm('Are you sure you want to delete this candidate?');">
                                                            Delete
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
                                                    <?php if (has_permission('safelegalsolutions_students', '', 'create') || is_sls_manager_or_admin()): ?>
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

<!-- Initialize DataTables -->
<script>
    $(document).ready(function() {
        <?php if (!empty($candidates)): ?>
        var table = $('#candidates-table').DataTable({
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "order": [[0, "asc"]],
            "columnDefs": [
                { "orderable": false, "targets": 0 } // Disable sorting on serial number
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
        
        // Renumber serial numbers on each page/sort/search
        table.on('order.dt search.dt', function () {
            table.column(0, {search:'applied', order:'applied'}).nodes().each(function (cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();
        <?php endif; ?>
    });
</script>

<?php init_tail(); ?>