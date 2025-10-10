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
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category_id" class="control-label">
                                        <span class="text-danger">*</span> Category
                                    </label>
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
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="branch_name" class="control-label">
                                        <span class="text-danger">*</span> Branch Name
                                    </label>
                                    <input type="text" name="branch_name" id="branch_name" 
                                           class="form-control" required
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
                                    <textarea name="address" id="address" class="form-control" rows="3"><?php echo isset($branch) ? htmlspecialchars($branch->address) : ''; ?></textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="contact_phone" class="control-label">
                                        Contact Phone
                                    </label>
                                    <input type="text" name="contact_phone" id="contact_phone" class="form-control"
                                           value="<?php echo isset($branch) ? htmlspecialchars($branch->contact_phone) : ''; ?>">
                                    <small class="text-muted">Branch contact number</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nodal_partner_manager_id" class="control-label">
                                        <span class="text-danger">*</span> Nodal Partner Manager
                                    </label>
                                    <select name="nodal_partner_manager_id" id="nodal_partner_manager_id" 
                                            class="form-control selectpicker" data-live-search="true" required>
                                        <option value="">Select Manager</option>
                                        <?php foreach ($staff_members as $staff): ?>
                                            <option value="<?php echo $staff['staffid']; ?>"
                                                    data-subtext="<?php echo htmlspecialchars($staff['email']); ?>"
                                                    <?php echo (isset($branch) && $branch->nodal_partner_manager_id == $staff['staffid']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($staff['firstname'] . ' ' . $staff['lastname']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">Select staff member to manage this branch</small>
                                </div>
                            </div>

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

                        <div class="form-group mtop20">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> <?php echo isset($branch) ? 'Update Branch' : 'Save Branch'; ?>
                            </button>
                            <a href="<?php echo admin_url('safelegalsolutions/branches'); ?>" class="btn btn-default">
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

<?php init_tail(); ?>

<script>
(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Initialize selectpicker
        if (typeof $.fn.selectpicker !== 'undefined') {
            $('.selectpicker').selectpicker('refresh');
        }
    });
    
})(jQuery);
</script>