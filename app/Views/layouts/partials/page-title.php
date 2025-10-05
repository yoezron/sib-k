<?php

/**
 * File Path: app/Views/layouts/partials/page-title.php
 * 
 * Page Title & Breadcrumb
 * Menampilkan judul halaman dan breadcrumb navigasi
 * 
 * @package    SIB-K
 * @subpackage Views/Layouts/Partials
 * @category   Layout
 * @author     Development Team
 * @created    2025-01-01
 */
?>
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="page-title mb-0 font-size-18"><?= esc($title ?? 'Dashboard') ?></h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);"><?= esc($li_1 ?? 'SIB-K') ?></a></li>
                    <?php if (isset($li_2)): ?>
                        <li class="breadcrumb-item active"><?= esc($li_2) ?></li>
                    <?php endif; ?>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->