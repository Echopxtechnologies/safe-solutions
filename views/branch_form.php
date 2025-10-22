<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-building"></i> <?php echo isset($branch) ? 'Edit Branch' : 'Add Branch'; ?>
                        </h4>
                        <hr class="hr-panel-heading" />

                        <!-- NAVIGATION TABS (Only show for editing existing branch) -->
                        <?php if (isset($branch)): ?>
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#tab_basic_info" aria-controls="tab_basic_info" role="tab" data-toggle="tab">
                                    <i class="fa fa-info-circle"></i> Basic Information
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tab_documents" aria-controls="tab_documents" role="tab" data-toggle="tab">
                                    <i class="fa fa-file-text"></i> Documents 
                                    <span class="badge" id="documents_count">0</span>
                                </a>
                            </li>
                        </ul>

                        <!-- TAB CONTENT -->
                        <div class="tab-content mtop20">
                            <!-- TAB 1: BASIC INFO -->
                            <div role="tabpanel" class="tab-pane active" id="tab_basic_info">
                        <?php endif; ?>

                        <?php echo form_open(admin_url('safelegalsolutions/branch/' . (isset($branch) ? $branch->id : ''))); ?>
                        
                        <!-- STEP 1: BRANCH BASIC INFO -->
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="bold">
                                    <i class="fa fa-info-circle"></i> Partner Information
                                </h4>
                                <hr style="margin-top: 5px; margin-bottom: 15px;">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category_id" class="control-label">
                                        <span class="text-danger">*</span> Partner Type 
                                    </label>
                                    <div class="input-group">
                                        <select name="category_id" id="category_id" class="form-control selectpicker" 
                                                data-live-search="true" required>
                                            <option value="">Select Partner Type </option>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?php echo $cat->id; ?>" 
                                                        <?php echo (isset($branch) && $branch->category_id == $cat->id) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($cat->name); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#categoryModal" title="Create New Partner Type ">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="branch_name" class="control-label">
                                        <span class="text-danger">*</span> Partner Name
                                    </label>
                                    <input type="text" name="branch_name" id="branch_name" 
                                           class="form-control" required
                                           placeholder="e.g., Anush/hdfc bank(rajajingar)"
                                           value="<?php echo isset($branch) ? htmlspecialchars($branch->branch_name) : ''; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="branch_code" class="control-label">
                                        Branch Code
                                    </label>
                                    <input type="text" name="branch_code" id="branch_code" 
                                           class="form-control" 
                                           placeholder="Auto-generated if empty"
                                           value="<?php echo isset($branch) ? htmlspecialchars($branch->branch_code) : ''; ?>">
                                    <small class="text-muted">Unique identifier for this Partner</small>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Section -->
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="bold text-primary mtop20">
                                    <i class="fa fa-phone"></i> Contact Information
                                </h5>
                                <hr style="margin-top: 5px; margin-bottom: 15px;">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="contact_phone" class="control-label">
                                        <span class="text-danger">*</span> Primary Phone
                                    </label>
                                    <input type="text" name="contact_phone" id="contact_phone" 
                                           class="form-control" required
                                           placeholder="+91 9876543210"
                                           value="<?php echo isset($branch) ? htmlspecialchars($branch->contact_phone) : ''; ?>">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="alternative_phone" class="control-label">
                                        Alternative Phone
                                    </label>
                                    <input type="text" name="alternative_phone" id="alternative_phone" 
                                           class="form-control"
                                           placeholder="+91 9876543211"
                                           value="<?php echo isset($branch) ? htmlspecialchars($branch->alternative_phone) : ''; ?>">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="website_url" class="control-label">
                                        Website URL
                                    </label>
                                    <input type="url" name="website_url" id="website_url" 
                                           class="form-control"
                                           placeholder="https://example.com"
                                           value="<?php echo isset($branch) ? htmlspecialchars($branch->website_url) : ''; ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Business Address Section -->
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="bold text-primary mtop20">
                                    <i class="fa fa-map-marker"></i> Business Address
                                </h5>
                                <hr style="margin-top: 5px; margin-bottom: 15px;">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address" class="control-label">
                                        <span class="text-danger">*</span> Address Line 1
                                    </label>
                                    <input type="text" name="address" id="address" 
                                           class="form-control" required
                                           placeholder="Building/Street name"
                                           value="<?php echo isset($branch) ? htmlspecialchars($branch->address) : ''; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address_line2" class="control-label">
                                        Address Line 2
                                    </label>
                                    <input type="text" name="address_line2" id="address_line2" 
                                           class="form-control"
                                           placeholder="Area, Landmark (optional)"
                                           value="<?php echo isset($branch) ? htmlspecialchars($branch->address_line2) : ''; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location" class="control-label">
                                        <span class="text-danger">*</span> City
                                    </label>
                                    <input type="text" name="location" id="location" 
                                           class="form-control" required
                                           placeholder="e.g., Bangalore"
                                           value="<?php echo isset($branch) ? htmlspecialchars($branch->location) : ''; ?>">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="state" class="control-label">
                                        <span class="text-danger">*</span> State
                                    </label>
                                    <select name="state" id="state" class="form-control selectpicker" 
                                            data-live-search="true" required>
                                        <option value="">Select State</option>
                                        <?php
                                        $states = [
                                            'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh',
                                            'Delhi', 'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand',
                                            'Karnataka', 'Kerala', 'Madhya Pradesh', 'Maharashtra', 'Manipur',
                                            'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Punjab', 'Rajasthan',
                                            'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura', 'Uttar Pradesh',
                                            'Uttarakhand', 'West Bengal'
                                        ];
                                        foreach ($states as $state_name): ?>
                                            <option value="<?php echo $state_name; ?>"
                                                    <?php echo (isset($branch) && $branch->state == $state_name) ? 'selected' : ''; ?>>
                                                <?php echo $state_name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pin_code" class="control-label">
                                        <span class="text-danger">*</span> PIN Code
                                    </label>
                                    <input type="text" name="pin_code" id="pin_code" 
                                           class="form-control" required
                                           placeholder="560001" maxlength="6" pattern="[0-9]{6}"
                                           value="<?php echo isset($branch) ? htmlspecialchars($branch->pin_code) : ''; ?>">
                                    <small class="text-muted">6-digit PIN code</small>
                                </div>
                            </div>
                        </div>

                        <!-- Business Information Section -->
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="bold text-primary mtop20">
                                    <i class="fa fa-briefcase"></i> Business Information
                                </h5>
                                <hr style="margin-top: 5px; margin-bottom: 15px;">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="years_in_business" class="control-label">
                                        <span class="text-danger">*</span> Years in Business
                                    </label>
                                    <select name="years_in_business" id="years_in_business" 
                                            class="form-control selectpicker" required>
                                        <option value="">Select Experience</option>
                                        <option value="less_than_1" <?php echo (isset($branch) && $branch->years_in_business == 'less_than_1') ? 'selected' : ''; ?>>Less than 1 year</option>
                                        <option value="1_to_2" <?php echo (isset($branch) && $branch->years_in_business == '1_to_2') ? 'selected' : ''; ?>>1-2 years</option>
                                        <option value="3_to_5" <?php echo (isset($branch) && $branch->years_in_business == '3_to_5') ? 'selected' : ''; ?>>3-5 years</option>
                                        <option value="6_to_10" <?php echo (isset($branch) && $branch->years_in_business == '6_to_10') ? 'selected' : ''; ?>>6-10 years</option>
                                        <option value="more_than_10" <?php echo (isset($branch) && $branch->years_in_business == 'more_than_10') ? 'selected' : ''; ?>>More than 10 years</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="avg_students_per_year" class="control-label">
                                        <span class="text-danger">*</span> Average Students Per Year
                                    </label>
                                    <select name="avg_students_per_year" id="avg_students_per_year" 
                                            class="form-control selectpicker" required>
                                        <option value="">Select Volume</option>
                                        <option value="1_to_25" <?php echo (isset($branch) && $branch->avg_students_per_year == '1_to_25') ? 'selected' : ''; ?>>1-25 students</option>
                                        <option value="26_to_50" <?php echo (isset($branch) && $branch->avg_students_per_year == '26_to_50') ? 'selected' : ''; ?>>26-50 students</option>
                                        <option value="51_to_100" <?php echo (isset($branch) && $branch->avg_students_per_year == '51_to_100') ? 'selected' : ''; ?>>51-100 students</option>
                                        <option value="101_to_250" <?php echo (isset($branch) && $branch->avg_students_per_year == '101_to_250') ? 'selected' : ''; ?>>101-250 students</option>
                                        <option value="250_plus" <?php echo (isset($branch) && $branch->avg_students_per_year == '250_plus') ? 'selected' : ''; ?>>250+ students</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="gst_number" class="control-label">
                                        GST Number
                                    </label>
                                    <input type="text" name="gst_number" id="gst_number" 
                                           class="form-control" maxlength="15"
                                           placeholder="22AAAAA0000A1Z5"
                                           value="<?php echo isset($branch) ? htmlspecialchars($branch->gst_number) : ''; ?>">
                                    <small class="text-muted">Optional - 15 characters</small>
                                </div>
                            </div>
                        </div>

                        <!-- Partnership Details Section -->
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="bold text-primary mtop20">
                                    <i class="fa fa-handshake-o"></i> Partnership Preferences
                                </h5>
                                <hr style="margin-top: 5px; margin-bottom: 15px;">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="primary_destinations" class="control-label">
                                        Primary Destination Countries
                                    </label>
                                    <select name="primary_destinations[]" id="primary_destinations" 
                                            class="form-control selectpicker" multiple 
                                            data-live-search="true" data-actions-box="true">
                                        <?php
                                        $CI =& get_instance();
                                        $CI->load->model('safelegalsolutions_model');
                                        $all_countries = $CI->safelegalsolutions_model->get_all_countries(['is_active' => 1]);
                                        
                                        $selected_countries = [];
                                        if (isset($branch) && !empty($branch->primary_destinations)) {
                                            $selected_countries = json_decode($branch->primary_destinations, true);
                                            if (!is_array($selected_countries)) {
                                                $selected_countries = [];
                                            }
                                        }
                                        
                                        foreach ($all_countries as $country): ?>
                                            <option value="<?php echo $country->id; ?>"
                                                    <?php echo in_array($country->id, $selected_countries) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($country->country_name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">Select all countries you can provide services for</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="services_offered" class="control-label">
                                        Services Offered
                                    </label>
                                    <textarea name="services_offered" id="services_offered" 
                                              class="form-control" rows="3"
                                              placeholder="Describe the services you provide (e.g., Visa assistance, University admission, Documentation, etc.)"><?php echo isset($branch) ? htmlspecialchars($branch->services_offered) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expected_monthly_referrals" class="control-label">
                                        Expected Monthly Student Referrals
                                    </label>
                                    <select name="expected_monthly_referrals" id="expected_monthly_referrals" 
                                            class="form-control selectpicker">
                                        <option value="">Select Expected Volume</option>
                                        <option value="1_to_5" <?php echo (isset($branch) && $branch->expected_monthly_referrals == '1_to_5') ? 'selected' : ''; ?>>1-5 students</option>
                                        <option value="6_to_10" <?php echo (isset($branch) && $branch->expected_monthly_referrals == '6_to_10') ? 'selected' : ''; ?>>6-10 students</option>
                                        <option value="11_to_20" <?php echo (isset($branch) && $branch->expected_monthly_referrals == '11_to_20') ? 'selected' : ''; ?>>11-20 students</option>
                                        <option value="21_to_50" <?php echo (isset($branch) && $branch->expected_monthly_referrals == '21_to_50') ? 'selected' : ''; ?>>21-50 students</option>
                                        <option value="50_plus" <?php echo (isset($branch) && $branch->expected_monthly_referrals == '50_plus') ? 'selected' : ''; ?>>50+ students</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="preferred_communication" class="control-label">
                                        Preferred Communication Method
                                    </label>
                                    <select name="preferred_communication" id="preferred_communication" 
                                            class="form-control selectpicker">
                                        <option value="">Select Method</option>
                                        <option value="email" <?php echo (isset($branch) && $branch->preferred_communication == 'email') ? 'selected' : ''; ?>>Email</option>
                                        <option value="phone" <?php echo (isset($branch) && $branch->preferred_communication == 'phone') ? 'selected' : ''; ?>>Phone</option>
                                        <option value="whatsapp" <?php echo (isset($branch) && $branch->preferred_communication == 'whatsapp') ? 'selected' : ''; ?>>WhatsApp</option>
                                        <option value="video_call" <?php echo (isset($branch) && $branch->preferred_communication == 'video_call') ? 'selected' : ''; ?>>Video Call</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="additional_comments" class="control-label">
                                        Additional Comments/Requirements
                                    </label>
                                    <textarea name="additional_comments" id="additional_comments" 
                                              class="form-control" rows="3"
                                              placeholder="Any specific requirements or comments about the partnership"><?php echo isset($branch) ? htmlspecialchars($branch->additional_comments) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Consent & Terms Section -->
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="bold text-danger mtop20">
                                    <i class="fa fa-file-text"></i> Terms & Consent <span class="text-danger">*</span>
                                </h5>
                                <hr style="margin-top: 5px; margin-bottom: 15px;">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="checkbox checkbox-danger">
                                        <input type="checkbox" id="terms_accepted" name="terms_accepted" value="1" 
                                               <?php echo (isset($branch) && $branch->terms_accepted == 1) ? 'checked' : ''; ?> required>
                                        <label for="terms_accepted">
                                            <strong class="text-danger">*</strong> I agree to the SAFE Legal Solutions Partner Terms and Conditions and understand the partnership requirements.
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="checkbox checkbox-danger">
                                        <input type="checkbox" id="data_consent" name="data_consent" value="1" 
                                               <?php echo (isset($branch) && $branch->data_consent == 1) ? 'checked' : ''; ?> required>
                                        <label for="data_consent">
                                            <strong class="text-danger">*</strong> I consent to SAFE Legal Solutions collecting and processing my business and personal data for partnership evaluation and ongoing business relationship.
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="checkbox checkbox-info">
                                        <input type="checkbox" id="marketing_consent" name="marketing_consent" value="1" 
                                               <?php echo (isset($branch) && $branch->marketing_consent == 1) ? 'checked' : ''; ?>>
                                        <label for="marketing_consent">
                                            I agree to receive marketing communications, partnership updates, and promotional materials from SAFE Legal Solutions.
                                        </label>
                                    </div>
                                </div>

                                <div class="alert alert-info mtop15">
                                    <i class="fa fa-info-circle"></i> <strong>Data Privacy Notice:</strong> 
                                    Your information will be processed in accordance with our Privacy Policy. You can withdraw consent at any time by contacting us.
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="is_active" class="control-label">
                                        Branch Status
                                    </label>
                                    <select name="is_active" id="is_active" class="form-control">
                                        <option value="1" <?php echo (isset($branch) && $branch->is_active == 1) ? 'selected' : 'selected'; ?>>Active</option>
                                        <option value="0" <?php echo (isset($branch) && $branch->is_active == 0) ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- STEP 2: PARTNER ASSIGNMENT -->
                        <div class="row mtop20">
                            <div class="col-md-12">
                                <h4 class="bold">
                                    <i class="fa fa-user-tie"></i> Partner Assignment
                                </h4>
                                <hr style="margin-top: 5px; margin-bottom: 15px;">
                            </div>
                        </div>

                        <?php if (!isset($branch)): ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" id="create_new_staff" name="create_new_staff" value="1">
                                        <label for="create_new_staff">
                                            <strong>Create New Partner Account</strong>
                                            <small class="text-muted">(Check this to create a new partner instead of selecting existing)</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="newStaffSection" style="display: none;">
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> Creating a new partner will:
                                <ul class="mtop10">
                                    <li>Create a staff account with "SLS Nodal Partner Manager" role</li>
                                    <li>Grant access ONLY to SafeLegalSolutions module</li>
                                    <li>Send login credentials via email</li>
                                </ul>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="staff_email" class="control-label">
                                            <span class="text-danger">*</span> Partner Email
                                        </label>
                                        <input type="email" name="staff_email" id="staff_email" 
                                               class="form-control"
                                               placeholder="partner@example.com">
                                        <small class="text-muted">This will be their username</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="staff_phonenumber" class="control-label">
                                            Phone Number
                                        </label>
                                        <input type="text" name="staff_phonenumber" id="staff_phonenumber" 
                                               class="form-control"
                                               placeholder="+91 9876543210">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="staff_firstname" class="control-label">
                                            <span class="text-danger">*</span> First Name
                                        </label>
                                        <input type="text" name="staff_firstname" id="staff_firstname" 
                                               class="form-control"
                                               placeholder="First Name">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="staff_lastname" class="control-label">
                                            <span class="text-danger">*</span> Last Name
                                        </label>
                                        <input type="text" name="staff_lastname" id="staff_lastname" 
                                               class="form-control"
                                               placeholder="Last Name">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-warning">
                                        <i class="fa fa-lock"></i> <strong>Security:</strong> A secure password will be auto-generated and sent to the partner's email.
                                        They will be prompted to change it on first login.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div id="existingStaffSection">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="nodal_partner_manager_id" class="control-label">
                                            <span class="text-danger">*</span> Select Existing Partner
                                        </label>
                                        <select name="nodal_partner_manager_id" id="nodal_partner_manager_id" 
                                                class="form-control selectpicker" data-live-search="true" 
                                                <?php echo !isset($branch) ? '' : 'required'; ?>>
                                            <option value="">Select Partner</option>
                                            <?php foreach ($staff_members as $staff): ?>
                                                <option value="<?php echo $staff['staffid']; ?>"
                                                        data-subtext="<?php echo htmlspecialchars($staff['email']); ?>"
                                                        <?php echo (isset($branch) && $branch->nodal_partner_manager_id == $staff['staffid']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($staff['firstname'] . ' ' . $staff['lastname']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="text-muted">Select existing staff member to manage this branch</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mtop30">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fa fa-save"></i> 
                                <?php echo isset($branch) ? 'Update Branch' : 'Create Branch & Partner'; ?>
                            </button>
                            <a href="<?php echo admin_url('safelegalsolutions/branches'); ?>" class="btn btn-default btn-lg">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                        </div>

                        <?php echo form_close(); ?>

                        <?php if (isset($branch)): ?>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="tab_documents">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i> Upload partner documents such as GST Certificate, PAN Card, Business License, Partnership Agreement, etc.
                                            <br><strong>Allowed formats:</strong> PDF, DOC, DOCX, JPG, PNG, XLS, XLSX (Max 10MB per file)
                                            <br><strong>Storage:</strong> Files are stored securely in database as binary data (LONGBLOB)
                                        </div>
                                    </div>
                                </div>

                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <i class="fa fa-upload"></i> Upload New Document
                                        </h4>
                                    </div>
                                    <div class="panel-body">
                                        <form id="uploadDocumentForm" enctype="multipart/form-data">
                                            <input type="hidden" name="branch_id" value="<?php echo $branch->id; ?>">
                                            
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="document_type" class="control-label">
                                                            <span class="text-danger">*</span> Document Type
                                                        </label>
                                                        <select name="document_type" id="document_type" class="form-control selectpicker" required>
                                                            <option value="">Select Type</option>
                                                            <option value="GST Certificate">GST Certificate</option>
                                                            <option value="PAN Card">PAN Card</option>
                                                            <option value="Business License">Business License</option>
                                                            <option value="Partnership Agreement">Partnership Agreement</option>
                                                            <option value="Bank Details">Bank Details</option>
                                                            <option value="ID Proof">ID Proof</option>
                                                            <option value="Address Proof">Address Proof</option>
                                                            <option value="Other">Other</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="description" class="control-label">
                                                            Description/Notes
                                                        </label>
                                                        <input type="text" name="description" id="description" class="form-control" 
                                                               placeholder="Brief description of the document">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="document" class="control-label">
                                                            <span class="text-danger">*</span> Select File
                                                        </label>
                                                        <input type="file" name="document" id="document" class="form-control" 
                                                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xls,.xlsx" required>
                                                        <small class="text-muted">Max file size: 10MB</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <button type="button" class="btn btn-success" id="uploadDocBtn">
                                                    <i class="fa fa-upload"></i> Upload Document
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <i class="fa fa-files-o"></i> Uploaded Documents
                                        </h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">#</th>
                                                        <th width="20%">Document Type</th>
                                                        <th width="25%">File Name</th>
                                                        <th width="15%">Size</th>
                                                        <th width="15%">Uploaded By</th>
                                                        <th width="10%">Status</th>
                                                        <th width="10%" class="text-center">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="documentsTableBody">
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted">
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
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Create New Category</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="new_category_name" class="control-label">
                        <span class="text-danger">*</span> Partner Type 
                    </label>
                    <input type="text" class="form-control" id="new_category_name" 
                           placeholder="Enter Partner Type" required>
                </div>
                <div class="alert alert-danger hide" id="category_error"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveCategoryBtn">
                    <i class="fa fa-save"></i> Save Category
                </button>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
(function($) {
    'use strict';
    
    $(document).ready(function() {
        if (typeof $.fn.selectpicker !== 'undefined') {
            $('.selectpicker').selectpicker('refresh');
        }
        
        $('#create_new_staff').on('change', function() {
            if ($(this).is(':checked')) {
                $('#newStaffSection').slideDown();
                $('#existingStaffSection').slideUp();
                $('#staff_email, #staff_firstname, #staff_lastname').prop('required', true);
                $('#nodal_partner_manager_id').prop('required', false).val('').selectpicker('refresh');
            } else {
                $('#newStaffSection').slideUp();
                $('#existingStaffSection').slideDown();
                $('#staff_email, #staff_firstname, #staff_lastname').prop('required', false);
                $('#nodal_partner_manager_id').prop('required', true);
            }
        });
        
        $('form[action*="safelegalsolutions/branch"]').on('submit', function(e) {
            var createNewStaff = $('#create_new_staff').is(':checked');
            
            if (!$('#terms_accepted').is(':checked')) {
                alert('You must accept the Terms and Conditions');
                e.preventDefault();
                return false;
            }
            
            if (!$('#data_consent').is(':checked')) {
                alert('You must accept the Data Processing Consent');
                e.preventDefault();
                return false;
            }
            
            var pinCode = $('#pin_code').val().trim();
            if (pinCode && !/^[0-9]{6}$/.test(pinCode)) {
                alert('PIN Code must be exactly 6 digits');
                e.preventDefault();
                return false;
            }
            
            if (createNewStaff) {
                var email = $('#staff_email').val().trim();
                var firstname = $('#staff_firstname').val().trim();
                var lastname = $('#staff_lastname').val().trim();
                
                if (!email || !firstname || !lastname) {
                    alert('Please fill in all required partner fields');
                    e.preventDefault();
                    return false;
                }
                
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    alert('Please enter a valid email address');
                    e.preventDefault();
                    return false;
                }
            } else {
                if (!$('#nodal_partner_manager_id').val()) {
                    alert('Please select a partner');
                    e.preventDefault();
                    return false;
                }
            }
        });
        
        $('#saveCategoryBtn').on('click', function() {
            var btn = $(this);
            var categoryName = $('#new_category_name').val().trim();
            
            if (!categoryName) {
                $('#category_error').removeClass('hide').text('Category name is required');
                return;
            }
            
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
            $('#category_error').addClass('hide');
            
            $.post(admin_url + 'safelegalsolutions/create_category_ajax', {name: categoryName})
            .done(function(response) {
                if (response.success) {
                    $('#category_id').append(new Option(response.category.name, response.category.id, true, true)).selectpicker('refresh');
                    $('#categoryModal').modal('hide');
                    $('#new_category_name').val('');
                    alert_float('success', 'Category created successfully');
                } else {
                    $('#category_error').removeClass('hide').text(response.message || 'Failed to create category');
                }
            })
            .fail(function() {
                $('#category_error').removeClass('hide').text('An error occurred. Please try again.');
            })
            .always(function() {
                btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Category');
            });
        });
        
        $('#categoryModal').on('hidden.bs.modal', function() {
            $('#new_category_name').val('');
            $('#category_error').addClass('hide');
        });
        
        <?php if (isset($branch)): ?>
        var branchId = <?php echo $branch->id; ?>;

        $('a[href="#tab_documents"]').on('shown.bs.tab', loadDocuments);
        loadDocuments();

        $('#uploadDocBtn').on('click', function() {
            var btn = $(this);
            var formData = new FormData($('#uploadDocumentForm')[0]);
            
            if (!$('#document_type').val()) {
                alert('Please select document type');
                return;
            }
            
            if (!$('#document')[0].files[0]) {
                alert('Please select a file');
                return;
            }
            
            if ($('#document')[0].files[0].size > 10485760) {
                alert('File size exceeds 10MB');
                return;
            }
            
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Uploading...');
            
            $.ajax({
                url: admin_url + 'safelegalsolutions/upload_partner_document',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(r) {
                    alert_float(r.success ? 'success' : 'danger', r.message);
                    if (r.success) {
                        $('#uploadDocumentForm')[0].reset();
                        $('#document_type').selectpicker('refresh');
                        loadDocuments();
                    }
                },
                error: function() {
                    alert_float('danger', 'Upload failed');
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fa fa-upload"></i> Upload Document');
                }
            });
        });

        function loadDocuments() {
            $.get(admin_url + 'safelegalsolutions/get_partner_documents_ajax/' + branchId, function(r) {
                var tbody = $('#documentsTableBody');
                tbody.empty();
                
                if (!r.success || !r.documents.length) {
                    tbody.append('<tr><td colspan="7" class="text-center text-muted">No documents uploaded yet</td></tr>');
                    $('#documents_count').text('0');
                    return;
                }
                
                $('#documents_count').text(r.documents.length);
                
                $.each(r.documents, function(i, d) {
                    var badge = d.is_verified == 1 
                        ? '<span class="label label-success"><i class="fa fa-check"></i> Verified</span>'
                        : '<span class="label label-warning"><i class="fa fa-clock-o"></i> Pending</span>';
                    
                    tbody.append(
                        '<tr>' +
                        '<td>' + (i + 1) + '</td>' +
                        '<td>' + d.document_type + '</td>' +
                        '<td><i class="fa fa-file-o"></i> ' + d.file_name + (d.description ? '<br><small class="text-muted">' + d.description + '</small>' : '') + '</td>' +
                        '<td>' + formatBytes(d.file_size) + '</td>' +
                        '<td>' + d.uploaded_by_firstname + ' ' + d.uploaded_by_lastname + '<br><small>' + d.uploaded_at + '</small></td>' +
                        '<td>' + badge + '</td>' +
                        '<td class="text-center">' +
                        '<a href="' + admin_url + 'safelegalsolutions/download_partner_document/' + d.id + '" class="btn btn-xs btn-info" title="Download"><i class="fa fa-download"></i></a> ' +
                        (d.is_verified == 0 ? '<button class="btn btn-xs btn-success verify-doc" data-id="' + d.id + '" title="Verify"><i class="fa fa-check"></i></button> ' : '') +
                        '<button class="btn btn-xs btn-danger delete-doc" data-id="' + d.id + '" title="Delete"><i class="fa fa-trash"></i></button>' +
                        '</td>' +
                        '</tr>'
                    );
                });
            }, 'json');
        }

        $(document).on('click', '.verify-doc', function() {
            if (confirm('Mark this document as verified?')) {
                $.post(admin_url + 'safelegalsolutions/verify_partner_document/' + $(this).data('id'), function(r) {
                    alert_float(r.success ? 'success' : 'danger', r.message);
                    if (r.success) loadDocuments();
                }, 'json');
            }
        });

        $(document).on('click', '.delete-doc', function() {
            if (confirm('Delete this document? This cannot be undone.')) {
                $.post(admin_url + 'safelegalsolutions/delete_partner_document/' + $(this).data('id'), function(r) {
                    alert_float(r.success ? 'success' : 'danger', r.message);
                    if (r.success) loadDocuments();
                }, 'json');
            }
        });

        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            var k = 1024, sizes = ['Bytes', 'KB', 'MB', 'GB'];
            var i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        <?php endif; ?>
    });
    
})(jQuery);
</script>

<style>
#newStaffSection {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
    border: 1px solid #dee2e6;
    margin-bottom: 20px;
}
.checkbox-primary input[type="checkbox"]:checked + label::before {
    background-color: #2563eb;
    border-color: #2563eb;
}
.checkbox-danger input[type="checkbox"]:checked + label::before {
    background-color: #dc3545;
    border-color: #dc3545;
}
.checkbox-info input[type="checkbox"]:checked + label::before {
    background-color: #17a2b8;
    border-color: #17a2b8;
}
.text-primary {
    color: #2563eb !important;
}
.tab-content {
    border: 1px solid #ddd;
    border-top: none;
    padding: 20px;
    background-color: #fff;
}
#documents_count {
    background-color: #dc3545;
    color: white;
    border-radius: 10px;
    padding: 2px 6px;
    font-size: 11px;
}
</style>