<?php

/**
 * File Path: app/Views/counselor/sessions/detail.php
 * 
 * Session Detail View
 * Halaman untuk menampilkan detail sesi konseling lengkap dengan notes
 * 
 * @package    SIB-K
 * @subpackage Views/Counselor/Sessions
 * @category   View
 * @author     Development Team
 * @created    2025-01-06
 */

$this->extend('layouts/main');
$this->section('content');

// Get status badge class
$statusBadgeClass = match ($session['status']) {
    'Dijadwalkan' => 'bg-info',
    'Selesai' => 'bg-success',
    'Dibatalkan' => 'bg-danger',
    default => 'bg-secondary'
};
?>

<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Detail Sesi Konseling</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('counselor/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('counselor/sessions') ?>">Sesi Konseling</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Alert Messages -->
<?= show_alerts() ?>

<!-- Session Detail -->
<div class="row">
    <div class="col-lg-8">
        <!-- Main Session Info -->
        <div class="card">
            <div class="card-header bg-primary">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0 text-white">
                        <i class="mdi mdi-information-outline me-2"></i>Informasi Sesi
                    </h4>
                    <span class="badge <?= $statusBadgeClass ?> fs-6">
                        <?= esc($session['status']) ?>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <!-- Session Header -->
                <div class="mb-4 pb-3 border-bottom">
                    <h3 class="mb-3"><?= esc($session['topic']) ?></h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-soft-primary text-primary rounded">
                                            <i class="mdi mdi-shape fs-5"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Tipe Sesi</p>
                                    <h6 class="mb-0"><?= esc($session['session_type']) ?></h6>
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
                                    <p class="text-muted mb-1">Tanggal</p>
                                    <h6 class="mb-0"><?= date('d F Y', strtotime($session['session_date'])) ?></h6>
                                </div>
                            </div>
                        </div>
                        <?php if ($session['session_time']): ?>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <span class="avatar-title bg-soft-warning text-warning rounded">
                                                <i class="mdi mdi-clock-outline fs-5"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="text-muted mb-1">Waktu</p>
                                        <h6 class="mb-0"><?= date('H:i', strtotime($session['session_time'])) ?> WIB</h6>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($session['location']): ?>
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
                                        <h6 class="mb-0"><?= esc($session['location']) ?></h6>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($session['duration_minutes']): ?>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <span class="avatar-title bg-soft-danger text-danger rounded">
                                                <i class="mdi mdi-timer-outline fs-5"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="text-muted mb-1">Durasi</p>
                                        <h6 class="mb-0"><?= esc($session['duration_minutes']) ?> Menit</h6>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Student/Class Info -->
                <?php if ($session['session_type'] === 'Individual' && $session['student_name']): ?>
                    <div class="mb-4 pb-3 border-bottom">
                        <h5 class="mb-3"><i class="mdi mdi-account me-2"></i>Siswa</h5>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-md">
                                    <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-4">
                                        <?= strtoupper(substr($session['student_name'], 0, 2)) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1"><?= esc($session['student_name']) ?></h6>
                                <p class="text-muted mb-0">
                                    <?= esc($session['student_nisn']) ?>
                                    <?php if ($session['class_name']): ?>
                                        | Kelas <?= esc($session['class_name']) ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php elseif (in_array($session['session_type'], ['Kelompok', 'Klasikal']) && $session['class_name']): ?>
                    <div class="mb-4 pb-3 border-bottom">
                        <h5 class="mb-3"><i class="mdi mdi-google-classroom me-2"></i>Kelas</h5>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-md">
                                    <span class="avatar-title bg-soft-info text-info rounded-circle fs-4">
                                        <i class="mdi mdi-account-group"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Kelas <?= esc($session['class_name']) ?></h6>
                                <?php if (isset($session['participant_count'])): ?>
                                    <p class="text-muted mb-0">
                                        <i class="mdi mdi-account-multiple me-1"></i><?= $session['participant_count'] ?> Peserta
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Problem Description -->
                <?php if ($session['problem_description']): ?>
                    <div class="mb-4 pb-3 border-bottom">
                        <h5 class="mb-3"><i class="mdi mdi-text-box-outline me-2"></i>Deskripsi Masalah/Topik</h5>
                        <p class="text-muted mb-0"><?= nl2br(esc($session['problem_description'])) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Session Summary -->
                <?php if ($session['session_summary']): ?>
                    <div class="mb-4 pb-3 border-bottom">
                        <h5 class="mb-3"><i class="mdi mdi-file-document-outline me-2"></i>Ringkasan Hasil Sesi</h5>
                        <p class="text-muted mb-0"><?= nl2br(esc($session['session_summary'])) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Follow-up Plan -->
                <?php if ($session['follow_up_plan']): ?>
                    <div class="mb-4 pb-3 border-bottom">
                        <h5 class="mb-3"><i class="mdi mdi-calendar-check me-2"></i>Rencana Tindak Lanjut</h5>
                        <p class="text-muted mb-0"><?= nl2br(esc($session['follow_up_plan'])) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Cancellation Reason -->
                <?php if ($session['status'] === 'Dibatalkan' && $session['cancellation_reason']): ?>
                    <div class="mb-4 pb-3 border-bottom">
                        <h5 class="mb-3 text-danger"><i class="mdi mdi-alert-circle-outline me-2"></i>Alasan Pembatalan</h5>
                        <div class="alert alert-danger mb-0">
                            <?= nl2br(esc($session['cancellation_reason'])) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Confidential Badge -->
                <?php if ($session['is_confidential']): ?>
                    <div class="mb-3">
                        <span class="badge bg-warning fs-6">
                            <i class="mdi mdi-lock me-1"></i>Sesi Konfidensial
                        </span>
                    </div>
                <?php endif; ?>

                <!-- Metadata -->
                <div class="mt-4 pt-3 border-top">
                    <div class="row text-muted">
                        <div class="col-md-6">
                            <small><i class="mdi mdi-clock-outline me-1"></i>Dibuat: <?= date('d M Y H:i', strtotime($session['created_at'])) ?></small>
                        </div>
                        <?php if ($session['updated_at']): ?>
                            <div class="col-md-6 text-md-end">
                                <small><i class="mdi mdi-update me-1"></i>Diperbarui: <?= date('d M Y H:i', strtotime($session['updated_at'])) ?></small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Session Notes -->
        <div class="card">
            <div class="card-header bg-success">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0 text-white">
                        <i class="mdi mdi-note-text-outline me-2"></i>Catatan Sesi
                    </h4>
                    <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                        <i class="mdi mdi-plus me-1"></i>Tambah Catatan
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($session['notes'])): ?>
                    <!-- Notes Timeline -->
                    <div class="timeline">
                        <?php foreach ($session['notes'] as $note): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1"><?= esc($note['counselor_name']) ?></h6>
                                            <small class="text-muted">
                                                <i class="mdi mdi-clock-outline me-1"></i>
                                                <?= date('d M Y H:i', strtotime($note['created_at'])) ?>
                                            </small>
                                        </div>
                                        <?php if ($note['is_important']): ?>
                                            <span class="badge bg-danger">
                                                <i class="mdi mdi-star"></i> Penting
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-muted mb-0"><?= nl2br(esc($note['note_content'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="avatar-lg mx-auto mb-3">
                            <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-1">
                                <i class="mdi mdi-note-text-outline"></i>
                            </span>
                        </div>
                        <h5 class="mb-2">Belum Ada Catatan</h5>
                        <p class="text-muted mb-3">Tambahkan catatan pertama untuk sesi ini</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                            <i class="mdi mdi-plus me-1"></i>Tambah Catatan
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
                    <?php if ($session['status'] !== 'Selesai'): ?>
                        <a href="<?= base_url('counselor/sessions/edit/' . $session['id']) ?>" class="btn btn-warning">
                            <i class="mdi mdi-pencil me-1"></i>Edit Sesi
                        </a>
                    <?php endif; ?>

                    <button type="button" class="btn btn-danger" onclick="deleteSession(<?= $session['id'] ?>)">
                        <i class="mdi mdi-delete me-1"></i>Hapus Sesi
                    </button>

                    <a href="<?= base_url('counselor/sessions') ?>" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i>Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>

        <!-- Counselor Info -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="mdi mdi-account-tie me-2"></i>Konselor</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-lg">
                            <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-3">
                                <?= strtoupper(substr($session['counselor_name'], 0, 2)) ?>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1"><?= esc($session['counselor_name']) ?></h6>
                        <p class="text-muted mb-0">
                            <small><?= esc($session['counselor_email']) ?></small>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <?php if (!empty($session['notes'])): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="mdi mdi-chart-line me-2"></i>Statistik</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Total Catatan</span>
                        <strong><?= count($session['notes']) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Catatan Penting</span>
                        <strong class="text-danger">
                            <?= count(array_filter($session['notes'], fn($n) => $n['is_important'])) ?>
                        </strong>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Note Modal - Will be included via separate file -->
