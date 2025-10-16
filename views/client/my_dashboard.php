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

        <!-- Quick Info Cards -->
        <div class="row">
            <!-- Unique Student ID Card -->
            <div class="col-md-3 col-sm-6">
                <div class="panel" style="border-left: 4px solid #6f42c1; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="panel-body text-center" style="padding: 20px; color: white;">
                        <h4 style="margin: 0 0 5px 0; font-size: 14px; opacity: 0.9; color:black;">Student ID</h4>
                        <h2 style="margin: 0; font-size: 20px; font-weight: bold; font-family: monospace; letter-spacing: 2px; color:#6f42c1; ">
                            <?php echo !empty($student->unique_id) ? htmlspecialchars($student->unique_id) : 'N/A'; ?>
                        </h2>
                        <small style="opacity: 0.8; font-size: 11px; color:grey;">Unique Identifier</small>
                    </div>
                </div>
            </div>

            <!-- Passport Number Card -->
            <div class="col-md-3 col-sm-6">
                <div class="panel" style="border-left: 4px solid #17a2b8;">
                    <div class="panel-body text-center" style="padding: 20px;">
                        <h4 style="margin: 0 0 5px 0; font-size: 14px; color: #666;">Passport</h4>
                        <h2 style="margin: 0; font-size: 18px; font-weight: bold; color: #17a2b8; font-family: monospace;">
                            <?php echo !empty($student->passport_number) ? htmlspecialchars($student->passport_number) : 'Not Provided'; ?>
                        </h2>
                        <small style="color: #999; font-size: 11px;">Travel Document</small>
                    </div>
                </div>
            </div>

            <!-- Profile Completion -->
            <div class="col-md-3 col-sm-6">
                <div class="panel" style="border-left: 4px solid <?php echo ($student->profile_completion == 100) ? '#28a745' : '#ffc107'; ?>;">
                    <div class="panel-body text-center" style="padding: 20px;">
                        <h4 style="margin: 0 0 5px 0; font-size: 14px; color: #666;">Profile Completion</h4>
                        <h2 style="margin: 0; font-size: 24px; font-weight: bold; color: <?php echo ($student->profile_completion == 100) ? '#28a745' : '#ffc107'; ?>;">
                            <?php echo isset($student->profile_completion) ? $student->profile_completion : 0; ?>%
                        </h2>
                        
                    </div>
                </div>
            </div>

            <!-- Payment Status -->
            <div class="col-md-3 col-sm-6">
                <div class="panel" style="border-left: 4px solid <?php 
                    echo (isset($student->payment_status) && $student->payment_status == 'paid') ? '#28a745' : '#ffc107'; 
                ?>;">
                    <div class="panel-body text-center" style="padding: 20px;">
                        <h4 style="margin: 0 0 5px 0; font-size: 14px; color: #666;">Payment Status</h4>
                        <h2 style="margin: 0; font-size: 22px; font-weight: bold; color: <?php 
                            echo (isset($student->payment_status) && $student->payment_status == 'paid') ? '#28a745' : '#ffc107'; 
                        ?>;">
                            <?php 
                            if (isset($student->payment_status)) {
                                echo $student->payment_status == 'paid' ? 'PAID' : 'PENDING';
                            } else {
                                echo 'PENDING';
                            }
                            ?>
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Days Since Registration Card -->
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info" style="border-left: 5px solid #17a2b8;">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 style="margin-top: 0;">
                                <i class="fa fa-calendar"></i> 
                                You've been with us for 
                                <strong><?php echo $days_since_registration; ?> days</strong>
                            </h4>
                            <p style="margin-bottom: 0;">
                                Registration Date: <strong><?php echo date('d M Y', strtotime($student->created_at)); ?></strong>
                            </p>
                        </div>
                        <div class="col-md-4 text-right">
                            <div style="font-size: 48px; color: #17a2b8; line-height: 1;">
                                <?php echo $days_since_registration; ?>
                            </div>
                            <small style="color: #666;">DAYS</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Timeline Card -->
        <?php if ($enrollment): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-calendar"></i> Course Timeline
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <!-- Start Date -->
                            <div class="col-md-3 text-center">
                                <div style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
                                    <i class="fa fa-play-circle" style="font-size: 36px; color: #28a745; margin-bottom: 10px;"></i>
                                    <h5 style="margin: 10px 0 5px 0; color: #666;">Course Start Date</h5>
                                    <h3 style="margin: 0; color: #333; font-weight: bold;">
                                        <?php echo date('d M Y', strtotime($enrollment->start_date)); ?>
                                    </h3>
                                </div>
                            </div>

                            <!-- Duration -->
                            <div class="col-md-3 text-center">
                                <div style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
                                    <i class="fa fa-clock-o" style="font-size: 36px; color: #17a2b8; margin-bottom: 10px;"></i>
                                    <h5 style="margin: 10px 0 5px 0; color: #666;">Course Duration</h5>
                                    <h3 style="margin: 0; color: #333; font-weight: bold;">
                                        <?php echo $enrollment->duration_months; ?> Months
                                    </h3>
                                </div>
                            </div>

                            <!-- End Date -->
                            <div class="col-md-3 text-center">
                                <div style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
                                    <i class="fa fa-flag-checkered" style="font-size: 36px; color: #ffc107; margin-bottom: 10px;"></i>
                                    <h5 style="margin: 10px 0 5px 0; color: #666;">Course End Date</h5>
                                    <h3 style="margin: 0; color: #333; font-weight: bold;">
                                        <?php echo date('d M Y', strtotime($enrollment->end_date)); ?>
                                    </h3>
                                </div>
                            </div>

                            <!-- Days Remaining -->
                            <div class="col-md-3 text-center">
                                <div style="padding: 20px; background: <?php echo ($days_until_end >= 0) ? '#e8f5e9' : '#ffebee'; ?>; border-radius: 8px;">
                                    <i class="fa fa-hourglass-half" style="font-size: 36px; color: <?php echo ($days_until_end >= 0) ? '#4caf50' : '#f44336'; ?>; margin-bottom: 10px;"></i>
                                    <h5 style="margin: 10px 0 5px 0; color: #666;">
                                        <?php echo ($days_until_end >= 0) ? 'Days Remaining' : 'Course Ended'; ?>
                                    </h5>
                                    <h3 style="margin: 0; color: <?php echo ($days_until_end >= 0) ? '#4caf50' : '#f44336'; ?>; font-weight: bold;">
                                        <?php echo abs($days_until_end); ?>
                                    </h3>
                                    <?php if ($days_until_end < 0): ?>
                                        <small style="color: #666;">days ago</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                                            </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Registration Status Overview -->
        <div class="row">
            <div class="col-md-12">
                <div class="alert <?php 
                    echo $student->status == 'approved' ? 'alert-success' : 
                         ($student->status == 'pending_review' ? 'alert-warning' : 'alert-info'); 
                ?>" style="border-left: 5px solid;">
                    <h4 style="margin-top: 0;">
                        <i class="fa <?php 
                            echo $student->status == 'approved' ? 'fa-check-circle' : 
                                 ($student->status == 'pending_review' ? 'fa-clock-o' : 'fa-info-circle'); 
                        ?>"></i> 
                        Status: 
                        <strong><?php 
                            $status_display = [
                                'draft' => 'Draft',
                                'pending_review' => 'Under Review',
                                'approved' => 'Approved',
                                'active' => 'Active',
                                'locked' => 'Approved & Verified'
                            ];
                            echo $status_display[$student->status] ?? ucfirst($student->status);
                        ?></strong>
                        <?php if ($student->is_locked == 1): ?>
                            <span class="label label-success pull-right" style="font-size: 14px; padding: 8px 15px;">
                                <i class="fa fa-lock"></i> VERIFIED
                            </span>
                        <?php endif; ?>
                    </h4>
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
                            <div class="col-md-12 text-center">
                                <a href="https://safelegalsolutions.erpblr.in/safelegalsolutions/safelegalsolutions_client/my_profile" 
                                   class="btn btn-primary btn-lg" style="min-width: 250px;">
                                    <i class="fa fa-user fa-2x" style="display: block; margin-bottom: 10px;"></i>
                                    View Full Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Package & Branch Info -->
        <div class="row">
            <!-- Branch Info -->
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-building"></i> Branch Information
                        </h4>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td style="font-weight: bold; width: 40%;">
                                        <i class="fa fa-building"></i> Branch
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($branch->branch_name); ?>
                                        <?php if (!empty($branch->category_name)): ?>
                                            <br>
                                            <span class="badge" style="background: <?php echo $branch->color_code ?? '#007bff'; ?>; margin-top: 5px;">
                                                <?php echo htmlspecialchars($branch->category_name); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php if (!empty($branch->location)): ?>
                                <tr>
                                    <td style="font-weight: bold;">
                                        <i class="fa fa-map-marker"></i> Location
                                    </td>
                                    <td><?php echo htmlspecialchars($branch->location); ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($branch->contact_phone)): ?>
                                <tr>
                                    <td style="font-weight: bold;">
                                        <i class="fa fa-phone"></i> Contact
                                    </td>
                                    <td>
                                        <a href="tel:<?php echo $branch->contact_phone; ?>">
                                            <?php echo htmlspecialchars($branch->contact_phone); ?>
                                        </a>
                                    </td>
                                </tr>
                                <?php endif; ?>
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
                                            <i class="fa fa-cube"></i> Package
                                        </td>
                                        <td><?php echo htmlspecialchars($item->item_name); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;">
                                            <i class="fa fa-tag"></i> Code
                                        </td>
                                        <td><code><?php echo htmlspecialchars($item->item_code); ?></code></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;">
                                            <i class="fa fa-rupee"></i> Price
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

    </div>
</div>