<?php

/**
 * File Path: app/Views/layouts/partials/head-css.php
 * 
 * Head CSS
 * Memuat semua CSS dari template Qovex yang sudah ada
 * 
 * @package    SIB-K
 * @subpackage Views/Layouts/Partials
 * @category   Layout
 * @author     Development Team
 * @created    2025-01-01
 */
?>
<!-- Bootstrap Css -->
<link href="<?= base_url('assets/css/bootstrap.min.css') ?>" id="bootstrap-style" rel="stylesheet" type="text/css" />
<!-- Icons Css -->
<link href="<?= base_url('assets/css/icons.min.css') ?>" rel="stylesheet" type="text/css" />
<!-- App Css-->
<link href="<?= base_url('assets/css/app.min.css') ?>" id="app-style" rel="stylesheet" type="text/css" />
<!-- Custom Css (if exists) -->
<?php if (file_exists(FCPATH . 'assets/custom/css/app.css')): ?>
    <link href="<?= base_url('assets/custom/css/app.css') ?>" rel="stylesheet" type="text/css" />
<?php endif; ?>

<!-- App favicon -->
<link rel="shortcut icon" href="<?= base_url('assets/images/favicon.ico') ?>">

<!-- App Css-->
<link href="<?= base_url('assets/css/app.min.css') ?>" rel="stylesheet" type="text/css" />