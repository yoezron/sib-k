<?php if (isset($pageTitle) || isset($breadcrumbs)): ?>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0"><?= $pageTitle ?? 'Dashboard' ?></h4>

                <?php if (isset($breadcrumbs) && !empty($breadcrumbs)): ?>
                    <div class="page-title-right">
                        <?= create_breadcrumb($breadcrumbs) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>