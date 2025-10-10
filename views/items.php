<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="no-margin">
                                    <i class="fa fa-cubes"></i> Items / Packages Management
                                </h4>
                                <p class="text-muted">Manage course packages and pricing</p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?php echo admin_url('safelegalsolutions/item'); ?>" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Add Item / Package
                                </a>
                            </div>
                        </div>

                        <hr class="hr-panel-heading" />

                        <!-- Items Table -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Item Name</th>
                                                <th>Item Code</th>
                                                <th>Base Price</th>
                                                <th>GST %</th>
                                                <th>Total Price</th>
                                                <th>Duration</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $items = $this->safelegalsolutions_model->get_all_items();
                                            if (!empty($items)): 
                                                foreach ($items as $item): 
                                            ?>
                                                <tr>
                                                    <td><strong><?php echo htmlspecialchars($item->item_name); ?></strong></td>
                                                    <td><span class="label label-default"><?php echo htmlspecialchars($item->item_code); ?></span></td>
                                                    <td><span class="text-success">₹<?php echo number_format($item->base_price, 2); ?></span></td>
                                                    <td><span class="label label-info"><?php echo number_format($item->gst_percentage, 2); ?>%</span></td>
                                                    <td><strong class="text-primary">₹<?php echo number_format($item->total_price, 2); ?></strong></td>
                                                    <td>
                                                        <?php if (!empty($item->duration_months)): ?>
                                                            <?php echo $item->duration_months; ?> months
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($item->is_active == 1): ?>
                                                            <span class="label label-success">Active</span>
                                                        <?php else: ?>
                                                            <span class="label label-default">Inactive</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="<?php echo admin_url('safelegalsolutions/item/' . $item->id); ?>" class="btn btn-default btn-xs">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <a href="<?php echo admin_url('safelegalsolutions/delete_item/' . $item->id); ?>" 
                                                           class="btn btn-danger btn-xs" 
                                                           onclick="return confirm('Are you sure you want to delete this item?');">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php 
                                                endforeach;
                                            else: 
                                            ?>
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted">
                                                        <p class="mtop20 mbot20">No items found. Click "Add Item / Package" to create one.</p>
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
            </div>a
        </div>
    </div>
</div>

<?php init_tail(); ?>