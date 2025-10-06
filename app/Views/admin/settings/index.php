<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
/**
 * File Path: app/Views/admin/settings/index.php
 *
 * Settings View
 * Placeholder untuk pengaturan sistem
 *
 * @package    SIB-K
 * @subpackage Views/Admin/Settings
 * @category   Settings
 */
?>

<div class="row">
    <div class="col-lg-6">
        <?php if (session()->getFlashdata('info')): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?= esc(session()->getFlashdata('info')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Informasi Aplikasi</h4>

                <div class="alert alert-warning" role="alert">
                    <i class="mdi mdi-alert-circle-outline me-2"></i>
                    Form pengaturan ini masih berupa contoh dan belum terhubung dengan penyimpanan data.
                </div>

                <form>
                    <div class="mb-3">
                        <label for="app_name" class="form-label">Nama Aplikasi</label>
                        <input type="text" id="app_name" class="form-control" value="<?= esc($settings['app_name']) ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="school_name" class="form-label">Nama Sekolah</label>
                        <input type="text" id="school_name" class="form-control" value="<?= esc($settings['school_name']) ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="contact_email" class="form-label">Email Kontak</label>
                        <input type="email" id="contact_email" class="form-control" value="<?= esc($settings['contact_email']) ?>" disabled>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-primary" disabled>Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>