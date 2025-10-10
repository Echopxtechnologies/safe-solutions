<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="panel_s">
    <div class="panel-body">
        
        <!-- Header -->
        <div class="row">
            <div class="col-md-10">
                <h3 class="no-margin">
                    <i class="fa fa-id-card"></i> My Referral Card
                </h3>
            </div>
            <div class="col-md-2 text-right">
                <a href="<?php echo site_url('clients/safelegalsolutions_client/my_dashboard'); ?>" 
                   class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <hr>

        <!-- Referral Card Design -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                
                <!-- Main Card -->
                <div id="referral-card" style="
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    border-radius: 20px;
                    padding: 40px;
                    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
                    color: white;
                    margin: 40px 0;
                    position: relative;
                    overflow: hidden;
                ">
                    <!-- Background Pattern -->
                    <div style="
                        position: absolute;
                        top: -50px;
                        right: -50px;
                        width: 200px;
                        height: 200px;
                        background: rgba(255,255,255,0.1);
                        border-radius: 50%;
                    "></div>
                    <div style="
                        position: absolute;
                        bottom: -30px;
                        left: -30px;
                        width: 150px;
                        height: 150px;
                        background: rgba(255,255,255,0.1);
                        border-radius: 50%;
                    "></div>
                    
                    <!-- Card Content -->
                    <div style="position: relative; z-index: 10;">
                        
                        <!-- Logo/Header -->
                        <div style="text-align: center; margin-bottom: 30px;">
                            <h2 style="margin: 0 0 10px 0; font-size: 32px; font-weight: bold; color: white;">
                                <?php echo get_option('companyname'); ?>
                            </h2>
                            <p style="margin: 0; opacity: 0.9; font-size: 16px;">
                                Student Referral Program
                            </p>
                        </div>

                        <!-- Student Info -->
                        <div style="
                            background: rgba(255,255,255,0.15);
                            backdrop-filter: blur(10px);
                            border-radius: 15px;
                            padding: 25px;
                            margin: 30px 0;
                        ">
                            <div style="margin-bottom: 15px;">
                                <small style="opacity: 0.8; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">
                                    Student Name
                                </small>
                                <h3 style="margin: 5px 0 0 0; font-size: 24px; font-weight: bold;">
                                    <?php echo htmlspecialchars($student->student_name); ?>
                                </h3>
                            </div>
                            
                            
                        </div>

                        <!-- Referral Code - Main Focus -->
                        <div style="text-align: center; margin: 40px 0;">
                            <p style="margin: 0 0 15px 0; font-size: 14px; text-transform: uppercase; letter-spacing: 2px; opacity: 0.9;">
                                Your Unique Referral Code
                            </p>
                            <div style="
                                background: white;
                                color: #667eea;
                                padding: 30px;
                                border-radius: 15px;
                                box-shadow: 0 5px 20px rgba(0,0,0,0.2);
                            ">
                                <h1 style="
                                    margin: 0;
                                    font-size: 56px;
                                    font-weight: bold;
                                    font-family: 'Courier New', monospace;
                                    letter-spacing: 8px;
                                ">
                                    <?php echo htmlspecialchars($referral_code); ?>
                                </h1>
                            </div>
                        </div>

                        <!-- Earnings Display -->
                        <?php if (isset($student->earnings) && $student->earnings > 0): ?>
                        <div style="
                            text-align: center;
                            background: rgba(255,215,0,0.2);
                            border: 2px solid rgba(255,215,0,0.5);
                            border-radius: 10px;
                            padding: 20px;
                            margin: 30px 0;
                        ">
                            <p style="margin: 0 0 10px 0; font-size: 14px; opacity: 0.9;">
                                Your Total Referral Earnings
                            </p>
                            <h2 style="margin: 0; font-size: 36px; font-weight: bold; color: #FFD700;">
                                ₹<?php echo number_format($student->earnings, 2); ?>
                            </h2>
                        </div>
                        <?php endif; ?>

                        <!-- How to Use -->
                        <div style="
                            background: rgba(255,255,255,0.1);
                            border-radius: 10px;
                            padding: 20px;
                            margin-top: 30px;
                        ">
                            <h4 style="margin: 0 0 15px 0; font-size: 18px;">
                                <i class="fa fa-lightbulb-o"></i> How to Use Your Referral Code:
                            </h4>
                            <ol style="margin: 0; padding-left: 20px; line-height: 1.8;">
                                <li>Share your code with friends and family</li>
                                <li>They use it during registration</li>
                                <li>Both of you earn rewards!</li>
                                <li>Track earnings in your dashboard</li>
                            </ol>
                        </div>

                        <!-- Contact Info -->
                        <?php if ($branch): ?>
                        <div style="
                            text-align: center;
                            margin-top: 30px;
                            padding-top: 20px;
                            border-top: 1px solid rgba(255,255,255,0.2);
                        ">
                            <p style="margin: 5px 0; font-size: 12px; opacity: 0.8;">
                                <?php echo htmlspecialchars($branch->branch_name); ?> | 
                                <?php echo htmlspecialchars($branch->contact_phone); ?>
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div style="text-align: center; margin: 30px 0;">
                    <button onclick="copyReferralCode()" class="btn btn-primary btn-lg" style="margin: 5px;">
                        <i class="fa fa-copy"></i> Copy Code
                    </button>
                    
                    <button onclick="printCard()" class="btn btn-success btn-lg" style="margin: 5px;">
                        <i class="fa fa-print"></i> Print Card
                    </button>
                    
                    <button onclick="shareReferral()" class="btn btn-info btn-lg" style="margin: 5px;">
                        <i class="fa fa-share-alt"></i> Share via WhatsApp
                    </button>
                </div>

            </div>
        </div>

    </div>
</div>

<script>
// Copy referral code to clipboard
function copyReferralCode() {
    var code = '<?php echo $referral_code; ?>';
    
    var tempInput = document.createElement('input');
    tempInput.value = code;
    document.body.appendChild(tempInput);
    tempInput.select();
    
    try {
        document.execCommand('copy');
        alert('✓ Referral code copied to clipboard!\n\nCode: ' + code);
    } catch (err) {
        prompt('Copy your referral code:', code);
    }
    
    document.body.removeChild(tempInput);
}

// Print referral card
function printCard() {
    window.print();
}

// Share via WhatsApp
function shareReferral() {
    var code = '<?php echo $referral_code; ?>';
    var name = '<?php echo addslashes($student->student_name); ?>';
    var company = '<?php echo addslashes(get_option('companyname')); ?>';
    
    var message = 'Hi! 👋\n\n' +
                  'I\'m enrolled in ' + company + ' and wanted to share my referral code with you!\n\n' +
                  '🎓 My Referral Code: *' + code + '*\n\n' +
                  'Use this code during registration and we both get rewards! 🎁\n\n' +
                  'Register here: <?php echo site_url('safelegalsolutions/safelegalsolutions_client/register'); ?>';
    
    var whatsappUrl = 'https://wa.me/?text=' + encodeURIComponent(message);
    window.open(whatsappUrl, '_blank');
}
</script>

<style>
/* Print styles */
@media print {
    .panel_s > .panel-body > .row:first-child,
    .panel_s > .panel-body > hr,
    .panel_s > .panel-body > .row:last-child {
        display: none !important;
    }
    
    #referral-card {
        page-break-inside: avoid;
    }
}
</style>