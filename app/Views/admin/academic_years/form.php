<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
/**
 * File Path: app/Views/admin/academic_years/form.php
 *
 * Academic Year Form View
 * Placeholder form untuk create/edit tahun akademik
 *
 * @package    SIB-K
 * @subpackage Views/Admin/AcademicYears
 * @category   Academic Management
 */
?>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">
                    <?= $mode === 'create' ? 'Tambah Tahun Akademik' : 'Edit Tahun Akademik' ?>
                </h4>

                <div class="alert alert-warning" role="alert">
                    <i class="mdi mdi-alert-circle-outline me-2"></i>
                    Form ini bersifat demonstrasi dan belum terhubung dengan proses penyimpanan data.
                </div>

                <form>
                    <div class="mb-3">
                        <label for="year_name" class="form-label">Tahun Akademik</label>
                        <input type="text" id="year_name" class="form-control" disabled
                            value="<?= esc($academic_year['name'] ?? '') ?>"
                            placeholder="Contoh: 2024/2025">
                    </div>

                    <div class="mb-3">
                        <label for="semester" class="form-label">Semester</label>
                        <select id="semester" class="form-select" disabled>
                            <option value="Ganjil" <?= isset($academic_year['semester']) && $academic_year['semester'] === 'Ganjil' ? 'selected' : '' ?>>Ganjil</option>
                            <option value="Genap" <?= isset($academic_year['semester']) && $academic_year['semester'] === 'Genap' ? 'selected' : '' ?>>Genap</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" disabled value="<?= esc($academic_year['start_date'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Berakhir</label>
                                <input type="date" class="form-control" disabled value="<?= esc($academic_year['end_date'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="<?= base_url('admin/academic-years') ?>" class="btn btn-secondary">Kembali</a>
                        <button type="button" class="btn btn-primary" disabled>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>