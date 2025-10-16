<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-dashboard"></i> Dashboard
                        </h4>
                        <hr class="hr-panel-heading" />

                        <?php if (is_sls_manager_or_admin()): ?>
                            <!-- ADMIN/MANAGER DASHBOARD -->
                            
                            <!-- Branch Info with Registration Link -->
                            <?php 
                            $default_branch = $this->safelegalsolutions_model->get_default_branch();
                            if ($default_branch): 
                            ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none; margin-bottom: 20px; border-radius: 8px; padding: 25px;">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <h3 style="margin-top: 0; color: white;">
                                                    <i class="fa fa-building"></i> 
                                                    <?php echo htmlspecialchars($default_branch->branch_name); ?>
                                                </h3>
                                                <div style="margin-top: 10px;">
                                                    <span class="badge" style="background: rgba(255,255,255,0.3); color: white; padding: 8px 15px; font-size: 14px; border-radius: 4px;">
                                                        <i class="fa fa-tag"></i> <?php echo htmlspecialchars($default_branch->category_name); ?>
                                                    </span>
                                                    <?php if (!empty($default_branch->location)): ?>
                                                        <span style="margin-left: 15px; font-size: 14px;">
                                                            <i class="fa fa-map-marker"></i> <?php echo htmlspecialchars($default_branch->location); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <p style="margin-top: 15px; margin-bottom: 0; opacity: 0.9; font-size: 14px;">
                                                    <i class="fa fa-user"></i> 
                                                    <?php if (is_admin()): ?>
                                                        Administrator: <strong><?php echo get_staff_full_name(get_staff_user_id()); ?></strong>
                                                    <?php else: ?>
                                                        Manager: <strong><?php echo get_staff_full_name(get_staff_user_id()); ?></strong>
                                                        <span class="badge" style="background: rgba(255,255,255,0.3); margin-left: 10px;">
                                                            <i class="fa fa-star"></i> SL Administrator
                                                        </span>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                            <div class="col-md-4 text-right">
                                                <?php if (!empty($default_branch->registration_token)): ?>
                                                    <button class="btn btn-lg" onclick="showAdminRegistrationLinkModal()" style="background: white; color: #28a745; font-weight: bold; padding: 15px 30px; border: none; border-radius: 6px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                                        <i class="fa fa-share-alt"></i> Get Registration Link
                                                    </button>
                                                    <p style="margin-top: 10px; margin-bottom: 0; font-size: 12px; opacity: 0.9;">
                                                        <i class="fa fa-info-circle"></i> Share this link for Head Office registrations
                                                    </p>
                                                <?php else: ?>
                                                    <div class="alert alert-warning" style="margin: 0; padding: 15px; background: rgba(255,255,255,0.9); color: #856404;">
                                                        <i class="fa fa-exclamation-triangle"></i> 
                                                        <small>No registration link available</small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Statistics Row -->
                            <div class="row">
                                <div class="col-md-3 col-sm-6">
                                    <div class="sls-stat-box" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                        <h3><?php echo isset($stats['total_branches']) ? $stats['total_branches'] : 0; ?></h3>
                                        <p>Total Partners</p>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6">
                                    <div class="sls-stat-box" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                                        <h3><?php echo isset($stats['total_students']) ? $stats['total_students'] : 0; ?></h3>
                                        <p>Total Candidates</p>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6">
                                    <div class="sls-stat-box" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                                        <h3><?php echo isset($stats['pending_reviews']) ? $stats['pending_reviews'] : 0; ?></h3>
                                        <p>Pending Reviews</p>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6">
                                    <div class="sls-stat-box" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
                                        <h3><?php echo isset($stats['total_earnings']) ? app_format_money($stats['total_earnings'], get_base_currency()) : '$0.00'; ?></h3>
                                        <p>Total Earnings</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Admin/Manager Stats Cards -->
                            <div class="row mtop20">
                                <div class="col-md-4 col-sm-6">
                                    <div class="sls-card">
                                        <h4><i class="fa fa-lock text-success"></i> Locked Profiles</h4>
                                        <h2 class="text-success"><?php echo isset($stats['locked_profiles']) ? $stats['locked_profiles'] : 0; ?></h2>
                                        <p class="text-muted">Approved and locked</p>
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-6">
                                    <div class="sls-card">
                                        <h4><i class="fa fa-exchange text-warning"></i> Change Requests</h4>
                                        <h2 class="text-warning"><?php echo isset($stats['change_requests']) ? $stats['change_requests'] : 0; ?></h2>
                                        <p class="text-muted">Pending change requests</p>
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-6">
                                    <div class="sls-card">
                                        <h4><i class="fa fa-clock-o text-info"></i> Pending Reviews</h4>
                                        <h2 class="text-info"><?php echo isset($stats['pending_reviews']) ? $stats['pending_reviews'] : 0; ?></h2>
                                        <p class="text-muted">Awaiting approval</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Candidates -->
                            <?php if (!empty($recent_students)): ?>
                            <div class="row mtop20">
                                <div class="col-md-12">
                                    <div class="sls-card">
                                        <h4><i class="fa fa-history"></i> Recent Candidates</h4>
                                        <hr />
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Candidate Name</th>
                                                        <th>Partner</th>
                                                        <th>Manager</th>
                                                        <th>Package</th>
                                                        <th>Completion</th>
                                                        <th>Status</th>
                                                        <th>Date Added</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($recent_students as $student): ?>
                                                        <tr>
                                                            <td>
                                                                <a href="<?php echo admin_url('safelegalsolutions/student/' . $student->id); ?>">
                                                                    <?php echo htmlspecialchars($student->student_name); ?>
                                                                </a>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($student->branch_name); ?></td>
                                                            <td><?php echo htmlspecialchars($student->manager_firstname . ' ' . $student->manager_lastname); ?></td>
                                                            <td>
                                                                <?php if (!empty($student->item_name)): ?>
                                                                    <span class="label label-info" style="font-size: 11px;">
                                                                        <?php echo htmlspecialchars($student->item_name); ?>
                                                                    </span>
                                                                    <br>
                                                                    <small class="text-success">
                                                                        <strong>₹<?php echo number_format($student->item_total_price, 2); ?></strong>
                                                                    </small>
                                                                <?php else: ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <div class="sls-progress-bar">
                                                                    <div class="sls-progress-fill" style="width: <?php echo $student->profile_completion; ?>%;">
                                                                        <?php echo $student->profile_completion; ?>%
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="label label-<?php 
                                                                    echo $student->status == 'approved' ? 'success' : 
                                                                         ($student->status == 'pending_review' ? 'warning' : 'default'); 
                                                                ?>">
                                                                    <?php echo ucwords(str_replace('_', ' ', $student->status)); ?>
                                                                </span>
                                                            </td>
                                                            <td><?php echo date('Y-m-d', strtotime($student->created_at)); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                        <?php else: ?>
                            <!-- NODAL PARTNER MANAGER DASHBOARD -->
                            <?php if (isset($branch) && $branch): ?>
                                
                                <!-- Branch Info Banner with Registration Link Button -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; margin-bottom: 20px; border-radius: 8px; padding: 25px;">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <h3 style="margin-top: 0; color: white;">
                                                        <i class="fa fa-building"></i> 
                                                        <?php echo htmlspecialchars($branch->branch_name); ?>
                                                    </h3>
                                                    <div style="margin-top: 10px;">
                                                        <span class="badge" style="background: rgba(255,255,255,0.3); color: white; padding: 8px 15px; font-size: 14px; border-radius: 4px;">
                                                            <i class="fa fa-tag"></i> <?php echo htmlspecialchars($branch->category_name); ?>
                                                        </span>
                                                        <?php if (!empty($branch->location)): ?>
                                                            <span style="margin-left: 15px; font-size: 14px;">
                                                                <i class="fa fa-map-marker"></i> <?php echo htmlspecialchars($branch->location); ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <p style="margin-top: 15px; margin-bottom: 0; opacity: 0.9; font-size: 14px;">
                                                        <i class="fa fa-user"></i> Partner Manager: <strong><?php echo get_staff_full_name(get_staff_user_id()); ?></strong>
                                                    </p>
                                                </div>
                                                <div class="col-md-4 text-right">
                                                    <?php if (!empty($branch->registration_token)): ?>
                                                        <button class="btn btn-lg" onclick="showRegistrationLinkModal()" style="background: white; color: #667eea; font-weight: bold; padding: 15px 30px; border: none; border-radius: 6px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                                            <i class="fa fa-share-alt"></i> Get Registration Link
                                                        </button>
                                                        <p style="margin-top: 10px; margin-bottom: 0; font-size: 12px; opacity: 0.9;">
                                                            <i class="fa fa-info-circle"></i> Share this link to register new candidates
                                                        </p>
                                                    <?php else: ?>
                                                        <div class="alert alert-warning" style="margin: 0; padding: 15px; background: rgba(255,255,255,0.9); color: #856404;">
                                                            <i class="fa fa-exclamation-triangle"></i> 
                                                            <small>No registration link available. Contact admin.</small>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Statistics Row -->
                                <div class="row">
                                    <div class="col-md-3 col-sm-6">
                                        <div class="sls-stat-box" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                            <h3><?php echo isset($stats['my_students']) ? $stats['my_students'] : 0; ?></h3>
                                            <p>My Candidates</p>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-6">
                                        <div class="sls-stat-box" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                                            <h3><?php echo isset($stats['pending_reviews']) ? $stats['pending_reviews'] : 0; ?></h3>
                                            <p>Pending Reviews</p>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-6">
                                        <div class="sls-stat-box" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
                                            <h3><?php echo isset($stats['locked_profiles']) ? $stats['locked_profiles'] : 0; ?></h3>
                                            <p>Approved Candidates</p>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-6">
                                        <div class="sls-stat-box" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
                                            <h3><?php echo isset($stats['my_earnings']) ? app_format_money($stats['my_earnings'], get_base_currency()) : '$0.00'; ?></h3>
                                            <p>My Earnings</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Info and Actions -->
                                <div class="row mtop20">
                                    <div class="col-md-6">
                                        <div class="sls-card">
                                            <h4><i class="fa fa-percent text-info"></i> Average Profile Completion</h4>
                                            <div class="sls-progress-bar" style="height: 40px; margin-top: 15px;">
                                                <div class="sls-progress-fill" style="width: <?php echo isset($stats['avg_completion']) ? $stats['avg_completion'] : 0; ?>%; font-size: 16px; line-height: 40px;">
                                                    <?php echo isset($stats['avg_completion']) ? number_format($stats['avg_completion'], 2) : '0.00'; ?>%
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="sls-card">
                                            <h4><i class="fa fa-bolt text-success"></i> Quick Actions</h4>
                                            <div class="mtop15">
                                                <a href="<?php echo admin_url('safelegalsolutions/student'); ?>" class="btn btn-success btn-block btn-lg">
                                                    <i class="fa fa-plus-circle"></i> Add New Candidate
                                                </a>
                                                <a href="<?php echo admin_url('safelegalsolutions/students'); ?>" class="btn btn-info btn-block btn-lg mtop10">
                                                    <i class="fa fa-users"></i> View All Candidates
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Recent Candidates for Manager -->
                                <?php if (!empty($recent_students)): ?>
                                <div class="row mtop20">
                                    <div class="col-md-12">
                                        <div class="sls-card">
                                            <h4><i class="fa fa-history"></i> My Recent Candidates</h4>
                                            <hr />
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Candidate Name</th>
                                                            <th>Email</th>
                                                            <th>Phone</th>
                                                          
                                                            <th>Package</th>
                                                            <th>Completion</th>
                                                            <th>Status</th>
                                                            <th>Date Added</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($recent_students as $student): ?>
                                                            <tr>
                                                                <td>
                                                                    <a href="<?php echo admin_url('safelegalsolutions/student/' . $student->id); ?>">
                                                                        <?php echo htmlspecialchars($student->student_name); ?>
                                                                    </a>
                                                                </td>
                                                                <td><?php echo htmlspecialchars($student->email); ?></td>
                                                                <td><?php echo htmlspecialchars($student->phone); ?></td>
                                                             
                                                                <td>
                                                                    <?php if (!empty($student->item_name)): ?>
                                                                        <span class="label label-info" style="font-size: 11px;">
                                                                            <?php echo htmlspecialchars($student->item_name); ?>
                                                                        </span>
                                                                        <br>
                                                                        <small class="text-success">
                                                                            <strong>₹<?php echo number_format($student->item_total_price, 2); ?></strong>
                                                                        </small>
                                                                    <?php else: ?>
                                                                        <span class="text-muted">-</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <div class="sls-progress-bar" style="height: 20px;">
                                                                        <div class="sls-progress-fill" style="width: <?php echo $student->profile_completion; ?>%; font-size: 11px; line-height: 20px;">
                                                                            <?php echo $student->profile_completion; ?>%
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span class="label label-<?php 
                                                                        echo $student->status == 'approved' ? 'success' : 
                                                                             ($student->status == 'pending_review' ? 'warning' : 'default'); 
                                                                    ?>">
                                                                        <?php echo ucwords(str_replace('_', ' ', $student->status)); ?>
                                                                    </span>
                                                                </td>
                                                                <td><?php echo date('Y-m-d', strtotime($student->created_at)); ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fa fa-exclamation-triangle"></i> 
                                    <strong>No Partner assigned to your account</strong>
                                    <p>Please contact the administrator to get a Partner assigned.</p>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- NPM Registration Link Modal -->
