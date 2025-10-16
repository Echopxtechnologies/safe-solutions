<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review & Payment - Safe Legal Solutions</title>
    <link href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/plugins/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 30px 15px;
        }
        
        .review-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        /* Header */
        .review-header {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .review-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, rgba(26, 26, 26, 0) 100%);
        }
        
        .review-header h1 {
            margin: 0 0 10px 0;
            font-size: 32px;
            position: relative;
            z-index: 1;
        }
        
        .review-header h1 i {
            color: #d4af37;
            margin-right: 10px;
        }
        
        .review-header p {
            margin: 0;
            font-size: 16px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        /* Body */
        .review-body {
            padding: 40px;
        }
        
        .section-title {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 15px 20px;
            margin: -40px -40px 30px -40px;
            border-bottom: 3px solid #667eea;
        }
        
        .section-title h3 {
            margin: 0;
            font-size: 20px;
            color: #1a1a1a;
        }
        
        .section-title i {
            color: #667eea;
            margin-right: 10px;
        }
        
        /* Info Groups */
        .info-group {
            margin-bottom: 30px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            background: white;
        }
        
        .info-group h4 {
            color: #1a1a1a;
            margin-bottom: 20px;
            font-size: 18px;
            border-bottom: 2px solid #d4af37;
            padding-bottom: 10px;
        }
        
        .info-group h4 i {
            color: #667eea;
            margin-right: 10px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
            align-items: flex-start;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            color: #666;
            font-weight: 500;
            flex: 0 0 40%;
        }
        
        .info-value {
            color: #1a1a1a;
            font-weight: 600;
            flex: 0 0 58%;
            text-align: right;
            word-break: break-word;
        }
        
        /* Price Summary */
        .price-summary {
            background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            border: 2px solid #e9ecef;
        }
        
        .price-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 16px;
        }
        
        .price-row.total {
            border-top: 2px solid #1a1a1a;
            margin-top: 10px;
            padding-top: 15px;
            font-size: 24px;
            font-weight: bold;
            color: #1a1a1a;
        }
        
        .price-row.total .amount {
            color: #28a745;
        }
        
        /* Payment Section */
        .payment-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 40px 30px;
            margin: 30px 0;
            text-align: center;
            color: white;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .payment-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .payment-section h4 {
            color: white;
            margin-bottom: 15px;
            font-size: 24px;
            position: relative;
            z-index: 1;
        }
        
        .payment-section h4 i {
            color: #d4af37;
            margin-right: 10px;
        }
        
        .payment-section p {
            margin-bottom: 25px;
            font-size: 16px;
            opacity: 0.95;
            position: relative;
            z-index: 1;
        }
        
        /* Payment Button */
        .btn-pay-now {
            background: #28a745;
            color: white;
            padding: 18px 50px;
            font-size: 20px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            display: inline-block;
            text-decoration: none;
            position: relative;
            z-index: 1;
        }
        
        .btn-pay-now:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.4);
            color: white;
            text-decoration: none;
        }
        
        .btn-pay-now:active {
            transform: translateY(0);
        }
        
        .btn-pay-now i {
            margin-right: 10px;
        }
        
        /* Free Package Button */
        .btn-pay-now.free {
            background: #17a2b8;
        }
        
        .btn-pay-now.free:hover {
            background: #138496;
        }
        
        /* Security Note */
        .security-note {
            margin-top: 20px;
            padding: 15px;
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            position: relative;
            z-index: 1;
        }
        
        .security-note i {
            color: #d4af37;
            margin-right: 8px;
        }
        
        .security-note small {
            font-size: 14px;
            opacity: 0.95;
        }
        
        /* Alert Info */
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #17a2b8;
            box-shadow: 0 2px 8px rgba(23, 162, 184, 0.2);
        }
        
        .alert-info i {
            margin-right: 8px;
        }
        
        /* Back Button */
        .btn-back {
            background: #6c757d;
            color: white;
            padding: 12px 30px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            margin-top: 15px;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .btn-back:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }
        
        .btn-back i {
            margin-right: 8px;
        }
        
        /* Error Message */
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.2);
        }
        
        .error-message i {
            margin-right: 8px;
        }
        
        /* Loading Overlay */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.8);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        
        .loading-overlay.active {
            display: flex;
        }
        
        .loading-content {
            background: white;
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 15px 10px;
            }
            
            .review-body {
                padding: 25px 20px;
            }
            
            .review-header {
                padding: 30px 20px;
            }
            
            .review-header h1 {
                font-size: 24px;
            }
            
            .section-title {
                margin: -25px -20px 25px -20px;
            }
            
            .info-group {
                padding: 20px 15px;
            }
            
            .info-row {
                flex-direction: column;
                gap: 5px;
            }
            
            .info-label,
            .info-value {
                flex: 1;
                text-align: left;
            }
            
            .payment-section {
                padding: 30px 20px;
            }
            
            .btn-pay-now {
                padding: 16px 40px;
                font-size: 18px;
                width: 100%;
            }
            
            .price-row.total {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-content">
        <div class="loading-spinner"></div>
        <h4>Processing Payment...</h4>
        <p>Please wait while we redirect you to the payment gateway</p>
    </div>
</div>

<div class="review-container">
    <div class="review-header">
        <h1><i class="fa fa-check-circle"></i> Review Your Registration</h1>
        <p>Please verify your information before proceeding to payment</p>
    </div>
    
    <div class="review-body">
        <div class="section-title">
            <h3><i class="fa fa-file-text"></i> Registration Summary</h3>
        </div>
        
        <?php if (isset($error) && !empty($error)): ?>
            <div class="error-message">
                <i class="fa fa-exclamation-triangle"></i> 
                <strong>Error:</strong> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <!-- Personal Information -->
        <div class="info-group">
            <h4><i class="fa fa-user"></i> Personal Information</h4>
            <div class="info-row">
                <span class="info-label">Full Name:</span>
                <span class="info-value"><?php echo htmlspecialchars($registration_data['student_name']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Date of Birth:</span>
                <span class="info-value"><?php echo date('F d, Y', strtotime($registration_data['date_of_birth'])); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Passport Number:</span>
                <span class="info-value"><?php echo htmlspecialchars($registration_data['passport_number']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value"><?php echo htmlspecialchars($registration_data['email']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span class="info-value"><?php echo htmlspecialchars($registration_data['phone']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Address:</span>
                <span class="info-value"><?php echo nl2br(htmlspecialchars($registration_data['address'])); ?></span>
            </div>
        </div>
        
        <!-- Package Information -->
        <div class="info-group">
            <h4><i class="fa fa-graduation-cap"></i> Selected Package</h4>
            <div class="info-row">
                <span class="info-label">Package Name:</span>
                <span class="info-value"><?php echo htmlspecialchars($item->item_name); ?></span>
            </div>
            <?php if (!empty($item->item_code)): ?>
            <div class="info-row">
                <span class="info-label">Package Code:</span>
                <span class="info-value"><?php echo htmlspecialchars($item->item_code); ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($item->duration_months)): ?>
            <div class="info-row">
                <span class="info-label">Duration:</span>
                <span class="info-value"><?php echo $item->duration_months; ?> months</span>
            </div>
            <?php endif; ?>
            <?php if (!empty($item->description)): ?>
            <div class="info-row">
                <span class="info-label">Description:</span>
                <span class="info-value"><?php echo htmlspecialchars($item->description); ?></span>
            </div>
            <?php endif; ?>
            
            <!-- Price Breakdown -->
            <div class="price-summary">
                <div class="price-row">
                    <span>Base Price:</span>
                    <span>₹<?php echo number_format($item->base_price, 2); ?></span>
                </div>
                <?php if ($item->gst_amount > 0): ?>
                <div class="price-row">
                    <span>GST (<?php echo number_format($item->gst_percentage, 2); ?>%):</span>
                    <span>₹<?php echo number_format($item->gst_amount, 2); ?></span>
                </div>
                <?php endif; ?>
                <div class="price-row total">
                    <span>Total Amount:</span>
                    <span class="amount">₹<?php echo number_format($item->total_price, 2); ?></span>
                </div>
            </div>
        </div>
        
        <!-- PAYMENT SECTION -->
        <div class="payment-section">
            <h4><i class="fa fa-credit-card"></i> Ready to Complete Your Registration?</h4>
            <p>
                Your registration details have been saved. Click below to proceed with secure payment.
            </p>
            
            <?php if ($item->total_price > 0): ?>
                <a href="<?php echo site_url('safelegalsolutions/safelegalsolutions_client/purchase'); ?>" 
                   class="btn-pay-now"
                   id="payButton">
                    <i class="fa fa-lock"></i> Pay ₹<?php echo number_format($item->total_price, 2); ?> Now
                </a>
            <?php else: ?>
                <a href="<?php echo site_url('safelegalsolutions/safelegalsolutions_client/purchase'); ?>" 
                   class="btn-pay-now free"
                   id="payButton">
                    <i class="fa fa-check"></i> Complete Free Enrollment
                </a>
            <?php endif; ?>
            
            <div class="security-note">
                <i class="fa fa-shield"></i>
                <small>Secured by Razorpay's 256-bit SSL encryption</small>
            </div>
        </div>
        
        <div class="alert-info">
            <strong><i class="fa fa-info-circle"></i> What happens next?</strong><br>
            After successful payment, your client portal account will be automatically created. You will receive login credentials via email to access your dashboard, course materials, and track your progress.
        </div>
        
        <a href="<?php echo site_url('safelegalsolutions/safelegalsolutions_client/register/' . (isset($token) ? $token : '')); ?>" 
           class="btn btn-back">
            <i class="fa fa-arrow-left"></i> Edit Registration Details
        </a>
    </div>
</div>

<script src="<?php echo base_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>
<script>
$(document).ready(function() {
    // Show loading overlay when payment button is clicked
    $('#payButton').on('click', function(e) {
        $('#loadingOverlay').addClass('active');
        
        // Fallback: Remove loading after 10 seconds if still showing
        setTimeout(function() {
            $('#loadingOverlay').removeClass('active');
        }, 10000);
    });
});
</script>

</body>
</html>