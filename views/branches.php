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
                                    <i class="fa fa-building"></i> Partner Management
                                </h4>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?php echo admin_url('safelegalsolutions/branch'); ?>" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Add Partner
                                </a>
                            </div>
                        </div>

                        <hr class="hr-panel-heading" />

                        <!-- Branches Table -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Partner  Name</th>
                                                <th>Partner  Code</th>
                                                <th>Category</th>
                                                <th>Location</th>
                                                <th>Manager</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $branches = $this->safelegalsolutions_model->get_all_branches();
                                            if (!empty($branches)): 
                                                foreach ($branches as $branch): 
                                            ?>
                                                <tr>
                                                    <td><strong><?php echo htmlspecialchars($branch->branch_name); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($branch->branch_code); ?></td>
                                                    <td>
                                                        <span style="display: inline-block; width: 10px; height: 10px; background: <?php echo $branch->color_code; ?>; border-radius: 50%; margin-right: 5px;"></span>
                                                        <?php echo htmlspecialchars($branch->category_name); ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($branch->location); ?></td>
                                                    <td><?php echo htmlspecialchars($branch->manager_firstname . ' ' . $branch->manager_lastname); ?></td>
                                                    <td>
                                                        <?php if ($branch->is_active == 1): ?>
                                                            <span class="label label-success">Active</span>
                                                        <?php else: ?>
                                                            <span class="label label-default">Inactive</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="<?php echo admin_url('safelegalsolutions/branch/' . $branch->id); ?>" class="btn btn-default btn-xs">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <a href="<?php echo admin_url('safelegalsolutions/delete_branch/' . $branch->id); ?>" 
                                                           class="btn btn-danger btn-xs" 
                                                           onclick="return confirm('Are you sure you want to delete this branch?');">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php 
                                                endforeach;
                                            else: 
                                            ?>
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">
                                                        <p class="mtop20 mbot20">No branches found. Click "Add Branch" to create one.</p>
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

<?php init_tail(); ?>