<?php if (!is_sls_manager_or_admin() && isset($branch) && !empty($branch->registration_token)): ?>
<div class="modal fade" id="registrationLinkModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px 30px;">
                <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 1; font-size: 28px;">
                    <span>&times;</span>
                </button>
                <h4 class="modal-title" style="margin: 0; font-size: 22px;">
                    <i class="fa fa-share-alt"></i> Candidate Registration Link
                </h4>
            </div>
            <div class="modal-body" style="padding: 30px;">
                <div class="form-group">
                    <label style="font-weight: 600; color: #333; font-size: 15px; margin-bottom: 10px;">
                        <i class="fa fa-link"></i> Your Public Registration Link:
                    </label>
                    <div class="input-group input-group-lg">
                        <input type="text" class="form-control" id="registration-link-input" 
                               value="<?php echo site_url('safelegalsolutions/safelegalsolutions_client/register/' . $branch->registration_token); ?>" 
                               readonly style="font-size: 14px; font-family: 'Courier New', monospace; background: #f8f9fa; border: 2px solid #e0e0e0; color: #333;">
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="button" onclick="copyRegistrationLink()" id="copy-btn" style="padding: 12px 30px; font-size: 16px;">
                                <i class="fa fa-copy"></i> Copy
                            </button>
                        </span>
                    </div>
                    <small class="text-success" style="display: block; margin-top: 10px; font-weight: 600;">
                        <i class="fa fa-check-circle"></i> ✓ No login required - Anyone can access and register
                    </small>
                    <small class="text-muted" style="display: block; margin-top: 5px;">
                        <i class="fa fa-info-circle"></i> Candidates who use this link will be automatically registered under your branch (<strong><?php echo htmlspecialchars($branch->branch_name); ?></strong>)
                    </small>
                </div>
                
                <hr style="border-top: 2px solid #e0e0e0; margin: 25px 0;">
                
                <div class="form-group">
                    <label style="font-weight: 600; color: #333; font-size: 15px; margin-bottom: 15px;">
                        <i class="fa fa-paper-plane"></i> Quick Share Options:
                    </label>
                    <div class="row">
                        <div class="col-xs-6 col-sm-3" style="margin-bottom: 10px;">
                            <a href="https://wa.me/?text=<?php echo urlencode('Register for Safe Legal : ' . site_url('safelegalsolutions/safelegalsolutions_client/register/' . $branch->registration_token)); ?>" 
                               target="_blank" class="btn btn-success btn-block btn-lg" style="padding: 15px; font-size: 15px;">
                                <i class="fa fa-whatsapp"></i> WhatsApp
                            </a>
                        </div>
                        <div class="col-xs-6 col-sm-3" style="margin-bottom: 10px;">
                            <a href="mailto:?subject=<?php echo urlencode('Candidate Registration - Safe Legal '); ?>&body=<?php echo urlencode('Click this link to register: ' . site_url('safelegalsolutions/safelegalsolutions_client/register/' . $branch->registration_token)); ?>" 
                               class="btn btn-info btn-block btn-lg" style="padding: 15px; font-size: 15px;">
                                <i class="fa fa-envelope"></i> Email
                            </a>
                        </div>
                    
                        <div class="col-xs-6 col-sm-3" style="margin-bottom: 10px;">
                            <button onclick="generateQRCode()" class="btn btn-default btn-block btn-lg" id="qr-btn" style="padding: 15px; font-size: 15px;">
                                <i class="fa fa-qrcode"></i> QR Code
                            </button>
                        </div>
                    </div>
                </div>
                
                <div id="qr-code-container" style="display:none; text-align: center; padding: 25px; background: #f8f9fa; border-radius: 8px; border: 3px dashed #667eea; margin-top: 20px;">
                    <div id="qr-code" style="display: inline-block;"></div>
                    <p style="margin-top: 15px; margin-bottom: 0; color: #666;">
                        <i class="fa fa-mobile" style="font-size: 20px;"></i> 
                        <strong>Scan this QR code with a mobile device to register</strong>
                    </p>
                </div>
                
                <hr style="border-top: 2px solid #e0e0e0; margin: 25px 0;">
                
                <div class="alert" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border: none; color: white; padding: 20px; border-radius: 8px; margin-bottom: 0;">
                    <h5 style="margin-top: 0; font-weight: 600;">
                        <i class="fa fa-lightbulb-o"></i> Pro Tips:
                    </h5>
                    <ul style="margin-bottom: 0; padding-left: 20px;">
                        <li style="margin-bottom: 8px;">Share this link via WhatsApp groups, SMS campaigns, or email newsletters</li>
                        <li style="margin-bottom: 8px;">Print the QR code on posters or brochures for offline promotion</li>
                        <li style="margin-bottom: 0;">Candidates can register instantly without creating an account</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer" style="padding: 20px 30px; background: #f8f9fa;">
                <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Admin/Manager Registration Link Modal -->
