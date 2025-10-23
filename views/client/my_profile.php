<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="panel_s">
    <div class="panel-body">
        
        <!-- Header - EXACTLY AS ORIGINAL -->
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

        <!-- Lock Status Notice - EXACTLY AS ORIGINAL -->
        <?php if ($student->is_locked == 1): ?>
        <div class="alert alert-warning" style="border-left: 5px solid #f0ad4e;">
            <h4><i class="fa fa-lock"></i> Profile Locked</h4>
            <p>Your profile has been verified and locked by the administration. You cannot make any changes at this time. 
               If you need to update any information, please contact support.</p>
        </div>
        <?php endif; ?>

        <!-- Profile Status Banner - EXACTLY AS ORIGINAL -->
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
                        
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Start - EXACTLY AS ORIGINAL -->
        <?php echo form_open(site_url('safelegalsolutions/safelegalsolutions_client/update_profile'), ['id' => 'student_profile_form']); ?>
        
        <!-- Enhanced Tab Navigation with Better UI -->
        <div class="tabs-wrapper">
            <ul class="nav nav-tabs nav-tabs-enhanced" role="tablist" id="profileTabs">
                <li class="active">
                    <a href="#tab_personal" data-toggle="tab" aria-expanded="true">
                        <i class="fa fa-user"></i> 
                        <span class="tab-text">Personal Info</span>
                    </a>
                </li>
                <li>
                    <a href="#tab_passport" data-toggle="tab" aria-expanded="false">
                        <i class="fa fa-passport"></i> 
                        <span class="tab-text">Passport & ID</span>
                    </a>
                </li>
                <li>
                    <a href="#tab_contacts" data-toggle="tab" aria-expanded="false">
                        <i class="fa fa-phone"></i> 
                        <span class="tab-text">Emergency Contacts</span>
                    </a>
                </li>
                <li>
                    <a href="#tab_education" data-toggle="tab" aria-expanded="false">
                        <i class="fa fa-graduation-cap"></i> 
                        <span class="tab-text">Education</span>
                    </a>
                </li>
                <li>
                    <a href="#tab_destination" data-toggle="tab" aria-expanded="false">
                        <i class="fa fa-plane"></i> 
                        <span class="tab-text">Destination Details</span>
                    </a>
                </li>
                <li>
                    <a href="#tab_visa" data-toggle="tab" aria-expanded="false">
                        <i class="fa fa-id-card"></i> 
                        <span class="tab-text">Visa Information</span>
                    </a>
                </li>
                <li>
                    <a href="#tab_accommodation" data-toggle="tab" aria-expanded="false">
                        <i class="fa fa-home"></i> 
                        <span class="tab-text">Accommodation</span>
                    </a>
                </li>
                <li>
                    <a href="#tab_financial" data-toggle="tab" aria-expanded="false">
                        <i class="fa fa-money"></i> 
                        <span class="tab-text">Financial</span>
                    </a>
                </li>
                <li>
                    <a href="#tab_health" data-toggle="tab" aria-expanded="false">
                        <i class="fa fa-heartbeat"></i> 
                        <span class="tab-text">Health & Insurance</span>
                    </a>
                </li>
                <li>
                    <a href="#tab_additional" data-toggle="tab" aria-expanded="false">
                        <i class="fa fa-info-circle"></i> 
                        <span class="tab-text">Additional Info</span>
                    </a>
                </li>
                <li>
                    <a href="#tab_documents" data-toggle="tab" aria-expanded="false">
                        <i class="fa fa-files-o"></i> 
                        <span class="tab-text">My Documents</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Tab Content with Enhanced Styling -->
        <div class="tab-content tab-content-enhanced">
            
            <!-- TAB 1: PERSONAL INFORMATION - ALL ORIGINAL FIELDS -->
            <div class="tab-pane active" id="tab_personal">
                <h4 class="bold section-title"><i class="fa fa-user"></i> Personal Information</h4>
                <hr class="section-divider">
                
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

            <!-- TAB 2: PASSPORT & ID DETAILS - ALL ORIGINAL FIELDS -->
            <div class="tab-pane" id="tab_passport">
                <h4 class="bold section-title"><i class="fa fa-passport"></i> Passport & Identity Documents</h4>
                <hr class="section-divider">
                
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
                            <label>National ID Number</label>
                            <input type="text" name="national_id_number" class="form-control" 
                                   value="<?php echo set_value('national_id_number', $student->national_id_number); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Aadhar Card Number</label>
                            <input type="text" name="aadhar_number" class="form-control" 
                                   value="<?php echo set_value('aadhar_number', $student->aadhar_number); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>PAN Card Number</label>
                            <input type="text" name="pan_number" class="form-control" 
                                   value="<?php echo set_value('pan_number', $student->pan_number); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: EMERGENCY CONTACTS - ALL ORIGINAL FIELDS -->
            <div class="tab-pane" id="tab_contacts">
                <h4 class="bold section-title"><i class="fa fa-phone"></i> Guardian & Emergency Contacts</h4>
                <hr class="section-divider">
                
                <!-- Primary Guardian -->
                <h5 class="bold text-primary subsection-title">Primary Guardian / Parent</h5>
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
                <h5 class="bold text-primary subsection-title">Secondary Guardian (Optional)</h5>
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
                <h5 class="bold text-danger subsection-title">Emergency Contact</h5>
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

            <!-- TAB 4: EDUCATION BACKGROUND - ALL ORIGINAL FIELDS -->
            <div class="tab-pane" id="tab_education">
                <h4 class="bold section-title"><i class="fa fa-graduation-cap"></i> Educational Background</h4>
                <hr class="section-divider">
                
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

            <!-- TAB 5: DESTINATION DETAILS - ALL ORIGINAL FIELDS -->
            <div class="tab-pane" id="tab_destination">
                <h4 class="bold section-title"><i class="fa fa-plane"></i> Study Destination & Course Details</h4>
                <hr class="section-divider">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Destination Country <span class="text-danger">*</span></label>
                            <select name="destination_country_id" class="form-control selectpicker" data-live-search="true"
                                    <?php echo ($student->is_locked == 1) ? 'disabled' : 'required'; ?>>
                                <option value="">-- Select Country --</option>
                                <?php foreach($countries as $country): ?>
                                    <option value="<?php echo $country->id; ?>" 
                                        <?php echo ($student->destination_country_id == $country->id) ? 'selected' : ''; ?>>
                                        <?php echo $country->name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Preferred University</label>
                            <input type="text" name="university_name" class="form-control" 
                                   value="<?php echo set_value('university_name', $student->university_name); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Course Applied For</label>
                            <input type="text" name="course_name" class="form-control" 
                                   placeholder="e.g., Masters in Computer Science"
                                   value="<?php echo set_value('course_name', $student->course_name); ?>"
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

            <!-- TAB 6: VISA INFORMATION - ALL ORIGINAL FIELDS -->
            <div class="tab-pane" id="tab_visa">
                <h4 class="bold section-title"><i class="fa fa-id-card"></i> Visa & Immigration Details</h4>
                <hr class="section-divider">
                
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

            <!-- TAB 7: ACCOMMODATION - ALL ORIGINAL FIELDS -->
            <div class="tab-pane" id="tab_accommodation">
                <h4 class="bold section-title"><i class="fa fa-home"></i> Accommodation Details Abroad</h4>
                <hr class="section-divider">
                
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
                <h5 class="bold subsection-title">Landlord / Contact Person</h5>
                
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
                <h5 class="bold subsection-title">Local Emergency Contacts</h5>
                
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

            <!-- TAB 8: FINANCIAL INFORMATION (VIEW ONLY) - EXACTLY AS ORIGINAL -->
            <div class="tab-pane" id="tab_financial">
                <h4 class="bold section-title"><i class="fa fa-money"></i> Financial Information</h4>
                <div class="alert alert-info">
                    <i class="fa fa-lock"></i> <strong>Note:</strong> Financial information is read-only and managed by administration.
                </div>
                <hr class="section-divider">
                
                <!-- The rest of the financial content as in original file -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Total Course Fees</label>
                            <input type="text" name="total_fees" class="form-control" 
                                   value="<?php echo set_value('total_fees', $student->total_fees); ?>"
                                   readonly style="background: #f5f5f5;">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Paid Amount</label>
                            <input type="text" name="paid_amount" class="form-control" 
                                   value="<?php echo set_value('paid_amount', $student->paid_amount); ?>"
                                   readonly style="background: #f5f5f5;">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Pending Amount</label>
                            <input type="text" name="pending_amount" class="form-control" 
                                   value="<?php echo set_value('pending_amount', $student->pending_amount); ?>"
                                   readonly style="background: #f5f5f5;">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Next Payment Due</label>
                            <input type="date" name="next_payment_date" class="form-control" 
                                   value="<?php echo set_value('next_payment_date', $student->next_payment_date); ?>"
                                   readonly style="background: #f5f5f5;">
                        </div>
                    </div>
                </div>
                
                <hr>
                <h5 class="bold subsection-title">Loan Information</h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Loan Applied?</label>
                            <select name="loan_applied" class="form-control selectpicker" 
                                    <?php echo ($student->is_locked == 1) ? 'disabled' : ''; ?>>
                                <option value="">-- Select --</option>
                                <option value="Yes" <?php echo ($student->loan_applied == 'Yes') ? 'selected' : ''; ?>>Yes</option>
                                <option value="No" <?php echo ($student->loan_applied == 'No') ? 'selected' : ''; ?>>No</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Loan Amount</label>
                            <input type="text" name="loan_amount" class="form-control" 
                                   placeholder="₹"
                                   value="<?php echo set_value('loan_amount', $student->loan_amount); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Bank Name</label>
                            <input type="text" name="loan_bank_name" class="form-control" 
                                   value="<?php echo set_value('loan_bank_name', $student->loan_bank_name); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Loan Officer Contact</label>
                            <input type="text" name="loan_officer_contact" class="form-control" 
                                   placeholder="Phone or Email"
                                   value="<?php echo set_value('loan_officer_contact', $student->loan_officer_contact); ?>"
                                   <?php echo ($student->is_locked == 1) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="bold subsection-title">Payment Status (View Only)</h5>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td style="width: 50%; font-weight: bold; background: #f8f9fa;">
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 9: HEALTH & INSURANCE - ALL ORIGINAL FIELDS -->
            <div class="tab-pane" id="tab_health">
                <h4 class="bold section-title"><i class="fa fa-heartbeat"></i> Health & Insurance Information</h4>
                <hr class="section-divider">
                
                <h5 class="bold text-primary subsection-title">Health Insurance</h5>
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
                <h5 class="bold text-primary subsection-title">Medical Information</h5>
                
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

            <!-- TAB 10: ADDITIONAL INFORMATION - ALL ORIGINAL FIELDS -->
            <div class="tab-pane" id="tab_additional">
                <h4 class="bold section-title"><i class="fa fa-info-circle"></i> Additional Information</h4>
                <hr class="section-divider">
                
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
                <h5 class="bold text-primary subsection-title">How Did You Hear About Us?</h5>
                
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
            </div>
            
            <!-- Documents Tab - EXACTLY AS ORIGINAL -->
            <div role="tabpanel" class="tab-pane" id="tab_documents">
                <div class="row">
                    <div class="col-md-12">
                        <h4>
                            My Documents 
                            <span class="badge" id="documents_count">0</span>
                            <?php if ($student->is_locked == 0): ?>
                                <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openUploadModal()">
                                    <i class="fa fa-upload"></i> Upload Document
                                </button>
                            <?php endif; ?>
                        </h4>
                        <hr>
                        
                        <?php if ($student->is_locked == 1): ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> Your profile is locked. You cannot upload or delete documents.
                            </div>
                        <?php endif; ?>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="20%">Document Type</th>
                                        <th width="25%">File Name</th>
                                        <th width="10%">Size</th>
                                        <th width="15%">Status</th>
                                        <th width="15%">Uploaded</th>
                                        <th width="10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="my-documents-list">
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <i class="fa fa-spinner fa-spin"></i> Loading documents...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Form Actions -->
        <?php if ($student->is_locked == 0): ?>
        <div class="form-actions-bar">
            <button type="submit" class="btn btn-success btn-lg">
                <i class="fa fa-save"></i> Save All Changes
            </button>
            <button type="reset" class="btn btn-default btn-lg">
                <i class="fa fa-undo"></i> Reset Form
            </button>
        </div>
        <?php endif; ?>

        <?php echo form_close(); ?>
    </div>
