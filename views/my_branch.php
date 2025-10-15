<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-home"></i> My Partner: <?php echo htmlspecialchars($branch->branch_name); ?>
                            <?php if (!is_admin()): ?>
                                <span class="badge" style="background: #667eea; color: white; margin-left: 10px; padding: 8px 15px; font-size: 13px;">
                                    <i class="fa fa-star"></i> SL Administrator
                                </span>
                            <?php endif; ?>
                        </h4>
                        <hr class="hr-panel-heading" />

                        <!-- Branch Details -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="sls-card">
                                    <h4><i class="fa fa-info-circle"></i> Partner Details</h4>
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <td style="width: 40%;"><strong>Partner Code:</strong></td>
                                                <td><?php echo htmlspecialchars($branch->branch_code); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Category:</strong></td>
                                                <td>
                                                    <span style="display: inline-block; width: 10px; height: 10px; background: <?php echo $branch->color_code; ?>; border-radius: 50%; margin-right: 5px;"></span>
                                                    <?php echo htmlspecialchars($branch->category_name); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Location:</strong></td>
                                                <td><?php echo htmlspecialchars($branch->location); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Address:</strong></td>
                                                <td><?php echo nl2br(htmlspecialchars($branch->address)); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Assigned Manager:</strong></td>
                                                <td><?php echo htmlspecialchars($branch->manager_firstname . ' ' . $branch->manager_lastname); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Your Role:</strong></td>
                                                <td>
                                                    <?php if (is_admin()): ?>
                                                        <span class="label label-danger"><i class="fa fa-shield"></i> System Administrator</span>
                                                    <?php else: ?>
                                                        <span class="label label-primary"><i class="fa fa-star"></i> SL Manager</span>
                                                        <br><small class="text-muted" style="font-size: 11px;">Full SL Module Access</small>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Partner Status:</strong></td>
                                                <td>
                                                    <?php if ($branch->is_active == 1): ?>
                                                        <span class="label label-success"><i class="fa fa-check"></i> Active</span>
                                                    <?php else: ?>
                                                        <span class="label label-default"><i class="fa fa-times"></i> Inactive</span>
                                                    <?php endif; ?>
                                                    <?php if ($branch->is_default == 1): ?>
                                                        <span class="label label-primary"><i class="fa fa-home"></i> Default Partner</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php if (!empty($branch->registration_token)): ?>
                                            <tr>
                                                <td><strong>Registration Link:</strong></td>
                                                <td>
                                                    <button class="btn btn-xs btn-info" onclick="showBranchRegistrationModal()">
                                                        <i class="fa fa-share-alt"></i> Get Link
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="sls-card">
                                    <h4><i class="fa fa-bar-chart"></i> Partner Statistics</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="sls-stat-box" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                                <h3><?php echo $stats['total_students']; ?></h3>
                                                <p>Total Candidates</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="sls-stat-box" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                                                <h3><?php echo $stats['pending_reviews']; ?></h3>
                                                <p>Pending Reviews</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="sls-stat-box" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                                                <h3><?php echo $stats['approved_students']; ?></h3>
                                                <p>Approved Candidates</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="sls-stat-box" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
                                                <h3><?php echo $stats['locked_profiles']; ?></h3>
                                                <p>Locked Profiles</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="sls-stat-box" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
                                                <h3>₹<?php echo number_format($stats['total_earnings'], 2); ?></h3>
                                                <p>Total Earnings</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="sls-stat-box" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); color: white;">
                                                <h3><?php echo $stats['avg_completion']; ?>%</h3>
                                                <p>Avg. Completion</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Branch Candidates Table -->
                        <div class="row mtop20">
                            <div class="col-md-12">
                                <div class="sls-card">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h4><i class="fa fa-users"></i> Partners Candidates (<?php echo count($all_students); ?>)</h4>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <a href="<?php echo admin_url('safelegalsolutions/student'); ?>" class="btn btn-primary btn-sm">
                                                <i class="fa fa-plus"></i> Add Candidate to This Partner
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <div class="table-responsive mtop15">
                                        <table class="table table-striped table-hover" id="branch-candidates-table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Candidate Name</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                
                                                    <th>Package</th>
                                                    <th>Completion</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($all_students)): ?>
                                                    <?php $i = 1; foreach ($all_students as $student): ?>
                                                        <tr>
                                                            <td><?php echo $i++; ?></td>
                                                            <td>
                                                                <strong><?php echo htmlspecialchars($student->student_name); ?></strong>
                                                                <br>
                                                                <small class="text-muted">
                                                                    <i class="fa fa-barcode"></i> <?php echo htmlspecialchars($student->referral_code); ?>
                                                                </small>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($student->email); ?></td>
                                                            <td><?php echo htmlspecialchars($student->phone); ?></td>
                                                           
                                                            <td>
                                                                <?php if (!empty($student->item_name)): ?>
                                                                    <span class="label label-info">
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
                                                                <?php
                                                                $status_colors = [
                                                                    'draft' => 'default',
                                                                    'pending_review' => 'warning',
                                                                    'approved' => 'success',
                                                                    'locked' => 'info',
                                                                    'change_requested' => 'danger'
                                                                ];
                                                                $color = isset($status_colors[$student->status]) ? $status_colors[$student->status] : 'default';
                                                                ?>
                                                                <span class="label label-<?php echo $color; ?>">
                                                                    <?php echo ucwords(str_replace('_', ' ', $student->status)); ?>
                                                                </span>
                                                                <?php if ($student->is_locked == 1): ?>
                                                                    <br><i class="fa fa-lock text-danger" title="Profile Locked"></i>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <a href="<?php echo admin_url('safelegalsolutions/student/' . $student->id); ?>" 
                                                                   class="btn btn-default btn-xs" title="Edit">
                                                                    <i class="fa fa-pencil"></i>
                                                                </a>
                                                                <?php if ($student->is_locked == 0): ?>
                                                                    <a href="<?php echo admin_url('safelegalsolutions/delete_student/' . $student->id); ?>" 
                                                                       class="btn btn-danger btn-xs" 
                                                                       onclick="return confirm('Are you sure you want to delete this candidate?');" 
                                                                       title="Delete">
                                                                        <i class="fa fa-trash"></i>
                                                                    </a>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="9" class="text-center text-muted">
                                                            <div style="padding: 40px 0;">
                                                                <i class="fa fa-users" style="font-size: 48px; color: #ddd; margin-bottom: 15px;"></i>
                                                                <p style="font-size: 16px; margin-bottom: 10px;">No candidates found in this branch yet.</p>
                                                                <a href="<?php echo admin_url('safelegalsolutions/student'); ?>" class="btn btn-primary btn-sm">
                                                                    <i class="fa fa-plus"></i> Add Your First Candidate
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Branch Registration Link Modal -->
<?php if (!empty($branch->registration_token)): ?>
<div class="modal fade" id="branchRegistrationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px 30px;">
                <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 1; font-size: 28px;">
                    <span>&times;</span>
                </button>
                <h4 class="modal-title" style="margin: 0; font-size: 22px;">
                    <i class="fa fa-share-alt"></i> <?php echo htmlspecialchars($branch->branch_name); ?> Registration Link
                </h4>
            </div>
            <div class="modal-body" style="padding: 30px;">
                <div class="form-group">
                    <label style="font-weight: 600; color: #333; font-size: 15px; margin-bottom: 10px;">
                        <i class="fa fa-link"></i> Public Registration Link:
                    </label>
                    <div class="input-group input-group-lg">
                        <input type="text" class="form-control" id="branch-registration-link-input" 
                               value="<?php echo site_url('safelegalsolutions/register/' . $branch->registration_token); ?>" 
                               readonly style="font-size: 14px; font-family: 'Courier New', monospace; background: #f8f9fa; border: 2px solid #e0e0e0; color: #333;">
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="button" onclick="copyBranchRegistrationLink()" id="branch-copy-btn" style="padding: 12px 30px; font-size: 16px;">
                                <i class="fa fa-copy"></i> Copy
                            </button>
                        </span>
                    </div>
                    <small class="text-success" style="display: block; margin-top: 10px; font-weight: 600;">
                        <i class="fa fa-check-circle"></i> ✓ No login required - Anyone can register
                    </small>
                </div>
                
                <hr style="border-top: 2px solid #e0e0e0; margin: 25px 0;">
                
                <div class="form-group">
                    <label style="font-weight: 600; color: #333; font-size: 15px; margin-bottom: 15px;">
                        <i class="fa fa-paper-plane"></i> Quick Share:
                    </label>
                    <div class="row">
                        <div class="col-xs-6 col-sm-3">
                            <a href="https://wa.me/?text=<?php echo urlencode('Register at ' . $branch->branch_name . ': ' . site_url('safelegalsolutions/register/' . $branch->registration_token)); ?>" 
                               target="_blank" class="btn btn-success btn-block btn-lg">
                                <i class="fa fa-whatsapp"></i> WhatsApp
                            </a>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <a href="mailto:?subject=<?php echo urlencode('Register - ' . $branch->branch_name); ?>&body=<?php echo urlencode('Register here: ' . site_url('safelegalsolutions/register/' . $branch->registration_token)); ?>" 
                               class="btn btn-info btn-block btn-lg">
                                <i class="fa fa-envelope"></i> Email
                            </a>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <a href="sms:?body=<?php echo urlencode('Register: ' . site_url('safelegalsolutions/register/' . $branch->registration_token)); ?>" 
                               class="btn btn-warning btn-block btn-lg">
                                <i class="fa fa-commenting"></i> SMS
                            </a>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <button onclick="generateBranchQRCode()" class="btn btn-default btn-block btn-lg" id="branch-qr-btn">
                                <i class="fa fa-qrcode"></i> QR Code
                            </button>
                        </div>
                    </div>
                </div>
                
                <div id="branch-qr-code-container" style="display:none; text-align: center; padding: 25px; background: #f8f9fa; border-radius: 8px; border: 3px dashed #667eea; margin-top: 20px;">
                    <div id="branch-qr-code" style="display: inline-block;"></div>
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
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin-bottom: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.sls-stat-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.sls-stat-box h3 {
    font-size: 32px;
    font-weight: bold;
    margin: 0 0 8px 0;
}