<?php if (is_sls_manager_or_admin() && isset($default_branch) && !empty($default_branch->registration_token)): ?>
<div class="modal fade" id="adminRegistrationLinkModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 20px 30px;">
                <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 1; font-size: 28px;">
                    <span>&times;</span>
                </button>
                <h4 class="modal-title" style="margin: 0; font-size: 22px;">
                    <i class="fa fa-share-alt"></i> Head Office Registration Link
                </h4>
            </div>
            <div class="modal-body" style="padding: 30px;">
                <div class="form-group">
                    <label style="font-weight: 600; color: #333; font-size: 15px; margin-bottom: 10px;">
                        <i class="fa fa-link"></i> Your Public Registration Link:
                    </label>
                    <div class="input-group input-group-lg">
                        <input type="text" class="form-control" id="admin-registration-link-input" 
                               value="<?php echo site_url('safelegalsolutions/safelegalsolutions_client/register/' . $default_branch->registration_token); ?>" 
                               readonly style="font-size: 14px; font-family: 'Courier New', monospace; background: #f8f9fa; border: 2px solid #e0e0e0; color: #333;">
                        <span class="input-group-btn">
                            <button class="btn btn-success" type="button" onclick="copyAdminRegistrationLink()" id="admin-copy-btn" style="padding: 12px 30px; font-size: 16px;">
                                <i class="fa fa-copy"></i> Copy
                            </button>
                        </span>
                    </div>
                    <small class="text-success" style="display: block; margin-top: 10px; font-weight: 600;">
                        <i class="fa fa-check-circle"></i> ✓ No login required - Anyone can access and register
                    </small>
                    <small class="text-muted" style="display: block; margin-top: 5px;">
                        <i class="fa fa-info-circle"></i> Candidates who use this link will be registered under <strong><?php echo htmlspecialchars($default_branch->branch_name); ?></strong>
                    </small>
                </div>
                
                <hr style="border-top: 2px solid #e0e0e0; margin: 25px 0;">
                
                <div class="form-group">
                    <label style="font-weight: 600; color: #333; font-size: 15px; margin-bottom: 15px;">
                        <i class="fa fa-paper-plane"></i> Quick Share Options:
                    </label>
                    <div class="row">
                        <div class="col-xs-6 col-sm-3" style="margin-bottom: 10px;">
                            <a href="https://wa.me/?text=<?php echo urlencode('Register for Safe Legal : ' . site_url('safelegalsolutions/safelegalsolutions_client/register/' . $default_branch->registration_token)); ?>" 
                               target="_blank" class="btn btn-success btn-block btn-lg" style="padding: 15px; font-size: 15px;">
                                <i class="fa fa-whatsapp"></i> WhatsApp
                            </a>
                        </div>
                        <div class="col-xs-6 col-sm-3" style="margin-bottom: 10px;">
                            <a href="mailto:?subject=<?php echo urlencode('Candidate Registration - Safe Legal '); ?>&body=<?php echo urlencode('Click this link to register: ' . site_url('safelegalsolutions/safelegalsolutions_client/register/' . $default_branch->registration_token)); ?>" 
                               class="btn btn-info btn-block btn-lg" style="padding: 15px; font-size: 15px;">
                                <i class="fa fa-envelope"></i> Email
                            </a>
                        </div>
                        <div class="col-xs-6 col-sm-3" style="margin-bottom: 10px;">
                            <a href="sms:?body=<?php echo urlencode('Register: ' . site_url('safelegalsolutions/safelegalsolutions_client/register/' . $default_branch->registration_token)); ?>" 
                               class="btn btn-warning btn-block btn-lg" style="padding: 15px; font-size: 15px;">
                                <i class="fa fa-commenting"></i> SMS
                            </a>
                        </div>
                        <div class="col-xs-6 col-sm-3" style="margin-bottom: 10px;">
                            <button onclick="generateAdminQRCode()" class="btn btn-default btn-block btn-lg" id="admin-qr-btn" style="padding: 15px; font-size: 15px;">
                                <i class="fa fa-qrcode"></i> QR Code
                            </button>
                        </div>
                    </div>
                </div>
                
                <div id="admin-qr-code-container" style="display:none; text-align: center; padding: 25px; background: #f8f9fa; border-radius: 8px; border: 3px dashed #28a745; margin-top: 20px;">
                    <div id="admin-qr-code" style="display: inline-block;"></div>
                    <p style="margin-top: 15px; margin-bottom: 0; color: #666;">
                        <i class="fa fa-mobile" style="font-size: 20px;"></i> 
                        <strong>Scan this QR code to register</strong>
                    </p>
                </div>
                
                <hr style="border-top: 2px solid #e0e0e0; margin: 25px 0;">
                
                <div class="alert" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border: none; color: white; padding: 20px; border-radius: 8px; margin-bottom: 0;">
                    <h5 style="margin-top: 0; font-weight: 600;">
                        <i class="fa fa-lightbulb-o"></i> Pro Tips:
                    </h5>
                    <ul style="margin-bottom: 0; padding-left: 20px;">
                        <li style="margin-bottom: 8px;">Share via WhatsApp, email, or SMS for instant registrations</li>
                        <li style="margin-bottom: 8px;">Print QR code for offline marketing materials</li>
                        <li style="margin-bottom: 0;">Candidates register without needing an account</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer" style="padding: 20px 30px; background: #f8f9fa;">
                <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php init_tail(); ?>

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>

