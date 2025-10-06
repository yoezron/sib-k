<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
/**
 * File Path: app/Views/profile/index.php
 *
 * Profile Page View
 * Tampilan sederhana untuk profil pengguna
 *
 * @package    SIB-K
 * @subpackage Views/Profile
 * @category   Profile
 */
?>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Profil Pengguna</h4>
                <p class="card-text text-muted mb-4">
                    Halaman ini merupakan placeholder untuk informasi profil pengguna.
                    Silakan sesuaikan dengan kebutuhan aplikasi Anda.
                </p>

                <div class="alert alert-info" role="alert">
                    <i class="mdi mdi-account-circle-outline me-2"></i>
                    Modul profil belum terhubung dengan basis data. Anda dapat menambahkan
                    informasi pengguna aktif atau form pengaturan profil di sini.
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>