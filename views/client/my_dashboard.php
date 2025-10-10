<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="panel_s">
    <div class="panel-body">
        
        <!-- Welcome Header -->
        <div class="row">
            <div class="col-md-12">
                <h3 class="no-margin">
                    <i class="fa fa-dashboard"></i> Welcome, <?php echo htmlspecialchars($student->student_name); ?>!
                </h3>
                <hr>
            </div>
        </div>

        <!-- Registration Status Overview -->
        <div class="row">
            <!-- Status Card -->
            <div class="col-md-3 col-sm-6">
                <div class="panel" style="border-left: 4px solid <?php 
                    echo $student->status == 'approved' ? '#28a745' : 
                         ($student->status == 'pending_review' ? '#ffc107' : '#6c757d'); 
                ?>;">
                    <div class="panel-body text-center" style="padding: 25px;">
                        <h4 style="margin: 0 0 10px 0; font-size: 16px; color: #666;">Registration Status</h4>
                        <h2 style="margin: 0; font-size: 24px; font-weight: bold; color: <?php 
                            echo $student->status == 'approved' ? '#28a745' : 
                                 ($student->status == 'pending_review' ? '#ffc107' : '#6c757d'); 
                        ?>;">
                            <?php 
                            $status_display = [
                                'draft' => 'Draft',
                                'pending_review' => 'Under Review',
                                'approved' => 'Approved',
                                'locked' => 'Approved'
                            ];
                            echo $status_display[$student->status] ?? ucfirst($student->status);
                            ?>
                        </h2>
                        <?php if ($student->is_locked == 1): ?>
                            <div style="margin-top: 10px;">
                                <span class="label label-success">
                                    <i class="fa fa-lock"></i> Verified
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Profile Completion -->
            <div class="col-md-3 col-sm-6">
                <div class="panel" style="border-left: 4px solid #17a2b8;">
                    <div class="panel-body text-center" style="padding: 25px;">
                        <h4 style="margin: 0 0 10px 0; font-size: 16px; color: #666;">Profile Completion</h4>
                        <h2 style="margin: 0; font-size: 24px; font-weight: bold; color: #17a2b8;">
                            <?php echo isset($student->profile_completion) ? $student->profile_completion : 0; ?>%
                        </h2>
                        <div class="progress" style="margin-top: 15px; height: 8px;">
                            <div class="progress-bar progress-bar-info" style="width: <?php echo isset($student->profile_completion) ? $student->profile_completion : 0; ?>%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Status -->
            <div class="col-md-3 col-sm-6">
                <div class="panel" style="border-left: 4px solid <?php 
                    echo (isset($student->payment_status) && $student->payment_status == 'paid') ? '#28a745' : '#ffc107'; 
                ?>;">
                    <div class="panel-body text-center" style="padding: 25px;">
                        <h4 style="margin: 0 0 10px 0; font-size: 16px; color: #666;">Payment Status</h4>
                        <h2 style="margin: 0; font-size: 24px; font-weight: bold; color: <?php 
                            echo (isset($student->payment_status) && $student->payment_status == 'paid') ? '#28a745' : '#ffc107'; 
                        ?>;">
                            <?php 
                            if (isset($student->payment_status)) {
                                echo $student->payment_status == 'paid' ? 'Paid' : 'Pending';
                            } else {
                                echo 'Pending';
                            }
                            ?>
                        </h2>
                        <?php if (isset($student->payment_percentage) && $student->payment_percentage > 0): ?>
                            <p style="margin: 10px 0 0 0; font-size: 12px; color: #666;">
                                <?php echo $student->payment_percentage; ?>% Complete
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Days Since Registration -->
            <div class="col-md-3 col-sm-6">
                <div class="panel" style="border-left: 4px solid #6f42c1;">
                    <div class="panel-body text-center" style="padding: 25px;">
                        <h4 style="margin: 0 0 10px 0; font-size: 16px; color: #666;">Member Since</h4>
                        <h2 style="margin: 0; font-size: 24px; font-weight: bold; color: #6f42c1;">
                            <?php echo $days_since_registration; ?>
                        </h2>
                        <p style="margin: 10px 0 0 0; font-size: 12px; color: #666;">
                            Days
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-bolt"></i> Quick Actions
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 text-center" style="margin-bottom: 15px;">
                                <a href="<?php echo site_url('clients/safelegalsolutions_client/my_profile'); ?>" 
                                   class="btn btn-primary btn-block btn-lg">
                                    <i class="fa fa-user fa-2x" style="display: block; margin-bottom: 10px;"></i>
                                    View Full Profile
                                </a>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 text-center" style="margin-bottom: 15px;">
                                <a href="#referral-section" class="btn btn-success btn-block btn-lg">
                                    <i class="fa fa-share-alt fa-2x" style="display: block; margin-bottom: 10px;"></i>
                                    My Referral Code
                                </a>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 text-center" style="margin-bottom: 15px;">
                                <a href="<?php echo site_url('clients/tickets'); ?>" class="btn btn-info btn-block btn-lg">
                                    <i class="fa fa-ticket fa-2x" style="display: block; margin-bottom: 10px;"></i>
                                    Support Tickets
                                </a>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 text-center" style="margin-bottom: 15px;">
                                <a href="<?php echo site_url('clients/profile'); ?>" class="btn btn-warning btn-block btn-lg">
                                    <i class="fa fa-cog fa-2x" style="display: block; margin-bottom: 10px;"></i>
                                    Account Settings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registration Details -->
        <div class="row">
            <!-- Course & Branch Info -->
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-graduation-cap"></i> Course & Branch Information
                        </h4>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered">
                            <tbody>
                                
                                <tr>
                                    <td style="font-weight: bold;">
                                        <i class="fa fa-building"></i> Branch
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($branch->branch_name); ?>
                                        <?php if (!empty($branch->category_name)): ?>
                                            <br>
                                            <span class="badge" style="background: <?php echo $branch->color_code; ?>;">
                                                <?php echo htmlspecialchars($branch->category_name); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">
                                        <i class="fa fa-map-marker"></i> Branch Location
                                    </td>
                                    <td><?php echo htmlspecialchars($branch->location); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">
                                        <i class="fa fa-phone"></i> Branch Contact
                                    </td>
                                    <td>
                                        <a href="tel:<?php echo $branch->contact_phone; ?>">
                                            <?php echo htmlspecialchars($branch->contact_phone); ?>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Package Details -->
            <div class="col-md-6">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-shopping-cart"></i> Package Details
                        </h4>
                    </div>
                    <div class="panel-body">
                        <?php if ($item): ?>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td style="width: 40%; font-weight: bold;">
                                            <i class="fa fa-cube"></i> Package Name
                                        </td>
                                        <td><?php echo htmlspecialchars($item->item_name); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;">
                                            <i class="fa fa-tag"></i> Package Code
                                        </td>
                                        <td><code><?php echo htmlspecialchars($item->item_code); ?></code></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;">
                                            <i class="fa fa-rupee"></i> Total Price
                                        </td>
                                        <td>
                                            <strong style="font-size: 18px; color: #28a745;">
                                                ₹<?php echo number_format($item->total_price, 2); ?>
                                            </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;">
                                            <i class="fa fa-clock-o"></i> Duration
                                        </td>
                                        <td>
                                            <?php echo !empty($item->duration_months) ? $item->duration_months . ' Months' : 'N/A'; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-muted">No package information available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Referral Section -->
        <div class="row" id="referral-section">
            <div class="col-md-12">
                <div class="panel panel-warning">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-share-alt"></i> Your Referral Program
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6 text-center">
                                <h4>Your Referral Code</h4>
                                <div style="background: #f8f9fa; padding: 30px; border-radius: 10px; border: 3px dashed #ffc107; margin: 20px 0;">
                                    <h1 style="font-family: monospace; font-size: 48px; margin: 0; color: #333; letter-spacing: 5px;">
                                        <?php echo htmlspecialchars($student->referral_code); ?>
                                    </h1>
                                </div>
                                <button class="btn btn-primary btn-lg" onclick="copyReferralCode()">
                                    <i class="fa fa-copy"></i> Copy Code
                                </button>
                            </div>
                            <div class="col-md-6">
                                <h4>How It Works</h4>
                                <div style="padding: 20px;">
                                    <ol style="font-size: 16px; line-height: 2;">
                                        <li>Share your unique referral code with friends and family</li>
                                        <li>When they register using your code, both of you benefit</li>
                                        <li>Track your referral earnings in real-time</li>
                                        <li>Earn rewards for every successful referral!</li>
                                    </ol>
                                </div>
                                
                                <?php if (isset($student->earnings) && $student->earnings > 0): ?>
                                <div class="alert alert-success" style="margin-top: 20px;">
                                    <h4 style="margin-top: 0;">
                                        <i class="fa fa-trophy"></i> Your Earnings
                                    </h4>
                                    <h2 style="margin: 10px 0;">
                                        ₹<?php echo number_format($student->earnings, 2); ?>
                                    </h2>
                                    <p style="margin-bottom: 0;">Keep sharing to earn more!</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity / Timeline -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-history"></i> Registration Timeline
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-badge success">
                                    <i class="fa fa-check"></i>
                                </div>
                                <div class="timeline-panel">
                                    <div class="timeline-heading">
                                        <h4 class="timeline-title">Registration Completed</h4>
                                        <p><small class="text-muted">
                                            <i class="fa fa-clock-o"></i> 
                                            <?php echo date('d M Y, h:i A', strtotime($student->created_at)); ?>
                                        </small></p>
                                    </div>
                                    <div class="timeline-body">
                                        <p>Your registration was successfully submitted and recorded in our system.</p>
                                    </div>
                                </div>
                            </div>

                            <?php if ($student->status == 'pending_review' || $student->status == 'approved' || $student->status == 'locked'): ?>
                            <div class="timeline-item">
                                <div class="timeline-badge warning">
                                    <i class="fa fa-clock-o"></i>
                                </div>
                                <div class="timeline-panel">
                                    <div class="timeline-heading">
                                        <h4 class="timeline-title">Under Review</h4>
                                        <p><small class="text-muted">
                                            <i class="fa fa-clock-o"></i> Processing
                                        </small></p>
                                    </div>
                                    <div class="timeline-body">
                                        <p>Your application is being reviewed by our team.</p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ($student->status == 'approved' || $student->status == 'locked'): ?>
                            <div class="timeline-item">
                                <div class="timeline-badge success">
                                    <i class="fa fa-check-circle"></i>
                                </div>
                                <div class="timeline-panel">
                                    <div class="timeline-heading">
                                        <h4 class="timeline-title">Approved & Verified</h4>
                                        <p><small class="text-muted">
                                            <i class="fa fa-clock-o"></i> 
                                            <?php echo !empty($student->locked_at) ? date('d M Y, h:i A', strtotime($student->locked_at)) : 'Recently'; ?>
                                        </small></p>
                                    </div>
                                    <div class="timeline-body">
                                        <p>Congratulations! Your registration has been approved and verified.</p>
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
</div>

<script>
function copyReferralCode() {
    var code = '<?php echo $student->referral_code; ?>';
    
    // Create temporary input
    var tempInput = document.createElement('input');
    tempInput.value = code;
    document.body.appendChild(tempInput);
    tempInput.select();
    
    try {
        document.execCommand('copy');
        alert('Referral code copied to clipboard: ' + code);
    } catch (err) {
        prompt('Copy this code:', code);
    }
    
    document.body.removeChild(tempInput);
}
</script>

<style>
/* Timeline Styles */
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: 20px;
    width: 3px;
    background: #e0e0e0;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
    padding-left: 60px;
}

.timeline-badge {
    position: absolute;
    left: 8px;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    text-align: center;
    line-height: 28px;
    color: white;
    z-index: 100;
}

.timeline-badge.success {
    background-color: #28a745;
}

.timeline-badge.warning {
    background-color: #ffc107;
}

.timeline-panel {
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 5px;
    padding: 15px;
}

.timeline-title {
    margin-top: 0;
    color: #333;
    font-size: 18px;
}

.timeline-body {
    margin-top: 10px;
}
</style>