<?= $this->include('counselor/sessions/add_note') ?>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    // Delete Session with Confirmation
    function deleteSession(id) {
        if (confirm('Apakah Anda yakin ingin menghapus sesi konseling ini?\n\nData yang terhapus tidak dapat dikembalikan!')) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= base_url('counselor/sessions/delete/') ?>' + id;

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

<?php $this->section('styles'); ?>
<style>
    /* Timeline Styles */
    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 30px;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-marker {
        position: absolute;
        left: -24px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 0 0 2px #e9ecef;
    }

    .timeline-content {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 3px solid #0ab39c;
    }

    /* Avatar Styles */
    .avatar-sm {
        width: 48px;
        height: 48px;
    }

    .avatar-md {
        width: 64px;
        height: 64px;
    }

    .avatar-lg {
        width: 80px;
        height: 80px;
    }

    .avatar-title {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }

    /* Soft Backgrounds */
    .bg-soft-primary {
        background-color: rgba(64, 81, 237, 0.1) !important;
    }

    .bg-soft-info {
        background-color: rgba(41, 156, 219, 0.1) !important;
    }

    .bg-soft-success {
        background-color: rgba(10, 179, 156, 0.1) !important;
    }

    .bg-soft-warning {
        background-color: rgba(249, 176, 20, 0.1) !important;
    }

    .bg-soft-danger {
        background-color: rgba(242, 82, 82, 0.1) !important;
    }
</style>
<?php $this->endSection(); ?>