</div>

<!-- UPLOAD DOCUMENT MODAL - EXACTLY AS ORIGINAL -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-upload"></i> Upload Document
                </h4>
            </div>
            <form id="uploadDocumentForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="document_type">Document Type <span class="text-danger">*</span></label>
                        <select class="form-control" id="document_type" name="document_type" required>
                            <option value="">-- Select Document Type --</option>
                            <option value="Passport">Passport</option>
                            <option value="Visa">Visa</option>
                            <option value="Academic Transcript">Academic Transcript</option>
                            <option value="Degree Certificate">Degree Certificate</option>
                            <option value="English Test Score">English Test Score</option>
                            <option value="Bank Statement">Bank Statement</option>
                            <option value="Medical Certificate">Medical Certificate</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="document_file">Select File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="document_file" name="document_file" required
                               accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                               onchange="validateFileInput(this)">
                        <small class="text-muted">Accepted formats: PDF, JPG, PNG, DOC, DOCX (Max: 5MB)</small>
                    </div>
                    
                    <div id="upload-message"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="uploadDocument()">
                        <i class="fa fa-upload"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
var STUDENT_ID = <?php echo isset($student->id) ? $student->id : 'null'; ?>;
$(document).ready(function() {
    // Initialize site_url if not defined
    if (typeof site_url === 'undefined') {
        var site_url = '<?php echo site_url(); ?>';
    }
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Initialize select picker if exists
    if ($.fn.selectpicker) {
        $('.selectpicker').selectpicker({
            style: 'btn-default',
            size: 8
        });
    }
    
    // Load documents when tab is shown
    $('a[href="#tab_documents"]').on('shown.bs.tab', function (e) {
        loadClientDocuments();
    });
    
    // Auto load documents if already on documents tab
    if ($('#tab_documents').hasClass('active')) {
        loadClientDocuments();
    }
    
    // Handle legal issues dropdown
    $('#previous_legal_issues').on('change', function() {
        if ($(this).val() == 'Yes') {
            $('#legal_issues_details_row').show();
        } else {
            $('#legal_issues_details_row').hide();
        }
    });
});

