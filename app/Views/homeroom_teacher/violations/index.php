<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Daftar Pelanggaran Siswa</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('homeroom-teacher/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pelanggaran</li>
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
                        <a href="<?= base_url('homeroom-teacher/violations/create') ?>" class="btn btn-primary waves-effect waves-light">
                            <i class="bx bx-plus-circle font-size-16 align-middle me-2"></i>
                            Catat Pelanggaran
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-md-4">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium mb-2">Total Pelanggaran</p>
                        <h4 class="mb-0"><?= $statistics['total'] ?></h4>
                    </div>
                    <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                        <span class="avatar-title rounded-circle bg-primary">
                            <i class="bx bx-list-ul font-size-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium mb-2">Bulan Ini</p>
                        <h4 class="mb-0"><?= $statistics['this_month'] ?></h4>
                    </div>
                    <div class="avatar-sm rounded-circle bg-warning align-self-center mini-stat-icon">
                        <span class="avatar-title rounded-circle bg-warning">
                            <i class="bx bx-calendar font-size-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium mb-2">Per Tingkat</p>
                        <div class="d-flex gap-2">
                            <span class="badge bg-success-subtle text-success">
                                Ringan: <?= $statistics['severity']['Ringan'] ?>
                            </span>
                            <span class="badge bg-warning-subtle text-warning">
                                Sedang: <?= $statistics['severity']['Sedang'] ?>
                            </span>
                            <span class="badge bg-danger-subtle text-danger">
                                Berat: <?= $statistics['severity']['Berat'] ?>
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
                    <i class="bx bx-filter-alt"></i> Filter Pencarian
                </h4>
            </div>
            <div class="card-body">
                <form method="get" action="<?= base_url('homeroom-teacher/violations') ?>" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="Dilaporkan" <?= $filters['status'] == 'Dilaporkan' ? 'selected' : '' ?>>Dilaporkan</option>
                                <option value="Dalam Proses" <?= $filters['status'] == 'Dalam Proses' ? 'selected' : '' ?>>Dalam Proses</option>
                                <option value="Selesai" <?= $filters['status'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tingkat</label>
                            <select name="severity_level" class="form-select">
                                <option value="">Semua Tingkat</option>
                                <option value="Ringan" <?= $filters['severity_level'] == 'Ringan' ? 'selected' : '' ?>>Ringan</option>
                                <option value="Sedang" <?= $filters['severity_level'] == 'Sedang' ? 'selected' : '' ?>>Sedang</option>
                                <option value="Berat" <?= $filters['severity_level'] == 'Berat' ? 'selected' : '' ?>>Berat</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-select">
                                <option value="">Semua Kategori</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= $filters['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                        <?= esc($category['category_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Siswa</label>
                            <select name="student_id" class="form-select">
                                <option value="">Semua Siswa</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?= $student['id'] ?>" <?= $filters['student_id'] == $student['id'] ? 'selected' : '' ?>>
                                        <?= esc($student['full_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" name="date_from" class="form-control" value="<?= esc($filters['date_from'] ?? '') ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" name="date_to" class="form-control" value="<?= esc($filters['date_to'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Pencarian</label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Cari nama siswa, NISN, atau kategori..."
                                    value="<?= esc($filters['search'] ?? '') ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="bx bx-search-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                            <i class="bx bx-filter-alt me-1"></i> Terapkan Filter
                        </button>
                        <a href="<?= base_url('homeroom-teacher/violations') ?>" class="btn btn-secondary waves-effect">
                            <i class="bx bx-revision me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Violations Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Daftar Pelanggaran</h4>
                    <span class="badge bg-primary"><?= count($violations) ?> Data</span>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($violations)): ?>
                    <div class="text-center py-5">
                        <div class="avatar-md mx-auto mb-4">
                            <div class="avatar-title bg-soft-primary text-primary rounded-circle font-size-24">
                                <i class="bx bx-check-circle"></i>
                            </div>
                        </div>
                        <h5 class="mt-2">Belum Ada Data Pelanggaran</h5>
                        <p class="text-muted mb-0">Data pelanggaran akan muncul di sini setelah dicatat</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Tanggal</th>
                                    <th>Siswa</th>
                                    <th>Kategori</th>
                                    <th class="text-center">Tingkat</th>
                                    <th class="text-center">Poin</th>
                                    <th>Status</th>
                                    <th>Dilaporkan</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach ($violations as $violation): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <div class="font-size-13">
                                                <?= date('d/m/Y', strtotime($violation['violation_date'])) ?>
                                            </div>
                                            <small class="text-muted">
                                                <?= date('H:i', strtotime($violation['created_at'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs flex-shrink-0 me-2">
                                                    <span class="avatar-title rounded-circle bg-primary bg-soft text-primary font-size-14">
                                                        <?= strtoupper(substr($violation['student_name'], 0, 1)) ?>
                                                    </span>
                                                </div>
                                                <div>
                                                    <h5 class="font-size-14 mb-0"><?= esc($violation['student_name']) ?></h5>
                                                    <p class="text-muted mb-0 font-size-12"><?= esc($violation['nisn']) ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-dark"><?= esc($violation['category_name']) ?></span>
                                            <?php if ($violation['is_repeat_offender']): ?>
                                                <br><span class="badge bg-danger-subtle text-danger">
                                                    <i class="bx bx-error-circle"></i> Pelanggar Berulang
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            $severityClass = [
                                                'Ringan' => 'success',
                                                'Sedang' => 'warning',
                                                'Berat' => 'danger'
                                            ];
                                            $class = $severityClass[$violation['severity_level']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $class ?>-subtle text-<?= $class ?>">
                                                <?= esc($violation['severity_level']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger rounded-pill"><?= $violation['points'] ?></span>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = [
                                                'Dilaporkan' => 'warning',
                                                'Dalam Proses' => 'info',
                                                'Selesai' => 'success'
                                            ];
                                            $class = $statusClass[$violation['status']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $class ?>-subtle text-<?= $class ?>">
                                                <?= esc($violation['status']) ?>
                                            </span>
                                            <?php if ($violation['parent_notified']): ?>
                                                <br><small class="text-success">
                                                    <i class="bx bx-check"></i> Ortu diberitahu
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="font-size-12 text-muted">
                                                <?= esc($violation['reported_by_name'] ?? 'System') ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('homeroom-teacher/violations/' . $violation['id']) ?>"
                                                class="btn btn-sm btn-soft-primary" title="Lihat Detail">
                                                <i class="bx bx-show-alt"></i>
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

<script>
    // Auto-dismiss alerts
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });

    // Quick filter toggle
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        // Auto-submit on select change (optional)
        const selects = filterForm.querySelectorAll('select');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                // Uncomment to enable auto-submit
                // filterForm.submit();
            });
        });
    }
</script>

<style>
    .mini-stats-wid .mini-stat-icon {
        overflow: hidden;
        position: relative;
    }

    .mini-stats-wid .mini-stat-icon::after {
        content: "";
        position: absolute;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        top: -50%;
        left: -50%;
    }

    .table> :not(caption)>*>* {
        padding: 0.75rem 0.75rem;
    }

    .avatar-xs {
        width: 2rem;
        height: 2rem;
    }

    .avatar-title {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }
</style>

<?= $this->endSection() ?>