<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="panel_s">
    <div class="panel-body">
        
        <!-- Header -->
        <div class="row">
            <div class="col-md-10">
                <h3 class="no-margin">
                    <i class="fa fa-user"></i> My Complete Profile
                </h3>
            </div>
            <div class="col-md-2 text-right">
                <a href="<?php echo site_url('clients/safelegalsolutions_client/my_dashboard'); ?>" 
                   class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
        <hr>

        <!-- Profile Status Banner -->
        <div class="row">
            <div class="col-md-12">
                <div class="alert <?php 
                    echo $student->status == 'approved' ? 'alert-success' : 
                         ($student->status == 'pending_review' ? 'alert-warning' : 'alert-info'); 
                ?>" style="border-left: 5px solid;">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 style="margin-top: 0;">
                                <i class="fa <?php 
                                    echo $student->status == 'approved' ? 'fa-check-circle' : 
                                         ($student->status == 'pending_review' ? 'fa-clock-o' : 'fa-info-circle'); 
                                ?>"></i> 
                                Registration Status: 
                                <strong><?php 
                                    $status_display = [
                                        'draft' => 'Draft',
                                        'pending_review' => 'Under Review',
                                        'approved' => 'Approved',
                                        'locked' => 'Approved & Verified'
                                    ];
                                    echo $status_display[$student->status] ?? ucfirst($student->status);
                                ?></strong>
                            </h4>
                            <p style="margin-bottom: 0;">
                                <?php if ($student->status == 'approved' || $student->status == 'locked'): ?>
                                    Your profile has been approved and verified by our team.
                                <?php elseif ($student->status == 'pending_review'): ?>
                                    Your profile is currently under review. We'll notify you once it's approved.
                                <?php else: ?>
                                    Your registration is in progress. Please complete all required information.
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-4 text-right">
                            <?php if ($student->is_locked == 1): ?>
                                <div style="margin-top: 15px;">
                                    <span class="label label-success" style="font-size: 18px; padding: 10px 20px;">
                                        <i class="fa fa-lock"></i> VERIFIED
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-user"></i> Personal Information
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td style="width: 40%; font-weight: bold; background: #f8f9fa;">
                                                <i class="fa fa-user"></i> Full Name
                                            </td>
                                            <td><?php echo htmlspecialchars($student->student_name); ?></td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold; background: #f8f9fa;">
                                                <i class="fa fa-envelope"></i> Email
                                            </td>
                                            <td>
                                                <a href="mailto:<?php echo $student->email; ?>">
                                                    <?php echo htmlspecialchars($student->email); ?>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold; background: #f8f9fa;">
                                                <i class="fa fa-phone"></i> Phone
                                            </td>
                                            <td>
                                                <a href="tel:<?php echo $student->phone; ?>">
                                                    <?php echo htmlspecialchars($student->phone); ?>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td style="width: 40%; font-weight: bold; background: #f8f9fa;">
                                                <i class="fa fa-calendar"></i> Date of Birth
                                            </td>
                                            <td><?php echo date('d M Y', strtotime($student->date_of_birth)); ?></td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold; background: #f8f9fa;">
                                                <i class="fa fa-map-marker"></i> Address
                                            </td>
                                            <td><?php echo nl2br(htmlspecialchars($student->address)); ?></td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold; background: #f8f9fa;">
                                                <i class="fa fa-barcode"></i> Student ID
                                            </td>
                                            <td><code>#<?php echo str_pad($student->id, 6, '0', STR_PAD_LEFT); ?></code></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course & Branch Information -->
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-graduation-cap"></i> Course Information
                        </h4>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td style="width: 45%; font-weight: bold; background: #f8f9fa;">
                                        <i class="fa fa-book"></i> Course Applied
                                    </td>
                                    
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; background: #f8f9fa;">
                                        <i class="fa fa-calendar-check-o"></i> Registration Date
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($student->created_at)); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-building"></i> Partner Details
                        </h4>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td style="width: 45%; font-weight: bold; background: #f8f9fa;">
                                        <i class="fa fa-building"></i> Branch Name
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($branch->branch_name); ?></strong>
                                        <?php if (!empty($branch->category_name)): ?>
                                            <br>
                                            <span class="badge" style="background: <?php echo $branch->color_code; ?>; margin-top: 5px;">
                                                <?php echo htmlspecialchars($branch->category_name); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; background: #f8f9fa;">
                                        <i class="fa fa-map-marker"></i> Location
                                    </td>
                                    <td><?php echo htmlspecialchars($branch->location); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; background: #f8f9fa;">
                                        <i class="fa fa-phone"></i> Contact
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
        </div>

        <!-- Package Details -->
        <?php if ($item): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-warning">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-shopping-cart"></i> Enrolled Package Details
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 style="margin-top: 0; color: #333;">
                                    <?php echo htmlspecialchars($item->item_name); ?>
                                </h4>
                                <p style="color: #666; font-size: 14px;">
                                    <strong>Package Code:</strong> <code><?php echo htmlspecialchars($item->item_code); ?></code>
                                </p>
                                
                                <?php if (!empty($item->description)): ?>
                                <div style="margin: 15px 0;">
                                    <strong>Description:</strong>
                                    <p><?php echo nl2br(htmlspecialchars($item->description)); ?></p>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($item->features)): ?>
                                    <?php $features = json_decode($item->features, true); ?>
                                    <?php if (!empty($features) && is_array($features)): ?>
                                    <div style="margin: 15px 0;">
                                        <strong>Features:</strong>
                                        <ul style="margin-top: 10px;">
                                            <?php foreach ($features as $feature): ?>
                                                <?php if (!empty($feature)): ?>
                                                <li style="padding: 5px 0;">
                                                    <i class="fa fa-check text-success"></i> 
                                                    <?php echo htmlspecialchars($feature); ?>
                                                </li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4">
                                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 2px solid #ffc107;">
                                    <h4 style="margin-top: 0; color: #666;">Pricing Details</h4>
                                    
                                    <div style="margin: 15px 0;">
                                        <span style="color: #666;">Base Price:</span>
                                        <strong style="float: right; font-size: 16px;">
                                            ₹<?php echo number_format($item->base_price, 2); ?>
                                        </strong>
                                        <div style="clear: both;"></div>
                                    </div>
                                    
                                    <div style="margin: 15px 0;">
                                        <span style="color: #666;">GST (<?php echo $item->gst_percentage; ?>%):</span>
                                        <strong style="float: right; font-size: 16px;">
                                            ₹<?php echo number_format($item->gst_amount, 2); ?>
                                        </strong>
                                        <div style="clear: both;"></div>
                                    </div>
                                    
                                    <hr style="border-top: 2px dashed #ddd;">
                                    
                                    <div style="margin: 15px 0;">
                                        <span style="color: #333; font-size: 18px;"><strong>Total Amount:</strong></span>
                                        <strong style="float: right; color: #28a745; font-size: 24px;">
                                            ₹<?php echo number_format($item->total_price, 2); ?>
                                        </strong>
                                        <div style="clear: both;"></div>
                                    </div>
                                    
                                    <?php if (!empty($item->duration_months)): ?>
                                    <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #ddd;">
                                        <i class="fa fa-clock-o"></i> 
                                        <strong>Duration:</strong> <?php echo $item->duration_months; ?> Months
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Payment Information -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel <?php echo (isset($student->payment_status) && $student->payment_status == 'paid') ? 'panel-success' : 'panel-default'; ?>">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-credit-card"></i> Payment Information
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td style="width: 45%; font-weight: bold; background: #f8f9fa;">
                                                <i class="fa fa-info-circle"></i> Payment Status
                                            </td>
                                            <td>
                                                <?php if (isset($student->payment_status)): ?>
                                                    <span class="label label-<?php echo $student->payment_status == 'paid' ? 'success' : 'warning'; ?>" style="font-size: 14px; padding: 5px 15px;">
                                                        <?php echo $student->payment_status == 'paid' ? 'PAID' : 'PENDING'; ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="label label-default">Not Set</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold; background: #f8f9fa;">
                                                <i class="fa fa-percent"></i> Payment Progress
                                            </td>
                                            <td>
                                                <?php $payment_pct = isset($student->payment_percentage) ? $student->payment_percentage : 0; ?>
                                                <div class="progress" style="margin-bottom: 5px;">
                                                    <div class="progress-bar progress-bar-success" style="width: <?php echo $payment_pct; ?>%;">
                                                        <?php echo $payment_pct; ?>%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td style="width: 45%; font-weight: bold; background: #f8f9fa;">
                                                <i class="fa fa-rupee"></i> Amount Paid
                                            </td>
                                            <td>
                                                <strong style="color: #28a745; font-size: 18px;">
                                                    ₹<?php echo isset($student->amount_paid) ? number_format($student->amount_paid, 2) : '0.00'; ?>
                                                </strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold; background: #f8f9fa;">
                                                <i class="fa fa-money"></i> Total Amount
                                            </td>
                                            <td>
                                                <strong style="font-size: 18px;">
                                                    ₹<?php echo isset($student->total_amount) ? number_format($student->total_amount, 2) : ($item ? number_format($item->total_price, 2) : '0.00'); ?>
                                                </strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <?php if (isset($student->payment_status) && $student->payment_status == 'paid'): ?>
                        <div class="alert alert-success" style="margin-top: 15px; margin-bottom: 0;">
                            <i class="fa fa-check-circle"></i> 
                            <strong>Payment Completed!</strong> Thank you for completing your payment.
                        </div>
                        <?php else: ?>
                        <div class="alert alert-warning" style="margin-top: 15px; margin-bottom: 0;">
                            <i class="fa fa-exclamation-triangle"></i> 
                            <strong>Payment Pending:</strong> Please complete your payment to activate all benefits.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Referral Information -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-share-alt"></i> Referral Information
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Your Unique Referral Code</h4>
                                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 2px dashed #007bff; text-align: center;">
                                    <h2 style="font-family: monospace; margin: 10px 0; letter-spacing: 3px; color: #333;">
                                        <?php echo htmlspecialchars($student->referral_code); ?>
                                    </h2>
                                    <button class="btn btn-primary" onclick="copyReferralCode()">
                                        <i class="fa fa-copy"></i> Copy Code
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h4>Referral Earnings</h4>
                                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; border-radius: 8px; text-align: center; color: white;">
                                    <h1 style="margin: 0; font-size: 48px;">
                                        ₹<?php echo isset($student->earnings) ? number_format($student->earnings, 2) : '0.00'; ?>
                                    </h1>
                                    <p style="margin: 10px 0 0 0; opacity: 0.9;">
                                        Total Earnings from Referrals
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Notes -->
        <?php if (!empty($student->notes)): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-sticky-note"></i> Additional Notes
                        </h4>
                    </div>
                    <div class="panel-body">
                        <?php echo nl2br(htmlspecialchars($student->notes)); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="row">
            <div class="col-md-12 text-center" style="margin-top: 20px;">
                <a href="<?php echo site_url('clients/safelegalsolutions_client/my_dashboard'); ?>" 
                   class="btn btn-primary btn-lg">
                    <i class="fa fa-dashboard"></i> Back to Dashboard
                </a>
                
                <a href="<?php echo site_url('clients/tickets/new_ticket'); ?>" 
                   class="btn btn-success btn-lg">
                    <i class="fa fa-ticket"></i> Contact Support
                </a>
                
                <button onclick="window.print();" class="btn btn-default btn-lg">
                    <i class="fa fa-print"></i> Print Profile
                </button>
            </div>
        </div>

    </div>
</div>

<script>
function copyReferralCode() {
    var code = '<?php echo $student->referral_code; ?>';
    
    var tempInput = document.createElement('input');
    tempInput.value = code;
    document.body.appendChild(tempInput);
    tempInput.select();
    
    try {
        document.execCommand('copy');
        alert('Referral code copied: ' + code);
    } catch (err) {
        prompt('Copy this code:', code);
    }
    
    document.body.removeChild(tempInput);
}
</script>