/**
 * Open upload modal
 */
function openUploadModal() {
    $('#uploadDocumentModal').modal('show');
    $('#uploadDocumentForm')[0].reset();
    $('#upload-message').html('');
}

/**
 * Upload document
 */
function uploadDocument() {
    var formData = new FormData($('#uploadDocumentForm')[0]);
    
    // Add CSRF token
    formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', 
                    '<?php echo $this->security->get_csrf_hash(); ?>');
    
    // Show loading
    $('#upload-message').html('<div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> Uploading document...</div>');
    
    $.ajax({
        url: '<?php echo site_url("safelegalsolutions/safelegalsolutions_client/upload_client_document"); ?>/' + STUDENT_ID,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#uploadDocumentModal').modal('hide');
                if (typeof swal !== 'undefined') {
                    swal("Success!", response.message || "Document uploaded successfully!", "success");
                } else {
                    alert(response.message || "Document uploaded successfully!");
                }
                loadClientDocuments();
            } else {
                showUploadMessage(response.message || 'Upload failed. Please try again.', 'danger');
            }
        },
        error: function(xhr, status, error) {
            console.error('Upload error:', error);
            showUploadMessage('An error occurred during upload. Please try again.', 'danger');
        }
    });
}

/**
 * Load client documents
 */
function loadClientDocuments() {
    $.ajax({
        url: '<?php echo site_url("safelegalsolutions/safelegalsolutions_client/get_client_documents_ajax"); ?>/' + STUDENT_ID,
        type: 'GET',
        data: {
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.documents && response.documents.length > 0) {
                displayClientDocuments(response.documents);
            } else {
                $('#my-documents-list').html('<tr><td colspan="7" class="text-center">No documents uploaded yet.</td></tr>');
                $('#documents_count').text('0');
            }
        },
        error: function(xhr, status, error) {
            console.error('Load documents error:', error);
            $('#my-documents-list').html('<tr><td colspan="7" class="text-center text-danger">Failed to load documents. Please refresh the page.</td></tr>');
        }
    });
}

