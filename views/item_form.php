<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        
                        <!-- Header -->
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="no-margin">
                                    <i class="fa fa-cube"></i> 
                                    <?php echo isset($item) ? 'Edit Item / Package' : 'Add New Item / Package'; ?>
                                </h4>
                                <hr class="hr-panel-heading" />
                            </div>
                        </div>

                        <!-- Form -->
                        <?php echo form_open(admin_url('safelegalsolutions/item/' . (isset($item) ? $item->id : '')), ['id' => 'item-form']); ?>
                        
                        <div class="row">
                            <div class="col-md-8">
                                
                                <!-- Item Name -->
                                <div class="form-group">
                                    <label for="item_name" class="control-label">
                                        <span class="text-danger">*</span> Item / Package Name
                                    </label>
                                    <input type="text" 
                                           id="item_name" 
                                           name="item_name" 
                                           class="form-control" 
                                           value="<?php echo isset($item) ? htmlspecialchars($item->item_name) : ''; ?>" 
                                           required>
                                    <small class="text-muted">e.g., "Premium Legal Course Package"</small>
                                </div>

                                <!-- Item Code -->
                                <div class="form-group">
                                    <label for="item_code" class="control-label">Item Code</label>
                                    <input type="text" 
                                           id="item_code" 
                                           name="item_code" 
                                           class="form-control" 
                                           value="<?php echo isset($item) ? htmlspecialchars($item->item_code) : ''; ?>" 
                                           placeholder="Auto-generated if left empty">
                                    <small class="text-muted">Leave empty to auto-generate (e.g., PKG-ABC12345)</small>
                                </div>

                                <!-- Description -->
                                <div class="form-group">
                                    <label for="description" class="control-label">Description</label>
                                    <textarea id="description" 
                                              name="description" 
                                              class="form-control" 
                                              rows="4" 
                                              placeholder="Enter package description..."><?php echo isset($item) ? htmlspecialchars($item->description) : ''; ?></textarea>
                                </div>

                                <!-- Features (Multiple) -->
                                <div class="form-group">
                                    <label class="control-label">Package Features</label>
                                    <div id="features-container">
                                        <?php 
                                        $features_array = isset($item) && isset($item->features_array) ? $item->features_array : [''];
                                        if (empty($features_array)) {
                                            $features_array = [''];
                                        }
                                        foreach ($features_array as $index => $feature): 
                                        ?>
                                        <div class="input-group mtop10 feature-row">
                                            <span class="input-group-addon"><i class="fa fa-check"></i></span>
                                            <input type="text" 
                                                   name="features[]" 
                                                   class="form-control" 
                                                   value="<?php echo htmlspecialchars($feature); ?>" 
                                                   placeholder="e.g., Access to all study materials">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-danger remove-feature" <?php echo $index === 0 ? 'disabled' : ''; ?>>
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </span>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button type="button" class="btn btn-info btn-xs mtop10" id="add-feature">
                                        <i class="fa fa-plus"></i> Add Feature
                                    </button>
                                </div>

                            </div>

                            <div class="col-md-4">
                                
                                <!-- Pricing Section -->
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <i class="fa fa-rupee"></i> Pricing Details
                                    </div>
                                    <div class="panel-body">
                                        
                                        <!-- Base Price -->
                                        <div class="form-group">
                                            <label for="base_price" class="control-label">
                                                <span class="text-danger">*</span> Base Price (â‚¹)
                                            </label>
                                            <input type="number" 
                                                   id="base_price" 
                                                   name="base_price" 
                                                   class="form-control" 
                                                   value="<?php echo isset($item) ? $item->base_price : ''; ?>" 
                                                   step="0.01" 
                                                   min="0" 
                                                   required>
                                        </div>

                                        <!-- GST Percentage -->
                                        <div class="form-group">
                                            <label for="gst_percentage" class="control-label">
                                                <span class="text-danger">*</span> GST Percentage (%)
                                            </label>
                                            <input type="number" 
                                                   id="gst_percentage" 
                                                   name="gst_percentage" 
                                                   class="form-control" 
                                                   value="<?php echo isset($item) ? $item->gst_percentage : '18'; ?>" 
                                                   step="0.01" 
                                                   min="0" 
                                                   max="100" 
                                                   required>
                                        </div>

                                        <!-- GST Amount (Calculated) -->
                                        <div class="form-group">
                                            <label class="control-label">GST Amount (â‚¹)</label>
                                            <input type="text" 
                                                   id="gst_amount_display" 
                                                   class="form-control" 
                                                   value="<?php echo isset($item) ? number_format($item->gst_amount, 2) : '0.00'; ?>" 
                                                   readonly 
                                                   style="background-color: #f9f9f9;">
                                            <input type="hidden" 
                                                   id="gst_amount" 
                                                   name="gst_amount" 
                                                   value="<?php echo isset($item) ? $item->gst_amount : '0'; ?>">
                                        </div>

                                        <!-- Total Price (Calculated) -->
                                        <div class="form-group">
                                            <label class="control-label">
                                                <strong>Total Price (â‚¹)</strong>
                                            </label>
                                            <input type="text" 
                                                   id="total_price_display" 
                                                   class="form-control input-lg" 
                                                   value="<?php echo isset($item) ? number_format($item->total_price, 2) : '0.00'; ?>" 
                                                   readonly 
                                                   style="background-color: #e8f5e9; font-weight: bold; color: #2e7d32;">
                                            <input type="hidden" 
                                                   id="total_price" 
                                                   name="total_price" 
                                                   value="<?php echo isset($item) ? $item->total_price : '0'; ?>">
                                        </div>

                                    </div>
                                </div>

                                <!-- Additional Settings -->
                                <div class="panel panel-info">
                                    <div class="panel-heading">
                                        <i class="fa fa-cog"></i> Additional Settings
                                    </div>
                                    <div class="panel-body">
                                        
                                        <!-- Duration -->
                                        <div class="form-group">
                                            <label for="duration_months" class="control-label">Duration (Months)</label>
                                            <input type="number" 
                                                   id="duration_months" 
                                                   name="duration_months" 
                                                   class="form-control" 
                                                   value="<?php echo isset($item) ? $item->duration_months : ''; ?>" 
                                                   min="1" 
                                                   placeholder="e.g., 12">
                                        </div>

                                        <!-- Display Order -->
                                        <div class="form-group">
                                            <label for="display_order" class="control-label">Display Order</label>
                                            <input type="number" 
                                                   id="display_order" 
                                                   name="display_order" 
                                                   class="form-control" 
                                                   value="<?php echo isset($item) ? $item->display_order : '0'; ?>" 
                                                   min="0">
                                            <small class="text-muted">Lower number appears first</small>
                                        </div>

                                        <!-- Active Status -->
                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" 
                                                           name="is_active" 
                                                           value="1" 
                                                           <?php echo (isset($item) && $item->is_active == 1) || !isset($item) ? 'checked' : ''; ?>>
                                                    <strong>Active</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">Only active items appear in registration forms</small>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-md-12">
                                <hr />
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> 
                                    <?php echo isset($item) ? 'Update Item' : 'Create Item'; ?>
                                </button>
                                <a href="<?php echo admin_url('safelegalsolutions/items'); ?>" class="btn btn-default">
                                    <i class="fa fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>

                        <?php echo form_close(); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for GST Calculations & Features -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ==================== GST CALCULATION ====================
    
    const basePriceInput = document.getElementById('base_price');
    const gstPercentageInput = document.getElementById('gst_percentage');
    const gstAmountDisplay = document.getElementById('gst_amount_display');
    const gstAmountHidden = document.getElementById('gst_amount');
    const totalPriceDisplay = document.getElementById('total_price_display');
    const totalPriceHidden = document.getElementById('total_price');

    function calculateGST() {
        const basePrice = parseFloat(basePriceInput.value) || 0;
        const gstPercentage = parseFloat(gstPercentageInput.value) || 0;
        
        const gstAmount = (basePrice * gstPercentage) / 100;
        const totalPrice = basePrice + gstAmount;
        
        // Update display fields
        gstAmountDisplay.value = gstAmount.toFixed(2);
        gstAmountHidden.value = gstAmount.toFixed(2);
        totalPriceDisplay.value = totalPrice.toFixed(2);
        totalPriceHidden.value = totalPrice.toFixed(2);
    }

    // Attach event listeners
    basePriceInput.addEventListener('input', calculateGST);
    basePriceInput.addEventListener('change', calculateGST);
    gstPercentageInput.addEventListener('input', calculateGST);
    gstPercentageInput.addEventListener('change', calculateGST);

    // Calculate on page load
    calculateGST();

    // ==================== DYNAMIC FEATURES ====================
    
    const featuresContainer = document.getElementById('features-container');
    const addFeatureBtn = document.getElementById('add-feature');

    // Add new feature row
    addFeatureBtn.addEventListener('click', function() {
        const newFeature = document.createElement('div');
        newFeature.className = 'input-group mtop10 feature-row';
        newFeature.innerHTML = `
            <span class="input-group-addon"><i class="fa fa-check"></i></span>
            <input type="text" name="features[]" class="form-control" placeholder="e.g., Access to all study materials">
            <span class="input-group-btn">
                <button type="button" class="btn btn-danger remove-feature">
                    <i class="fa fa-trash"></i>
                </button>
            </span>
        `;
        featuresContainer.appendChild(newFeature);
        updateRemoveButtons();
    });

    // Remove feature row
    featuresContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-feature')) {
            const featureRow = e.target.closest('.feature-row');
            featureRow.remove();
            updateRemoveButtons();
        }
    });

    // Ensure at least one feature row exists
    function updateRemoveButtons() {
        const featureRows = featuresContainer.querySelectorAll('.feature-row');
        if (featureRows.length === 1) {
            featureRows[0].querySelector('.remove-feature').disabled = true;
        } else {
            featureRows.forEach(row => {
                row.querySelector('.remove-feature').disabled = false;
            });
        }
    }

    updateRemoveButtons();

    // ==================== FORM VALIDATION ====================
    
    const itemForm = document.getElementById('item-form');
    
    itemForm.addEventListener('submit', function(e) {
        const itemName = document.getElementById('item_name').value.trim();
        const basePrice = parseFloat(basePriceInput.value) || 0;
        const gstPercentage = parseFloat(gstPercentageInput.value) || 0;

        if (itemName === '') {
            alert('Please enter item name');
            e.preventDefault();
            return false;
        }

        if (basePrice <= 0) {
            alert('Please enter a valid base price');
            e.preventDefault();
            return false;
        }

        if (gstPercentage < 0 || gstPercentage > 100) {
            alert('GST percentage must be between 0 and 100');
            e.preventDefault();
            return false;
        }

        return true;
    });

});
</script>

<style>
.feature-row {
    margin-bottom: 5px;
}

.panel-primary .panel-heading {
    background-color: #2196F3 !important;
    border-color: #2196F3 !important;
}

.panel-info .panel-heading {
    background-color: #00bcd4 !important;
    border-color: #00bcd4 !important;
}

#total_price_display {
    font-size: 18px;
}
</style>

<?php init_tail(); ?>