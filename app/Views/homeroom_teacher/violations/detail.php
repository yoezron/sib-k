<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18"><?= esc($pageTitle) ?></h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <?php foreach ($breadcrumbs as $crumb): ?>
                        <?php if (isset($crumb['active']) && $crumb['active']): ?>
                            <li class="breadcrumb-item active"><?= esc($crumb['title']) ?></li>
                        <?php else: ?>
                            <li class="breadcrumb-item"><a href="<?= esc($crumb['url']) ?>"><?= esc($crumb['title']) ?></a></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="mdi mdi-check-all me-2"></i>
        <?= session('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Class Info -->
<div class="row">
    <div class="col-12">
        <div class="card bg-primary bg-soft">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm flex-shrink-0 me-3">
                            <span class="avatar-title bg-primary rounded-circle font-size-18">
                                <i class="bx bxs-graduation"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="font-size-16 mb-1"><?= esc($homeroom_class['class_name']) ?></h5>
                            <p class="text-muted mb-0">
                                <i class="bx bx-calendar me-1"></i>
                                <?= esc($homeroom_class['year_name']) ?> - Semester <?= esc($homeroom_class['semester']) ?>
                            </p>
                        </div>
                    </div>
                    <div>
                        <a href="<?= route_to('homeroom.violations.index') ?>" class="btn btn-light">
                            <i class="bx bx-arrow-back me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Violation Details -->
    <div class="col-lg-8">
        <!-- Main Violation Info -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="bx bx-info-circle me-1"></i> Detail Pelanggaran
                </h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>Status:</strong>
                    </div>
                    <div class="col-sm-8">
                        <?php
                        $statusBadge = match ($violation['status']) {
                            'Dilaporkan' => 'bg-warning',
                            'Dalam Proses' => 'bg-info',
                            'Selesai' => 'bg-success',
                            'Dibatalkan' => 'bg-secondary',
                            default => 'bg-secondary'
                        };
                        ?>
                        <span class="badge <?= $statusBadge ?> font-size-13">
                            <?= esc($violation['status']) ?>
                        </span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>Tanggal Pelanggaran:</strong>
                    </div>
                    <div class="col-sm-8">
                        <?= date('d F Y', strtotime($violation['violation_date'])) ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>Kategori:</strong>
                    </div>
                    <div class="col-sm-8">
                        <?= esc($violation['category_name']) ?>
                        <div class="mt-1">
                            <?php
                            $severityBadge = match ($violation['severity_level']) {
                                'Ringan' => 'bg-info',
                                'Sedang' => 'bg-warning',
                                'Berat' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $severityBadge ?> me-2">
                                <?= esc($violation['severity_level']) ?>
                            </span>
                            <span class="badge bg-danger">
                                <?= $violation['category_points'] ?> Poin
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>Deskripsi:</strong>
                    </div>
                    <div class="col-sm-8">
                        <p class="mb-0"><?= nl2br(esc($violation['description'])) ?></p>
                    </div>
                </div>

                <?php if (!empty($violation['location'])): ?>
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong>Lokasi:</strong>
                        </div>
                        <div class="col-sm-8">
                            <?= esc($violation['location']) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($violation['witness'])): ?>
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong>Saksi:</strong>
                        </div>
                        <div class="col-sm-8">
                            <?= esc($violation['witness']) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($violation['evidence'])): ?>
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong>Bukti:</strong>
                        </div>
                        <div class="col-sm-8">
                            <a href="<?= base_url('uploads/violations/' . $violation['evidence']) ?>"
                                target="_blank"
                                class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-link-external me-1"></i> Lihat Bukti
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <hr class="my-4">

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>Dilaporkan Oleh:</strong>
                    </div>
                    <div class="col-sm-8">
                        <?= esc($violation['reporter_name'] ?? 'N/A') ?>
                        <?php if (!empty($violation['reporter_email'])): ?>
                            <div class="text-muted font-size-12">
                                <?= esc($violation['reporter_email']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>Ditangani Oleh:</strong>
                    </div>
                    <div class="col-sm-8">
                        <?= esc($violation['handler_name'] ?? 'Belum ditangani') ?>
                        <?php if (!empty($violation['handler_email'])): ?>
                            <div class="text-muted font-size-12">
                                <?= esc($violation['handler_email']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-4">
                        <strong>Dicatat Tanggal:</strong>
                    </div>
                    <div class="col-sm-8">
                        <span class="text-muted">
                            <?= date('d F Y, H:i', strtotime($violation['created_at'])) ?> WIB
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sanctions (if any) -->
        <?php if (!empty($sanctions)): ?>
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="bx bx-shield me-1"></i> Sanksi yang Diberikan
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Jenis Sanksi</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Diberikan Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sanctions as $sanction): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($sanction['sanction_type']) ?></strong>
                                            <?php if (!empty($sanction['description'])): ?>
                                                <div class="text-muted font-size-12">
                                                    <?= esc($sanction['description']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($sanction['sanction_date'])) ?></td>
                                        <td>
                                            <?php
                                            $sanctionBadge = match ($sanction['status']) {
                                                'Aktif' => 'bg-warning',
                                                'Selesai' => 'bg-success',
                                                'Dibatalkan' => 'bg-secondary',
                                                default => 'bg-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $sanctionBadge ?>">
                                                <?= esc($sanction['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= esc($sanction['assigned_by_name']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Column: Student Info & History -->
    <div class="col-lg-4">
        <!-- Student Information -->
        <div class="card">
            <div class="card-header bg-soft-primary">
                <h4 class="card-title mb-0">
                    <i class="bx bx-user me-1"></i> Informasi Siswa
                </h4>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="avatar-lg mx-auto mb-3">
                        <span class="avatar-title rounded-circle bg-primary text-white font-size-24">
                            <?= strtoupper(substr($violation['student_name'], 0, 1)) ?>
                        </span>
                    </div>
                    <h5 class="font-size-16 mb-1"><?= esc($violation['student_name']) ?></h5>
                    <p class="text-muted mb-0"><?= esc($violation['nisn']) ?></p>
                </div>

                <hr>

                <div class="mb-3">
                    <small class="text-muted">Jenis Kelamin:</small>
                    <p class="mb-0">
                        <?= $violation['gender'] == 'L' ? 'Laki-laki' : 'Perempuan' ?>
                    </p>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Total Poin Pelanggaran:</small>
                    <p class="mb-0">
                        <span class="badge bg-danger font-size-14">
                            <?= $violation['student_total_points'] ?> Poin
                        </span>
                    </p>
                </div>

                <div class="mb-0">
                    <small class="text-muted">Status:</small>
                    <p class="mb-0">
                        <span class="badge bg-success">Aktif</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Violation History -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="bx bx-history me-1"></i> Riwayat Pelanggaran
                </h4>
            </div>
            <div class="card-body">
                <?php if (empty($violation_history)): ?>
                    <div class="text-center py-3">
                        <i class="bx bx-check-circle text-success font-size-36"></i>
                        <p class="text-muted mb-0 mt-2">Tidak ada riwayat pelanggaran lain</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-hover mb-0">
                            <tbody>
                                <?php foreach ($violation_history as $history): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-start">
                                                <div class="flex-shrink-0 me-2">
                                                    <?php
                                                    $histBadge = match ($history['severity_level']) {
                                                        'Ringan' => 'bg-info',
                                                        'Sedang' => 'bg-warning',
                                                        'Berat' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                    ?>
                                                    <span class="badge <?= $histBadge ?> badge-sm">
                                                        <?= substr($history['severity_level'], 0, 1) ?>
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="font-size-12">
                                                        <strong><?= esc($history['category_name']) ?></strong>
                                                    </div>
                                                    <div class="text-muted font-size-11">
                                                        <?= date('d/m/Y', strtotime($history['violation_date'])) ?>
                                                        <span class="ms-2 badge bg-danger badge-sm">
                                                            <?= $history['points'] ?> poin
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>