/**
 * Display client documents in table
 */
function displayClientDocuments(documents) {
    var html = '';
    var isLocked = <?php echo (isset($student->is_locked) && $student->is_locked == 1) ? 'true' : 'false'; ?>;
    
    documents.forEach(function(doc, index) {
        var statusLabel = getVerificationLabel(doc.is_verified);
        var fileSize = formatFileSize(doc.file_size);
        var uploadDate = formatDate(doc.uploaded_at);
        
        html += '<tr>';
        html += '<td>' + (index + 1) + '</td>';
        html += '<td>' + escapeHtml(doc.document_type) + '</td>';
        html += '<td>' + escapeHtml(doc.file_name) + '</td>';
        html += '<td>' + fileSize + '</td>';
        html += '<td>' + statusLabel + '</td>';
        html += '<td>' + uploadDate + '</td>';
        html += '<td>';
        
        // View/Download button
        html += '<a href="' + doc.file_url + '" class="btn btn-xs btn-info" target="_blank" title="Download">';
        html += '<i class="fa fa-download"></i></a> ';
        
        // Delete button (only if not locked and not verified)
        if (!isLocked && !doc.is_verified) {
            html += '<button class="btn btn-xs btn-danger" onclick="deleteClientDocument(' + doc.id + ')" title="Delete">';
            html += '<i class="fa fa-trash"></i></button>';
        }
        
        html += '</td>';
        html += '</tr>';
    });
    
    $('#my-documents-list').html(html);
    $('#documents_count').text(documents.length);
}

