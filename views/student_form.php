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
                        
                        <!-- TAB NAVIGATION -->
                        <div class="nav-tabs-horizontal" style="overflow-x: auto; white-space: nowrap;">
                            <ul class="nav nav-tabs" role="tablist" style="display: inline-flex; min-width: 100%;">
                                <li class="active">
                                    <a href="#tab_basic" data-toggle="tab" aria-expanded="true">
                                        <i class="fa fa-user"></i> Basic Details
                                    </a>
                                </li>
                                <li class="<?php echo !isset($student) ? 'disabled' : ''; ?>">
                                    <a href="#tab_contact" data-toggle="tab" aria-expanded="false" <?php echo !isset($student) ? 'onclick="return false;" style="cursor: not-allowed; opacity: 0.5;"' : ''; ?>>
                                        <i class="fa fa-phone"></i> Contact Info
                                    </a>
                                </li>
                                <li class="<?php echo !isset($student) ? 'disabled' : ''; ?>">
                                    <a href="#tab_destination" data-toggle="tab" aria-expanded="false" <?php echo !isset($student) ? 'onclick="return false;" style="cursor: not-allowed; opacity: 0.5;"' : ''; ?>>
                                        <i class="fa fa-globe"></i> Destination
                                    </a>
                                </li>
                                <li class="<?php echo !isset($student) ? 'disabled' : ''; ?>">
                                    <a href="#tab_payment" data-toggle="tab" aria-expanded="false" <?php echo !isset($student) ? 'onclick="return false;" style="cursor: not-allowed; opacity: 0.5;"' : ''; ?>>
                                        <i class="fa fa-money"></i> Payment
                                    </a>
                                </li>
                                <?php if (isset($student)): ?>
                                <li>
                                    <a href="#tab_extended" data-toggle="tab" aria-expanded="false">
                                        <i class="fa fa-id-card"></i> Extended Info
                                    </a>
                                </li>
                                <li>
                                    <a href="#tab_family" data-toggle="tab" aria-expanded="false">
                                        <i class="fa fa-users"></i> Family
                                    </a>
                                </li>
                                <li>
                                    <a href="#tab_education" data-toggle="tab" aria-expanded="false">
                                        <i class="fa fa-graduation-cap"></i> Education
                                    </a>
                                </li>
                                <li>
                                    <a href="#tab_accommodation" data-toggle="tab" aria-expanded="false">
                                        <i class="fa fa-home"></i> Accommodation
                                    </a>
                                </li>
                                <li>
                                    <a href="#tab_financial" data-toggle="tab" aria-expanded="false">
                                        <i class="fa fa-bank"></i> Financial
                                    </a>
                                </li>
                                <li>
                                    <a href="#tab_medical" data-toggle="tab" aria-expanded="false">
                                        <i class="fa fa-medkit"></i> Medical
                                    </a>
                                </li>
                                <li>
                                    <a href="#tab_referral" data-toggle="tab" aria-expanded="false">
                                        <i class="fa fa-handshake-o"></i> Referral
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <!-- TAB CONTENT -->
                        <div class="tab-content mtop20">
                            
                            <!-- TAB 1: BASIC DETAILS -->
                            <div class="tab-pane active" id="tab_basic">
                                
                                <!-- Branch Selection -->
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
                                            $('#candidate-form').on('submit', function(e) {
                                                e.preventDefault();
                                                alert('You cannot create candidates without an assigned branch. Please contact admin.');
                                                return false;
                                            });
                                        </script>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <!-- Personal Information -->
                                <h5 class="bold"><i class="fa fa-user"></i> Personal Information</h5>
                                <hr/>
                                
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

                                <!-- Data Consent -->
                           <h5 class="bold mtop30"><i class="fa fa-shield"></i> Data Processing Consent</h5>
                                <hr/>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div style="display: flex; align-items: flex-start; gap: 10px;">
                                                <input type="checkbox" 
                                                       name="consent_given" 
                                                       id="consent_given" 
                                                       value="1" 
                                                       required
                                                       style="margin-top: 5px; width: 18px; height: 18px; cursor: pointer;"
                                                       <?php echo (isset($student) && $student->consent_given == 1) ? 'checked' : ''; ?>>
                                                <label for="consent_given" style="font-weight: normal; cursor: pointer; margin: 0;">
                                                    <strong>I consent</strong> to SAFE Legal Solutions collecting and processing my personal data for providing legal protection services. I understand that I will need to complete a detailed registration form for full service activation.
                                                </label>
                                            </div>
                                        </div>
                                        <small class="text-muted" style="display: block; margin-top: 10px;">
                                            <i class="fa fa-info-circle"></i> Your data will be processed in accordance with applicable data protection laws. You can withdraw consent at any time.
                                        </small>
                                    </div>
                                </div>
                                </div>

                            <!-- TAB 2: CONTACT INFORMATION -->
                            <div class="tab-pane" id="tab_contact">
                                <h5 class="bold"><i class="fa fa-phone"></i> Contact Information</h5>
                                <hr/>
                                
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

                            <!-- TAB 3: DESTINATION & PROGRAM -->
                            <div class="tab-pane" id="tab_destination">
                                <h5 class="bold"><i class="fa fa-globe"></i> Destination & Program Details</h5>
                                <hr/>
                                
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

                                <!-- Internal Notes -->
                                <h5 class="bold mtop30"><i class="fa fa-sticky-note"></i> Internal Notes</h5>
                                <hr/>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="notes" class="control-label">Notes (Optional)</label>
                                            <textarea name="notes" id="notes" class="form-control" rows="4"
                                                      placeholder="Add any internal notes or comments about this candidate (not visible to candidate)"><?php echo isset($student) ? htmlspecialchars($student->notes) : ''; ?></textarea>
                                            <small class="text-muted">
                                                <i class="fa fa-info-circle"></i> These notes are for internal use only and will not be visible to the candidate
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 4: PAYMENT -->
                            <div class="tab-pane" id="tab_payment">
                                <h5 class="bold"><i class="fa fa-shopping-cart"></i> Package & Payment Details</h5>
                                <hr/>
                                
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
                                <h5 class="bold"><i class="fa fa-money"></i> Payment Information</h5>
                                <hr/>

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

                            <?php if (isset($student)): ?>
                            <!-- Additional tabs for edit mode only -->
                            
                            <!-- TAB 5: EXTENDED PERSONAL -->
                            <div class="tab-pane" id="tab_extended">
                                <h5 class="bold"><i class="fa fa-id-card"></i> Extended Personal Information</h5>
                                <hr/>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="gender" class="control-label">Gender</label>
                                            <select name="gender" id="gender" class="form-control selectpicker">
                                                <option value="">-- Select Gender --</option>
                                                <option value="Male" <?php echo ($student->gender == 'Male') ? 'selected' : ''; ?>>Male</option>
                                                <option value="Female" <?php echo ($student->gender == 'Female') ? 'selected' : ''; ?>>Female</option>
                                                <option value="Other" <?php echo ($student->gender == 'Other') ? 'selected' : ''; ?>>Other</option>
                                                <option value="Prefer not to say" <?php echo ($student->gender == 'Prefer not to say') ? 'selected' : ''; ?>>Prefer not to say</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nationality" class="control-label">Nationality</label>
                                            <input type="text" name="nationality" id="nationality" class="form-control"
                                                   placeholder="E.g., Indian"
                                                   value="<?php echo htmlspecialchars($student->nationality); ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="religion" class="control-label">Religion</label>
                                            <input type="text" name="religion" id="religion" class="form-control"
                                                   placeholder="E.g., Hindu, Muslim, Christian"
                                                   value="<?php echo htmlspecialchars($student->religion); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="aadhar_number" class="control-label">Aadhar Card Number</label>
                                            <input type="text" name="aadhar_number" id="aadhar_number" class="form-control"
                                                   placeholder="1234 5678 9012" maxlength="12"
                                                   value="<?php echo htmlspecialchars($student->aadhar_number); ?>">
                                            <small class="text-muted">12-digit Aadhar number</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="pan_number" class="control-label">PAN Card Number</label>
                                            <input type="text" name="pan_number" id="pan_number" class="form-control"
                                                   placeholder="ABCDE1234F" maxlength="10" style="text-transform: uppercase;"
                                                   value="<?php echo htmlspecialchars($student->pan_number); ?>">
                                            <small class="text-muted">10-character PAN</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="place_of_birth" class="control-label">Place of Birth</label>
                                            <input type="text" name="place_of_birth" id="place_of_birth" class="form-control"
                                                   placeholder="City, State, Country"
                                                   value="<?php echo htmlspecialchars($student->place_of_birth); ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="languages_spoken" class="control-label">Languages Spoken</label>
                                            <input type="text" name="languages_spoken" id="languages_spoken" class="form-control"
                                                   placeholder="E.g., English, Hindi, Tamil"
                                                   value="<?php echo htmlspecialchars($student->languages_spoken); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="whatsapp_number" class="control-label">WhatsApp Number</label>
                                            <input type="text" name="whatsapp_number" id="whatsapp_number" class="form-control"
                                                   placeholder="+91 9876543210"
                                                   value="<?php echo htmlspecialchars($student->whatsapp_number); ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="skype_teams_id" class="control-label">Skype/Teams ID</label>
                                            <input type="text" name="skype_teams_id" id="skype_teams_id" class="form-control"
                                                   placeholder="your.skype.id"
                                                   value="<?php echo htmlspecialchars($student->skype_teams_id); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="address_line2" class="control-label">Address Line 2</label>
                                            <input type="text" name="address_line2" id="address_line2" class="form-control"
                                                   placeholder="Apartment, Suite, Floor, etc."
                                                   value="<?php echo htmlspecialchars($student->address_line2); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 6: FAMILY & GUARDIAN -->
                            <div class="tab-pane" id="tab_family">
                                <!-- Primary Contact -->
                                <h5 class="bold"><i class="fa fa-user"></i> Primary Contact (Parent/Guardian)</h5>
                                <hr/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="primary_contact_name">Full Name</label>
                                            <input type="text" name="primary_contact_name" id="primary_contact_name" class="form-control"
                                                   placeholder="Parent/Guardian name"
                                                   value="<?php echo htmlspecialchars($student->primary_contact_name); ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="primary_contact_relationship">Relationship</label>
                                            <input type="text" name="primary_contact_relationship" id="primary_contact_relationship" class="form-control"
                                                   placeholder="E.g., Father, Mother"
                                                   value="<?php echo htmlspecialchars($student->primary_contact_relationship); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="primary_contact_mobile">Mobile Number</label>
                                            <input type="text" name="primary_contact_mobile" id="primary_contact_mobile" class="form-control"
                                                   placeholder="+91 9876543210"
                                                   value="<?php echo htmlspecialchars($student->primary_contact_mobile); ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="primary_contact_email">Email</label>
                                            <input type="email" name="primary_contact_email" id="primary_contact_email" class="form-control"
                                                   placeholder="parent@example.com"
                                                   value="<?php echo htmlspecialchars($student->primary_contact_email); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="primary_contact_occupation">Occupation</label>
                                            <input type="text" name="primary_contact_occupation" id="primary_contact_occupation" class="form-control"
                                                   placeholder="E.g., Business, Government Service"
                                                   value="<?php echo htmlspecialchars($student->primary_contact_occupation); ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="primary_contact_annual_income">Annual Income</label>
                                            <input type="number" name="primary_contact_annual_income" id="primary_contact_annual_income" class="form-control"
                                                   placeholder="0.00" step="0.01"
                                                   value="<?php echo $student->primary_contact_annual_income; ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- Secondary Contact -->
                                <h5 class="bold mtop30"><i class="fa fa-user"></i> Secondary Contact (Guardian)</h5>
                                <hr/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="secondary_contact_name">Full Name</label>
                                            <input type="text" name="secondary_contact_name" id="secondary_contact_name" class="form-control"
                                                   placeholder="Secondary guardian name"
                                                   value="<?php echo htmlspecialchars($student->secondary_contact_name); ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="secondary_contact_relationship">Relationship</label>
                                            <input type="text" name="secondary_contact_relationship" id="secondary_contact_relationship" class="form-control"
                                                   placeholder="E.g., Uncle, Aunt"
                                                   value="<?php echo htmlspecialchars($student->secondary_contact_relationship); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="secondary_contact_mobile">Mobile Number</label>
                                            <input type="text" name="secondary_contact_mobile" id="secondary_contact_mobile" class="form-control"
                                                   placeholder="+91 9876543210"
                                                   value="<?php echo htmlspecialchars($student->secondary_contact_mobile); ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="secondary_contact_email">Email</label>
                                            <input type="email" name="secondary_contact_email" id="secondary_contact_email" class="form-control"
                                                   placeholder="guardian@example.com"
                                                   value="<?php echo htmlspecialchars($student->secondary_contact_email); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="secondary_contact_occupation">Occupation</label>
                                            <input type="text" name="secondary_contact_occupation" id="secondary_contact_occupation" class="form-control"
                                                   placeholder="E.g., Business, Government Service"
                                                   value="<?php echo htmlspecialchars($student->secondary_contact_occupation); ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- Emergency Contact -->
                                <h5 class="bold mtop30"><i class="fa fa-phone"></i> Emergency Contact Details</h5>
                                <hr/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="emergency_contact_name">Emergency Contact Name</label>
                                            <input type="text" name="emergency_contact_name" id="emergency_contact_name" class="form-control"
                                                   placeholder="Emergency contact person"
                                                   value="<?php echo htmlspecialchars($student->emergency_contact_name); ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="emergency_contact_relationship">Relationship</label>
                                            <input type="text" name="emergency_contact_relationship" id="emergency_contact_relationship" class="form-control"
                                                   placeholder="E.g., Brother, Sister"
                                                   value="<?php echo htmlspecialchars($student->emergency_contact_relationship); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="emergency_contact_address">Emergency Contact Address</label>
                                            <textarea name="emergency_contact_address" id="emergency_contact_address" rows="2" class="form-control"
                                                      placeholder="Full address of emergency contact"><?php echo htmlspecialchars($student->emergency_contact_address); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 7: EDUCATION & VISA -->
                            <div class="tab-pane" id="tab_education">
                                <!-- Education Details -->
                                <h5 class="bold"><i class="fa fa-book"></i> Previous Education</h5>
                                <hr/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="highest_qualification">Highest Qualification</label>
                                            <input type="text" name="highest_qualification" id="highest_qualification" class="form-control"
                                                   placeholder="E.g., Bachelor's Degree, Master's"
                                                   value="<?php echo htmlspecialchars($student->highest_qualification); ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="institution_name">Institution Name</label>
                                            <input type="text" name="institution_name" id="institution_name" class="form-control"
                                                   placeholder="Previous university/college"
                                                   value="<?php echo htmlspecialchars($student->institution_name); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="year_of_completion">Year of Completion</label>
                                            <input type="number" name="year_of_completion" id="year_of_completion" class="form-control"
                                                   placeholder="E.g., 2023" min="1950" max="<?php echo date('Y'); ?>"
                                                   value="<?php echo $student->year_of_completion; ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="percentage_cgpa">Marks/CGPA</label>
                                            <input type="text" name="percentage_cgpa" id="percentage_cgpa" class="form-control"
                                                   placeholder="E.g., 85% or 8.5 CGPA"
                                                   value="<?php echo htmlspecialchars($student->percentage_cgpa); ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- Study Abroad Details -->
                                <h5 class="bold mtop30"><i class="fa fa-plane"></i> Study Abroad Program</h5>
                                <hr/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="city_destination">Destination City</label>
                                            <input type="text" name="city_destination" id="city_destination" class="form-control"
                                                   placeholder="City where you'll study"
                                                   value="<?php echo htmlspecialchars($student->city_destination); ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="state_destination">Destination State/Province</label>
                                            <input type="text" name="state_destination" id="state_destination" class="form-control"
                                                   placeholder="State/Province"
                                                   value="<?php echo htmlspecialchars($student->state_destination); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="course_duration">Course Duration</label>
                                            <input type="text" name="course_duration" id="course_duration" class="form-control"
                                                   placeholder="E.g., 2 years"
                                                   value="<?php echo htmlspecialchars($student->course_duration); ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="start_date">Course Start Date</label>
                                            <input type="date" name="start_date" id="start_date" class="form-control"
                                                   value="<?php echo $student->start_date; ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="expected_completion_date">Expected Completion</label>
                                            <input type="date" name="expected_completion_date" id="expected_completion_date" class="form-control"
                                                   value="<?php echo $student->expected_completion_date; ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="student_id_number">University Student ID</label>
                                            <input type="text" name="student_id_number" id="student_id_number" class="form-control"
                                                   placeholder="Student ID from university"
                                                   value="<?php echo htmlspecialchars($student->student_id_number); ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- Visa Details -->
                                <h5 class="bold mtop30"><i class="fa fa-passport"></i> Visa Information</h5>
                                <hr/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="visa_type">Visa Type</label>
                                            <input type="text" name="visa_type" id="visa_type" class="form-control"
                                                   placeholder="E.g., F-1, Tier 4"
                                                   value="<?php echo htmlspecialchars($student->visa_type); ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="visa_number">Visa Number</label>
                                            <input type="text" name="visa_number" id="visa_number" class="form-control"
                                                   placeholder="Visa number"
                                                   value="<?php echo htmlspecialchars($student->visa_number); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="visa_issue_date">Visa Issue Date</label>
                                            <input type="date" name="visa_issue_date" id="visa_issue_date" class="form-control"
                                                   value="<?php echo $student->visa_issue_date; ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="visa_expiry_date">Visa Expiry Date</label>
                                            <input type="date" name="visa_expiry_date" id="visa_expiry_date" class="form-control"
                                                   value="<?php echo $student->visa_expiry_date; ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="visa_status">Visa Status</label>
                                            <select name="visa_status" id="visa_status" class="form-control selectpicker">
                                                <option value="">-- Select Status --</option>
                                                <option value="Applied" <?php echo ($student->visa_status == 'Applied') ? 'selected' : ''; ?>>Applied</option>
                                                <option value="Pending" <?php echo ($student->visa_status == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                                <option value="Approved" <?php echo ($student->visa_status == 'Approved') ? 'selected' : ''; ?>>Approved</option>
                                                <option value="Rejected" <?php echo ($student->visa_status == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 8: ACCOMMODATION -->
            <div class="tab-pane" id="tab_accommodation">
                <h5 class="bold"><i class="fa fa-home"></i> Accommodation Details</h5>
                <hr/>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="accommodation_type">Accommodation Type</label>
                            <select name="accommodation_type" id="accommodation_type" class="form-control selectpicker">
                                <option value="">-- Select Type --</option>
                                <option value="University Housing" <?php echo ($student->accommodation_type == 'University Housing') ? 'selected' : ''; ?>>University Housing</option>
                                <option value="Private Rental" <?php echo ($student->accommodation_type == 'Private Rental') ? 'selected' : ''; ?>>Private Rental</option>
                                <option value="Homestay" <?php echo ($student->accommodation_type == 'Homestay') ? 'selected' : ''; ?>>Homestay</option>
                                <option value="Other" <?php echo ($student->accommodation_type == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="accommodation_address">Address Abroad</label>
                            <textarea name="accommodation_address" id="accommodation_address" rows="2" class="form-control"
                                      placeholder="Full accommodation address"><?php echo htmlspecialchars($student->accommodation_address); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="accommodation_city">City</label>
                            <input type="text" name="accommodation_city" id="accommodation_city" class="form-control"
                                   placeholder="City"
                                   value="<?php echo htmlspecialchars($student->accommodation_city); ?>">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="accommodation_state">State/Province</label>
                            <input type="text" name="accommodation_state" id="accommodation_state" class="form-control"
                                   placeholder="State/Province"
                                   value="<?php echo htmlspecialchars($student->accommodation_state); ?>">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="accommodation_zip">ZIP/Postal Code</label>
                            <input type="text" name="accommodation_zip" id="accommodation_zip" class="form-control"
                                   placeholder="ZIP/Postal code"
                                   value="<?php echo htmlspecialchars($student->accommodation_zip); ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="accommodation_country">Country</label>
                            <input type="text" name="accommodation_country" id="accommodation_country" class="form-control"
                                   placeholder="Country"
                                   value="<?php echo htmlspecialchars($student->accommodation_country); ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="local_phone_number">Local Phone Number</label>
                            <input type="text" name="local_phone_number" id="local_phone_number" class="form-control"
                                   placeholder="Local contact number"
                                   value="<?php echo htmlspecialchars($student->local_phone_number); ?>">
                        </div>
                    </div>
                </div>

                <!-- Landlord/Contact Person -->
                <h5 class="bold mtop30"><i class="fa fa-user-circle"></i> Landlord/Contact Person</h5>
                <hr/>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="landlord_name">Landlord Name</label>
                            <input type="text" name="landlord_name" id="landlord_name" class="form-control"
                                   placeholder="Landlord or contact person"
                                   value="<?php echo htmlspecialchars($student->landlord_name); ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="landlord_contact">Landlord Contact</label>
                            <input type="text" name="landlord_contact" id="landlord_contact" class="form-control"
                                   placeholder="Phone/Email"
                                   value="<?php echo htmlspecialchars($student->landlord_contact); ?>">
                        </div>
                    </div>
                </div>

                <!-- Local Emergency Contacts -->
                <h5 class="bold mtop30"><i class="fa fa-phone-square"></i> Local Emergency Contacts</h5>
                <hr/>
                
                <!-- Local Contact 1 -->
                <h6 class="bold">Contact 1</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="local_contact1_name">Name</label>
                            <input type="text" name="local_contact1_name" id="local_contact1_name" class="form-control"
                                   placeholder="Emergency contact name"
                                   value="<?php echo htmlspecialchars($student->local_contact1_name); ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="local_contact1_relationship">Relationship</label>
                            <input type="text" name="local_contact1_relationship" id="local_contact1_relationship" class="form-control"
                                   placeholder="Relationship"
                                   value="<?php echo htmlspecialchars($student->local_contact1_relationship); ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="local_contact1_phone">Phone</label>
                            <input type="text" name="local_contact1_phone" id="local_contact1_phone" class="form-control"
                                   placeholder="Phone number"
                                   value="<?php echo htmlspecialchars($student->local_contact1_phone); ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="local_contact1_email">Email</label>
                            <input type="email" name="local_contact1_email" id="local_contact1_email" class="form-control"
                                   placeholder="Email address"
                                   value="<?php echo htmlspecialchars($student->local_contact1_email); ?>">
                        </div>
                    </div>
                </div>

                <!-- Local Contact 2 -->
                <h6 class="bold mtop20">Contact 2</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="local_contact2_name">Name</label>
                            <input type="text" name="local_contact2_name" id="local_contact2_name" class="form-control"
                                   placeholder="Emergency contact name"
                                   value="<?php echo htmlspecialchars($student->local_contact2_name); ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="local_contact2_relationship">Relationship</label>
                            <input type="text" name="local_contact2_relationship" id="local_contact2_relationship" class="form-control"
                                   placeholder="Relationship"
                                   value="<?php echo htmlspecialchars($student->local_contact2_relationship); ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="local_contact2_phone">Phone</label>
                            <input type="text" name="local_contact2_phone" id="local_contact2_phone" class="form-control"
                                   placeholder="Phone number"
                                   value="<?php echo htmlspecialchars($student->local_contact2_phone); ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="local_contact2_email">Email</label>
                            <input type="email" name="local_contact2_email" id="local_contact2_email" class="form-control"
                                   placeholder="Email address"
                                   value="<?php echo htmlspecialchars($student->local_contact2_email); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 9: FINANCIAL -->
            <div class="tab-pane" id="tab_financial">
                <!-- Course Fees -->
                <h5 class="bold"><i class="fa fa-university"></i> Course Fees</h5>
                <hr/>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="total_course_fee">Total Course Fee</label>
                            <input type="number" name="total_course_fee" id="total_course_fee" class="form-control"
                                   placeholder="0.00" step="0.01"
                                   value="<?php echo $student->total_course_fee; ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="funding_source">Funding Source</label>
                            <input type="text" name="funding_source" id="funding_source" class="form-control"
                                   placeholder="E.g., Self-funded, Parents, Scholarship, Loan"
                                   value="<?php echo htmlspecialchars($student->funding_source); ?>">
                            <small class="text-muted">Separate multiple sources with commas</small>
                        </div>
                    </div>
                </div>

                <!-- Education Loan Details -->
                <h5 class="bold mtop30"><i class="fa fa-bank"></i> Education Loan Details</h5>
                <hr/>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="loan_bank_name">Bank Name</label>
                            <input type="text" name="loan_bank_name" id="loan_bank_name" class="form-control"
                                   placeholder="Bank providing the loan"
                                   value="<?php echo htmlspecialchars($student->loan_bank_name); ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="loan_amount">Loan Amount</label>
                            <input type="number" name="loan_amount" id="loan_amount" class="form-control"
                                   placeholder="0.00" step="0.01"
                                   value="<?php echo $student->loan_amount; ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="loan_account_number">Loan Account Number</label>
                            <input type="text" name="loan_account_number" id="loan_account_number" class="form-control"
                                   placeholder="Account number"
                                   value="<?php echo htmlspecialchars($student->loan_account_number); ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="loan_officer_contact">Loan Officer Contact</label>
                            <input type="text" name="loan_officer_contact" id="loan_officer_contact" class="form-control"
                                   placeholder="Phone/Email of loan officer"
                                   value="<?php echo htmlspecialchars($student->loan_officer_contact); ?>">
                        </div>
                    </div>
                </div>

                <!-- Insurance Details -->
                <h5 class="bold mtop30"><i class="fa fa-shield"></i> Health Insurance</h5>
                <hr/>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="health_insurance_provider">Insurance Provider</label>
                            <input type="text" name="health_insurance_provider" id="health_insurance_provider" class="form-control"
                                   placeholder="Insurance company name"
                                   value="<?php echo htmlspecialchars($student->health_insurance_provider); ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="health_insurance_policy">Policy Number</label>
                            <input type="text" name="health_insurance_policy" id="health_insurance_policy" class="form-control"
                                   placeholder="Policy number"
                                   value="<?php echo htmlspecialchars($student->health_insurance_policy); ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="health_insurance_coverage">Coverage Amount</label>
                            <input type="number" name="health_insurance_coverage" id="health_insurance_coverage" class="form-control"
                                   placeholder="0.00" step="0.01"
                                   value="<?php echo $student->health_insurance_coverage; ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="other_insurance">Other Insurance</label>
                            <input type="text" name="other_insurance" id="other_insurance" class="form-control"
                                   placeholder="Travel, Life, etc."
                                   value="<?php echo htmlspecialchars($student->other_insurance); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 10: MEDICAL & SPECIAL -->
            <div class="tab-pane" id="tab_medical">
                <!-- Medical Information -->
                <h5 class="bold"><i class="fa fa-heartbeat"></i> Medical Information</h5>
                <hr/>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="blood_group">Blood Group</label>
                            <input type="text" name="blood_group" id="blood_group" class="form-control"
                                   placeholder="E.g., A+, B-, O+"
                                   value="<?php echo htmlspecialchars($student->blood_group); ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="emergency_medical_contact">Emergency Medical Contact</label>
                            <input type="text" name="emergency_medical_contact" id="emergency_medical_contact" class="form-control"
                                   placeholder="Doctor/Hospital contact"
                                   value="<?php echo htmlspecialchars($student->emergency_medical_contact); ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="allergies_medical_conditions">Allergies/Medical Conditions</label>
                            <textarea name="allergies_medical_conditions" id="allergies_medical_conditions" rows="3" class="form-control"
                                      placeholder="List any known allergies or medical conditions"><?php echo htmlspecialchars($student->allergies_medical_conditions); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="regular_medications">Regular Medications</label>
                            <textarea name="regular_medications" id="regular_medications" rows="3" class="form-control"
                                      placeholder="List any regular medications"><?php echo htmlspecialchars($student->regular_medications); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Legal & Special Requirements -->
                <h5 class="bold mtop30"><i class="fa fa-balance-scale"></i> Legal & Special Requirements</h5>
                <hr/>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" 
                                       name="previous_legal_issues" 
                                       id="previous_legal_issues" 
                                       value="1"
                                       style="width: 18px; height: 18px; cursor: pointer;"
                                       <?php echo ($student->previous_legal_issues == 1) ? 'checked' : ''; ?>>
                                <label for="previous_legal_issues" style="font-weight: normal; cursor: pointer; margin: 0;">
                                    Previous Legal Issues
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row" id="legal_issues_details_row" style="display: none;">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="legal_issues_details">Legal Issues Details</label>
                            <textarea name="legal_issues_details" id="legal_issues_details" rows="3" class="form-control"
                                      placeholder="Please provide details"><?php echo htmlspecialchars($student->legal_issues_details); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="special_dietary_requirements">Special Dietary Requirements</label>
                            <input type="text" name="special_dietary_requirements" id="special_dietary_requirements" class="form-control"
                                   placeholder="E.g., Vegetarian, Vegan, Halal"
                                   value="<?php echo htmlspecialchars($student->special_dietary_requirements); ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="other_special_needs">Other Special Needs</label>
                            <textarea name="other_special_needs" id="other_special_needs" rows="2" class="form-control"
                                      placeholder="Any other requirements"><?php echo htmlspecialchars($student->other_special_needs); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 11: REFERRAL -->
            <div class="tab-pane" id="tab_referral">
                <!-- Referral Information -->
                <h5 class="bold"><i class="fa fa-share-alt"></i> Referral Information</h5>
                <hr/>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="referral_source">How did you hear about us?</label>
                            <input type="text" name="referral_source" id="referral_source" class="form-control"
                                   placeholder="E.g., Friend, Website, Social Media, Advertisement"
                                   value="<?php echo htmlspecialchars($student->referral_source); ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="referrer_name">Referrer Name</label>
                            <input type="text" name="referrer_name" id="referrer_name" class="form-control"
                                   placeholder="Name of person who referred you"
                                   value="<?php echo htmlspecialchars($student->referrer_name); ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="referrer_organization">Referrer Organization</label>
                            <input type="text" name="referrer_organization" id="referrer_organization" class="form-control"
                                   placeholder="Organization/Company"
                                   value="<?php echo htmlspecialchars($student->referrer_organization); ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="referrer_contact">Referrer Contact Details</label>
                            <input type="text" name="referrer_contact" id="referrer_contact" class="form-control"
                                   placeholder="Phone/Email of referrer"
                                   value="<?php echo htmlspecialchars($student->referrer_contact); ?>">
                        </div>
                    </div>
                </div>

                <?php if (isset($student->referral_code) && $student->referral_code): ?>
                <div class="row mtop30">
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
            </div>

            <?php endif; ?>
                        </div>
                        <!-- END TAB CONTENT -->

                        <!-- Profile Status (Edit Mode Only) -->
                        <?php if (isset($student)): ?>
                        <div class="panel panel-info mtop20">
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

<!-- Keep all existing JavaScript exactly as is -->
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

    // Show/hide legal issues details based on checkbox
    $('#previous_legal_issues').on('change', function() {
        if ($(this).is(':checked')) {
            $('#legal_issues_details_row').slideDown();
        } else {
            $('#legal_issues_details_row').slideUp();
            $('#legal_issues_details').val('');
        }
    });
    
    // Trigger on page load if editing
    <?php if (isset($student)): ?>
    if ($('#previous_legal_issues').is(':checked')) {
        $('#legal_issues_details_row').show();
    }
    <?php endif; ?>
    
    // Auto-uppercase PAN number
    $('#pan_number').on('input', function() {
        this.value = this.value.toUpperCase();
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