<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Candidate Registration Review'; ?> - Safe Legal Solutions</title>
    
    <!-- Bootstrap & FontAwesome -->
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
            background: #f5f5f5;
            color: #333;
            padding: 30px 15px;
            line-height: 1.6;
        }
        
        .review-wrapper {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .review-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        
        /* Header */
        .review-header {
            background: #1a1a1a;
            color: white;
            padding: 30px;
            text-align: center;
            border-bottom: 4px solid #d4af37;
        }
        
        .review-header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: 600;
        }
        
        .review-header h1 i {
            color: #d4af37;
            margin-right: 10px;
        }
        
        .review-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 15px;
        }
        
        /* Body */
        .review-body {
            padding: 40px;
        }
        
        /* Error Message */
        .error-message {
            background: #fff3cd;
            color: #856404;
            padding: 15px 20px;
            border-radius: 4px;
            margin-bottom: 25px;
            border-left: 4px solid #ffc107;
        }
        
        .error-message i {
            margin-right: 8px;
        }
        
        /* Review Sections */
        .review-section {
            margin-bottom: 25px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            overflow: hidden;
        }
        
        .review-section-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .review-section-header h4 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
        }
        
        .review-section-header i {
            margin-right: 10px;
            color: #d4af37;
        }
        
        .review-section-body {
            padding: 20px;
            background: white;
        }
        
        .review-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .review-item:last-child {
            border-bottom: none;
        }
        
        .review-label {
            font-weight: 500;
            color: #666;
            flex: 0 0 180px;
        }
        
        .review-value {
            flex: 1;
            color: #1a1a1a;
            font-weight: 500;
            text-align: right;
        }
        
        /* Package Info Card */
        .package-info-card {
            background: linear-gradient(135deg, #1a1a1a 0%, #333 100%);
            color: white;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .package-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #d4af37;
        }
        
        .package-duration {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 15px;
        }
        
        .package-features {
            margin: 15px 0 0 0;
        }
        
        .package-features ul {
            list-style: none;
            padding: 0;
            margin: 0;
            columns: 2;
            column-gap: 20px;
        }
        
        .package-features li {
            padding: 6px 0;
            font-size: 14px;
            break-inside: avoid;
        }
        
        .package-features li i {
            color: #d4af37;
            margin-right: 8px;
        }
        
        /* Price Summary */
        .price-summary {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 6px;
            border: 2px solid #e0e0e0;
            margin-top: 20px;
        }
        
        .price-summary h4 {
            margin: 0 0 15px 0;
            font-size: 18px;
            color: #1a1a1a;
        }
        
        .price-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 16px;
        }
        
        .price-label {
            color: #666;
        }
        
        .price-value {
            font-weight: 600;
            color: #1a1a1a;
        }
        
        .price-row.subtotal {
            border-top: 1px solid #ddd;
            margin-top: 5px;
            padding-top: 15px;
        }
        
        .price-row.total {
            border-top: 2px solid #1a1a1a;
            margin-top: 10px;
            padding-top: 15px;
            font-size: 22px;
            font-weight: bold;
        }
        
        .price-row.total .price-value {
            color: #28a745;
            font-size: 26px;
        }
        
        /* Payment Button */
        .btn-payment {
            background: #28a745;
            color: white;
            padding: 18px 50px;
            font-size: 20px;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            margin-top: 25px;
        }
        
        .btn-payment:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
            color: white;
        }
        
        .btn-payment:active {
            transform: translateY(0);
        }
        
        .btn-payment i {
            margin-right: 10px;
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
            padding: 12px 30px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            margin-top: 15px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-back:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
        }
        
        .btn-back i {
            margin-right: 8px;
        }
        
        /* Security Note */
        .security-note {
            text-align: center;
            margin-top: 25px;
            padding: 15px;
            background: #e8f5e9;
            border-radius: 4px;
        }
        
        .security-note i {
            color: #28a745;
            margin-right: 8px;
        }
        
        .security-note small {
            color: #666;
            font-size: 14px;
        }
        
        /* Payment Info Banner */
        .payment-info {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 4px;
        }
        
        .payment-info i {
            color: #856404;
            margin-right: 8px;
        }
        
        .payment-info strong {
            color: #856404;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .review-body {
                padding: 25px 20px;
            }
            
            .review-header {
                padding: 25px 20px;
            }
            
            .review-header h1 {
                font-size: 24px;
            }
            
            .review-label {
                flex: 0 0 120px;
                font-size: 14px;
            }
            
            .review-value {
                font-size: 14px;
            }
            
            .package-name {
                font-size: 20px;
            }
            
            .package-features ul {
                columns: 1;
            }
            
            .price-row {
                font-size: 14px;
            }
            
            .price-row.total {
                font-size: 18px;
            }
            
            .price-row.total .price-value {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>

<div class="review-wrapper">
    <div class="review-container">
        <!-- Header -->
        <div class="review-header">
            <h1><i class="fa fa-file-text-o"></i> Review Candidate Details</h1>
            <p>Please verify your information before completing registration</p>
        </div>
        
        <!-- Body -->
        <div class="review-body">
            <!-- Error Message -->
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <i class="fa fa-exclamation-triangle"></i> 
                    <strong>Error:</strong> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <!-- Payment Info Banner -->
            <div class="payment-info">
                <i class="fa fa-info-circle"></i> 
                <strong>Note:</strong> Payment gateway integration is currently in progress. For now, clicking "Complete Registration" will register your application without actual payment processing.
            </div>
            
            <!-- Personal Information -->
            <div class="review-section">
                <div class="review-section-header">
                    <h4><i class="fa fa-user"></i> Personal Information</h4>
                </div>
                <div class="review-section-body">
                    <div class="review-item">
                        <div class="review-label">Full Name:</div>
                        <div class="review-value"><?php echo htmlspecialchars($registration_data['student_name']); ?></div>
                    </div>
                    <div class="review-item">
                        <div class="review-label">Date of Birth:</div>
                        <div class="review-value"><?php echo date('d M Y', strtotime($registration_data['date_of_birth'])); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="review-section">
                <div class="review-section-header">
                    <h4><i class="fa fa-phone"></i> Contact Information</h4>
                </div>
                <div class="review-section-body">
                    <div class="review-item">
                        <div class="review-label">Email:</div>
                        <div class="review-value"><?php echo htmlspecialchars($registration_data['email']); ?></div>
                    </div>
                    <div class="review-item">
                        <div class="review-label">Phone:</div>
                        <div class="review-value"><?php echo htmlspecialchars($registration_data['phone']); ?></div>
                    </div>
                    <div class="review-item">
                        <div class="review-label">Address:</div>
                        <div class="review-value"><?php echo htmlspecialchars($registration_data['address']); ?></div>
                    </div>
                </div>
            </div>
            
         
            
            <!-- Selected Package -->
            <div class="review-section">
                <div class="review-section-header">
                    <h4><i class="fa fa-shopping-cart"></i> Selected Package</h4>
                </div>
                <div class="review-section-body">
                    <div class="package-info-card">
                        <div class="package-name"><?php echo htmlspecialchars($item->item_name); ?></div>
                        <?php if (!empty($item->duration_months)): ?>
                            <div class="package-duration">
                                <i class="fa fa-clock-o"></i> Duration: <?php echo $item->duration_months; ?> months
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($item->description)): ?>
                            <p style="margin: 10px 0; opacity: 0.9;"><?php echo htmlspecialchars($item->description); ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($item->features)): ?>
                            <?php $features = json_decode($item->features, true); ?>
                            <?php if (!empty($features)): ?>
                                <div class="package-features">
                                    <ul>
                                        <?php foreach ($features as $feature): ?>
                                            <li>
                                                <i class="fa fa-check-circle"></i> 
                                                <?php echo htmlspecialchars($feature); ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Price Summary -->
                    <div class="price-summary">
                        <h4><i class="fa fa-calculator"></i> Price Breakdown</h4>
                        
                        <div class="price-row">
                            <div class="price-label">Package Base Price:</div>
                            <div class="price-value">₹<?php echo number_format($item->base_price, 2); ?></div>
                        </div>
                        
                        <div class="price-row">
                            <div class="price-label">GST (<?php echo number_format($item->gst_percentage, 2); ?>%):</div>
                            <div class="price-value">₹<?php echo number_format($item->gst_amount, 2); ?></div>
                        </div>
                        
                        <div class="price-row total">
                            <div class="price-label">Total Amount:</div>
                            <div class="price-value">₹<?php echo number_format($item->total_price, 2); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Payment Form -->
            <?php echo form_open(current_url(), ['id' => 'payment-form']); ?>
                <button type="submit" class="btn-payment" id="payment-btn">
                    <i class="fa fa-check-circle"></i> Complete Registration (₹<?php echo number_format($item->total_price, 2); ?>)
                </button>
            <?php echo form_close(); ?>
            
            <!-- Back Button -->
            <a href="<?php echo site_url('safelegalsolutions/safelegalsolutions_client/register/' . (isset($token) ? $token : '')); ?>" class="btn-back">
                <i class="fa fa-arrow-left"></i> Back to Edit Details
            </a>
            
            <!-- Security Note -->
            <div class="security-note">
                <small>
                    <i class="fa fa-shield"></i> 
                    Your information is secure and will be kept confidential
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="<?php echo base_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>"></script>

<script>
$(document).ready(function() {
    // Prevent double submission
    var isSubmitting = false;
    
    $('#payment-form').on('submit', function(e) {
        if (isSubmitting) {
            e.preventDefault();
            return false;
        }
        
        // Confirm before submission
        if (!confirm('Confirm registration and proceed with payment of ₹<?php echo number_format($item->total_price, 2); ?>?')) {
            e.preventDefault();
            return false;
        }
        
        isSubmitting = true;
        
        // Disable button and show loading
        $('#payment-btn').prop('disabled', true)
                         .html('<i class="fa fa-spinner fa-spin"></i> Processing...');
        
        return true;
    });
});
</script>

</body>
</html>