<!doctype html>
<html lang="id">

<head>
    <?php
    /**
     * File Path: app/Views/layouts/main.php
     * 
     * Main Layout Template
     * Template utama yang mengintegrasikan semua partials (Qovex Template)
     * 
     * @package    SIB-K
     * @subpackage Views/Layouts
     * @category   Layout
     * @author     Development Team
     * @created    2025-01-01
     */
    ?>


    <?= $this->include('layouts/partials/title-meta') ?>

    <?= $this->include('layouts/partials/head-css') ?>
</head>

<?= $this->include('layouts/partials/body') ?>

<!-- Begin page -->
<div id="layout-wrapper">

    <?= $this->include('layouts/partials/menu') ?>

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">

        <div class="page-content">

            <?= $this->include('layouts/partials/page-title') ?>

            <div class="container-fluid">
                <?= $this->renderSection('content') ?>
            </div>
            <!-- container-fluid -->
        </div>
        <!-- End Page-content -->

        <?= $this->include('layouts/partials/footer') ?>

    </div>
    <!-- end main content-->

</div>
<!-- END layout-wrapper -->

<?= $this->include('layouts/partials/right-sidebar') ?>

<?= $this->include('layouts/partials/vendor-scripts') ?>

<!-- App js -->
<script src="<?= base_url('assets/js/app.js') ?>"></script>

<?= $this->renderSection('scripts') ?>

</body>

</html>