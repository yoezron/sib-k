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

<?php if (session()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="mdi mdi-block-helper me-2"></i>
        <?= session('error') ?>
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
                        <a href="<?= route_to('homeroom.violations.create') ?>" class="btn btn-primary">
                            <i class="bx bx-plus-circle me-1"></i> Catat Pelanggaran
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="<?= route_to('homeroom.violations.index') ?>" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="Dilaporkan" <?= ($filters['status'] ?? '') == 'Dilaporkan' ? 'selected' : '' ?>>Dilaporkan</option>
                                <option value="Dalam Proses" <?= ($filters['status'] ?? '') == 'Dalam Proses' ? 'selected' : '' ?>>Dalam Proses</option>
                                <option value="Selesai" <?= ($filters['status'] ?? '') == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                <option value="Dibatalkan" <?= ($filters['status'] ?? '') == 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tingkat Pelanggaran</label>
                            <select name="severity_level" class="form-select">
                                <option value="">Semua Tingkat</option>
                                <option value="Ringan" <?= ($filters['severity_level'] ?? '') == 'Ringan' ? 'selected' : '' ?>>Ringan</option>
                                <option value="Sedang" <?= ($filters['severity_level'] ?? '') == 'Sedang' ? 'selected' : '' ?>>Sedang</option>
                                <option value="Berat" <?= ($filters['severity_level'] ?? '') == 'Berat' ? 'selected' : '' ?>>Berat</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-select">
                                <option value="">Semua Kategori</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($filters['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                        <?= esc($cat['category_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Siswa</label>
                            <select name="student_id" class="form-select">
                                <option value="">Semua Siswa</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?= $student['id'] ?>" <?= ($filters['student_id'] ?? '') == $student['id'] ? 'selected' : '' ?>>
                                        <?= esc($student['full_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tanggal Dari</label>
                            <input type="date" name="date_from" class="form-control" value="<?= esc($filters['date_from'] ?? '') ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tanggal Sampai</label>
                            <input type="date" name="date_to" class="form-control" value="<?= esc($filters['date_to'] ?? '') ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Pencarian</label>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama, NISN, deskripsi..." value="<?= esc($filters['search'] ?? '') ?>">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label d-block">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bx bx-search me-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>

                <?php if (!empty(array_filter($filters, fn($v) => !empty($v) && $v !== $filters['class_id']))): ?>
                    <div class="mt-3">
                        <a href="<?= route_to('homeroom.violations.index') ?>" class="btn btn-sm btn-soft-secondary">
                            <i class="bx bx-x-circle me-1"></i> Reset Filter
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Violations Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="bx bx-list-ul me-1"></i> Daftar Pelanggaran
                    <span class="badge bg-primary ms-2"><?= count($violations) ?></span>
                </h4>
            </div>
            <div class="card-body">
                <?php if (empty($violations)): ?>
                    <div class="text-center py-5">
                        <div class="avatar-lg mx-auto mb-4">
                            <div class="avatar-title bg-soft-primary text-primary rounded-circle font-size-24">
                                <i class="mdi mdi-check-circle-outline"></i>
                            </div>
                        </div>
                        <h5 class="text-muted">Tidak ada data pelanggaran</h5>
                        <p class="text-muted">Belum ada pelanggaran yang tercatat untuk filter yang dipilih.</p>
                        <a href="<?= route_to('homeroom.violations.create') ?>" class="btn btn-primary mt-3">
                            <i class="bx bx-plus-circle me-1"></i> Catat Pelanggaran Baru
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Tanggal</th>
                                    <th>Siswa</th>
                                    <th>Kategori</th>
                                    <th>Tingkat</th>
                                    <th class="text-center">Poin</th>
                                    <th>Status</th>
                                    <th width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($violations as $index => $v): ?>
                                    <tr>
                                        <td class="text-center"><?= $index + 1 ?></td>
                                        <td>
                                            <span class="text-nowrap">
                                                <?= date('d/m/Y', strtotime($v['violation_date'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?= esc($v['student_name']) ?></strong>
                                                <div class="text-muted font-size-11"><?= esc($v['nisn']) ?></div>
                                            </div>
                                        </td>
                                        <td><?= esc($v['category_name']) ?></td>
                                        <td>
                                            <?php
                                            $badgeClass = match ($v['severity_level']) {
                                                'Ringan' => 'bg-info',
                                                'Sedang' => 'bg-warning',
                                                'Berat' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $badgeClass ?>">
                                                <?= esc($v['severity_level']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger rounded-pill">
                                                <?= $v['points'] ?> Poin
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $statusBadge = match ($v['status']) {
                                                'Dilaporkan' => 'bg-warning',
                                                'Dalam Proses' => 'bg-info',
                                                'Selesai' => 'bg-success',
                                                'Dibatalkan' => 'bg-secondary',
                                                default => 'bg-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $statusBadge ?>">
                                                <?= esc($v['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= route_to('homeroom.violations.detail', $v['id']) ?>"
                                                class="btn btn-sm btn-soft-primary"
                                                title="Lihat Detail">
                                                <i class="bx bx-show"></i>
                                            </a>
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

<?= $this->section('scripts') ?>
<script>
    // Auto submit filter on select change
    document.querySelectorAll('#filterForm select').forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });
</script>
<?= $this->endSection() ?>