<style>
.sls-stat-box {
    padding: 25px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.sls-stat-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.sls-stat-box h3 {
    font-size: 36px;
    font-weight: bold;
    margin: 0 0 10px 0;
}

.sls-stat-box p {
    font-size: 14px;
    margin: 0;
    opacity: 0.9;
}

.sls-card {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 20px;
    transition: box-shadow 0.3s ease;
}

.sls-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}

.sls-card h4 {
    margin-top: 0;
    margin-bottom: 15px;
    font-weight: 600;
}

.sls-progress-bar {
    background: #e9ecef;
    border-radius: 5px;
    overflow: hidden;
    height: 25px;
}

.sls-progress-fill {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-align: center;
    line-height: 25px;
    font-weight: bold;
    font-size: 12px;
    transition: width 0.3s ease;
}

.modal-content {
    border-radius: 10px;
    overflow: hidden;
}

.input-group-lg .form-control {
    height: 50px;
}
</style>

<script>
// NPM Functions
function showRegistrationLinkModal() {
    $('#registrationLinkModal').modal('show');
}

function copyRegistrationLink() {
    var copyText = document.getElementById("registration-link-input");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    
    try {
        var successful = document.execCommand('copy');
        
        if (successful) {
            var btn = document.getElementById('copy-btn');
            var originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fa fa-check"></i> Copied!';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-success');
            
            setTimeout(function() {
                btn.innerHTML = originalHTML;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-primary');
            }, 2000);
        } else {
            alert('Failed to copy. Please copy manually.');
        }
    } catch (err) {
        alert('Please copy the link manually: Ctrl+C (Windows) or Cmd+C (Mac)');
    }
}

