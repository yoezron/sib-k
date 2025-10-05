<?php

/**
 * File Path: app/Views/layouts/partials/title-meta.php
 * 
 * Title & Meta Tags
 * Meta tags dan title untuk setiap halaman
 * 
 * @package    SIB-K
 * @subpackage Views/Layouts/Partials
 * @category   Layout
 * @author     Development Team
 * @created    2025-01-01
 */
?>
<meta charset="utf-8" />
<title><?= esc($title ?? 'Dashboard') ?> | SIB-K - <?= env('school.name', 'MA Persis 31 Banjaran') ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta content="Sistem Informasi Bimbingan dan Konseling" name="description" />
<meta content="<?= env('school.name', 'MA Persis 31 Banjaran') ?>" name="author" />
<meta name="csrf-token" content="<?= csrf_hash() ?>">
<!-- App favicon -->
<link rel="shortcut icon" href="<?= base_url('assets/images/favicon.ico') ?>">