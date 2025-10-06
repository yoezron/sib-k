<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
/**
 * File Path: app/Views/admin/classes/detail.php
 *
 * Class Detail View
 * Placeholder untuk detail kelas dan daftar siswa
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
                <h4 class="card-title">Informasi Kelas</h4>

                <dl class="row mb-0">
                    <dt class="col-sm-4">Nama Kelas</dt>
                    <dd class="col-sm-8"><?= esc($class['name']) ?></dd>

                    <dt class="col-sm-4">Wali Kelas</dt>
                    <dd class="col-sm-8"><?= esc($class['homeroom']) ?></dd>

                    <dt class="col-sm-4">Jumlah Siswa</dt>
                    <dd class="col-sm-8"><?= esc($class['student_count']) ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Siswa</h4>
                <p class="text-muted">Data siswa berikut merupakan contoh statis untuk keperluan tampilan.</p>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>NIS</th>
                                <th>Nama Lengkap</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?= esc($student['nis']) ?></td>
                                    <td><?= esc($student['name']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="text-end">
                    <a href="<?= base_url('admin/classes') ?>" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>