var qrGenerated = false;

function generateQRCode() {
    var qrContainer = document.getElementById('qr-code-container');
    var qrCodeDiv = document.getElementById('qr-code');
    var qrBtn = document.getElementById('qr-btn');
    var registrationLink = document.getElementById('registration-link-input').value;
    
    if (!qrGenerated) {
        qrCodeDiv.innerHTML = '';
        
        QRCode.toCanvas(registrationLink, { 
            errorCorrectionLevel: 'H',
            width: 300,
            margin: 2,
            color: {
                dark: '#667eea',
                light: '#ffffff'
            }
        }, function (error, canvas) {
            if (error) {
                alert('Error generating QR code: ' + error);
                console.error(error);
            } else {
                qrCodeDiv.appendChild(canvas);
                qrContainer.style.display = 'block';
                
                var downloadBtn = document.createElement('button');
                downloadBtn.className = 'btn btn-success btn-lg';
                downloadBtn.style.marginTop = '15px';
                downloadBtn.innerHTML = '<i class="fa fa-download"></i> Download QR Code';
                downloadBtn.onclick = function() {
                    var link = document.createElement('a');
                    link.download = 'registration-qr-code-<?php echo isset($branch->branch_code) ? $branch->branch_code : "sls"; ?>.png';
                    link.href = canvas.toDataURL();
                    link.click();
                };
                qrCodeDiv.appendChild(document.createElement('br'));
                qrCodeDiv.appendChild(downloadBtn);
                
                qrBtn.innerHTML = '<i class="fa fa-times"></i> Hide QR';
                qrBtn.classList.remove('btn-default');
                qrBtn.classList.add('btn-danger');
                qrGenerated = true;
            }
        });
    } else {
        qrContainer.style.display = 'none';
        qrBtn.innerHTML = '<i class="fa fa-qrcode"></i> QR Code';
        qrBtn.classList.remove('btn-danger');
        qrBtn.classList.add('btn-default');
        qrGenerated = false;
    }
}