/**
 * Get verification label HTML
 */
function getVerificationLabel(isVerified) {
    if (isVerified == 1) {
        return '<span class="label label-success">Verified</span>';
    } else {
        return '<span class="label label-warning">Pending</span>';
    }
}

/**
 * Delete client document with confirmation
 */
function deleteClientDocument(docId) {
    // Use SweetAlert if available, otherwise use confirm
    if (typeof swal !== 'undefined') {
        swal({
            title: "Are you sure?",
            text: "This document will be permanently deleted!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false
        }, function() {
            performDeleteClientDocument(docId);
        });
    } else {
        if (confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
            performDeleteClientDocument(docId);
        }
    }
}

/**
 * Perform client document deletion
 */
function performDeleteClientDocument(docId) {
    $.ajax({
        url: '<?php echo site_url("safelegalsolutions/safelegalsolutions_client/delete_client_document"); ?>',
        type: 'POST',
        data: {
            doc_id: docId,
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                if (typeof swal !== 'undefined') {
                    swal("Deleted!", response.message || "Document has been deleted.", "success");
                } else {
                    alert(response.message || "Document has been deleted.");
                }
                loadClientDocuments();
            } else {
                if (typeof swal !== 'undefined') {
                    swal("Error!", response.message || "Failed to delete document.", "error");
                } else {
                    alert("Error: " + (response.message || "Failed to delete document."));
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Delete error:', error);
            if (typeof swal !== 'undefined') {
                swal("Error!", "Failed to delete document. Please try again.", "error");
            } else {
                alert("Failed to delete document. Please try again.");
            }
        }
    });
}

/**
 * Validate file input
 */
function validateFileInput(input) {
    if (!input.files || !input.files[0]) return;
    
    var file = input.files[0];
    var maxSize = 10 * 1024 * 1024; // 10MB
    var allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
    var fileExt = file.name.split('.').pop().toLowerCase();
    
    // Clear previous messages
    $('#upload-message').html('');
    
    // Check file type
    if (!allowedExtensions.includes(fileExt)) {
        showUploadMessage('Invalid file type. Allowed: ' + allowedExtensions.join(', '), 'danger');
        input.value = '';
        return false;
    }
    
    // Check file size
    if (file.size > maxSize) {
        showUploadMessage('File size exceeds 10MB limit', 'danger');
        input.value = '';
        return false;
    }
    
    // Show file info
    var info = 'Selected: ' + file.name + ' (' + formatFileSize(file.size) + ')';
    showUploadMessage(info, 'info');
    return true;
}

// =====================================================
// HELPER FUNCTIONS
// =====================================================

/**
 * Show upload message in modal
 */
function showUploadMessage(message, type) {
    var alertClass = 'alert-info';
    if (type === 'success') alertClass = 'alert-success';
    else if (type === 'danger') alertClass = 'alert-danger';
    else if (type === 'warning') alertClass = 'alert-warning';
    
    $('#upload-message').html(
        '<div class="alert ' + alertClass + ' alert-dismissible" role="alert">' +
        '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>' +
        message +
        '</div>'
    );
}

/**
 * Format file size
 */
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    if (!bytes || isNaN(bytes)) return 'Unknown';
    
    var k = 1024;
    var sizes = ['Bytes', 'KB', 'MB', 'GB'];
    var i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

/**
 * Format date
 */
function formatDate(dateString) {
    if (!dateString) return 'Unknown';
    
    var date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;
    
    var options = { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit' 
    };
    
    return date.toLocaleDateString('en-US', options);
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    if (!text) return '';
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

/**
 * Show floating alert message
 */
function showFloatingAlert(message, type) {
    var alertClass = 'alert-info';
    if (type === 'success') alertClass = 'alert-success';
    else if (type === 'danger') alertClass = 'alert-danger';
    else if (type === 'warning') alertClass = 'alert-warning';
    
    var html = '<div class="floating-alert alert ' + alertClass + '">' +
               '<button type="button" class="close" onclick="$(this).parent().remove();">' +
               '<span>&times;</span></button>' + message + '</div>';
    
    $('body').append(html);
    
    setTimeout(function() {
        $('.floating-alert').fadeOut(function() {
            $(this).remove();
        });
    }, 5000);
}

// =====================================================
// INITIALIZATION
// =====================================================
console.log('Document management system loaded successfully');
</script>

<!-- ENHANCED CSS TO YOUR EXISTING STYLES WITH BETTER UI FOR TABS -->
<style>
/* Enhanced Tab Navigation Styles */
.tabs-wrapper {
    background: #fff;
    border-radius: 8px 8px 0 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    margin-bottom: 0;
}

.nav-tabs-enhanced {
    border-bottom: 2px solid #e4e7ea;
    background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
    padding: 10px 10px 0;
    display: flex;
    flex-wrap: nowrap;
    overflow-x: auto;
}

.nav-tabs-enhanced::-webkit-scrollbar {
    height: 4px;
}

.nav-tabs-enhanced::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.nav-tabs-enhanced::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 2px;
}

.nav-tabs-enhanced > li {
    margin-bottom: -2px;
    margin-right: 2px;
}

.nav-tabs-enhanced > li > a {
    color: #667085;
    padding: 12px 20px;
    border-radius: 8px 8px 0 0;
    border: 1px solid transparent;
    background: #f9fafb;
    transition: all 0.3s ease;
    font-weight: 500;
    position: relative;
}

.nav-tabs-enhanced > li > a:hover {
    background: #ffffff;
    border-color: #e4e7ea #e4e7ea transparent;
    color: #344054;
}

.nav-tabs-enhanced > li.active > a,
.nav-tabs-enhanced > li.active > a:hover,
.nav-tabs-enhanced > li.active > a:focus {
    color: #1570ef;
    background: #ffffff;
    border-color: #e4e7ea #e4e7ea #fff;
    border-bottom: 2px solid #1570ef;
    font-weight: 600;
}

.nav-tabs-enhanced > li > a > i {
    margin-right: 6px;
    font-size: 14px;
}

.tab-text {
    display: inline-block;
}

/* Enhanced Tab Content */
.tab-content-enhanced {
    background: #ffffff;
    padding: 30px;
    border: 1px solid #e4e7ea;
    border-top: none;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.04);
}

