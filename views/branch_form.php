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

                        <?php echo form_open(admin_url('safelegalsolutions/branch/' . (isset($branch) ? $branch->id : ''))); ?>
                        
                        <!-- STEP 1: BRANCH BASIC INFO -->
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="bold">
                                    <i class="fa fa-info-circle"></i> Branch Information
                                </h4>
                                <hr style="margin-top: 5px; margin-bottom: 15px;">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category_id" class="control-label">
                                        <span class="text-danger">*</span> Category
                                    </label>
                                    <div class="input-group">
                                        <select name="category_id" id="category_id" class="form-control selectpicker" 
                                                data-live-search="true" required>
                                            <option value="">Select Category</option>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?php echo $cat->id; ?>" 
                                                        <?php echo (isset($branch) && $branch->category_id == $cat->id) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($cat->name); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#categoryModal" title="Create New Category">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="branch_name" class="control-label">
                                        <span class="text-danger">*</span> Branch Name
                                    </label>
                                    <input type="text" name="branch_name" id="branch_name" 
                                           class="form-control" required
                                           placeholder="e.g., Mumbai Branch"
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
                                    <small class="text-muted">Unique identifier for this branch</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="location" class="control-label">
                                        Location
                                    </label>
                                    <input type="text" name="location" id="location" 
                                           class="form-control" 
                                           placeholder="City / Area"
                                           value="<?php echo isset($branch) ? htmlspecialchars($branch->location) : ''; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="address" class="control-label">
                                        Address
                                    </label>
                                    <textarea name="address" id="address" class="form-control" rows="3" 
                                              placeholder="Complete branch address"><?php echo isset($branch) ? htmlspecialchars($branch->address) : ''; ?></textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="contact_phone" class="control-label">
                                        Contact Phone
                                    </label>
                                    <input type="text" name="contact_phone" id="contact_phone" 
                                           class="form-control"
                                           placeholder="+91 9876543210"
                                           value="<?php echo isset($branch) ? htmlspecialchars($branch->contact_phone) : ''; ?>">
                                    <small class="text-muted">Branch contact number</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="is_active" class="control-label">
                                        Status
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
                        <!-- CREATE NEW PARTNER OPTION (Only for new branches) -->
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

                        <!-- NEW PARTNER CREATION FORM (Hidden by default) -->
                        <div id="newStaffSection" style="display: none;">
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> Creating a new partner will:
                                <ul class="mtop10">
                                    <li>Create a staff account with "SLS Nodal Partner Manager" role</li>
                                    <li>Grant access ONLY to SafeLegalSolutions module</li>
                                    <li>Send login credentials via email</li>
                                    <li>Automatically assign this partner to the branch</li>
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

                        <!-- SELECT EXISTING PARTNER (Always visible, hidden when creating new) -->
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

                        <!-- SUBMIT BUTTONS -->
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

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Category Creation Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Create New Category</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="new_category_name" class="control-label">
                        <span class="text-danger">*</span> Category Name
                    </label>
                    <input type="text" class="form-control" id="new_category_name" 
                           placeholder="Enter category name" required>
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
        // Initialize selectpicker
        if (typeof $.fn.selectpicker !== 'undefined') {
            $('.selectpicker').selectpicker('refresh');
        }
        
        // ============================================================
        // TOGGLE: Create New Partner vs Select Existing
        // ============================================================
        $('#create_new_staff').on('change', function() {
            if ($(this).is(':checked')) {
                $('#newStaffSection').slideDown();
                $('#existingStaffSection').slideUp();
                
                // Make new staff fields required
                $('#staff_email, #staff_firstname, #staff_lastname').prop('required', true);
                
                // Make existing staff select NOT required
                $('#nodal_partner_manager_id').prop('required', false);
                $('#nodal_partner_manager_id').val('').selectpicker('refresh');
            } else {
                $('#newStaffSection').slideUp();
                $('#existingStaffSection').slideDown();
                
                // Make new staff fields NOT required
                $('#staff_email, #staff_firstname, #staff_lastname').prop('required', false);
                
                // Make existing staff select required
                $('#nodal_partner_manager_id').prop('required', true);
            }
        });
        
        // ============================================================
        // FORM VALIDATION BEFORE SUBMIT
        // ============================================================
        $('form').on('submit', function(e) {
            var createNewStaff = $('#create_new_staff').is(':checked');
            
            if (createNewStaff) {
                // Validate new staff fields
                var email = $('#staff_email').val().trim();
                var firstname = $('#staff_firstname').val().trim();
                var lastname = $('#staff_lastname').val().trim();
                
                if (!email || !firstname || !lastname) {
                    alert('Please fill in all required partner fields (Email, First Name, Last Name)');
                    e.preventDefault();
                    return false;
                }
                
                // Email validation
                var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email)) {
                    alert('Please enter a valid email address');
                    e.preventDefault();
                    return false;
                }
            } else {
                // Validate existing staff selection
                var managerId = $('#nodal_partner_manager_id').val();
                if (!managerId) {
                    alert('Please select a partner or check "Create New Partner Account"');
                    e.preventDefault();
                    return false;
                }
            }
        });
        
        // ============================================================
        // CATEGORY CREATION (AJAX)
        // ============================================================
        $('#saveCategoryBtn').on('click', function() {
            var btn = $(this);
            var categoryName = $('#new_category_name').val().trim();
            
            if (categoryName === '') {
                $('#category_error').removeClass('hide').text('Category name is required');
                return;
            }
            
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
            $('#category_error').addClass('hide');
            
            $.ajax({
                url: admin_url + 'safelegalsolutions/create_category_ajax',
                type: 'POST',
                data: {
                    name: categoryName,
                    <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var newOption = new Option(response.category.name, response.category.id, true, true);
                        $('#category_id').append(newOption);
                        $('#category_id').selectpicker('refresh');
                        
                        $('#categoryModal').modal('hide');
                        $('#new_category_name').val('');
                        
                        alert_float('success', 'Category created successfully');
                    } else {
                        $('#category_error').removeClass('hide').text(response.message || 'Failed to create category');
                    }
                },
                error: function() {
                    $('#category_error').removeClass('hide').text('An error occurred. Please try again.');
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Category');
                }
            });
        });
        
        // Reset category modal when closed
        $('#categoryModal').on('hidden.bs.modal', function() {
            $('#new_category_name').val('');
            $('#category_error').addClass('hide');
        });
    });
    
})(jQuery);
</script>

<style>
/* Custom styles for better UX */
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

.alert ul {
    margin-bottom: 0;
}
</style>