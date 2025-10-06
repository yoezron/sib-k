<?php

/**
 * File Path: app/Views/counselor/cases/index.php
 * 
 * Cases Index View
 * Tampilan daftar kasus pelanggaran siswa dengan filter dan statistik
 * 
 * @package    SIB-K
 * @subpackage Views/Counselor/Cases
 * @category   View
 * @author     Development Team
 * @created    2025-01-06
 */

$this->extend('layouts/main');
$this->section('content');
?>

<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Manajemen Kasus & Pelanggaran</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('counselor/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Kasus & Pelanggaran</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Alert Messages -->
<?php helper('app'); ?>
<?= show_alerts() ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="mdi mdi-alert-circle me-2"></i>
        <strong>Terdapat kesalahan pada input:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block">Total Pelanggaran</span>
                        <h4 class="mb-3">
                            <?= $stats['overall']['total_violations'] ?? 0 ?>
                        </h4>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <div class="avatar-sm rounded-circle bg-soft-primary">
                            <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-3">
                                <i class="mdi mdi-alert-circle-outline"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block">Dalam Proses</span>
                        <h4 class="mb-3">
                            <span class="text-warning"><?= $stats['overall']['in_process'] ?? 0 ?></span>
                        </h4>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <div class="avatar-sm rounded-circle bg-soft-warning">
                            <span class="avatar-title bg-soft-warning text-warning rounded-circle fs-3">
                                <i class="mdi mdi-progress-clock"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block">Selesai</span>
                        <h4 class="mb-3">
                            <span class="text-success"><?= $stats['overall']['completed'] ?? 0 ?></span>
                        </h4>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <div class="avatar-sm rounded-circle bg-soft-success">
                            <span class="avatar-title bg-soft-success text-success rounded-circle fs-3">
                                <i class="mdi mdi-check-circle-outline"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block">Pending Notifikasi</span>
                        <h4 class="mb-3">
                            <span class="text-danger"><?= $stats['overall']['parents_not_notified'] ?? 0 ?></span>
                        </h4>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <div class="avatar-sm rounded-circle bg-soft-danger">
                            <span class="avatar-title bg-soft-danger text-danger rounded-circle fs-3">
                                <i class="mdi mdi-bell-alert-outline"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="mdi mdi-filter-variant me-2"></i>Filter Data
                </h4>
            </div>
            <div class="card-body">
                <form action="<?= base_url('counselor/cases') ?>" method="get" id="filterForm">
                    <div class="row g-3">
                        <!-- Status Filter -->
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="Dilaporkan" <?= ($filters['status'] ?? '') === 'Dilaporkan' ? 'selected' : '' ?>>Dilaporkan</option>
                                <option value="Dalam Proses" <?= ($filters['status'] ?? '') === 'Dalam Proses' ? 'selected' : '' ?>>Dalam Proses</option>
                                <option value="Selesai" <?= ($filters['status'] ?? '') === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                <option value="Dibatalkan" <?= ($filters['status'] ?? '') === 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                            </select>
                        </div>

                        <!-- Severity Level Filter -->
                        <div class="col-md-3">
                            <label class="form-label">Tingkat Keparahan</label>
                            <select name="severity_level" class="form-select">
                                <option value="">Semua Tingkat</option>
                                <option value="Ringan" <?= ($filters['severity_level'] ?? '') === 'Ringan' ? 'selected' : '' ?>>Ringan</option>
                                <option value="Sedang" <?= ($filters['severity_level'] ?? '') === 'Sedang' ? 'selected' : '' ?>>Sedang</option>
                                <option value="Berat" <?= ($filters['severity_level'] ?? '') === 'Berat' ? 'selected' : '' ?>>Berat</option>
                            </select>
                        </div>

                        <!-- Student Filter -->
                        <div class="col-md-3">
                            <label class="form-label">Siswa</label>
                            <select name="student_id" class="form-select">
                                <option value="">Semua Siswa</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?= $student['id'] ?>" <?= ($filters['student_id'] ?? '') == $student['id'] ? 'selected' : '' ?>>
                                        <?= esc($student['full_name']) ?> - <?= esc($student['nisn']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Category Filter -->
                        <div class="col-md-3">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-select">
                                <option value="">Semua Kategori</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= ($filters['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                                        <?= esc($category['category_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Date From -->
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Dari</label>
                            <input type="date" name="date_from" class="form-control" value="<?= $filters['date_from'] ?? '' ?>">
                        </div>

                        <!-- Date To -->
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Sampai</label>
                            <input type="date" name="date_to" class="form-control" value="<?= $filters['date_to'] ?? '' ?>">
                        </div>

                        <!-- Repeat Offender -->
                        <div class="col-md-3">
                            <label class="form-label">Pelanggar Berulang</label>
                            <select name="is_repeat_offender" class="form-select">
                                <option value="">Semua</option>
                                <option value="1" <?= ($filters['is_repeat_offender'] ?? '') === '1' ? 'selected' : '' ?>>Ya</option>
                            </select>
                        </div>

                        <!-- Parent Notified -->
                        <div class="col-md-3">
                            <label class="form-label">Notifikasi Ortu</label>
                            <select name="parent_notified" class="form-select">
                                <option value="">Semua</option>
                                <option value="no" <?= ($filters['parent_notified'] ?? '') === 'no' ? 'selected' : '' ?>>Belum Dinotifikasi</option>
                            </select>
                        </div>

                        <!-- Search -->
                        <div class="col-md-6">
                            <label class="form-label">Pencarian</label>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama siswa, NISN, atau deskripsi..." value="<?= $filters['search'] ?? '' ?>">
                        </div>

                        <!-- Buttons -->
                        <div class="col-md-6">
                            <label class="form-label d-block">&nbsp;</label>
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-magnify me-1"></i>Filter
                            </button>
                            <a href="<?= base_url('counselor/cases') ?>" class="btn btn-secondary">
                                <i class="mdi mdi-refresh me-1"></i>Reset
                            </a>
                            <a href="<?= base_url('counselor/cases/create') ?>" class="btn btn-success">
                                <i class="mdi mdi-plus me-1"></i>Tambah Pelanggaran
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Daftar Pelanggaran</h4>
                    <div>
                        <span class="badge bg-primary">Total: <?= count($violations) ?> data</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($violations)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Tanggal</th>
                                    <th>Siswa</th>
                                    <th>Kategori</th>
                                    <th>Tingkat</th>
                                    <th>Poin</th>
                                    <th>Status</th>
                                    <th>Penanganan</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($violations as $index => $violation): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <strong><?= date('d/m/Y', strtotime($violation['violation_date'])) ?></strong>
                                            <?php if ($violation['violation_time']): ?>
                                                <br><small class="text-muted"><?= date('H:i', strtotime($violation['violation_time'])) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?= esc($violation['student_name']) ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <?= esc($violation['nisn']) ?>
                                                <?php if ($violation['class_name']): ?>
                                                    | <?= esc($violation['class_name']) ?>
                                                <?php endif; ?>
                                            </small>
                                            <?php if ($violation['is_repeat_offender']): ?>
                                                <br><span class="badge bg-danger"><i class="mdi mdi-repeat"></i> Pelanggar Berulang</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($violation['category_name']) ?></td>
                                        <td>
                                            <?php
                                            $severityBadge = match ($violation['severity_level']) {
                                                'Ringan' => 'bg-info',
                                                'Sedang' => 'bg-warning',
                                                'Berat' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $severityBadge ?>">
                                                <?= esc($violation['severity_level']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong class="text-danger">-<?= $violation['point_deduction'] ?></strong>
                                        </td>
                                        <td>
                                            <?php
                                            $statusBadge = match ($violation['status']) {
                                                'Dilaporkan' => 'bg-info',
                                                'Dalam Proses' => 'bg-warning',
                                                'Selesai' => 'bg-success',
                                                'Dibatalkan' => 'bg-secondary',
                                                default => 'bg-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $statusBadge ?>">
                                                <?= esc($violation['status']) ?>
                                            </span>
                                            <?php if (!$violation['parent_notified']): ?>
                                                <br><span class="badge bg-danger mt-1"><i class="mdi mdi-bell-off"></i> Belum Notifikasi</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($violation['handler_name']): ?>
                                                <small><?= esc($violation['handler_name']) ?></small>
                                            <?php else: ?>
                                                <small class="text-muted">Belum ditangani</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url('counselor/cases/detail/' . $violation['id']) ?>" class="btn btn-sm btn-info" title="Detail">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <?php if (is_koordinator()): ?>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteViolation(<?= $violation['id'] ?>)" title="Hapus">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="avatar-lg mx-auto mb-3">
                            <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-1">
                                <i class="mdi mdi-alert-circle-outline"></i>
                            </span>
                        </div>
                        <h5 class="mb-2">Tidak Ada Data Pelanggaran</h5>
                        <p class="text-muted mb-3">Belum ada data pelanggaran yang tercatat atau sesuai filter yang dipilih.</p>
                        <a href="<?= base_url('counselor/cases/create') ?>" class="btn btn-success">
                            <i class="mdi mdi-plus me-1"></i>Tambah Pelanggaran Baru
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    // Delete Violation with Confirmation
    function deleteViolation(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data pelanggaran ini?\n\nData yang terhapus tidak dapat dikembalikan!')) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= base_url('counselor/cases/delete/') ?>' + id;

            // Add CSRF token
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '<?= csrf_token() ?>';
            csrf.value = '<?= csrf_hash() ?>';
            form.appendChild(csrf);

            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
<?php $this->endSection(); ?>