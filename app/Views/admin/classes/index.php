<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
/**
 * File Path: app/Views/admin/classes/index.php
 *
 * Classes List View
 * Placeholder untuk daftar kelas
 *
 * @package    SIB-K
 * @subpackage Views/Admin/Classes
 * @category   Academic Management
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
                    <h4 class="card-title">Daftar Kelas</h4>
                    <a href="<?= base_url('admin/classes/create') ?>" class="btn btn-primary">
                        <i class="mdi mdi-plus me-1"></i> Tambah Kelas
                    </a>
                </div>

                <p class="text-muted">Data berikut masih bersifat statis sebagai contoh tampilan modul kelas.</p>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Kelas</th>
                                <th>Wali Kelas</th>
                                <th>Jumlah Siswa</th>
                                <th class="text-center" style="width: 220px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($classes as $class): ?>
                                <tr>
                                    <td><?= esc($class['id']) ?></td>
                                    <td><?= esc($class['name']) ?></td>
                                    <td><?= esc($class['homeroom']) ?></td>
                                    <td><?= esc($class['student_count']) ?></td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/classes/detail/' . $class['id']) ?>" class="btn btn-sm btn-info">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                        <a href="<?= base_url('admin/classes/edit/' . $class['id']) ?>" class="btn btn-sm btn-warning">
                                            <i class="mdi mdi-pencil"></i>
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