<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="panel_s">
    <div class="panel-body">
        
        <!-- Header -->
        <div class="row">
            <div class="col-md-10">
                <h3 class="no-margin">
                    <i class="fa fa-user"></i> My Complete Profile
                    <?php if ($student->is_locked == 1): ?>
                        <span class="label label-success" style="margin-left: 10px;">
                            <i class="fa fa-lock"></i> VERIFIED & LOCKED
                        </span>
                    <?php else: ?>
                        <span class="label label-info" style="margin-left: 10px;">
                            <i class="fa fa-edit"></i> EDITABLE
                        </span>
                    <?php endif; ?>
                </h3>
            </div>
            <div class="col-md-2 text-right">
                <a href="<?php echo site_url('safelegalsolutions/safelegalsolutions_client/my_dashboard'); ?>" 
                   class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
        <hr>

        <!-- Lock Status Notice -->
        <?php if ($student->is_locked == 1): ?>
        <div class="alert alert-warning" style="border-left: 5px solid #f0ad4e;">
            <h4><i class="fa fa-lock"></i> Profile Locked</h4>
            <p>Your profile has been verified and locked by the administration. You cannot make any changes at this time. 
               If you need to update any information, please contact support.</p>
        </div>
        <?php endif; ?>

        <!-- Profile Status Banner -->
        <div class="row">
            <div class="col-md-12">
                <div class="alert <?php 
                    echo $student->status == 'approved' ? 'alert-success' : 
                         ($student->status == 'pending_review' ? 'alert-warning' : 'alert-info'); 
                ?>" style="border-left: 5px solid;">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 style="margin-top: 0;">
                                <i class="fa <?php 
                                    echo $student->status == 'approved' ? 'fa-check-circle' : 
                                         ($student->status == 'pending_review' ? 'fa-clock-o' : 'fa-info-circle'); 
                                ?>"></i> 
                                Registration Status: 
                                <strong><?php 
                                    $status_display = [
                                        'draft' => 'Draft',
                                        'pending_review' => 'Under Review',
                                        'approved' => 'Approved',
                                        'active' => 'Active',
                                        'locked' => 'Approved & Verified'
                                    ];
                                    echo $status_display[$student->status] ?? ucfirst($student->status);
                                ?></strong>
                            </h4>
                            <p style="margin-bottom: 0;">
                                Profile Completion: <strong><?php echo $student->profile_completion ?? 0; ?>%</strong>
                            </p>
                        </div>
                        <div class="col-md-4 text-right">
                            <div style="margin-top: 15px;">
                                <div class="progress" style="height: 30px; margin-bottom: 10px;">
                                    <div class="progress-bar progress-bar-<?php 
                                        $completion = $student->profile_completion ?? 0;
                                        echo $completion >= 80 ? 'success' : ($completion >= 50 ? 'warning' : 'danger'); 
                                    ?>" 
                                         role="progressbar" 
                                         style="width: <?php echo $completion; ?>%; font-size: 16px; line-height: 30px;">
                                        <?php echo $completion; ?>%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Start -->
        <?php echo form_open(site_url('safelegalsolutions/safelegalsolutions_client/update_profile'), ['id' => 'student_profile_form']); ?>
        
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" role="tablist" id="profileTabs">
            <li class="active">
                <a href="#tab_personal" data-toggle="tab" aria-expanded="true">
                    <i class="fa fa-user"></i> Personal Info
                </a>
            </li>
            <li>
                <a href="#tab_passport" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-passport"></i> Passport & ID
                </a>
            </li>
            <li>
                <a href="#tab_contacts" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-phone"></i> Emergency Contacts
                </a>
            </li>
            <li>
                <a href="#tab_education" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-graduation-cap"></i> Education
                </a>
            </li>
            <li>
                <a href="#tab_destination" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-plane"></i> Destination Details
                </a>
            </li>
            <li>
                <a href="#tab_visa" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-id-card"></i> Visa Information
                </a>
            </li>
            <li>
                <a href="#tab_accommodation" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-home"></i> Accommodation
                </a>
            </li>
            <li>
                <a href="#tab_financial" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-money"></i> Financial (View Only)
                </a>
            </li>
            <li>
                <a href="#tab_health" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-heartbeat"></i> Health & Insurance
                </a>
            </li>
            <li>
                <a href="#tab_additional" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-info-circle"></i> Additional Info
                </a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" style="padding: 20px; border: 1px solid #ddd; border-top: none;">
            
            <!-- TAB 1: PERSONAL INFORMATION -->
            <div class="tab-pane active" id="tab_personal">
                <h4 class="bold"><i class="fa fa-user"></i> Personal Information</h4>
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="student_name" class="form-control" 
                                   value="<?php echo set_value('student_name', $student->student_name); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : 'required'; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span> 
                                <small class="text-muted">(Cannot be changed)</small>
                            </label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?php echo $student->email; ?>" readonly style="background: #f5f5f5;">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" 
                                   value="<?php echo set_value('phone', $student->phone); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : 'required'; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>WhatsApp Number</label>
                            <input type="text" name="whatsapp_number" class="form-control" 
                                   value="<?php echo set_value('whatsapp_number', $student->whatsapp_number); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="date_of_birth" class="form-control" 
                                   value="<?php echo set_value('date_of_birth', $student->date_of_birth); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : 'required'; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Gender</label>
                            <select name="gender" class="form-control selectpicker" 
                                    <?php echo ($student->is_locked == 1) ? 'disabled' : ''; ?>>
                                <option value="">-- Select Gender --</option>
                                <option value="Male" <?php echo ($student->gender == 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo ($student->gender == 'Female') ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo ($student->gender == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Address Line 1 <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control" rows="2" 
                                      <?php echo ($student->is_locked == 1) ? 'readonly' : 'required'; ?>><?php echo set_value('address', $student->address); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Address Line 2</label>
                            <textarea name="address_line2" class="form-control" rows="2" 
                                      <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>><?php echo set_value('address_line2', $student->address_line2); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" class="form-control" 
                                   value="<?php echo set_value('city', $student->city); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>State/Province</label>
                            <input type="text" name="state" class="form-control" 
                                   value="<?php echo set_value('state', $student->state); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>PIN/ZIP Code</label>
                            <input type="text" name="pin_code" class="form-control" 
                                   value="<?php echo set_value('pin_code', $student->pin_code); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nationality</label>
                            <input type="text" name="nationality" class="form-control" 
                                   value="<?php echo set_value('nationality', $student->nationality); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Place of Birth</label>
                            <input type="text" name="place_of_birth" class="form-control" 
                                   value="<?php echo set_value('place_of_birth', $student->place_of_birth); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Religion</label>
                            <input type="text" name="religion" class="form-control" 
                                   value="<?php echo set_value('religion', $student->religion); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Languages Known</label>
                            <input type="text" name="languages_spoken" class="form-control" 
                                   placeholder="e.g., English, Hindi, Tamil"
                                   value="<?php echo set_value('languages_spoken', $student->languages_spoken); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Skype/Teams ID</label>
                            <input type="text" name="skype_teams_id" class="form-control" 
                                   value="<?php echo set_value('skype_teams_id', $student->skype_teams_id); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: PASSPORT & ID DETAILS -->
            <div class="tab-pane" id="tab_passport">
                <h4 class="bold"><i class="fa fa-passport"></i> Passport & Identity Documents</h4>
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Passport Number <span class="text-danger">*</span></label>
                            <input type="text" name="passport_number" class="form-control" 
                                   value="<?php echo set_value('passport_number', $student->passport_number); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : 'required'; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Passport Expiry Date</label>
                            <input type="date" name="passport_expiry_date" class="form-control" 
                                   value="<?php echo set_value('passport_expiry_date', $student->passport_expiry_date); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Aadhar Card Number</label>
                            <input type="text" name="aadhar_number" class="form-control" 
                                   placeholder="XXXX-XXXX-XXXX"
                                   value="<?php echo set_value('aadhar_number', $student->aadhar_number); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>PAN Card Number</label>
                            <input type="text" name="pan_number" class="form-control" 
                                   placeholder="ABCDE1234F"
                                   value="<?php echo set_value('pan_number', $student->pan_number); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> 
                            <strong>Student ID:</strong> 
                            <code><?php echo $student->unique_id ?? 'saflg-' . str_pad($student->id, 5, '0', STR_PAD_LEFT); ?></code>
                            (Auto-generated, cannot be changed)
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: EMERGENCY CONTACTS -->
            <div class="tab-pane" id="tab_contacts">
                <h4 class="bold"><i class="fa fa-phone"></i> Emergency & Guardian Contacts</h4>
                <hr>
                
                <!-- Primary Guardian -->
                <h5 class="bold text-primary">Primary Guardian / Parent</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="primary_contact_name" class="form-control" 
                                   value="<?php echo set_value('primary_contact_name', $student->primary_contact_name); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Relationship</label>
                            <input type="text" name="primary_contact_relationship" class="form-control" 
                                   placeholder="Father, Mother, Guardian"
                                   value="<?php echo set_value('primary_contact_relationship', $student->primary_contact_relationship); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Mobile Number</label>
                            <input type="text" name="primary_contact_mobile" class="form-control" 
                                   value="<?php echo set_value('primary_contact_mobile', $student->primary_contact_mobile); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="primary_contact_email" class="form-control" 
                                   value="<?php echo set_value('primary_contact_email', $student->primary_contact_email); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Occupation</label>
                            <input type="text" name="primary_contact_occupation" class="form-control" 
                                   value="<?php echo set_value('primary_contact_occupation', $student->primary_contact_occupation); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Annual Income</label>
                            <input type="text" name="primary_contact_annual_income" class="form-control" 
                                   placeholder="₹"
                                   value="<?php echo set_value('primary_contact_annual_income', $student->primary_contact_annual_income); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Secondary Guardian -->
                <h5 class="bold text-primary">Secondary Guardian (Optional)</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="secondary_contact_name" class="form-control" 
                                   value="<?php echo set_value('secondary_contact_name', $student->secondary_contact_name); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Relationship</label>
                            <input type="text" name="secondary_contact_relationship" class="form-control" 
                                   value="<?php echo set_value('secondary_contact_relationship', $student->secondary_contact_relationship); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Mobile Number</label>
                            <input type="text" name="secondary_contact_mobile" class="form-control" 
                                   value="<?php echo set_value('secondary_contact_mobile', $student->secondary_contact_mobile); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="secondary_contact_email" class="form-control" 
                                   value="<?php echo set_value('secondary_contact_email', $student->secondary_contact_email); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Occupation</label>
                            <input type="text" name="secondary_contact_occupation" class="form-control" 
                                   value="<?php echo set_value('secondary_contact_occupation', $student->secondary_contact_occupation); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Emergency Contact -->
                <h5 class="bold text-danger">Emergency Contact</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="emergency_contact_name" class="form-control" 
                                   value="<?php echo set_value('emergency_contact_name', $student->emergency_contact_name); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Relationship</label>
                            <input type="text" name="emergency_contact_relationship" class="form-control" 
                                   value="<?php echo set_value('emergency_contact_relationship', $student->emergency_contact_relationship); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Mobile Number</label>
                            <input type="text" name="emergency_contact_mobile" class="form-control" 
                                   value="<?php echo set_value('emergency_contact_mobile', $student->emergency_contact_mobile); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="emergency_contact_address" class="form-control" rows="2" 
                                      <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>><?php echo set_value('emergency_contact_address', $student->emergency_contact_address); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: EDUCATION BACKGROUND -->
            <div class="tab-pane" id="tab_education">
                <h4 class="bold"><i class="fa fa-graduation-cap"></i> Educational Background</h4>
                <hr>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Highest Qualification</label>
                            <input type="text" name="highest_qualification" class="form-control" 
                                   placeholder="e.g., B.Tech, MBA, 12th Standard"
                                   value="<?php echo set_value('highest_qualification', $student->highest_qualification); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Previous Institution Name</label>
                            <input type="text" name="institution_name" class="form-control" 
                                   value="<?php echo set_value('institution_name', $student->institution_name); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Year of Completion</label>
                            <input type="text" name="year_of_completion" class="form-control" 
                                   placeholder="YYYY"
                                   value="<?php echo set_value('year_of_completion', $student->year_of_completion); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Percentage / CGPA</label>
                            <input type="text" name="percentage_cgpa" class="form-control" 
                                   placeholder="e.g., 85% or 8.5 CGPA"
                                   value="<?php echo set_value('percentage_cgpa', $student->percentage_cgpa); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 5: DESTINATION DETAILS -->
            <div class="tab-pane" id="tab_destination">
                <h4 class="bold"><i class="fa fa-plane"></i> Study Destination & Course Details</h4>
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Destination Country <span class="text-danger">*</span></label>
                            <select name="destination_country_id" class="form-control selectpicker" data-live-search="true"
                                    <?php echo ($student->is_locked == 1) ? 'disabled' : 'required'; ?>>
                                <option value="">-- Select Country --</option>
                                <?php if(isset($countries) && is_array($countries)): ?>
                                    <?php foreach($countries as $country): ?>
                                        <option value="<?php echo $country->id; ?>" 
                                                <?php echo ($student->destination_country_id == $country->id) ? 'selected' : ''; ?>>
                                            <?php echo $country->country_name; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>University / Institution Name <span class="text-danger">*</span></label>
                            <input type="text" name="university_name" class="form-control" 
                                   value="<?php echo set_value('university_name', $student->university_name); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : 'required'; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Course / Program <span class="text-danger">*</span></label>
                            <input type="text" name="course_program" class="form-control" 
                                   placeholder="e.g., Master of Computer Science"
                                   value="<?php echo set_value('course_program', $student->course_program); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : 'required'; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city_destination" class="form-control" 
                                   value="<?php echo set_value('city_destination', $student->city_destination); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>State / Province</label>
                            <input type="text" name="state_destination" class="form-control" 
                                   value="<?php echo set_value('state_destination', $student->state_destination); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Course Duration</label>
                            <input type="text" name="course_duration" class="form-control" 
                                   placeholder="e.g., 2 years"
                                   value="<?php echo set_value('course_duration', $student->course_duration); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Course Start Date</label>
                            <input type="date" name="start_date" class="form-control" 
                                   value="<?php echo set_value('start_date', $student->start_date); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Expected Completion Date</label>
                            <input type="date" name="expected_completion_date" class="form-control" 
                                   value="<?php echo set_value('expected_completion_date', $student->expected_completion_date); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>University Student ID Number</label>
                            <input type="text" name="student_id_number" class="form-control" 
                                   placeholder="ID provided by university"
                                   value="<?php echo set_value('student_id_number', $student->student_id_number); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 6: VISA INFORMATION -->
            <div class="tab-pane" id="tab_visa">
                <h4 class="bold"><i class="fa fa-id-card"></i> Visa & Immigration Details</h4>
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Visa Type</label>
                            <input type="text" name="visa_type" class="form-control" 
                                   placeholder="e.g., Student Visa, F-1 Visa"
                                   value="<?php echo set_value('visa_type', $student->visa_type); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Visa Number</label>
                            <input type="text" name="visa_number" class="form-control" 
                                   value="<?php echo set_value('visa_number', $student->visa_number); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Visa Issue Date</label>
                            <input type="date" name="visa_issue_date" class="form-control" 
                                   value="<?php echo set_value('visa_issue_date', $student->visa_issue_date); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Visa Expiry Date</label>
                            <input type="date" name="visa_expiry_date" class="form-control" 
                                   value="<?php echo set_value('visa_expiry_date', $student->visa_expiry_date); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Visa Status</label>
                            <select name="visa_status" class="form-control selectpicker" 
                                    <?php echo ($student->is_locked == 1) ? 'disabled' : ''; ?>>
                                <option value="">-- Select Status --</option>
                                <option value="Not Applied" <?php echo ($student->visa_status == 'Not Applied') ? 'selected' : ''; ?>>Not Applied</option>
                                <option value="Applied" <?php echo ($student->visa_status == 'Applied') ? 'selected' : ''; ?>>Applied</option>
                                <option value="Approved" <?php echo ($student->visa_status == 'Approved') ? 'selected' : ''; ?>>Approved</option>
                                <option value="Rejected" <?php echo ($student->visa_status == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                                <option value="In Process" <?php echo ($student->visa_status == 'In Process') ? 'selected' : ''; ?>>In Process</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 7: ACCOMMODATION -->
            <div class="tab-pane" id="tab_accommodation">
                <h4 class="bold"><i class="fa fa-home"></i> Accommodation Details Abroad</h4>
                <hr>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Accommodation Type</label>
                            <select name="accommodation_type" class="form-control selectpicker" 
                                    <?php echo ($student->is_locked == 1) ? 'disabled' : ''; ?>>
                                <option value="">-- Select Type --</option>
                                <option value="University Hostel" <?php echo ($student->accommodation_type == 'University Hostel') ? 'selected' : ''; ?>>University Hostel</option>
                                <option value="Private Apartment" <?php echo ($student->accommodation_type == 'Private Apartment') ? 'selected' : ''; ?>>Private Apartment</option>
                                <option value="Homestay" <?php echo ($student->accommodation_type == 'Homestay') ? 'selected' : ''; ?>>Homestay</option>
                                <option value="Shared Housing" <?php echo ($student->accommodation_type == 'Shared Housing') ? 'selected' : ''; ?>>Shared Housing</option>
                                <option value="Other" <?php echo ($student->accommodation_type == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Accommodation Address</label>
                            <textarea name="accommodation_address" class="form-control" rows="2" 
                                      <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>><?php echo set_value('accommodation_address', $student->accommodation_address); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="accommodation_city" class="form-control" 
                                   value="<?php echo set_value('accommodation_city', $student->accommodation_city); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>State / Province</label>
                            <input type="text" name="accommodation_state" class="form-control" 
                                   value="<?php echo set_value('accommodation_state', $student->accommodation_state); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>ZIP / Postal Code</label>
                            <input type="text" name="accommodation_zip" class="form-control" 
                                   value="<?php echo set_value('accommodation_zip', $student->accommodation_zip); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" name="accommodation_country" class="form-control" 
                                   value="<?php echo set_value('accommodation_country', $student->accommodation_country); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Local Phone Number</label>
                            <input type="text" name="local_phone_number" class="form-control" 
                                   value="<?php echo set_value('local_phone_number', $student->local_phone_number); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <hr>
                <h5 class="bold">Landlord / Contact Person</h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="landlord_name" class="form-control" 
                                   value="<?php echo set_value('landlord_name', $student->landlord_name); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Contact Number</label>
                            <input type="text" name="landlord_contact" class="form-control" 
                                   value="<?php echo set_value('landlord_contact', $student->landlord_contact); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <hr>
                <h5 class="bold">Local Emergency Contacts</h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Contact Person 1</h6>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="local_contact1_name" class="form-control" 
                                   value="<?php echo set_value('local_contact1_name', $student->local_contact1_name); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                        <div class="form-group">
                            <label>Relationship</label>
                            <input type="text" name="local_contact1_relationship" class="form-control" 
                                   value="<?php echo set_value('local_contact1_relationship', $student->local_contact1_relationship); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="local_contact1_phone" class="form-control" 
                                   value="<?php echo set_value('local_contact1_phone', $student->local_contact1_phone); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="local_contact1_email" class="form-control" 
                                   value="<?php echo set_value('local_contact1_email', $student->local_contact1_email); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-muted">Contact Person 2</h6>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="local_contact2_name" class="form-control" 
                                   value="<?php echo set_value('local_contact2_name', $student->local_contact2_name); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                        <div class="form-group">
                            <label>Relationship</label>
                            <input type="text" name="local_contact2_relationship" class="form-control" 
                                   value="<?php echo set_value('local_contact2_relationship', $student->local_contact2_relationship); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="local_contact2_phone" class="form-control" 
                                   value="<?php echo set_value('local_contact2_phone', $student->local_contact2_phone); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="local_contact2_email" class="form-control" 
                                   value="<?php echo set_value('local_contact2_email', $student->local_contact2_email); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 8: FINANCIAL INFORMATION (VIEW ONLY) -->
            <div class="tab-pane" id="tab_financial">
                <h4 class="bold"><i class="fa fa-money"></i> Financial Information</h4>
                <div class="alert alert-info">
                    <i class="fa fa-lock"></i> <strong>Note:</strong> Financial information is read-only and managed by administration.
                </div>
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td style="width: 50%; font-weight: bold; background: #f8f9fa;">
                                        <i class="fa fa-money"></i> Total Course Fee
                                    </td>
                                    <td>
                                        <?php if(!empty($student->total_course_fee)): ?>
                                            <strong>₹<?php echo number_format($student->total_course_fee, 2); ?></strong>
                                        <?php else: ?>
                                            <span class="text-muted">Not specified</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; background: #f8f9fa;">
                                        <i class="fa fa-credit-card"></i> Funding Source
                                    </td>
                                    <td><?php echo !empty($student->funding_source) ? htmlspecialchars($student->funding_source) : '<span class="text-muted">Not specified</span>'; ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; background: #f8f9fa;">
                                        <i class="fa fa-university"></i> Loan Bank Name
                                    </td>
                                    <td><?php echo !empty($student->loan_bank_name) ? htmlspecialchars($student->loan_bank_name) : '<span class="text-muted">Not applicable</span>'; ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; background: #f8f9fa;">
                                        <i class="fa fa-rupee"></i> Loan Amount
                                    </td>
                                    <td>
                                        <?php if(!empty($student->loan_amount)): ?>
                                            ₹<?php echo number_format($student->loan_amount, 2); ?>
                                        <?php else: ?>
                                            <span class="text-muted">Not applicable</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td style="width: 50%; font-weight: bold; background: #f8f9fa;">
                                        <i class="fa fa-credit-card"></i> Loan Account Number
                                    </td>
                                    <td><?php echo !empty($student->loan_account_number) ? htmlspecialchars($student->loan_account_number) : '<span class="text-muted">Not applicable</span>'; ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; background: #f8f9fa;">
                                        <i class="fa fa-phone"></i> Loan Officer Contact
                                    </td>
                                    <td><?php echo !empty($student->loan_officer_contact) ? htmlspecialchars($student->loan_officer_contact) : '<span class="text-muted">Not applicable</span>'; ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; background: #f8f9fa;">
                                        <i class="fa fa-check-circle"></i> Payment Status
                                    </td>
                                    <td>
                                        <?php 
                                        $payment_status_label = [
                                            'paid' => '<span class="label label-success">Paid</span>',
                                            'partial' => '<span class="label label-warning">Partial</span>',
                                            'pending' => '<span class="label label-danger">Pending</span>',
                                            'processing' => '<span class="label label-info">Processing</span>'
                                        ];
                                        echo $payment_status_label[$student->payment_status] ?? '<span class="label label-default">Unknown</span>';
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; background: #f8f9fa;">
                                        <i class="fa fa-percent"></i> Payment Progress
                                    </td>
                                    <td>
                                        <div class="progress" style="margin-bottom: 0;">
                                            <div class="progress-bar progress-bar-<?php 
                                                $pmt_pct = $student->payment_percentage ?? 0;
                                                echo $pmt_pct >= 80 ? 'success' : ($pmt_pct >= 50 ? 'warning' : 'danger'); 
                                            ?>" 
                                                 role="progressbar" 
                                                 style="width: <?php echo $pmt_pct; ?>%">
                                                <?php echo $pmt_pct; ?>%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-warning">
                            <i class="fa fa-info-circle"></i> 
                            To update financial information or make payments, please contact our finance department.
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 9: HEALTH & INSURANCE -->
            <div class="tab-pane" id="tab_health">
                <h4 class="bold"><i class="fa fa-heartbeat"></i> Health & Insurance Information</h4>
                <hr>
                
                <h5 class="bold text-primary">Health Insurance</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Insurance Provider</label>
                            <input type="text" name="health_insurance_provider" class="form-control" 
                                   value="<?php echo set_value('health_insurance_provider', $student->health_insurance_provider); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Policy Number</label>
                            <input type="text" name="health_insurance_policy" class="form-control" 
                                   value="<?php echo set_value('health_insurance_policy', $student->health_insurance_policy); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Coverage Amount</label>
                            <input type="text" name="health_insurance_coverage" class="form-control" 
                                   placeholder="₹"
                                   value="<?php echo set_value('health_insurance_coverage', $student->health_insurance_coverage); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Other Insurance</label>
                            <input type="text" name="other_insurance" class="form-control" 
                                   placeholder="Travel, Life, etc."
                                   value="<?php echo set_value('other_insurance', $student->other_insurance); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <hr>
                <h5 class="bold text-primary">Medical Information</h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Blood Group</label>
                            <select name="blood_group" class="form-control selectpicker" 
                                    <?php echo ($student->is_locked == 1) ? 'disabled' : ''; ?>>
                                <option value="">-- Select Blood Group --</option>
                                <?php 
                                $blood_groups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                                foreach($blood_groups as $bg): ?>
                                    <option value="<?php echo $bg; ?>" <?php echo ($student->blood_group == $bg) ? 'selected' : ''; ?>><?php echo $bg; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Emergency Medical Contact</label>
                            <input type="text" name="emergency_medical_contact" class="form-control" 
                                   placeholder="Doctor's name and phone"
                                   value="<?php echo set_value('emergency_medical_contact', $student->emergency_medical_contact); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Allergies / Medical Conditions</label>
                            <textarea name="allergies_medical_conditions" class="form-control" rows="3" 
                                      placeholder="List any allergies, chronic conditions, or medical issues"
                                      <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>><?php echo set_value('allergies_medical_conditions', $student->allergies_medical_conditions); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Regular Medications</label>
                            <textarea name="regular_medications" class="form-control" rows="3" 
                                      placeholder="List any medications you take regularly"
                                      <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>><?php echo set_value('regular_medications', $student->regular_medications); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 10: ADDITIONAL INFORMATION -->
            <div class="tab-pane" id="tab_additional">
                <h4 class="bold"><i class="fa fa-info-circle"></i> Additional Information</h4>
                <hr>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Previous Legal Issues</label>
                            <select name="previous_legal_issues" class="form-control selectpicker" 
                                    id="previous_legal_issues"
                                    <?php echo ($student->is_locked == 1) ? 'disabled' : ''; ?>>
                                <option value="No" <?php echo ($student->previous_legal_issues == 'No' || empty($student->previous_legal_issues)) ? 'selected' : ''; ?>>No</option>
                                <option value="Yes" <?php echo ($student->previous_legal_issues == 'Yes') ? 'selected' : ''; ?>>Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row" id="legal_issues_details_row" style="display: <?php echo ($student->previous_legal_issues == 'Yes') ? 'block' : 'none'; ?>;">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Legal Issues Details <span class="text-danger">*</span></label>
                            <textarea name="legal_issues_details" class="form-control" rows="4" 
                                      placeholder="Please provide details about any legal issues"
                                      <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>><?php echo set_value('legal_issues_details', $student->legal_issues_details); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Special Dietary Requirements</label>
                            <textarea name="special_dietary_requirements" class="form-control" rows="3" 
                                      placeholder="Vegetarian, Vegan, Allergies, Religious restrictions, etc."
                                      <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>><?php echo set_value('special_dietary_requirements', $student->special_dietary_requirements); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Other Special Needs</label>
                            <textarea name="other_special_needs" class="form-control" rows="3" 
                                      placeholder="Physical disabilities, learning accommodations, etc."
                                      <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>><?php echo set_value('other_special_needs', $student->other_special_needs); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <hr>
                <h5 class="bold text-primary">How Did You Hear About Us?</h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Referral Source</label>
                            <input type="text" name="referral_source" class="form-control" 
                                   placeholder="e.g., Friend, Website, Advertisement"
                                   value="<?php echo set_value('referral_source', $student->referral_source); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Referrer Name</label>
                            <input type="text" name="referrer_name" class="form-control" 
                                   placeholder="Name of person who referred you"
                                   value="<?php echo set_value('referrer_name', $student->referrer_name); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Referrer Organization</label>
                            <input type="text" name="referrer_organization" class="form-control" 
                                   value="<?php echo set_value('referrer_organization', $student->referrer_organization); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Referrer Contact Details</label>
                            <input type="text" name="referrer_contact" class="form-control" 
                                   placeholder="Phone or email"
                                   value="<?php echo set_value('referrer_contact', $student->referrer_contact); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="consent_given" value="1" 
                                           <?php echo ($student->consent_given == 1) ? 'checked' : ''; ?>
                                           <?php echo ($student->is_locked == 1) ? 'disabled' : ''; ?>>
                                    I consent to the processing of my personal data for registration and service purposes
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- End Tab Content -->

        <!-- Form Actions -->
        <div class="row" style="margin-top: 30px;">
            <div class="col-md-12">
                <?php if ($student->is_locked == 0): ?>
                    <button type="submit" class="btn btn-primary btn-lg pull-right" id="save_profile_btn">
                        <i class="fa fa-save"></i> Save Profile Changes
                    </button>
                    <button type="button" class="btn btn-default btn-lg pull-right" style="margin-right: 10px;" 
                            onclick="window.location.href='<?php echo site_url('safelegalsolutions/safelegalsolutions_client/my_dashboard'); ?>'">
                        <i class="fa fa-times"></i> Cancel
                    </button>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fa fa-lock"></i> Your profile is locked and cannot be edited. 
                        <a href="<?php echo site_url('safelegalsolutions/safelegalsolutions_client/my_dashboard'); ?>" class="btn btn-default btn-sm">
                            Back to Dashboard
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php echo form_close(); ?>

    </div>
</div>

<!-- JavaScript -->
<script>
$(document).ready(function() {
    
    // Initialize selectpicker if available
    if($.fn.selectpicker) {
        $('.selectpicker').selectpicker('refresh');
    }
    
    // Show/hide legal issues details based on selection
    $('#previous_legal_issues').on('change', function() {
        if($(this).val() === 'Yes') {
            $('#legal_issues_details_row').slideDown();
        } else {
            $('#legal_issues_details_row').slideUp();
            $('textarea[name="legal_issues_details"]').val('');
        }
    });
    
    // Form submission handler
    $('#student_profile_form').on('submit', function(e) {
        <?php if($student->is_locked == 1): ?>
            e.preventDefault();
            alert('Your profile is locked and cannot be edited.');
            return false;
        <?php endif; ?>
        
        // Show loading state
        var $btn = $('#save_profile_btn');
        $btn.prop('disabled', true);
        $btn.html('<i class="fa fa-spinner fa-spin"></i> Saving...');
    });
    
    // Tab change handler to save scroll position
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        window.scrollTo(0, 0);
    });
    
    // Warn user before leaving if form has changes
    var formChanged = false;
    $('#student_profile_form :input').on('change', function() {
        formChanged = true;
    });
    
    $(window).on('beforeunload', function() {
        if(formChanged) {
            return 'You have unsaved changes. Are you sure you want to leave?';
        }
    });
    
    $('#student_profile_form').on('submit', function() {
        formChanged = false;
    });
});
</script>

<style>
.nav-tabs > li > a {
    padding: 12px 20px;
}

.nav-tabs > li.active > a {
    font-weight: bold;
}

.form-group label {
    font-weight: 600;
    color: #333;
}

.panel-heading {
    background: linear-gradient(to right, #4A90E2, #357ABD) !important;
    color: white !important;
}

.tab-content {
    background: #fff;
    min-height: 400px;
}

.bold {
    font-weight: 600;
}

input[readonly], 
textarea[readonly], 
select[disabled] {
    background-color: #f5f5f5 !important;
    cursor: not-allowed !important;
}

.alert {
    border-radius: 5px;
}

@media print {
    .btn, .nav-tabs {
        display: none !important;
    }
}
</style>