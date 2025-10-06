<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
/**
 * File Path: app/Views/admin/academic_years/index.php
 *
 * Academic Years List View
 * Placeholder data tahun akademik
 *
 * @package    SIB-K
 * @subpackage Views/Admin/AcademicYears
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
                    <h4 class="card-title">Daftar Tahun Akademik</h4>
                    <a href="<?= base_url('admin/academic-years/create') ?>" class="btn btn-primary">
                        <i class="mdi mdi-plus me-1"></i> Tambah Tahun Akademik
                    </a>
                </div>

                <p class="text-muted">Data berikut masih berupa contoh statis. Integrasikan dengan basis data
                    untuk menampilkan data sebenarnya.</p>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tahun</th>
                                <th>Semester</th>
                                <th>Periode</th>
                                <th>Status</th>
                                <th class="text-center" style="width: 180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($academic_years as $year): ?>
                                <tr>
                                    <td><?= esc($year['id']) ?></td>
                                    <td><?= esc($year['name']) ?></td>
                                    <td><?= esc($year['semester']) ?></td>
                                    <td><?= date('d M Y', strtotime($year['start_date'])) ?> - <?= date('d M Y', strtotime($year['end_date'])) ?></td>
                                    <td>
                                        <?php if ($year['is_active']): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Tidak Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/academic-years/edit/' . $year['id']) ?>" class="btn btn-sm btn-warning">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" disabled>
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-success" disabled>
                                            <i class="mdi mdi-check"></i>
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