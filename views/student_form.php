<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-10">
                                <h4 class="no-margin">
                                    <i class="fa fa-user-plus"></i> <?php echo isset($student) ? 'Edit Candidate' : 'Add New Candidate'; ?>
                                </h4>
                            </div>
                            <div class="col-md-2 text-right">
                                <a href="<?php echo admin_url('safelegalsolutions/students'); ?>" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <hr class="hr-panel-heading" />

                        <?php if (isset($student) && $student->is_locked == 1 && !is_admin()): ?>
                            <div class="alert alert-warning">
                                <i class="fa fa-lock"></i> 
                                <strong>Profile Locked</strong>
                                <p>This candidate profile has been approved and locked by admin. You cannot make changes directly. Please submit a change request if modifications are needed.</p>
                            </div>
                        <?php endif; ?>

                        <?php echo form_open(admin_url('safelegalsolutions/student/' . (isset($student) ? $student->id : '')), ['id' => 'candidate-form']); ?>
                        
                        <!-- Branch Selection (Admin Only) -->
                        <?php if (is_admin()): ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="branch_id" class="control-label">
                                            <span class="text-danger">*</span> Branch
                                        </label>
                                        <select name="branch_id" id="branch_id" class="form-control selectpicker" 
                                                data-live-search="true" data-width="100%" required>
                                            <option value="">-- Select Branch --</option>
                                            <?php if (!empty($branches)): ?>
                                                <?php foreach ($branches as $branch): ?>
                                                    <option value="<?php echo $branch->id; ?>"
                                                            <?php echo (isset($student) && $student->branch_id == $branch->id) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($branch->branch_name); ?>
                                                        <?php if (isset($branch->category_name)): ?>
                                                            (<?php echo htmlspecialchars($branch->category_name); ?>)
                                                        <?php endif; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php if (isset($branches[0]) && $branches[0]): ?>
                                <input type="hidden" name="branch_id" value="<?php echo $branches[0]->id; ?>">
                                <div class="alert alert-info">
                                    <i class="fa fa-building"></i> 
                                    <strong>Branch:</strong> <?php echo htmlspecialchars($branches[0]->branch_name); ?>
                                    <?php if (isset($branches[0]->category_name)): ?>
                                        <span class="badge" style="background: <?php echo $branches[0]->color_code; ?>; color: white;">
                                            <?php echo htmlspecialchars($branches[0]->category_name); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger">
                                    <i class="fa fa-exclamation-triangle"></i> 
                                    <strong>No Branch Assigned!</strong>
                                    <p>You need to have a branch assigned to create candidates. Please contact your administrator.</p>
                                </div>
                                <script>
                                    // Disable form submission if no branch
                                    $('#candidate-form').on('submit', function(e) {
                                        e.preventDefault();
                                        alert('You cannot create candidates without an assigned branch. Please contact admin.');
                                        return false;
                                    });
                                </script>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Personal Information -->
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <i class="fa fa-user"></i> Personal Information
                                </h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="student_name" class="control-label">
                                                <span class="text-danger">*</span> Full Name
                                            </label>
                                            <input type="text" name="student_name" id="student_name" 
                                                   class="form-control" required
                                                   placeholder="Enter candidate's full name"
                                                   value="<?php echo isset($student) ? htmlspecialchars($student->student_name) : ''; ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date_of_birth" class="control-label">
                                                <span class="text-danger">*</span> Date of Birth
                                            </label>
                                            <input type="date" name="date_of_birth" id="date_of_birth" 
                                                   class="form-control" required
                                                   max="<?php echo date('Y-m-d'); ?>"
                                                   value="<?php echo isset($student) ? $student->date_of_birth : ''; ?>">
                                            <small class="text-muted">Candidate must be at least 18 years old</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- PASSPORT NUMBER & EXPIRY ROW -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="passport_number" class="control-label">
                                                <span class="text-danger">*</span> Passport Number
                                            </label>
                                            <input type="text" name="passport_number" id="passport_number" 
                                                   class="form-control" required
                                                   placeholder="Enter passport number"
                                                   pattern="[A-Z0-9]+"
                                                   minlength="6"
                                                   style="text-transform: uppercase;"
                                                   value="<?php echo isset($student) ? htmlspecialchars($student->passport_number) : ''; ?>">
                                            <small class="text-muted">Valid passport number (letters and numbers only, minimum 6 characters)</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="passport_expiry_date" class="control-label">
                                                <span class="text-danger">*</span> Passport Expiry Date
                                            </label>
                                            <input type="date" name="passport_expiry_date" id="passport_expiry_date" 
                                                   class="form-control" required
                                                   min="<?php echo date('Y-m-d'); ?>"
                                                   value="<?php echo isset($student) ? $student->passport_expiry_date : ''; ?>">
                                            <small class="text-muted">Passport must be valid</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- UNIQUE ID DISPLAY - ONLY FOR EDIT MODE -->
                                <?php if (isset($student) && !empty($student->unique_id)): ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-info" style="background: #e3f2fd; border-left: 4px solid #2196f3;">
                                            <i class="fa fa-id-card-o"></i> 
                                            <strong>Student Unique ID:</strong>
                                            <code style="font-size: 16px; color: #1976d2; background: white; padding: 5px 15px; border-radius: 4px; margin-left: 10px;">
                                                <?php echo htmlspecialchars($student->unique_id); ?>
                                            </code>
                                            <button type="button" class="btn btn-xs btn-primary" onclick="copyUniqueId()" style="margin-left: 10px;">
                                                <i class="fa fa-copy"></i> Copy
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <i class="fa fa-phone"></i> Contact Information
                                </h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email" class="control-label">
                                                <span class="text-danger">*</span> Email Address
                                            </label>
                                            <input type="email" name="email" id="email" 
                                                   class="form-control" required
                                                   placeholder="candidate@example.com"
                                                   value="<?php echo isset($student) ? htmlspecialchars($student->email) : ''; ?>">
                                            <small class="text-muted">Must be a valid email address</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone" class="control-label">
                                                <span class="text-danger">*</span> Phone Number
                                            </label>
                                            <input type="text" name="phone" id="phone" 
                                                   class="form-control" required
                                                   placeholder="+91 9876543210"
                                                   pattern="[0-9+\-\s()]+"
                                                   value="<?php echo isset($student) ? htmlspecialchars($student->phone) : ''; ?>">
                                            <small class="text-muted">10 digit mobile number</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="address" class="control-label">
                                                <span class="text-danger">*</span> Street Address
                                            </label>
                                            <textarea name="address" id="address" class="form-control" rows="2" required
                                                      placeholder="House/Flat No., Street, Area"><?php echo isset($student) ? htmlspecialchars($student->address) : ''; ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="city" class="control-label">
                                                <span class="text-danger">*</span> City
                                            </label>
                                            <input type="text" name="city" id="city" 
                                                   class="form-control" required
                                                   placeholder="Enter city"
                                                   value="<?php echo isset($student) ? htmlspecialchars($student->city) : ''; ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="state" class="control-label">
                                                <span class="text-danger">*</span> State/Province
                                            </label>
                                            <input type="text" name="state" id="state" 
                                                   class="form-control" required
                                                   placeholder="Enter state"
                                                   value="<?php echo isset($student) ? htmlspecialchars($student->state) : ''; ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="pin_code" class="control-label">
                                                <span class="text-danger">*</span> PIN Code
                                            </label>
                                            <input type="text" name="pin_code" id="pin_code" 
                                                   class="form-control" required
                                                   placeholder="000000"
                                                   pattern="[0-9]{5,10}"
                                                   value="<?php echo isset($student) ? htmlspecialchars($student->pin_code) : ''; ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="emergency_contact_mobile" class="control-label">
                                                <span class="text-danger">*</span> Emergency Contact Mobile
                                            </label>
                                            <input type="text" name="emergency_contact_mobile" id="emergency_contact_mobile" 
                                                   class="form-control" required
                                                   placeholder="+91 9876543210"
                                                   pattern="[0-9+\-\s()]+"
                                                   value="<?php echo isset($student) ? htmlspecialchars($student->emergency_contact_mobile) : ''; ?>">
                                            <small class="text-muted">Emergency contact number (can be different from student's phone)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Destination & Program Details -->
                        <div class="panel panel-success">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <i class="fa fa-globe"></i> Destination & Program Details
                                </h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="destination_country_id" class="control-label">
                                                <span class="text-danger">*</span> Destination Country
                                            </label>
                                            <select name="destination_country_id" id="destination_country_id" 
                                                    class="form-control selectpicker" 
                                                    data-live-search="true" data-width="100%" required>
                                                <option value="">-- Select Country --</option>
                                                <?php 
                                                $countries = $this->safelegalsolutions_model->get_all_countries(['is_active' => 1]);
                                                if (!empty($countries)): 
                                                    foreach ($countries as $country): 
                                                ?>
                                                    <option value="<?php echo $country->id; ?>"
                                                            <?php echo (isset($student) && $student->destination_country_id == $country->id) ? 'selected' : ''; ?>>
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
                                            <small class="text-muted">Select country where student plans to study</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="university_name" class="control-label">
                                                <span class="text-danger">*</span> University/Institution Name
                                            </label>
                                            <input type="text" name="university_name" id="university_name" 
                                                   class="form-control" required
                                                   placeholder="Enter university/institution name"
                                                   value="<?php echo isset($student) ? htmlspecialchars($student->university_name) : ''; ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="course_program" class="control-label">
                                                <span class="text-danger">*</span> Course/Program
                                            </label>
                                            <input type="text" name="course_program" id="course_program" 
                                                   class="form-control" required
                                                   placeholder="E.g., MBA, Bachelor of Computer Science, etc."
                                                   value="<?php echo isset($student) ? htmlspecialchars($student->course_program) : ''; ?>">
                                            <small class="text-muted">Enter the course/program the student is enrolling in</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Package Selection & Payment Tracking -->
                        <div class="panel panel-warning">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <i class="fa fa-shopping-cart"></i> Package & Payment Details
                                </h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="item_id" class="control-label">
                                                <span class="text-danger">*</span> Select Package
                                            </label>
                                            <select name="item_id" id="item_id" class="form-control selectpicker" 
                                                    data-live-search="true" data-width="100%" required>
                                                <option value="">-- Select Package --</option>
                                                <?php 
                                                // Get all active items/packages
                                                $items = $this->safelegalsolutions_model->get_active_items();
                                                if (!empty($items)): 
                                                    foreach ($items as $item): 
                                                ?>
                                                    <option value="<?php echo $item->id; ?>"
                                                            data-base-price="<?php echo $item->base_price; ?>"
                                                            data-gst="<?php echo $item->gst_amount; ?>"
                                                            data-total="<?php echo $item->total_price; ?>"
                                                            data-duration="<?php echo $item->duration_months; ?>"
                                                            <?php echo (isset($student) && $student->item_id == $item->id) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($item->item_name); ?> 
                                                        - ₹<?php echo number_format($item->total_price, 2); ?>
                                                        <?php if (!empty($item->duration_months)): ?>
                                                            (<?php echo $item->duration_months; ?> months)
                                                        <?php endif; ?>
                                                    </option>
                                                <?php 
                                                    endforeach;
                                                endif; 
                                                ?>
                                            </select>
                                            <small class="text-muted">Select the package candidate is enrolling in</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Package Price Details</label>
                                            <div id="price-details" style="background: #f8f9fa; padding: 15px; border-radius: 4px; border: 1px solid #e0e0e0;">
                                                <div style="margin-bottom: 8px;">
                                                    <span style="color: #666;">Base Price:</span>
                                                    <strong style="float: right;" id="display-base-price">₹0.00</strong>
                                                </div>
                                                <div style="margin-bottom: 8px;">
                                                    <span style="color: #666;">GST:</span>
                                                    <strong style="float: right;" id="display-gst">₹0.00</strong>
                                                </div>
                                                <hr style="margin: 10px 0;">
                                                <div>
                                                    <span style="color: #333; font-size: 16px;"><strong>Total Amount:</strong></span>
                                                    <strong style="float: right; color: #28a745; font-size: 18px;" id="display-total">₹0.00</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr style="border-top: 2px dashed #ddd; margin: 20px 0;">

                                <!-- Payment Collection Section -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5 style="margin-top: 0; margin-bottom: 15px;">
                                            <i class="fa fa-money"></i> Payment Information
                                        </h5>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="payment_status" class="control-label">
                                                <span class="text-danger">*</span> Payment Status
                                            </label>
                                            <select name="payment_status" id="payment_status" class="form-control" required>
                                                <option value="unpaid" <?php echo (isset($student) && $student->payment_status == 'unpaid') ? 'selected' : ''; ?>>
                                                    Unpaid
                                                </option>
                                                <option value="partial" <?php echo (isset($student) && $student->payment_status == 'partial') ? 'selected' : ''; ?>>
                                                    Partial Payment
                                                </option>
                                                <option value="paid" <?php echo (isset($student) && $student->payment_status == 'paid') ? 'selected' : ''; ?>>
                                                    Fully Paid
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="amount_paid" class="control-label">Amount Paid</label>
                                            <input type="number" name="amount_paid" id="amount_paid" 
                                                   class="form-control" 
                                                   min="0" 
                                                   step="0.01"
                                                   placeholder="0.00"
                                                   value="<?php echo isset($student) ? $student->amount_paid : '0.00'; ?>">
                                            <small class="text-muted">Enter amount received</small>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="payment_method" class="control-label">Payment Method</label>
                                            <select name="payment_method" id="payment_method" class="form-control">
                                                <option value="cash">Cash</option>
                                                <option value="bank_transfer">Bank Transfer</option>
                                                <option value="upi">UPI</option>
                                                <option value="card">Card</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="payment_date" class="control-label">Payment Date</label>
                                            <input type="date" name="payment_date" id="payment_date" 
                                                   class="form-control"
                                                   max="<?php echo date('Y-m-d'); ?>"
                                                   value="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="transaction_reference" class="control-label">Transaction Reference</label>
                                            <input type="text" name="transaction_reference" id="transaction_reference" 
                                                   class="form-control"
                                                   placeholder="UTR/Ref Number (optional)">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="payment_notes" class="control-label">Payment Notes (Optional)</label>
                                            <textarea name="payment_notes" id="payment_notes" class="form-control" rows="2"
                                                      placeholder="Add any notes about the payment (receipt number, installment details, etc.)"></textarea>
                                            <small class="text-muted">These notes will be saved with the payment transaction</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Internal Notes -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <i class="fa fa-sticky-note"></i> Internal Notes (Optional)
                                </h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="notes" class="control-label">
                                                Notes
                                            </label>
                                            <textarea name="notes" id="notes" class="form-control" rows="4"
                                                      placeholder="Add any internal notes or comments about this candidate (not visible to candidate)"><?php echo isset($student) ? htmlspecialchars($student->notes) : ''; ?></textarea>
                                            <small class="text-muted">
                                                <i class="fa fa-info-circle"></i> These notes are for internal use only and will not be visible to the candidate
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Consent -->
                        <div class="panel panel-danger">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <i class="fa fa-shield"></i> Data Processing Consent
                                </h4>
                            </div>
                            <div class="panel-body">
                                <div class="checkbox">
                                    <label style="font-size: 14px; font-weight: normal;">
                                        <input type="checkbox" name="consent_given" id="consent_given" value="1" required
                                               <?php echo (isset($student) && $student->consent_given == 1) ? 'checked' : ''; ?>>
                                        <strong>I consent</strong> to SAFE Legal Solutions collecting and processing my personal data for providing legal protection services. I understand that I will need to complete a detailed registration form for full service activation.
                                    </label>
                                </div>
                                <small class="text-muted" style="display: block; margin-top: 10px;">
                                    <i class="fa fa-info-circle"></i> Your data will be processed in accordance with applicable data protection laws. You can withdraw consent at any time.
                                </small>
                            </div>
                        </div>

                        <!-- Profile Status (Edit Mode Only) -->
                        <?php if (isset($student)): ?>
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <i class="fa fa-info-circle"></i> Profile Status
                                </h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Profile Completion</label>
                                            <div class="progress" style="height: 30px; margin-bottom: 10px;">
                                                <div class="progress-bar progress-bar-success progress-bar-striped" 
                                                     role="progressbar" 
                                                     style="width: <?php echo $student->profile_completion; ?>%; line-height: 30px; font-size: 14px; font-weight: bold;">
                                                    <?php echo $student->profile_completion; ?>%
                                                </div>
                                            </div>
                                            <?php if ($student->profile_completion < 100): ?>
                                                <small class="text-warning">
                                                    <i class="fa fa-exclamation-triangle"></i> Profile must be 100% complete before submitting for review
                                                </small>
                                            <?php else: ?>
                                                <small class="text-success">
                                                    <i class="fa fa-check-circle"></i> Profile is complete and ready for review
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Current Status</label>
                                            <div style="padding: 10px; background: #f8f9fa; border-radius: 4px;">
                                                <?php
                                                $badge_class = 'default';
                                                $status_icon = 'fa-circle-o';
                                                
                                                switch($student->status) {
                                                    case 'draft':
                                                        $badge_class = 'default';
                                                        $status_icon = 'fa-pencil';
                                                        break;
                                                    case 'pending_review':
                                                        $badge_class = 'warning';
                                                        $status_icon = 'fa-clock-o';
                                                        break;
                                                    case 'approved':
                                                    case 'locked':
                                                        $badge_class = 'success';
                                                        $status_icon = 'fa-check-circle';
                                                        break;
                                                    case 'change_requested':
                                                        $badge_class = 'info';
                                                        $status_icon = 'fa-exchange';
                                                        break;
                                                }
                                                ?>
                                                <span class="label label-<?php echo $badge_class; ?>" style="font-size: 14px; padding: 8px 15px;">
                                                    <i class="fa <?php echo $status_icon; ?>"></i> 
                                                    <?php echo ucwords(str_replace('_', ' ', $student->status)); ?>
                                                </span>
                                                
                                                <?php if ($student->is_locked == 1): ?>
                                                    <span class="label label-danger" style="font-size: 14px; padding: 8px 15px; margin-left: 5px;">
                                                        <i class="fa fa-lock"></i> Locked
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if (isset($student->referral_code) && $student->referral_code): ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Referral Code</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($student->referral_code); ?>" readonly style="font-family: monospace; font-weight: bold; font-size: 16px;">
                                                <span class="input-group-addon" style="cursor: pointer;" onclick="copyReferralCode()">
                                                    <i class="fa fa-copy"></i> Copy
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if ($student->is_locked == 1): ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-info">
                                            <i class="fa fa-lock"></i> 
                                            <strong>Locked Information:</strong><br>
                                            Locked at: <strong><?php echo date('d M Y, h:i A', strtotime($student->locked_at)); ?></strong><br>
                                            Locked by: <strong>Admin</strong>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Form Actions -->
                        <div class="form-group mtop20">
                            <?php if (!isset($student) || $student->is_locked == 0 || is_admin()): ?>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fa fa-save"></i> 
                                    <?php echo isset($student) ? 'Update Candidate' : 'Save Candidate'; ?>
                                </button>
                            <?php endif; ?>
                            
                            <a href="<?php echo admin_url('safelegalsolutions/students'); ?>" class="btn btn-default btn-lg">
                                <i class="fa fa-times"></i> Cancel
                            </a>

                            <?php if (isset($student) && !is_admin()): ?>
                                <?php if ($student->profile_completion == 100 && $student->status == 'draft' && $student->is_locked == 0): ?>
                                    <a href="<?php echo admin_url('safelegalsolutions/submit_for_review/' . $student->id); ?>" 
                                       class="btn btn-success btn-lg pull-right"
                                       onclick="return confirm('Are you sure you want to submit this profile for admin review?');">
                                        <i class="fa fa-send"></i> Submit for Review
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if (isset($student) && is_admin() && $student->status == 'pending_review'): ?>
                                <a href="<?php echo admin_url('safelegalsolutions/approve_student/' . $student->id); ?>" 
                                   class="btn btn-success btn-lg pull-right"
                                   onclick="return confirm('Are you sure you want to approve and lock this candidate profile?');">
                                    <i class="fa fa-check"></i> Approve & Lock Profile
                                </a>
                            <?php endif; ?>
                        </div>

                        <?php echo form_close(); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(function() {
    // Initialize selectpicker
    if ($.fn.selectpicker) {
        $('.selectpicker').selectpicker('refresh');
    }

    // Update price display when package is selected
    $('#item_id').on('change', function() {
        updatePriceDisplay();
    });

    // Calculate profile completion on form input
    $('#candidate-form input, #candidate-form textarea, #candidate-form select').on('input change', function() {
        calculateCompletion();
    });

    // Calculate initial completion and price if editing
    <?php if (isset($student)): ?>
    calculateCompletion();
    updatePriceDisplay();
    <?php endif; ?>
});

// Update price display based on selected package
function updatePriceDisplay() {
    var selectedOption = $('#item_id option:selected');
    
    if (selectedOption.val()) {
        var basePrice = parseFloat(selectedOption.data('base-price')) || 0;
        var gst = parseFloat(selectedOption.data('gst')) || 0;
        var total = parseFloat(selectedOption.data('total')) || 0;
        
        $('#display-base-price').text('₹' + basePrice.toFixed(2));
        $('#display-gst').text('₹' + gst.toFixed(2));
        $('#display-total').text('₹' + total.toFixed(2));
        
        // Highlight the price box
        $('#price-details').css('border-color', '#28a745');
        setTimeout(function() {
            $('#price-details').css('border-color', '#e0e0e0');
        }, 1000);
    } else {
        $('#display-base-price').text('₹0.00');
        $('#display-gst').text('₹0.00');
        $('#display-total').text('₹0.00');
    }
}

// Calculate profile completion percentage
function calculateCompletion() {
    var requiredFields = [
        'student_name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'pin_code',
        'date_of_birth',
        'passport_number',
        'passport_expiry_date',
        'emergency_contact_mobile',
        'destination_country_id',
        'university_name',
        'course_program',
        'item_id'
    ];
    
    var filled = 0;
    
    requiredFields.forEach(function(field) {
        var value = $('#' + field).val();
        if (value && value.trim() !== '') {
            filled++;
        }
    });
    
    var percentage = Math.round((filled / requiredFields.length) * 100);
    
    // Update progress bar if it exists
    if ($('.progress-bar').length > 0) {
        $('.progress-bar')
            .css('width', percentage + '%')
            .text(percentage + '%');
        
        // Change color based on completion
        $('.progress-bar').removeClass('progress-bar-danger progress-bar-warning progress-bar-success');
        if (percentage < 50) {
            $('.progress-bar').addClass('progress-bar-danger');
        } else if (percentage < 100) {
            $('.progress-bar').addClass('progress-bar-warning');
        } else {
            $('.progress-bar').addClass('progress-bar-success');
        }
    }
}

// Copy referral code to clipboard
function copyReferralCode() {
    var copyText = event.target.closest('.input-group').querySelector('input');
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    
    try {
        document.execCommand('copy');
        alert('Referral code copied to clipboard!');
    } catch (err) {
        alert('Failed to copy referral code');
    }
}

// Copy unique ID to clipboard
function copyUniqueId() {
    var uniqueId = '<?php echo isset($student) ? $student->unique_id : ""; ?>';
    
    if (!uniqueId) {
        alert('No unique ID available');
        return;
    }
    
    // Create temporary input
    var tempInput = document.createElement('input');
    tempInput.value = uniqueId;
    document.body.appendChild(tempInput);
    tempInput.select();
    
    try {
        document.execCommand('copy');
        alert('Unique ID copied: ' + uniqueId);
    } catch (err) {
        alert('Failed to copy unique ID');
    }
    
    document.body.removeChild(tempInput);
}

// Auto-uppercase passport number
$('#passport_number').on('input', function() {
    this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
});

// Form validation
$('#candidate-form').on('submit', function(e) {
    // Validate package selection
    if (!$('#item_id').val()) {
        alert('Please select a package');
        $('#item_id').focus();
        $('.selectpicker').selectpicker('toggle');
        return false;
    }
    
    // Validate email format
    var email = $('#email').val();
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address');
        $('#email').focus();
        return false;
    }
    
    // Validate phone format (basic)
    var phone = $('#phone').val();
    if (phone.replace(/[^0-9]/g, '').length < 10) {
        alert('Please enter a valid 10-digit phone number');
        $('#phone').focus();
        return false;
    }
    
    // Validate passport number
    var passport = $('#passport_number').val();
    if (!passport || passport.trim() === '') {
        alert('Please enter passport number');
        $('#passport_number').focus();
        return false;
    }
    if (passport.length < 6) {
        alert('Please enter a valid passport number (minimum 6 characters)');
        $('#passport_number').focus();
        return false;
    }
    
    // Validate age (must be 18+)
    var dob = new Date($('#date_of_birth').val());
    var today = new Date();
    var age = today.getFullYear() - dob.getFullYear();
    var monthDiff = today.getMonth() - dob.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
        age--;
    }
    
    if (age < 18) {
        alert('Candidate must be at least 18 years old');
        $('#date_of_birth').focus();
        return false;
    }
    
    // Validate passport expiry date
    var expiryDate = new Date($('#passport_expiry_date').val());
    if (expiryDate <= today) {
        alert('Passport expiry date must be in the future');
        $('#passport_expiry_date').focus();
        return false;
    }
    
    // Validate destination country
    if (!$('#destination_country_id').val()) {
        alert('Please select destination country');
        $('#destination_country_id').focus();
        $('.selectpicker').selectpicker('toggle');
        return false;
    }
    
    // Validate university name
    if (!$('#university_name').val().trim()) {
        alert('Please enter university/institution name');
        $('#university_name').focus();
        return false;
    }
    
    // Validate course/program
    if (!$('#course_program').val().trim()) {
        alert('Please enter course/program');
        $('#course_program').focus();
        return false;
    }
    
    // Validate city
    if (!$('#city').val().trim()) {
        alert('Please enter city');
        $('#city').focus();
        return false;
    }
    
    // Validate state
    if (!$('#state').val().trim()) {
        alert('Please enter state');
        $('#state').focus();
        return false;
    }
    
    // Validate PIN code
    var pin = $('#pin_code').val();
    if (!pin || pin.trim() === '' || pin.length < 5) {
        alert('Please enter a valid PIN code (minimum 5 digits)');
        $('#pin_code').focus();
        return false;
    }
    
    // Validate emergency contact
    var emergency = $('#emergency_contact_mobile').val();
    var emergencyDigits = emergency.replace(/[^0-9]/g, '');
    if (emergencyDigits.length < 10) {
        alert('Please enter a valid emergency contact mobile number');
        $('#emergency_contact_mobile').focus();
        return false;
    }
    
    // Validate consent checkbox
    if (!$('#consent_given').is(':checked')) {
        alert('You must consent to data processing to proceed');
        $('#consent_given').focus();
        return false;
    }
    
    return true;
});
</script>