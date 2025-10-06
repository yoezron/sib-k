<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
/**
 * File Path: app/Views/admin/classes/form.php
 *
 * Class Form View
 * Placeholder form untuk create/edit kelas
 *
 * @package    SIB-K
 * @subpackage Views/Admin/Classes
 * @category   Academic Management
 */
?>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">
                    <?= $mode === 'create' ? 'Tambah Kelas Baru' : 'Edit Data Kelas' ?>
                </h4>

                <div class="alert alert-warning" role="alert">
                    <i class="mdi mdi-alert-circle-outline me-2"></i>
                    Form ini merupakan placeholder dan belum terhubung dengan proses penyimpanan data.
                </div>

                <form>
                    <div class="mb-3">
                        <label for="class_name" class="form-label">Nama Kelas</label>
                        <input type="text" id="class_name" class="form-control" disabled
                            value="<?= esc($class['name'] ?? '') ?>"
                            placeholder="Contoh: X IPA 1">
                    </div>

                    <div class="mb-3">
                        <label for="homeroom_teacher" class="form-label">Wali Kelas</label>
                        <input type="text" id="homeroom_teacher" class="form-control" disabled
                            value="<?= esc($class['homeroom'] ?? '') ?>"
                            placeholder="Nama wali kelas">
                    </div>

                    <div class="mb-3">
                        <label for="student_count" class="form-label">Jumlah Siswa</label>
                        <input type="number" id="student_count" class="form-control" disabled
                            value="<?= esc($class['student_count'] ?? '') ?>"
                            placeholder="Contoh: 32">
                    </div>

                    <div class="text-end">
                        <a href="<?= base_url('admin/classes') ?>" class="btn btn-secondary">Kembali</a>
                        <button type="button" class="btn btn-primary" disabled>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>