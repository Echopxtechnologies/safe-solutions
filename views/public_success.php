<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Success'; ?> - Safe Legal Solutions</title>
    
    <link href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/plugins/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Arial', sans-serif;
            padding: 20px 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .success-container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .success-header {
            background: linear-gradient(135deg, #5cb85c 0%, #449d44 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .success-icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: successPulse 1.5s ease-in-out;
        }
        @keyframes successPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        .success-header h1 {
            margin: 0 0 10px 0;
            font-size: 32px;
        }
        .success-body {
            padding: 40px;
        }
        .info-card {
            background: #f8f9fa;
            border-left: 4px solid #5cb85c;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .referral-code {
            background: #fff3cd;
            border: 2px dashed #ffc107;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            margin: 20px 0;
        }
        .referral-code-text {
            font-size: 24px;
            font-weight: bold;
            color: #856404;
            letter-spacing: 2px;
        }
        .next-steps {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 20px;
            margin-top: 20px;
            border-radius: 5px;
        }
        .next-steps h4 {
            margin-top: 0;
            color: #1976d2;
        }
        .next-steps ul {
            margin-bottom: 0;
        }
        .next-steps li {
            margin-bottom: 8px;
        }
    </style>
</head>
<body>

<div class="success-container">
    <div class="success-header">
        <div class="success-icon">
            <i class="fa fa-check-circle"></i>
        </div>
        <h1>Registration Successful!</h1>
        <p>Welcome to Safe Legal Solutions</p>
    </div>
    
    <div class="success-body">
        <div class="info-card">
            <h4 style="margin-top: 0; color: #333;">
                <i class="fa fa-user"></i> Thank You for Registering
            </h4>
            
            <?php if (isset($student_name) && !empty($student_name)): ?>
            <p style="font-size: 18px; color: #333; margin-bottom: 0;">
                <strong><?php echo htmlspecialchars($student_name); ?></strong>, your registration has been submitted successfully.
            </p>
            <?php else: ?>
            <p style="font-size: 18px; color: #333; margin-bottom: 0;">
                Your registration has been submitted successfully.
            </p>
            <?php endif; ?>
        </div>
        
        <!-- Referral Code -->
        <?php if (isset($referral_code) && !empty($referral_code)): ?>
        <div class="referral-code">
            <p style="margin: 0 0 10px 0; color: #856404;">
                <i class="fa fa-star"></i> <strong>Your Unique Referral Code</strong>
            </p>
            <div class="referral-code-text">
                <?php echo htmlspecialchars($referral_code); ?>
            </div>
            <small style="color: #856404;">Save this code for future reference</small>
        </div>
        <?php endif; ?>
        
        <!-- Next Steps -->
        <div class="next-steps">
            <h4><i class="fa fa-list-ol"></i> What Happens Next?</h4>
            <ul>
                <li><i class="fa fa-check"></i> Our team will review your registration within 24-48 hours</li>
                <li><i class="fa fa-envelope"></i> You will receive a confirmation email shortly</li>
                <li><i class="fa fa-phone"></i> A branch representative may contact you for verification</li>
    
            </ul>
        </div>
        
        <!-- Branch Info (if available) -->
        <?php if (isset($branch_name) && !empty($branch_name)): ?>
        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
            <p style="margin: 0; color: #777;">
                <i class="fa fa-map-marker"></i> Registered at: <strong><?php echo htmlspecialchars($branch_name); ?></strong>
            </p>
        </div>
        <?php endif; ?>
        

    </div>
</div>

<script src="<?php echo base_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>"></script>

</body>
</html>