.sls-stat-box p {
    font-size: 13px;
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
    height: 20px;
}

.sls-progress-fill {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-align: center;
    line-height: 20px;
    font-weight: bold;
    font-size: 11px;
    transition: width 0.3s ease;
}

.table-borderless td {
    border: none !important;
    padding: 8px 0;
}
</style>

<script>
// Branch Registration Link Modal
function showBranchRegistrationModal() {
    $('#branchRegistrationModal').modal('show');
}

function copyBranchRegistrationLink() {
    var copyText = document.getElementById("branch-registration-link-input");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    
    try {
        var successful = document.execCommand('copy');
        
        if (successful) {
            var btn = document.getElementById('branch-copy-btn');
            var originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fa fa-check"></i> Copied!';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-success');
            
            setTimeout(function() {
                btn.innerHTML = originalHTML;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-primary');
            }, 2000);
        }
    } catch (err) {
        alert('Please copy the link manually');
    }
}

var branchQrGenerated = false;

function generateBranchQRCode() {
    var qrContainer = document.getElementById('branch-qr-code-container');
    var qrCodeDiv = document.getElementById('branch-qr-code');
    var qrBtn = document.getElementById('branch-qr-btn');
    var registrationLink = document.getElementById('branch-registration-link-input').value;
    
    if (!branchQrGenerated) {
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
                alert('Error generating QR code');
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
                    link.download = 'branch-registration-qr.png';
                    link.href = canvas.toDataURL();
                    link.click();
                };
                qrCodeDiv.appendChild(document.createElement('br'));
                qrCodeDiv.appendChild(downloadBtn);
                
                qrBtn.innerHTML = '<i class="fa fa-times"></i> Hide QR';
                qrBtn.classList.remove('btn-default');
                qrBtn.classList.add('btn-danger');
                branchQrGenerated = true;
            }
        });
    } else {
        qrContainer.style.display = 'none';
        qrBtn.innerHTML = '<i class="fa fa-qrcode"></i> QR Code';
        qrBtn.classList.remove('btn-danger');
        qrBtn.classList.add('btn-default');
        branchQrGenerated = false;
    }
}
</script>

</body>
</html>