.section-title {
    color: #101828;
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 8px;
}

.section-divider {
    border-top: 2px solid #f2f4f7;
    margin: 20px 0 25px;
}

.subsection-title {
    color: #344054;
    font-size: 16px;
    font-weight: 600;
    margin: 20px 0 15px;
    padding-bottom: 8px;
    border-bottom: 1px solid #f2f4f7;
}

/* Form Enhancements */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    color: #344054;
    font-weight: 500;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-control {
    border: 1px solid #d0d5dd;
    border-radius: 6px;
    font-size: 14px;
    padding: 10px 14px;
    transition: all 0.2s ease;
}

.form-control:focus {
    border-color: #84caff;
    box-shadow: 0 0 0 3px rgba(21, 112, 239, 0.1);
    outline: none;
}

.form-control[readonly] {
    background-color: #f9fafb;
    color: #667085;
}

/* Form Actions Bar */
.form-actions-bar {
    background: #f9fafb;
    padding: 20px;
    margin: 30px -15px -15px;
    border-top: 1px solid #e4e7ea;
    text-align: center;
    border-radius: 0 0 8px 8px;
}

.form-actions-bar .btn {
    margin: 0 5px;
    min-width: 150px;
}

/* Button Enhancements */
.btn {
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-success {
    background: #12b76a;
    border-color: #12b76a;
}

.btn-success:hover {
    background: #0e9f5d;
    border-color: #0e9f5d;
}

/* YOUR ORIGINAL CSS PRESERVED */
.document-row:hover {
    background-color: #f5f5f5;
}

.document-actions .btn {
    margin: 0 2px;
}

.floating-alert {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 300px;
    box-shadow: 0 3px 6px rgba(0,0,0,0.16);
}

#upload-message .alert {
    margin-bottom: 10px;
}

.mt-2 {
    margin-top: 10px !important;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .nav-tabs-enhanced > li > a {
        padding: 10px 15px;
        font-size: 13px;
    }
    
    .nav-tabs-enhanced > li > a > i {
        display: block;
        margin-right: 0;
        margin-bottom: 4px;
    }
    
    .tab-text {
        font-size: 11px;
    }
    
    .tab-content-enhanced {
        padding: 20px 15px;
    }
}
</style>