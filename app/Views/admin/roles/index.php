<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
/**
 * File Path: app/Views/admin/roles/index.php
 *
 * Roles List View
 * Placeholder untuk daftar role
 *
 * @package    SIB-K
 * @subpackage Views/Admin/Roles
 * @category   Role Management
 */
?>

<div class="row">
    <div class="col-12">
        <?php if (session()->getFlashdata('info')): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?= esc(session()->getFlashdata('info')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Daftar Role</h4>
                    <a href="<?= base_url('admin/roles/create') ?>" class="btn btn-primary">
                        <i class="mdi mdi-plus me-1"></i> Tambah Role
                    </a>
                </div>

                <p class="text-muted">
                    Data pada tabel berikut masih bersifat statis sebagai contoh tampilan modul role.
                    Silakan lengkapi logika bisnis dan koneksi basis data sesuai kebutuhan aplikasi Anda.
                </p>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th style="width: 80px;">ID</th>
                                <th>Nama Role</th>
                                <th>Deskripsi</th>
                                <th class="text-center" style="width: 160px;">Jumlah Hak Akses</th>
                                <th class="text-center" style="width: 220px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($roles as $role): ?>
                                <tr>
                                    <td><?= esc($role['id']) ?></td>
                                    <td><?= esc($role['name']) ?></td>
                                    <td><?= esc($role['description']) ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-primary text-primary">
                                            <?= esc($role['permissions']) ?> Hak Akses
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/roles/edit/' . $role['id']) ?>" class="btn btn-sm btn-warning">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <a href="<?= base_url('admin/roles/permissions/' . $role['id']) ?>" class="btn btn-sm btn-info">
                                            <i class="mdi mdi-shield-key"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" disabled>
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>