<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
    .panel_s {
        border: 1px solid #d9d9d9;
        margin-bottom: 20px;
    }
    
    .change-requests-table {
        font-size: 13px;
    }
    
    .change-requests-table thead th {
        background-color: #f9fafb;
        font-weight: 600;
        color: #4a5568;
        text-transform: uppercase;
        font-size: 12px;
        border-bottom: 2px solid #e2e8f0;
        padding: 12px;
    }
    
    .change-requests-table tbody td {
        padding: 12px;
        vertical-align: middle;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .change-requests-table tbody tr:hover {
        background-color: #f7fafc;
    }
    
    .field-badge {
        display: inline-block;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 3px;
        background: #e3f2fd;
        color: #1976d2;
    }
    
    .status-badge {
        display: inline-block;
        padding: 5px 12px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 3px;
        text-transform: uppercase;
    }
    
    .status-badge.pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-badge.approved {
        background: #d4edda;
        color: #155724;
    }
    
    .status-badge.rejected {
        background: #f8d7da;
        color: #721c24;
    }
    
    .value-display {
        background: #f8f9fa;
        padding: 8px 12px;
        border-radius: 4px;
        border-left: 3px solid #dee2e6;
        margin: 5px 0;
        font-family: monospace;
        font-size: 12px;
    }
    
    .value-display.old {
        border-left-color: #dc3545;
    }
    
    .value-display.new {
        border-left-color: #28a745;
    }
    
    .action-btn {
        padding: 5px 12px;
        font-size: 12px;
        margin-right: 5px;
    }
    
    .stats-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        padding: 20px;
        margin-bottom: 20px;
        text-align: center;
    }
    
    .stats-card .number {
        font-size: 32px;
        font-weight: bold;
        color: #2d3748;
        margin: 10px 0;
    }
    
    .stats-card .label {
        font-size: 13px;
        color: #718096;
        text-transform: uppercase;
        font-weight: 600;
    }
    
    .stats-card.pending {
        border-left: 4px solid #ffc107;
    }
    
    .stats-card.approved {
        border-left: 4px solid #28a745;
    }
    
    .stats-card.rejected {
        border-left: 4px solid #dc3545;
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
                                    <i class="fa fa-exchange"></i> Change Requests Management
                                </h4>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?php echo admin_url('safelegalsolutions/students'); ?>" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Back to Candidates
                                </a>
                            </div>
                        </div>

                        <hr class="hr-panel-heading" />

                        <!-- Statistics Cards -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-card pending">
                                    <div class="label">Pending Requests</div>
                                    <div class="number"><?php echo isset($pending_count) ? $pending_count : 0; ?></div>
                                    <a href="<?php echo admin_url('safelegalsolutions/change_requests?status=pending'); ?>">
                                        View All
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stats-card approved">
                                    <div class="label">Approved</div>
                                    <div class="number"><?php echo isset($approved_count) ? $approved_count : 0; ?></div>
                                    <a href="<?php echo admin_url('safelegalsolutions/change_requests?status=approved'); ?>">
                                        View All
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stats-card rejected">
                                    <div class="label">Rejected</div>
                                    <div class="number"><?php echo isset($rejected_count) ? $rejected_count : 0; ?></div>
                                    <a href="<?php echo admin_url('safelegalsolutions/change_requests?status=rejected'); ?>">
                                        View All
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col-md-12">
                                <form method="get" action="<?php echo admin_url('safelegalsolutions/change_requests'); ?>">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Filter by Status</label>
                                                <select name="status" class="form-control" onchange="this.form.submit()">
                                                    <option value="">All Statuses</option>
                                                    <option value="pending" <?php echo $this->input->get('status') == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="approved" <?php echo $this->input->get('status') == 'approved' ? 'selected' : ''; ?>>Approved</option>
                                                    <option value="rejected" <?php echo $this->input->get('status') == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <div>
                                                    <a href="<?php echo admin_url('safelegalsolutions/change_requests'); ?>" class="btn btn-default">
                                                        <i class="fa fa-refresh"></i> Clear Filters
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Change Requests Table -->
                        <div class="table-responsive">
                            <table class="table change-requests-table" id="change-requests-table">
                                <thead>
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Candidate</th>
                                        <th>Field</th>
                                        <th>Old → New Value</th>
                                        <th>Reason</th>
                                        <th>Requested By</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th width="180">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($change_requests)): 
                                        $serial = 1;
                                        foreach ($change_requests as $request): 
                                    ?>
                                        <tr>
                                            <td><?php echo $serial++; ?></td>
                                            
                                            <!-- Candidate Name -->
                                            <td>
                                                <strong><?php echo htmlspecialchars($request->student_name); ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo htmlspecialchars($request->email); ?></small>
                                            </td>
                                            
                                            <!-- Field Name -->
                                            <td>
                                                <span class="field-badge">
                                                    <?php echo ucwords(str_replace('_', ' ', $request->field_name)); ?>
                                                </span>
                                            </td>
                                            
                                            <!-- Old → New Values -->
                                            <td>
                                                <div class="value-display old">
                                                    <strong>Old:</strong> <?php echo htmlspecialchars($request->old_value); ?>
                                                </div>
                                                <div class="value-display new">
                                                    <strong>New:</strong> <?php echo htmlspecialchars($request->new_value); ?>
                                                </div>
                                            </td>
                                            
                                            <!-- Reason -->
                                            <td>
                                                <div style="max-width: 250px;">
                                                    <?php echo nl2br(htmlspecialchars($request->reason)); ?>
                                                </div>
                                            </td>
                                            
                                            <!-- Requested By -->
                                            <td>
                                                <?php echo htmlspecialchars($request->requester_firstname . ' ' . $request->requester_lastname); ?>
                                            </td>
                                            
                                            <!-- Date -->
                                            <td>
                                                <?php echo date('d M Y', strtotime($request->created_at)); ?>
                                                <br>
                                                <small class="text-muted"><?php echo date('h:i A', strtotime($request->created_at)); ?></small>
                                            </td>
                                            
                                            <!-- Status -->
                                            <td>
                                                <span class="status-badge <?php echo $request->status; ?>">
                                                    <?php echo ucfirst($request->status); ?>
                                                </span>
                                                
                                                <?php if ($request->status == 'approved' || $request->status == 'rejected'): ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        by <?php echo htmlspecialchars($request->reviewer_firstname . ' ' . $request->reviewer_lastname); ?>
                                                        <br>
                                                        on <?php echo date('d M Y', strtotime($request->reviewed_at)); ?>
                                                    </small>
                                                <?php endif; ?>
                                                
                                                <?php if ($request->status == 'rejected' && !empty($request->review_notes)): ?>
                                                    <br>
                                                    <small class="text-danger">
                                                        <strong>Note:</strong> <?php echo htmlspecialchars($request->review_notes); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            
                                            <!-- Actions -->
                                            <td>
                                                <?php if ($request->status == 'pending'): ?>
                                                    <a href="<?php echo admin_url('safelegalsolutions/approve_change_request/' . $request->id); ?>" 
                                                       class="btn btn-success btn-xs action-btn"
                                                       onclick="return confirm('Approve this change request and apply the change?');">
                                                        <i class="fa fa-check"></i> Approve
                                                    </a>
                                                    
                                                    <button type="button" 
                                                            class="btn btn-danger btn-xs action-btn"
                                                            onclick="openRejectModal(<?php echo $request->id; ?>)">
                                                        <i class="fa fa-times"></i> Reject
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <a href="<?php echo admin_url('safelegalsolutions/delete_change_request/' . $request->id); ?>" 
                                                   class="btn btn-default btn-xs action-btn"
                                                   onclick="return confirm('Delete this change request?');">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php 
                                        endforeach;
                                    else: 
                                    ?>
                                        <tr>
                                            <td colspan="9" class="text-center">
                                                <div style="padding: 40px 0;">
                                                    <i class="fa fa-exchange" style="font-size: 48px; color: #ddd; margin-bottom: 15px; display: block;"></i>
                                                    <p class="text-muted" style="font-size: 16px;">No change requests found.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-times-circle"></i> Reject Change Request
                </h4>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <input type="hidden" name="request_id" id="reject-request-id">
                    
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i> 
                        Are you sure you want to reject this change request?
                    </div>
                    
                    <div class="form-group">
                        <label for="review_notes">Rejection Reason (Optional)</label>
                        <textarea name="review_notes" id="review_notes" class="form-control" rows="4"
                                  placeholder="Explain why this change request is being rejected..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-times"></i> Reject Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    <?php if (!empty($change_requests)): ?>
    $('#change-requests-table').DataTable({
        "pageLength": 25,
        "order": [[6, "desc"]],
        "columnDefs": [
            { "orderable": false, "targets": [0, 8] }
        ]
    });
    <?php endif; ?>
});

// Open Reject Modal
function openRejectModal(requestId) {
    $('#reject-request-id').val(requestId);
    $('#review_notes').val('');
    $('#rejectModal').modal('show');
}

// Handle Reject Form Submission
$('#rejectForm').on('submit', function(e) {
    e.preventDefault();
    
    var requestId = $('#reject-request-id').val();
    var reviewNotes = $('#review_notes').val();
    var submitBtn = $(this).find('button[type="submit"]');
    var originalText = submitBtn.html();
    
    submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Rejecting...');
    
    $.ajax({
        url: '<?php echo admin_url("safelegalsolutions/reject_change_request/"); ?>' + requestId,
        type: 'POST',
        data: { review_notes: reviewNotes },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', response.message);
                $('#rejectModal').modal('hide');
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            } else {
                alert_float('danger', response.message || 'Failed to reject change request');
                submitBtn.prop('disabled', false).html(originalText);
            }
        },
        error: function() {
            alert_float('danger', 'An error occurred');
            submitBtn.prop('disabled', false).html(originalText);
        }
    });
});
</script>

<?php init_tail(); ?>