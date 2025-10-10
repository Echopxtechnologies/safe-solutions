<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="panel_s">
    <div class="panel-body">
        
        <div class="row">
            <div class="col-md-12 text-center">
                <div style="padding: 60px 20px;">
                    <i class="fa fa-exclamation-triangle" style="font-size: 80px; color: #ffc107; margin-bottom: 30px;"></i>
                    
                    <h2>No Registration Record Found</h2>
                    
                    <p style="font-size: 16px; color: #666; margin: 20px 0;">
                        We couldn't find a student registration linked to your account.
                    </p>
                    
                    <p style="font-size: 14px; color: #999; margin-bottom: 40px;">
                        If you recently registered, your account may still be under review.<br>
                        Please contact support if you believe this is an error.
                    </p>
                    
                    <a href="<?php echo site_url('clients/tickets/new_ticket'); ?>" 
                       class="btn btn-primary btn-lg">
                        <i class="fa fa-ticket"></i> Contact Support
                    </a>
                    
                    <a href="<?php echo site_url('clients/dashboard'); ?>" 
                       class="btn btn-default btn-lg">
                        <i class="fa fa-home"></i> Go to Dashboard
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>