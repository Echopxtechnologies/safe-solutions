<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Student Registration'; ?> - Safe Legal Solutions</title>
    
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            padding: 30px 15px;
            min-height: 100vh;
        }
        
        .registration-wrapper {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .registration-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        /* Header */
        .registration-header {
            background: #1a1a1a;
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .registration-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, rgba(26, 26, 26, 0) 100%);
        }
        
        .registration-header .logo-icon {
            font-size: 50px;
            margin-bottom: 15px;
            color: #d4af37;
            position: relative;
            z-index: 1;
        }
        
        .registration-header h1 {
            margin: 0 0 10px 0;
            font-size: 32px;
            font-weight: 600;
            letter-spacing: 0.5px;
            position: relative;
            z-index: 1;
        }
        
        .registration-header p {
            margin: 0;
            font-size: 16px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        /* Body */
        .registration-body {
            padding: 40px;
        }
        
        /* Branch Info Banner */
        .branch-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-left: 4px solid #d4af37;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .branch-info strong {
            color: #1a1a1a;
            font-size: 15px;
        }
        
        .branch-info .branch-name {
            font-size: 20px;
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
            display: inline-block;
        }
        
        .branch-info small {
            color: #666;
            display: block;
            margin-top: 8px;
        }
        
        /* Error Messages */
        .error-message {
            background: #fff3cd;
            color: #856404;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #ffc107;
            box-shadow: 0 2px 8px rgba(255, 193, 7, 0.2);
        }
        
        .error-message i {
            margin-right: 8px;
        }
        
        .error-message ul {
            margin: 10px 0 0 20px;
            padding: 0;
        }
        
        /* Form Sections */
        .form-section {
            margin-bottom: 30px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .form-section-header {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: white;
            padding: 15px 20px;
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
            font-size: 16px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-control::placeholder {
            color: #999;
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }
        
        .text-muted {
            color: #666;
            font-size: 13px;
            margin-top: 5px;
            display: block;
        }
        
        /* Package List */
        .package-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .package-item {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .package-item:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
            transform: translateY(-2px);
        }
        
        .package-item:has(input:checked) {
            border-color: #667eea;
            background: linear-gradient(135deg, #f8f9ff 0%, #fff 100%);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
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
            accent-color: #667eea;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .price-tag {
            margin-left: auto;
            font-size: 24px;
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
            border-radius: 4px;
            font-size: 12px;
            color: #555;
        }
        
        .feature-tag i {
            color: #28a745;
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
        
        /* Consent Checkbox */
        .consent-section {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .consent-section .checkbox {
            margin: 0;
        }
        
        .consent-section label {
            font-size: 14px;
            font-weight: normal;
            cursor: pointer;
            display: flex;
            align-items: start;
            gap: 10px;
        }
        
        .consent-section input[type="checkbox"] {
            margin-top: 3px;
            width: 20px;
            height: 20px;
            cursor: pointer;
            flex-shrink: 0;
            accent-color: #667eea;
        }
        
        /* Submit Button */
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 40px;
            font-size: 18px;
            font-weight: 500;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
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
            border-radius: 8px;
        }
        
        .security-note i {
            color: #28a745;
            margin-right: 8px;
        }
        
        .security-note small {
            color: #666;
            font-size: 14px;
        }
        
        /* Required Field Indicator */
        .required-indicator {
            color: #dc3545;
            font-size: 12px;
            margin-top: 10px;
            display: block;
            text-align: center;
        }
        
        .required-indicator i {
            margin-right: 5px;
        }
        
        /* Alert */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }
        
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border-color: #ffc107;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 15px 10px;
            }
            
            .registration-body {
                padding: 25px 20px;
            }
            
            .registration-header {
                padding: 30px 20px;
            }
            
            .registration-header h1 {
                font-size: 24px;
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
            <h1>Student Registration</h1>
            <p>Safe Legal Solutions - Professional Legal Education</p>
        </div>
        
        <!-- Body -->
        <div class="registration-body">
            <!-- Error Messages -->
            <?php if (isset($error) && !empty($error)): ?>
                <div class="error-message">
                    <i class="fa fa-exclamation-triangle"></i> 
                    <strong>Error:</strong> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (validation_errors()): ?>
                <div class="error-message">
                    <i class="fa fa-exclamation-triangle"></i> 
                    <strong>Please correct the following errors:</strong>
                    <?php echo validation_errors(); ?>
                </div>
            <?php endif; ?>
            
            <!-- Branch Information -->
            <div class="branch-info">
                <strong><i class="fa fa-building"></i> Registering Under:</strong>
                <div class="branch-name">
                    <?php echo htmlspecialchars($branch->branch_name); ?>
                    <?php if (isset($branch->category_name) && !empty($branch->category_name)): ?>
                        <span class="label"><?php echo htmlspecialchars($branch->category_name); ?></span>
                    <?php endif; ?>
                </div>
                <?php if (isset($branch->manager_firstname) && !empty($branch->manager_firstname)): ?>
                    <small>
                        <i class="fa fa-user"></i> Partner Manager: 
                        <?php echo htmlspecialchars($branch->manager_firstname . ' ' . $branch->manager_lastname); ?>
                    </small>
                <?php endif; ?>
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
                                           value="<?php echo set_value('student_name'); ?>">
                                    <small class="text-muted">As per official documents</small>
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
                                           max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>"
                                           value="<?php echo set_value('date_of_birth'); ?>">
                                    <small class="text-muted">You must be at least 18 years old</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
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
                                           minlength="6"
                                           maxlength="50"
                                           style="text-transform: uppercase;"
                                           value="<?php echo set_value('passport_number'); ?>">
                                    <small class="text-muted">Valid passport number (letters and numbers only, minimum 6 characters)</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="passport_expiry_date">
                                        <span class="text-danger">*</span> Passport Expiry Date
                                    </label>
                                    <input type="date" 
                                           name="passport_expiry_date" 
                                           id="passport_expiry_date" 
                                           class="form-control" 
                                           required
                                           min="<?php echo date('Y-m-d'); ?>"
                                           value="<?php echo set_value('passport_expiry_date'); ?>">
                                    <small class="text-muted">Passport must be valid</small>
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
                                           placeholder="your.email@example.com"
                                           value="<?php echo set_value('email'); ?>">
                                    <small class="text-muted">Your login credentials will be sent here</small>
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
                                           value="<?php echo set_value('phone'); ?>">
                                    <small class="text-muted">10 digit mobile number</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address">
                                        <span class="text-danger">*</span> Street Address
                                    </label>
                                    <textarea name="address" 
                                              id="address" 
                                              class="form-control" 
                                              rows="2" 
                                              required
                                              placeholder="House/Flat No., Street, Area"><?php echo set_value('address'); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city">
                                        <span class="text-danger">*</span> City
                                    </label>
                                    <input type="text" 
                                           name="city" 
                                           id="city" 
                                           class="form-control" 
                                           required
                                           placeholder="Enter city"
                                           value="<?php echo set_value('city'); ?>">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="state">
                                        <span class="text-danger">*</span> State/Province
                                    </label>
                                    <input type="text" 
                                           name="state" 
                                           id="state" 
                                           class="form-control" 
                                           required
                                           placeholder="Enter state"
                                           value="<?php echo set_value('state'); ?>">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pin_code">
                                        <span class="text-danger">*</span> PIN Code
                                    </label>
                                    <input type="text" 
                                           name="pin_code" 
                                           id="pin_code" 
                                           class="form-control" 
                                           required
                                           placeholder="000000"
                                           pattern="[0-9]{5,10}"
                                           value="<?php echo set_value('pin_code'); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="emergency_contact_mobile">
                                        <span class="text-danger">*</span> Emergency Contact Mobile
                                    </label>
                                    <input type="text" 
                                           name="emergency_contact_mobile" 
                                           id="emergency_contact_mobile" 
                                           class="form-control" 
                                           required
                                           placeholder="+91 9876543210"
                                           pattern="[0-9+\-\s()]+"
                                           value="<?php echo set_value('emergency_contact_mobile'); ?>">
                                    <small class="text-muted">Emergency contact number (can be different from your phone)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Destination & Program Details -->
                <div class="form-section">
                    <div class="form-section-header">
                        <h4>
                            <i class="fa fa-globe"></i> Destination & Program Details
                        </h4>
                    </div>
                    <div class="form-section-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="destination_country_id">
                                        <span class="text-danger">*</span> Destination Country
                                    </label>
                                    <select name="destination_country_id" 
                                            id="destination_country_id" 
                                            class="form-control" 
                                            required>
                                        <option value="">-- Select Country --</option>
                                        <?php 
                                        if (!empty($countries)): 
                                            foreach ($countries as $country): 
                                        ?>
                                            <option value="<?php echo $country->id; ?>"
                                                    <?php echo (set_value('destination_country_id') == $country->id) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($country->country_name); ?>
                                                <?php if ($country->is_popular): ?>
                                                    ✨
                                                <?php endif; ?>
                                            </option>
                                        <?php 
                                            endforeach;
                                        endif; 
                                        ?>
                                    </select>
                                    <small class="text-muted">Select country where you plan to study</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="university_name">
                                        <span class="text-danger">*</span> University/Institution Name
                                    </label>
                                    <input type="text" 
                                           name="university_name" 
                                           id="university_name" 
                                           class="form-control" 
                                           required
                                           placeholder="Enter university/institution name"
                                           value="<?php echo set_value('university_name'); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="course_program">
                                        <span class="text-danger">*</span> Course/Program
                                    </label>
                                    <input type="text" 
                                           name="course_program" 
                                           id="course_program" 
                                           class="form-control" 
                                           required
                                           placeholder="E.g., MBA, Bachelor of Computer Science, etc."
                                           value="<?php echo set_value('course_program'); ?>">
                                    <small class="text-muted">Enter the course/program you're enrolling in</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
                <!-- Package Selection -->
                <div class="form-section">
                    <div class="form-section-header">
                        <h4>
                            <i class="fa fa-graduation-cap"></i> Select Your Package
                        </h4>
                    </div>
                    <div class="form-section-body">
                        <?php if (!empty($items) && is_array($items)): ?>
                            <div class="package-list">
                                <?php foreach ($items as $item): ?>
                                    <div class="package-item">
                                        <label class="package-option">
                                            <input type="radio" 
                                                   name="item_id" 
                                                   value="<?php echo $item->id; ?>" 
                                                   <?php echo (set_value('item_id') == $item->id) ? 'checked' : ''; ?>
                                                   required>
                                            
                                            <div class="package-details">
                                                <div class="package-name-price">
                                                    <strong><?php echo htmlspecialchars($item->item_name); ?></strong>
                                                    <?php if (!empty($item->duration_months)): ?>
                                                        <span class="duration-badge">
                                                            <i class="fa fa-clock-o"></i> <?php echo $item->duration_months; ?> months
                                                        </span>
                                                    <?php endif; ?>
                                                    <span class="price-tag">₹<?php echo number_format($item->total_price, 2); ?></span>
                                                </div>
                                                
                                                <?php if (!empty($item->description)): ?>
                                                    <div class="package-desc">
                                                        <?php echo htmlspecialchars($item->description); ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($item->features)): ?>
                                                    <?php 
                                                    $features = json_decode($item->features, true); 
                                                    if (!empty($features) && is_array($features)):
                                                    ?>
                                                        <div class="package-mini-features">
                                                            <?php foreach (array_slice($features, 0, 3) as $feature): ?>
                                                                <span class="feature-tag">
                                                                    <i class="fa fa-check"></i> <?php echo htmlspecialchars($feature); ?>
                                                                </span>
                                                            <?php endforeach; ?>
                                                            <?php if (count($features) > 3): ?>
                                                                <span class="feature-tag more">
                                                                    +<?php echo count($features) - 3; ?> more features
                                                                </span>
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

                <!-- Data Processing Consent -->
                <div class="consent-section">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" 
                                   name="consent_given" 
                                   id="consent_given" 
                                   value="1" 
                                   required
                                   <?php echo (set_value('consent_given') == '1') ? 'checked' : ''; ?>>
                            <span>
                                <strong>I consent</strong> to SAFE Legal Solutions collecting and processing my personal data for providing legal protection services. I understand that I will need to complete a detailed registration form for full service activation.
                            </span>
                        </label>
                    </div>
                    <small style="display: block; margin-top: 10px; color: #856404;">
                        <i class="fa fa-info-circle"></i> Your data will be processed in accordance with applicable data protection laws. You can withdraw consent at any time.
                    </small>
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
                        <i class="fa fa-arrow-right"></i> Proceed to Review
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
    // Auto-uppercase passport number
    $('#passport_number').on('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    });
    
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
        var phoneDigits = phone.replace(/[^0-9]/g, '');
        if (phoneDigits.length < 10) {
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
        
        // Passport expiry validation
        var expiryDate = new Date($('#passport_expiry_date').val());
        var today = new Date();
        if (expiryDate <= today) {
            e.preventDefault();
            alert('Passport expiry date must be in the future');
            $('#passport_expiry_date').focus();
            return false;
        }
        
        // Age validation
        var dob = new Date($('#date_of_birth').val());
        var age = today.getFullYear() - dob.getFullYear();
        var monthDiff = today.getMonth() - dob.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        
        if (age < 18) {
            e.preventDefault();
            alert('You must be at least 18 years old to register. Current age: ' + age + ' years.');
            $('#date_of_birth').focus();
            return false;
        }
        
        // Destination country validation
        if (!$('#destination_country_id').val()) {
            e.preventDefault();
            alert('Please select your destination country');
            $('#destination_country_id').focus();
            return false;
        }
        
        // University name validation
        if (!$('#university_name').val().trim()) {
            e.preventDefault();
            alert('Please enter university/institution name');
            $('#university_name').focus();
            return false;
        }
        
        // Course/program validation
        if (!$('#course_program').val().trim()) {
            e.preventDefault();
            alert('Please enter course/program');
            $('#course_program').focus();
            return false;
        }
        
        // City validation
        if (!$('#city').val().trim()) {
            e.preventDefault();
            alert('Please enter city');
            $('#city').focus();
            return false;
        }
        
        // State validation
        if (!$('#state').val().trim()) {
            e.preventDefault();
            alert('Please enter state');
            $('#state').focus();
            return false;
        }
        
        // PIN code validation
        var pin = $('#pin_code').val();
        if (!pin || pin.trim() === '' || pin.length < 5) {
            e.preventDefault();
            alert('Please enter a valid PIN code (minimum 5 digits)');
            $('#pin_code').focus();
            return false;
        }
        
        // Emergency contact validation
        var emergency = $('#emergency_contact_mobile').val();
        var emergencyDigits = emergency.replace(/[^0-9]/g, '');
        if (emergencyDigits.length < 10) {
            e.preventDefault();
            alert('Please enter a valid emergency contact mobile number');
            $('#emergency_contact_mobile').focus();
            return false;
        }
        
        // Consent checkbox validation
        if (!$('#consent_given').is(':checked')) {
            e.preventDefault();
            alert('You must consent to data processing to proceed. Please check the consent checkbox.');
            $('#consent_given').focus();
            $('html, body').animate({
                scrollTop: $('.consent-section').offset().top - 100
            }, 500);
            return false;
        }
        
        return true;
    });
    
    // Package selection highlight
    $('input[name="item_id"]').on('change', function() {
        $('.package-item').removeClass('selected');
        $(this).closest('.package-item').addClass('selected');
    });
    
    // Add initial selected class
    $('input[name="item_id"]:checked').closest('.package-item').addClass('selected');
});
</script>

</body>
</html>