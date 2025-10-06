<?php

/**
 * File Path: app/Views/counselor/cases/detail.php
 * 
 * Case Detail View
 * Tampilan detail kasus pelanggaran dengan sanksi dan riwayat siswa
 * 
 * @package    SIB-K
 * @subpackage Views/Counselor/Cases
 * @category   View
 * @author     Development Team
 * @created    2025-01-06
 */

$this->extend('layouts/main');
$this->section('content');

// Get status badge class
$statusBadgeClass = match ($violation['status']) {
    'Dilaporkan' => 'bg-info',
    'Dalam Proses' => 'bg-warning',
    'Selesai' => 'bg-success',
    'Dibatalkan' => 'bg-secondary',
    default => 'bg-secondary'
};

$severityBadgeClass = match ($violation['severity_level']) {
    'Ringan' => 'bg-info',
    'Sedang' => 'bg-warning',
    'Berat' => 'bg-danger',
    default => 'bg-secondary'
};
?>

<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Detail Kasus Pelanggaran</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('counselor/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('counselor/cases') ?>">Kasus & Pelanggaran</a></li>
                    <li class="breadcrumb-item active">Detail</li>
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

<!-- Violation Detail -->
<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Violation Info Card -->
        <div class="card">
            <div class="card-header bg-danger">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0 text-white">
                        <i class="mdi mdi-alert-circle-outline me-2"></i>Informasi Pelanggaran
                    </h4>
                    <span class="badge <?= $statusBadgeClass ?> fs-6">
                        <?= esc($violation['status']) ?>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <!-- Header Info -->
                <div class="mb-4 pb-3 border-bottom">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-soft-danger text-danger rounded">
                                            <i class="mdi mdi-alert fs-5"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Kategori</p>
                                    <h6 class="mb-0"><?= esc($violation['category_name']) ?></h6>
                                    <span class="badge <?= $severityBadgeClass ?> mt-1">
                                        <?= esc($violation['severity_level']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-soft-info text-info rounded">
                                            <i class="mdi mdi-calendar fs-5"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Tanggal Kejadian</p>
                                    <h6 class="mb-0"><?= date('d F Y', strtotime($violation['violation_date'])) ?></h6>
                                    <?php if ($violation['violation_time']): ?>
                                        <small class="text-muted"><?= date('H:i', strtotime($violation['violation_time'])) ?> WIB</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php if ($violation['location']): ?>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <span class="avatar-title bg-soft-success text-success rounded">
                                                <i class="mdi mdi-map-marker fs-5"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="text-muted mb-1">Lokasi</p>
                                        <h6 class="mb-0"><?= esc($violation['location']) ?></h6>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-soft-warning text-warning rounded">
                                            <i class="mdi mdi-chart-line fs-5"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Poin Pengurangan</p>
                                    <h6 class="mb-0 text-danger">-<?= $violation['point_deduction'] ?> Poin</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Info -->
                <div class="mb-4 pb-3 border-bottom">
                    <h5 class="mb-3"><i class="mdi mdi-account me-2"></i>Data Siswa</h5>
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-md">
                                <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-4">
                                    <?= strtoupper(substr($violation['student_name'], 0, 2)) ?>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1"><?= esc($violation['student_name']) ?></h6>
                            <p class="text-muted mb-0">
                                NISN: <?= esc($violation['nisn']) ?>
                                <?php if ($violation['class_name']): ?>
                                    | Kelas: <?= esc($violation['class_name']) ?>
                                <?php endif; ?>
                            </p>
                            <?php if ($violation['is_repeat_offender']): ?>
                                <span class="badge bg-danger mt-1">
                                    <i class="mdi mdi-repeat"></i> Pelanggar Berulang
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-4 pb-3 border-bottom">
                    <h5 class="mb-3"><i class="mdi mdi-text-box-outline me-2"></i>Deskripsi Pelanggaran</h5>
                    <p class="text-muted mb-0"><?= nl2br(esc($violation['description'])) ?></p>
                </div>

                <!-- Resolution Section -->
                <?php if ($violation['resolution_notes']): ?>
                    <div class="mb-4 pb-3 border-bottom">
                        <h5 class="mb-3"><i class="mdi mdi-file-document-outline me-2"></i>Catatan Penyelesaian</h5>
                        <p class="text-muted mb-0"><?= nl2br(esc($violation['resolution_notes'])) ?></p>
                        <?php if ($violation['resolution_date']): ?>
                            <small class="text-muted">
                                <i class="mdi mdi-calendar-check me-1"></i>
                                Diselesaikan: <?= date('d M Y', strtotime($violation['resolution_date'])) ?>
                            </small>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Notes -->
                <?php if ($violation['notes']): ?>
                    <div class="mb-4 pb-3 border-bottom">
                        <h5 class="mb-3"><i class="mdi mdi-note-text-outline me-2"></i>Catatan Tambahan</h5>
                        <p class="text-muted mb-0"><?= nl2br(esc($violation['notes'])) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Reporter & Handler Info -->
                <div class="mb-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted mb-1">Dilaporkan Oleh:</label>
                            <p class="mb-0"><strong><?= esc($violation['reporter_name']) ?></strong></p>
                            <small class="text-muted"><?= esc($violation['reporter_email']) ?></small>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted mb-1">Ditangani Oleh:</label>
                            <?php if ($violation['handler_name']): ?>
                                <p class="mb-0"><strong><?= esc($violation['handler_name']) ?></strong></p>
                                <small class="text-muted"><?= esc($violation['handler_email']) ?></small>
                            <?php else: ?>
                                <p class="mb-0 text-muted">Belum ditugaskan</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Parent Notification -->
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Status Notifikasi Orang Tua:</strong>
                            <?php if ($violation['parent_notified']): ?>
                                <span class="badge bg-success ms-2">
                                    <i class="mdi mdi-check"></i> Sudah Dinotifikasi
                                </span>
                                <br><small class="text-muted">
                                    Pada: <?= date('d M Y H:i', strtotime($violation['parent_notified_at'])) ?>
                                </small>
                            <?php else: ?>
                                <span class="badge bg-danger ms-2">
                                    <i class="mdi mdi-bell-off"></i> Belum Dinotifikasi
                                </span>
                            <?php endif; ?>
                        </div>
                        <?php if (!$violation['parent_notified']): ?>
                            <form action="<?= base_url('counselor/cases/notifyParent/' . $violation['id']) ?>" method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-warning">
                                    <i class="mdi mdi-send me-1"></i>Kirim Notifikasi
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Metadata -->
                <div class="mt-4 pt-3 border-top">
                    <div class="row text-muted">
                        <div class="col-md-6">
                            <small><i class="mdi mdi-clock-outline me-1"></i>Dibuat: <?= date('d M Y H:i', strtotime($violation['created_at'])) ?></small>
                        </div>
                        <?php if ($violation['updated_at']): ?>
                            <div class="col-md-6 text-md-end">
                                <small><i class="mdi mdi-update me-1"></i>Diperbarui: <?= date('d M Y H:i', strtotime($violation['updated_at'])) ?></small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sanctions Section -->
        <div class="card">
            <div class="card-header bg-warning">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0 text-white">
                        <i class="mdi mdi-gavel me-2"></i>Sanksi yang Diberikan
                    </h4>
                    <?php if ($violation['status'] !== 'Dibatalkan'): ?>
                        <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addSanctionModal">
                            <i class="mdi mdi-plus me-1"></i>Tambah Sanksi
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($violation['sanctions'])): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Jenis Sanksi</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Pemberi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($violation['sanctions'] as $sanction): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($sanction['sanction_type']) ?></strong>
                                            <?php if ($sanction['duration_days']): ?>
                                                <br><small class="text-muted">Durasi: <?= $sanction['duration_days'] ?> hari</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= date('d/m/Y', strtotime($sanction['sanction_date'])) ?>
                                            <?php if ($sanction['start_date'] && $sanction['end_date']): ?>
                                                <br><small class="text-muted">
                                                    <?= date('d/m', strtotime($sanction['start_date'])) ?> -
                                                    <?= date('d/m/Y', strtotime($sanction['end_date'])) ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $sanctionBadge = match ($sanction['status']) {
                                                'Dijadwalkan' => 'bg-info',
                                                'Sedang Berjalan' => 'bg-warning',
                                                'Selesai' => 'bg-success',
                                                'Dibatalkan' => 'bg-secondary',
                                                default => 'bg-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $sanctionBadge ?>">
                                                <?= esc($sanction['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small><?= esc($sanction['assigned_by_name']) ?></small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <div class="avatar-md mx-auto mb-3">
                            <span class="avatar-title bg-soft-warning text-warning rounded-circle fs-3">
                                <i class="mdi mdi-gavel"></i>
                            </span>
                        </div>
                        <h6 class="mb-2">Belum Ada Sanksi</h6>
                        <p class="text-muted mb-3">Tambahkan sanksi untuk pelanggaran ini</p>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#addSanctionModal">
                            <i class="mdi mdi-plus me-1"></i>Tambah Sanksi
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Action Buttons -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="mdi mdi-cog-outline me-2"></i>Aksi</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <?php if ($violation['status'] !== 'Selesai' && $violation['status'] !== 'Dibatalkan'): ?>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                            <i class="mdi mdi-pencil me-1"></i>Update Status
                        </button>
                    <?php endif; ?>

                    <?php if (is_koordinator()): ?>
                        <button type="button" class="btn btn-danger" onclick="deleteViolation(<?= $violation['id'] ?>)">
                            <i class="mdi mdi-delete me-1"></i>Hapus Pelanggaran
                        </button>
                    <?php endif; ?>

                    <a href="<?= base_url('counselor/cases') ?>" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i>Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>

        <!-- Student History -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="mdi mdi-history me-2"></i>Riwayat Siswa</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Pelanggaran:</span>
                        <strong><?= $student_history['statistics']['total_violations'] ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Poin:</span>
                        <strong class="text-danger">-<?= $student_history['statistics']['total_points'] ?></strong>
                    </div>
                </div>

                <div class="border-top pt-3">
                    <h6 class="mb-2">Berdasarkan Tingkat:</h6>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="badge bg-info">Ringan</span>
                        <strong><?= $student_history['statistics']['by_severity']['Ringan'] ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="badge bg-warning">Sedang</span>
                        <strong><?= $student_history['statistics']['by_severity']['Sedang'] ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="badge bg-danger">Berat</span>
                        <strong><?= $student_history['statistics']['by_severity']['Berat'] ?></strong>
                    </div>
                </div>

                <?php if ($student_history['statistics']['is_repeat_offender']): ?>
                    <div class="alert alert-danger mt-3 mb-0">
                        <i class="mdi mdi-alert-circle-outline me-1"></i>
                        <strong>Pelanggar Berulang!</strong>
                        <p class="mb-0 mt-1 small">Siswa ini memiliki 3+ pelanggaran dalam 3 bulan terakhir</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Sanction Modal -->
<?= $this->include('counselor/cases/add_sanction') ?>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= base_url('counselor/cases/update/' . $violation['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Update Status Pelanggaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status Baru</label>
                        <select name="status" class="form-select" required>
                            <option value="Dilaporkan" <?= $violation['status'] === 'Dilaporkan' ? 'selected' : '' ?>>Dilaporkan</option>
                            <option value="Dalam Proses" <?= $violation['status'] === 'Dalam Proses' ? 'selected' : '' ?>>Dalam Proses</option>
                            <option value="Selesai" <?= $violation['status'] === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                            <option value="Dibatalkan" <?= $violation['status'] === 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan Penyelesaian</label>
                        <textarea name="resolution_notes" class="form-control" rows="3" placeholder="Tuliskan catatan penyelesaian kasus..."><?= $violation['resolution_notes'] ?? '' ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    // Delete Violation
    function deleteViolation(id) {
        if (confirm('Apakah Anda yakin ingin menghapus pelanggaran ini?\n\nData yang terhapus tidak dapat dikembalikan!')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= base_url('counselor/cases/delete/') ?>' + id;

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