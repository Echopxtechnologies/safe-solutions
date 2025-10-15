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
        
        .review-header {
            background: #1a1a1a;
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .review-header h1 {
            margin: 0;
            font-size: 28px;
        }
        
        .review-body {
            padding: 40px;
        }
        
        .section-title {
            background: #f8f9fa;
            padding: 15px 20px;
            margin: -40px -40px 30px -40px;
            border-bottom: 3px solid #1a1a1a;
        }
        
        .section-title h3 {
            margin: 0;
            font-size: 20px;
            color: #1a1a1a;
        }
        
        .info-group {
            margin-bottom: 30px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 25px;
        }
        
        .info-group h4 {
            color: #1a1a1a;
            margin-bottom: 20px;
            font-size: 18px;
            border-bottom: 2px solid #d4af37;
            padding-bottom: 10px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            color: #666;
            font-weight: 500;
        }
        
        .info-value {
            color: #1a1a1a;
            font-weight: 600;
        }
        
        .price-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
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
        
        /* Payment Section - NEW */
        .payment-section {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 8px;
            padding: 30px;
            margin: 30px 0;
        }
        
        .payment-section h4 {
            color: #856404;
            margin-bottom: 20px;
        }
        
        .payment-options {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .payment-option {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }
        
        .payment-option:hover {
            border-color: #1a1a1a;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .payment-option input[type="radio"] {
            margin-right: 15px;
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        
        .payment-option label {
            cursor: pointer;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .payment-option.selected {
            border-color: #1a1a1a;
            background: #f8f9fa;
        }
        
        .payment-amount-input {
            margin-top: 15px;
            display: none;
        }
        
        .payment-amount-input.active {
            display: block;
        }
        
        .payment-amount-input input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .btn-submit {
            background: #1a1a1a;
            color: white;
            padding: 16px 40px;
            font-size: 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            margin-top: 30px;
            transition: all 0.3s ease;
        }
        
        .btn-submit:hover {
            background: #000;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        
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
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #17a2b8;
        }
    </style>
</head>
<body>

<div class="review-container">
    <div class="review-header">
        <h1><i class="fa fa-check-circle"></i> Review & Complete Registration</h1>
        <p>Please review your information and complete payment</p>
    </div>
    
    <div class="review-body">
        <div class="section-title">
            <h3>Registration Summary</h3>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-triangle"></i> <?php echo $error; ?>
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
                <span class="info-label">Email:</span>
                <span class="info-value"><?php echo htmlspecialchars($registration_data['email']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span class="info-value"><?php echo htmlspecialchars($registration_data['phone']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Address:</span>
                <span class="info-value"><?php echo htmlspecialchars($registration_data['address']); ?></span>
            </div>
        </div>
        
        <!-- Package Information -->
        <div class="info-group">
            <h4><i class="fa fa-shopping-cart"></i> Selected Package</h4>
            <div class="info-row">
                <span class="info-label">Package Name:</span>
                <span class="info-value"><?php echo htmlspecialchars($item->item_name); ?></span>
            </div>
            <?php if (!empty($item->duration_months)): ?>
            <div class="info-row">
                <span class="info-label">Duration:</span>
                <span class="info-value"><?php echo $item->duration_months; ?> months</span>
            </div>
            <?php endif; ?>
            <div class="info-row">
                <span class="info-label">Description:</span>
                <span class="info-value"><?php echo htmlspecialchars($item->description); ?></span>
            </div>
            
            <!-- Price Breakdown -->
            <div class="price-summary">
                <div class="price-row">
                    <span>Base Price:</span>
                    <span>₹<?php echo number_format($item->base_price, 2); ?></span>
                </div>
                <div class="price-row">
                    <span>GST (<?php echo number_format($item->gst_percentage, 2); ?>%):</span>
                    <span>₹<?php echo number_format($item->gst_amount, 2); ?></span>
                </div>
                <div class="price-row total">
                    <span>Total Amount:</span>
                    <span>₹<?php echo number_format($item->total_price, 2); ?></span>
                </div>
            </div>
        </div>
        
        <!-- PAYMENT SECTION - NEW -->
        <?php echo form_open(current_url(), ['id' => 'payment-form']); ?>
            
            <div class="payment-section">
                <h4><i class="fa fa-credit-card"></i> Payment Information</h4>
                
                <div class="alert-info">
                    <strong><i class="fa fa-info-circle"></i> Important:</strong> 
                    Your client portal account will be created automatically once payment is confirmed as complete.
                </div>
                
                <div class="payment-options">
                    <!-- Option 1: Full Payment -->
                    <div class="payment-option" onclick="selectPayment('paid')">
                        <label>
                            <input type="radio" name="payment_status" value="paid" id="payment_paid" required>
                            <div>
                                <strong>Payment Completed (100%)</strong>
                                <div style="font-size: 14px; color: #666; margin-top: 5px;">
                                    I have completed full payment of ₹<?php echo number_format($item->total_price, 2); ?>
                                </div>
                            </div>
                        </label>
                        <div class="payment-amount-input" id="amount_paid_input">
                            <label>Paid Amount (₹):</label>
                            <input type="number" 
                                   name="amount_paid" 
                                   id="amount_paid" 
                                   step="0.01" 
                                   min="0" 
                                   max="<?php echo $item->total_price; ?>"
                                   value="<?php echo $item->total_price; ?>"
                                   placeholder="Enter amount paid">
                        </div>
                    </div>
                    
                    <!-- Option 2: Partial Payment -->
                    <div class="payment-option" onclick="selectPayment('partial')">
                        <label>
                            <input type="radio" name="payment_status" value="partial" id="payment_partial">
                            <div>
                                <strong>Partial Payment</strong>
                                <div style="font-size: 14px; color: #666; margin-top: 5px;">
                                    I have made a partial payment (Portal access will be granted after full payment)
                                </div>
                            </div>
                        </label>
                        <div class="payment-amount-input" id="amount_partial_input">
                            <label>Partial Amount Paid (₹):</label>
                            <input type="number" 
                                   name="amount_partial" 
                                   id="amount_partial" 
                                   step="0.01" 
                                   min="0" 
                                   max="<?php echo $item->total_price; ?>"
                                   placeholder="Enter partial amount paid">
                        </div>
                    </div>
                    
                    <!-- Option 3: Pending Payment -->
                    <div class="payment-option" onclick="selectPayment('unpaid')">
                        <label>
                            <input type="radio" name="payment_status" value="unpaid" id="payment_unpaid">
                            <div>
                                <strong>Payment Pending</strong>
                                <div style="font-size: 14px; color: #666; margin-top: 5px;">
                                    I will complete payment later (Portal access will be granted after payment)
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn-submit">
                <i class="fa fa-check"></i> Complete Registration
            </button>
            
            <a href="<?php echo site_url('safelegalsolutions/safelegalsolutions_client/register/' . $token); ?>" 
               class="btn btn-back">
                <i class="fa fa-arrow-left"></i> Back to Registration Form
            </a>
            
        <?php echo form_close(); ?>
    </div>
</div>

<script src="<?php echo base_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>
<script>
function selectPayment(type) {
    // Remove all selected classes
    $('.payment-option').removeClass('selected');
    
    // Hide all amount inputs
    $('.payment-amount-input').removeClass('active');
    
    // Get the clicked option
    const option = event.currentTarget;
    $(option).addClass('selected');
    
    // Handle amount input visibility
    if (type === 'paid') {
        $('#payment_paid').prop('checked', true);
        $('#amount_paid_input').addClass('active');
        $('#amount_paid').prop('required', true);
        $('#amount_partial').prop('required', false);
    } else if (type === 'partial') {
        $('#payment_partial').prop('checked', true);
        $('#amount_partial_input').addClass('active');
        $('#amount_partial').prop('required', true);
        $('#amount_paid').prop('required', false);
    } else {
        $('#payment_unpaid').prop('checked', true);
        $('#amount_paid').prop('required', false);
        $('#amount_partial').prop('required', false);
    }
}

// Form submission validation
$('#payment-form').on('submit', function(e) {
    const paymentStatus = $('input[name="payment_status"]:checked').val();
    
    if (!paymentStatus) {
        e.preventDefault();
        alert('Please select a payment option');
        return false;
    }
    
    if (paymentStatus === 'paid') {
        const amountPaid = parseFloat($('#amount_paid').val());
        const totalPrice = <?php echo $item->total_price; ?>;
        
        if (!amountPaid || amountPaid < totalPrice) {
            e.preventDefault();
            alert('For full payment, amount must equal ₹' + totalPrice.toFixed(2));
            return false;
        }
    }
    
    if (paymentStatus === 'partial') {
        const amountPartial = parseFloat($('#amount_partial').val());
        
        if (!amountPartial || amountPartial <= 0) {
            e.preventDefault();
            alert('Please enter a valid partial payment amount');
            return false;
        }
    }
    
    return true;
});
</script>

</body>
</html>