// Admin/Manager Functions
function showAdminRegistrationLinkModal() {
    $('#adminRegistrationLinkModal').modal('show');
}

function copyAdminRegistrationLink() {
    var copyText = document.getElementById("admin-registration-link-input");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    
    try {
        var successful = document.execCommand('copy');
        
        if (successful) {
            var btn = document.getElementById('admin-copy-btn');
            var originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fa fa-check"></i> Copied!';
            btn.classList.remove('btn-success');
            btn.classList.add('btn-primary');
            
            setTimeout(function() {
                btn.innerHTML = originalHTML;
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-success');
            }, 2000);
        } else {
            alert('Failed to copy. Please copy manually.');
        }
    } catch (err) {
        alert('Please copy the link manually');
    }
}

var adminQrGenerated = false;

function generateAdminQRCode() {
    var qrContainer = document.getElementById('admin-qr-code-container');
    var qrCodeDiv = document.getElementById('admin-qr-code');
    var qrBtn = document.getElementById('admin-qr-btn');
    var registrationLink = document.getElementById('admin-registration-link-input').value;
    
    if (!adminQrGenerated) {
        qrCodeDiv.innerHTML = '';
        
        QRCode.toCanvas(registrationLink, { 
            errorCorrectionLevel: 'H',
            width: 300,
            margin: 2,
            color: {
                dark: '#28a745',
                light: '#ffffff'
            }
        }, function (error, canvas) {
            if (error) {
                alert('Error generating QR code: ' + error);
                console.error(error);
            } else {
                qrCodeDiv.appendChild(canvas);
                qrContainer.style.display = 'block';
                
                var downloadBtn = document.createElement('button');
                downloadBtn.className = 'btn btn-success btn-lg';
                downloadBtn.style.marginTop = '15px';
                downloadBtn.innerHTML = '<i class="fa fa-download"></i> Download QR Code';
                downloadBtn.onclick = function() {
                    var link = document.createElement('a');
                    link.download = 'head-office-registration-qr.png';
                    link.href = canvas.toDataURL();
                    link.click();
                };
                qrCodeDiv.appendChild(document.createElement('br'));
                qrCodeDiv.appendChild(downloadBtn);
                
                qrBtn.innerHTML = '<i class="fa fa-times"></i> Hide QR';
                qrBtn.classList.remove('btn-default');
                qrBtn.classList.add('btn-danger');
                adminQrGenerated = true;
            }
        });
    } else {
        qrContainer.style.display = 'none';
        qrBtn.innerHTML = '<i class="fa fa-qrcode"></i> QR Code';
        qrBtn.classList.remove('btn-danger');
        qrBtn.classList.add('btn-default');
        adminQrGenerated = false;
    }
}
</script>

</body>
</html>