<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Safe Legal Solutions</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .success-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .success-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .success-header {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .success-icon {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .success-icon i {
            font-size: 50px;
            color: #38ef7d;
        }
        .success-body {
            padding: 40px;
        }
        .info-card {
            background: #f8f9fa;
            border-left: 4px solid #38ef7d;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .info-card.warning {
            border-left-color: #ffc107;
            background: #fff3cd;
        }
        .info-card.info {
            border-left-color: #17a2b8;
            background: #d1ecf1;
        }
        .info-card.danger {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
        }
        .info-value {
            color: #212529;
            text-align: right;
        }
        .btn-custom {
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary-custom {
            background: #6c757d;
            color: white;
        }
        .btn-secondary-custom:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        .alert-custom {
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .alert-custom i {
            font-size: 30px;
        }
        .alert-success-custom {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .alert-warning-custom {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        .alert-danger-custom {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .credentials-box {
            background: #e7f3ff;
            border: 2px solid #0066cc;
            border-radius: 10px;
            padding: 25px;
            margin: 20px 0;
        }
        .credentials-box h4 {
            color: #0066cc;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .credential-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .credential-label {
            font-weight: 600;
            color: #495057;
        }
        .credential-value {
            font-family: monospace;
            font-size: 16px;
            color: #0066cc;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-card">
            <!-- Success Header -->
            <div class="success-header">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h1 style="margin: 0; font-size: 32px; font-weight: 700;">Registration Successful!</h1>
                <p style="margin: 10px 0 0 0; font-size: 18px; opacity: 0.9;">Welcome to Safe Legal Solutions</p>
            </div>

            <!-- Success Body -->
            <div class="success-body">
                
                <?php if ($payment_status === 'paid'): ?>
                    <!-- Payment Successful Message -->
                    <?php if ($client_account_created && $credentials_sent): ?>
                        <!-- NEW CUSTOMER - Credentials Sent -->
                        <div class="alert-custom alert-success-custom">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Account Created Successfully!</strong>
                                <p style="margin: 5px 0 0 0;">Your login credentials have been sent to <strong><?php echo $email; ?></strong></p>
                            </div>
                        </div>

                        <div class="credentials-box">
                            <h4>
                                <i class="fas fa-key"></i>
                                Your Login Credentials
                            </h4>
                            <div class="credential-item">
                                <span class="credential-label">Email:</span>
                                <span class="credential-value"><?php echo $email; ?></span>
                            </div>
                            <div class="credential-item">
                                <span class="credential-label">Password:</span>
                                <span class="credential-value">Sent to your email</span>
                            </div>
                            <p style="margin: 15px 0 0 0; color: #856404; background: #fff3cd; padding: 12px; border-radius: 6px;">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Important:</strong> Please change your password after your first login for security.
                            </p>
                        </div>

                    <?php elseif ($has_client_account): ?>
                        <!-- EXISTING CUSTOMER -->
                        <div class="alert-custom alert-warning-custom">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <strong>Account Already Exists!</strong>
                                <p style="margin: 5px 0 0 0;">Your login credentials have been sent again to <strong><?php echo $email; ?></strong></p>
                            </div>
                        </div>

                        <div class="info-card info">
                            <h5 style="margin-top: 0;"><i class="fas fa-envelope"></i> Check Your Email</h5>
                            <p style="margin-bottom: 0;">We've sent your login credentials to your registered email address. If you don't see it, please check your spam folder.</p>
                        </div>

                    <?php else: ?>
                        <!-- ACCOUNT CREATION FAILED -->
                        <div class="alert-custom alert-danger-custom">
                            <i class="fas fa-exclamation-circle"></i>
                            <div>
                                <strong>Payment Successful, but Account Creation Failed</strong>
                                <p style="margin: 5px 0 0 0;">Your payment was successful, but there was an issue creating your login account. Please contact support.</p>
                                <?php if (!empty($client_error)): ?>
                                    <p style="margin: 5px 0 0 0; font-size: 14px;"><strong>Error:</strong> <?php echo $client_error; ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($payment_id)): ?>
                        <div class="info-card">
                            <h5 style="margin-top: 0;"><i class="fas fa-receipt"></i> Payment Details</h5>
                            <div class="info-row">
                                <span class="info-label">Transaction ID:</span>
                                <span class="info-value"><?php echo $payment_id; ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Amount Paid:</span>
                                <span class="info-value">₹<?php echo number_format($item->total_price, 2); ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Payment Status:</span>
                                <span class="info-value"><span class="badge badge-success">Paid</span></span>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- Payment Pending or Free Package -->
                    <div class="alert-custom alert-warning-custom">
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Registration Pending</strong>
                            <p style="margin: 5px 0 0 0;">Your registration is incomplete. Please complete the payment process.</p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Student Information -->
                <div class="info-card">
                    <h5 style="margin-top: 0;"><i class="fas fa-user"></i> Your Information</h5>
                    <div class="info-row">
                        <span class="info-label">Name:</span>
                        <span class="info-value"><?php echo $student_name; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo $email; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Student ID:</span>
                        <span class="info-value"><strong><?php echo $student_id; ?></strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Referral Code:</span>
                        <span class="info-value"><strong><?php echo $referral_code; ?></strong></span>
                    </div>
                </div>

                <!-- Package Information -->
                <?php if ($item): ?>
                <div class="info-card">
                    <h5 style="margin-top: 0;"><i class="fas fa-box"></i> Package Details</h5>
                    <div class="info-row">
                        <span class="info-label">Package Name:</span>
                        <span class="info-value"><?php echo $item->item_name; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Base Price:</span>
                        <span class="info-value">₹<?php echo number_format($item->base_price, 2); ?></span>
                    </div>
                    <?php if ($item->gst_amount > 0): ?>
                    <div class="info-row">
                        <span class="info-label">GST (<?php echo $item->gst_percentage; ?>%):</span>
                        <span class="info-value">₹<?php echo number_format($item->gst_amount, 2); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="info-row">
                        <span class="info-label">Total Amount:</span>
                        <span class="info-value"><strong>₹<?php echo number_format($item->total_price, 2); ?></strong></span>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Branch Information -->
                <?php if ($branch): ?>
                <div class="info-card">
                    <h5 style="margin-top: 0;"><i class="fas fa-map-marker-alt"></i> Branch Information</h5>
                    <div class="info-row">
                        <span class="info-label">Branch Name:</span>
                        <span class="info-value"><?php echo $branch->branch_name; ?></span>
                    </div>
                    <?php if (!empty($branch->branch_city)): ?>
                    <div class="info-row">
                        <span class="info-label">Location:</span>
                        <span class="info-value"><?php echo $branch->branch_city; ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <?php if ($payment_status === 'paid' && ($client_account_created || $has_client_account)): ?>
                        <a href="<?php echo site_url('authentication/login'); ?>" class="btn btn-custom btn-primary-custom">
                            <i class="fas fa-sign-in-alt"></i> Login to Your Account
                        </a>
                    <?php endif; ?>
                    
                    <a href="<?php echo site_url('safelegalsolutions/safelegalsolutions_client/register'); ?>" class="btn btn-custom btn-secondary-custom">
                        <i class="fas fa-home"></i> Back to Home
                    </a>
                </div>

                <!-- Email Notice -->
                <?php if ($payment_status === 'paid' && $email_sent): ?>
                <div style="margin-top: 30px; padding: 20px; background: #e7f3ff; border-radius: 10px; text-align: center;">
                    <i class="fas fa-envelope" style="font-size: 40px; color: #0066cc; margin-bottom: 15px;"></i>
                    <p style="margin: 0; color: #495057; font-size: 16px;">
                        <strong>Check your email!</strong><br>
                        We've sent your login credentials and important information to <strong><?php echo $email; ?></strong>
                    </p>
                </div>
                <?php endif; ?>

                <!-- Support Contact -->
                <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 10px; text-align: center;">
                    <p style="margin: 0; color: #6c757d;">
                        <i class="fas fa-question-circle"></i> Need help? Contact our support team
                    </p>
                </div>

            </div>
        </div>
    </div>

    <script src="<?php echo base_url('assets/js/jquery-3.6.0.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/bootstrap.bundle.min.js'); ?>"></script>
</body>
</html>