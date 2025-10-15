<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Candidate Registration'; ?> - Safe Legal Solutions</title>
    
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
        
        .registration-wrapper {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .registration-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        
        /* Header - Professional Black */
        .registration-header {
            background: #1a1a1a;
            color: white;
            padding: 40px 30px;
            text-align: center;
            border-bottom: 4px solid #d4af37; /* Gold accent */
        }
        
        .registration-header .logo-icon {
            font-size: 50px;
            margin-bottom: 15px;
            color: #d4af37;
        }
        
        .registration-header h1 {
            margin: 0 0 10px 0;
            font-size: 32px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .registration-header p {
            margin: 0;
            font-size: 16px;
            opacity: 0.9;
        }
        
        /* Body */
        .registration-body {
            padding: 40px;
        }
        
        /* Branch Info Banner */
        .branch-info {
            background: #f8f9fa;
            border-left: 4px solid #1a1a1a;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 4px;
        }
        
        .branch-info strong {
            color: #1a1a1a;
            font-size: 15px;
        }
        
        .branch-info .branch-name {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 8px 0;
        }
        
        .branch-info .label {
            background: #1a1a1a;
            color: white;
            padding: 4px 12px;
            border-radius: 3px;
            font-size: 12px;
            margin-left: 10px;
        }
        
        .branch-info small {
            color: #666;
        }
        
        /* Error Messages */
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
        
        /* Form Panels */
        .form-section {
            margin-bottom: 25px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            overflow: hidden;
        }
        
        .form-section-header {
            background: #1a1a1a;
            color: white;
            padding: 15px 20px;
            border-bottom: 3px solid #d4af37;
        }
        
        .form-section-header h4 {
            margin: 0;
            font-size: 18px;
            font-weight: 500;
        }
        
        .form-section-header i {
            margin-right: 10px;
            color: #d4af37;
        }
        
        .form-section-body {
            padding: 25px;
            background: white;
        }
        
        /* Form Elements */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #1a1a1a;
            font-size: 14px;
        }
        
        .form-group label .text-danger {
            color: #dc3545;
            margin-right: 3px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #1a1a1a;
            box-shadow: 0 0 0 3px rgba(26, 26, 26, 0.1);
        }
        
        .form-control::placeholder {
            color: #999;
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        
        .text-muted {
            color: #666;
            font-size: 13px;
            margin-top: 5px;
            display: block;
        }
        
        /* Package List - Simple Compact Version */
        .package-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .package-item {
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .package-item:hover {
            border-color: #d4af37;
            box-shadow: 0 2px 10px rgba(212, 175, 55, 0.1);
        }
        
        .package-item:has(input:checked) {
            border-color: #1a1a1a;
            background: #f8f9fa;
            box-shadow: 0 2px 10px rgba(26, 26, 26, 0.1);
        }
        
        .package-option {
            display: flex;
            align-items: flex-start;
            padding: 20px;
            cursor: pointer;
            margin: 0;
            gap: 15px;
        }
        
        .package-option input[type="radio"] {
            margin-top: 3px;
            width: 20px;
            height: 20px;
            cursor: pointer;
            flex-shrink: 0;
        }
        
        .package-details {
            flex: 1;
        }
        
        .package-name-price {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
            flex-wrap: wrap;
        }
        
        .package-name-price strong {
            font-size: 18px;
            color: #1a1a1a;
        }
        
        .duration-badge {
            background: #e0e0e0;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 12px;
            color: #666;
        }
        
        .price-tag {
            margin-left: auto;
            font-size: 22px;
            font-weight: bold;
            color: #28a745;
        }
        
        .package-desc {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
            line-height: 1.5;
        }
        
        .package-mini-features {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 10px;
        }
        
        .feature-tag {
            background: #f0f0f0;
            padding: 5px 12px;
            border-radius: 3px;
            font-size: 12px;
            color: #555;
        }
        
        .feature-tag i {
            color: #d4af37;
            margin-right: 5px;
        }
        
        .feature-tag.more {
            background: #d4af37;
            color: white;
            font-weight: 500;
        }
        
        .package-price-breakdown {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #e0e0e0;
        }
        
        .package-price-breakdown small {
            color: #888;
            font-size: 12px;
        }
        
        /* Submit Button */
        .btn-register {
            background: #1a1a1a;
            color: white;
            padding: 16px 40px;
            font-size: 18px;
            font-weight: 500;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(26, 26, 26, 0.2);
        }
        
        .btn-register:hover {
            background: #000;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(26, 26, 26, 0.3);
            color: white;
        }
        
        .btn-register:active {
            transform: translateY(0);
        }
        
        .btn-register i {
            margin-right: 10px;
        }
        
        /* Footer Note */
        .security-note {
            text-align: center;
            margin-top: 25px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .security-note i {
            color: #d4af37;
            margin-right: 8px;
        }
        
        .security-note small {
            color: #666;
            font-size: 14px;
        }
        
        /* Required Field Indicator */
        .required-indicator {
            color: #dc3545;
            font-size: 11px;
            margin-top: 10px;
            display: block;
        }
        
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .registration-body {
                padding: 25px 20px;
            }
            
            .registration-header {
                padding: 30px 20px;
            }
            
            .registration-header h1 {
                font-size: 26px;
            }
            
            .form-section-body {
                padding: 20px 15px;
            }
            
            .package-option {
                padding: 15px;
            }
            
            .package-name-price {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .price-tag {
                margin-left: 0;
                font-size: 20px;
            }
        }
    </style>
</head>
<body>

<div class="registration-wrapper">
    <div class="registration-container">
        <!-- Header -->
        <div class="registration-header">
            <div class="logo-icon">
                <i class="fa fa-balance-scale"></i>
            </div>
            <h1>Candidate Registration</h1>
            <p>Safe Legal Solutions - Professional Legal Education</p>
        </div>
        
        <!-- Body -->
        <div class="registration-body">
            <!-- Error Messages -->
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <i class="fa fa-exclamation-triangle"></i> 
                    <strong>Error:</strong> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (validation_errors()): ?>
                <div class="error-message">
                    <i class="fa fa-exclamation-triangle"></i> 
                    <?php echo validation_errors(); ?>
                </div>
            <?php endif; ?>
            
            <!-- Branch Information -->
            <div class="branch-info">
                <strong><i class="fa fa-building"></i> Registering Under:</strong>
                <div class="branch-name">
                    <?php echo htmlspecialchars($branch->branch_name); ?>
                    <?php if (isset($branch->category_name)): ?>
                        <span class="label"><?php echo htmlspecialchars($branch->category_name); ?></span>
                    <?php endif; ?>
                </div>
                <small>
                    <i class="fa fa-user"></i> Branch Manager: 
                    <?php echo htmlspecialchars($branch->manager_firstname . ' ' . $branch->manager_lastname); ?>
                </small>
            </div>
            
            <!-- Registration Form -->
            <?php echo form_open(current_url(), ['id' => 'registration-form', 'autocomplete' => 'on']); ?>
                
                <!-- Personal Information -->
                <div class="form-section">
                    <div class="form-section-header">
                        <h4>
                            <i class="fa fa-user"></i> Personal Information
                        </h4>
                    </div>
                    <div class="form-section-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="student_name">
                                        <span class="text-danger">*</span> Full Name
                                    </label>
                                    <input type="text" 
                                           name="student_name" 
                                           id="student_name" 
                                           class="form-control" 
                                           required
                                           placeholder="Enter your full name"
                                           value="<?php echo isset($form_data['student_name']) ? htmlspecialchars($form_data['student_name']) : ''; ?>">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_of_birth">
                                        <span class="text-danger">*</span> Date of Birth
                                    </label>
                                    <input type="date" 
                                           name="date_of_birth" 
                                           id="date_of_birth" 
                                           class="form-control" 
                                           required
                                           max="<?php echo date('Y-m-d'); ?>"
                                           value="<?php echo isset($form_data['date_of_birth']) ? $form_data['date_of_birth'] : ''; ?>">
                                    <small class="text-muted">You must be at least 18 years old</small>
                                </div>
                                  <!-- Passport Number Row -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="passport_number">
                                        <span class="text-danger">*</span> Passport Number
                                    </label>
                                    <input type="text" 
                                           name="passport_number" 
                                           id="passport_number" 
                                           class="form-control" 
                                           required
                                           placeholder="Enter your passport number"
                                           pattern="[A-Z0-9]+"
                                           style="text-transform: uppercase;"
                                           value="<?php echo isset($form_data['passport_number']) ? htmlspecialchars($form_data['passport_number']) : ''; ?>">
                                    <small class="text-muted">Valid passport number (letters and numbers only, minimum 6 characters)</small>
                                </div>
                            </div>
                        </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="form-section">
                    <div class="form-section-header">
                        <h4>
                            <i class="fa fa-phone"></i> Contact Information
                        </h4>
                    </div>
                    <div class="form-section-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">
                                        <span class="text-danger">*</span> Email Address
                                    </label>
                                    <input type="email" 
                                           name="email" 
                                           id="email" 
                                           class="form-control" 
                                           required
                                           placeholder="candidate@example.com"
                                           value="<?php echo isset($form_data['email']) ? htmlspecialchars($form_data['email']) : ''; ?>">
                                    <small class="text-muted">Must be a valid email address</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">
                                        <span class="text-danger">*</span> Phone Number
                                    </label>
                                    <input type="text" 
                                           name="phone" 
                                           id="phone" 
                                           class="form-control" 
                                           required
                                           placeholder="+91 9876543210"
                                           pattern="[0-9+\-\s()]+"
                                           value="<?php echo isset($form_data['phone']) ? htmlspecialchars($form_data['phone']) : ''; ?>">
                                    <small class="text-muted">10 digit mobile number</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address">
                                        <span class="text-danger">*</span> Complete Address
                                    </label>
                                    <textarea name="address" 
                                              id="address" 
                                              class="form-control" 
                                              rows="3" 
                                              required
                                              placeholder="Enter your complete residential address"><?php echo isset($form_data['address']) ? htmlspecialchars($form_data['address']) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            
                <!-- Package Selection -->
                <div class="form-section">
                    <div class="form-section-header">
                        <h4>
                            <i class="fa fa-shopping-cart"></i> Select Your Package
                        </h4>
                    </div>
                    <div class="form-section-body">
                        <?php if (!empty($items)): ?>
                            <div class="package-list">
                                <?php foreach ($items as $item): ?>
                                    <div class="package-item">
                                        <label class="package-option">
                                            <input type="radio" 
                                                   name="item_id" 
                                                   value="<?php echo $item->id; ?>" 
                                                   <?php echo (isset($form_data['item_id']) && $form_data['item_id'] == $item->id) ? 'checked' : ''; ?>
                                                   required>
                                            
                                            <div class="package-details">
                                                <div class="package-name-price">
                                                    <strong><?php echo htmlspecialchars($item->item_name); ?></strong>
                                                    <?php if (!empty($item->duration_months)): ?>
                                                        <span class="duration-badge"><?php echo $item->duration_months; ?> months</span>
                                                    <?php endif; ?>
                                                    <span class="price-tag">₹<?php echo number_format($item->total_price, 2); ?></span>
                                                </div>
                                                
                                                <?php if (!empty($item->description)): ?>
                                                    <div class="package-desc">
                                                        <?php echo htmlspecialchars($item->description); ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($item->features)): ?>
                                                    <?php $features = json_decode($item->features, true); ?>
                                                    <?php if (!empty($features)): ?>
                                                        <div class="package-mini-features">
                                                            <?php foreach (array_slice($features, 0, 3) as $feature): ?>
                                                                <span class="feature-tag">
                                                                    <i class="fa fa-check"></i> <?php echo htmlspecialchars($feature); ?>
                                                                </span>
                                                            <?php endforeach; ?>
                                                            <?php if (count($features) > 3): ?>
                                                                <span class="feature-tag more">+<?php echo count($features) - 3; ?> more</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                
                                                <div class="package-price-breakdown">
                                                    <small>
                                                        Base: ₹<?php echo number_format($item->base_price, 2); ?> 
                                                        + GST (<?php echo number_format($item->gst_percentage, 2); ?>%): ₹<?php echo number_format($item->gst_amount, 2); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i> 
                                No packages available at the moment. Please contact support.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Required Fields Note -->
                <div class="row">
                    <div class="col-md-12">
                        <small class="required-indicator">
                            <i class="fa fa-info-circle"></i> Fields marked with <span class="text-danger">*</span> are mandatory
                        </small>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="form-group" style="margin-top: 30px;">
                    <button type="submit" class="btn btn-register">
                        <i class="fa fa-arrow-right"></i> Complete Registration
                    </button>
                </div>
            
            <?php echo form_close(); ?>
            
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
    // Form validation
    $('#registration-form').on('submit', function(e) {
        // Check if package is selected
        if (!$('input[name="item_id"]:checked').length) {
            e.preventDefault();
            alert('Please select a package before proceeding.');
            $('html, body').animate({
                scrollTop: $('.package-list').offset().top - 100
            }, 500);
            return false;
        }
        
        // Email validation
        var email = $('#email').val();
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Please enter a valid email address');
            $('#email').focus();
            return false;
        }
        
        // Phone validation
        var phone = $('#phone').val();
        if (phone.replace(/[^0-9]/g, '').length < 10) {
            e.preventDefault();
            alert('Please enter a valid 10-digit phone number');
            $('#phone').focus();
            return false;
        }
        // Passport validation
        var passport = $('#passport_number').val();
        if (!passport || passport.trim() === '') {
            e.preventDefault();
            alert('Please enter your passport number');
            $('#passport_number').focus();
            return false;
        }
        if (passport.length < 6) {
            e.preventDefault();
            alert('Please enter a valid passport number (minimum 6 characters)');
            $('#passport_number').focus();
            return false;
        }
        // Age validation
        var dob = new Date($('#date_of_birth').val());
        var today = new Date();
        var age = today.getFullYear() - dob.getFullYear();
        var monthDiff = today.getMonth() - dob.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        
        if (age < 18) {
            e.preventDefault();
            alert('Candidate must be at least 18 years old');
            $('#date_of_birth').focus();
            return false;
        }
        
        return true;
    });
});

// Auto-uppercase passport number
    $('#passport_number').on('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    });
</script>

</body>
</html>