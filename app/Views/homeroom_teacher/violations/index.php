<?php

/**
 * File Path: app/Views/homeroom_teacher/violations/index.php
 * 
 * Homeroom Teacher Violations Index View
 * Daftar pelanggaran dengan filter dan search
 * 
 * @package    SIB-K
 * @subpackage Views/HomeroomTeacher
 * @category   View
 * @author     Development Team
 * @created    2025-01-07
 */

$this->extend('layouts/main');
$this->section('content');
?>

<!-- Start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18"><?= esc($pageTitle) ?></h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <?php foreach ($breadcrumbs as $breadcrumb): ?>
                        <?php if (isset($breadcrumb['active']) && $breadcrumb['active']): ?>
                            <li class="breadcrumb-item active"><?= esc($breadcrumb['title']) ?></li>
                        <?php else: ?>
                            <li class="breadcrumb-item"><a href="<?= esc($breadcrumb['url']) ?>"><?= esc($breadcrumb['title']) ?></a></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- End page title -->

<!-- Alert Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="mdi mdi-check-circle me-2"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="mdi mdi-alert-circle me-2"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Class Info Card -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-1">
                            <i class="mdi mdi-google-classroom text-primary me-2"></i>
                            <?= esc($class['class_name']) ?>
                        </h5>
                        <p class="text-muted mb-0">
                            Tahun Ajaran <?= esc($class['year_name']) ?> - Semester <?= esc($class['semester']) ?>
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <a href="<?= base_url('homeroom/violations/create') ?>" class="btn btn-danger">
                            <i class="mdi mdi-plus-circle me-1"></i> Laporkan Pelanggaran Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-filter-variant me-2"></i>Filter Pelanggaran
                    </h5>
                    <button type="button" class="btn btn-sm btn-soft-secondary" id="toggleFilter">
                        <i class="mdi mdi-chevron-down"></i>
                    </button>
                </div>

                <div id="filterSection" style="display: none;">
                    <form method="GET" action="<?= base_url('homeroom/violations') ?>" id="filterForm">
                        <div class="row">
                            <!-- Student Filter -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Siswa</label>
                                <select name="student_id" class="form-select">
                                    <option value="">Semua Siswa</option>
                                    <?php foreach ($students as $student): ?>
                                        <option value="<?= $student['id'] ?>" <?= ($filters['student_id'] == $student['id']) ? 'selected' : '' ?>>
                                            <?= esc($student['nisn']) ?> - <?= esc($student['full_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Category Filter -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Kategori</label>
                                <select name="category_id" class="form-select">
                                    <option value="">Semua Kategori</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" <?= ($filters['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                            <?= esc($category['category_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Severity Filter -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Tingkat Keparahan</label>
                                <select name="severity_level" class="form-select">
                                    <option value="">Semua</option>
                                    <option value="Ringan" <?= ($filters['severity_level'] == 'Ringan') ? 'selected' : '' ?>>Ringan</option>
                                    <option value="Sedang" <?= ($filters['severity_level'] == 'Sedang') ? 'selected' : '' ?>>Sedang</option>
                                    <option value="Berat" <?= ($filters['severity_level'] == 'Berat') ? 'selected' : '' ?>>Berat</option>
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="Dilaporkan" <?= ($filters['status'] == 'Dilaporkan') ? 'selected' : '' ?>>Dilaporkan</option>
                                    <option value="Ditindaklanjuti" <?= ($filters['status'] == 'Ditindaklanjuti') ? 'selected' : '' ?>>Ditindaklanjuti</option>
                                    <option value="Selesai" <?= ($filters['status'] == 'Selesai') ? 'selected' : '' ?>>Selesai</option>
                                </select>
                            </div>

                            <!-- Start Date -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Dari Tanggal</label>
                                <input type="date" name="start_date" class="form-control" value="<?= esc($filters['start_date']) ?>">
                            </div>

                            <!-- End Date -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="date" name="end_date" class="form-control" value="<?= esc($filters['end_date']) ?>">
                            </div>

                            <!-- Search -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Pencarian</label>
                                <input type="text" name="search" class="form-control" placeholder="Cari siswa, NISN, kategori..." value="<?= esc($filters['search']) ?>">
                            </div>

                            <!-- Buttons -->
                            <div class="col-md-2 mb-3">
                                <label class="form-label d-block">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="mdi mdi-magnify me-1"></i> Filter
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <a href="<?= base_url('homeroom/violations') ?>" class="btn btn-sm btn-soft-secondary">
                                    <i class="mdi mdi-refresh me-1"></i> Reset Filter
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Violations Table -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-format-list-bulleted me-2"></i>
                        Daftar Pelanggaran (<?= count($violations) ?> data)
                    </h5>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-soft-success" id="exportExcel" disabled>
                            <i class="mdi mdi-file-excel me-1"></i> Excel
                        </button>
                        <button type="button" class="btn btn-sm btn-soft-danger" id="exportPDF" disabled>
                            <i class="mdi mdi-file-pdf me-1"></i> PDF
                        </button>
                    </div>
                </div>

                <?php if (!empty($violations)): ?>
                    <div class="table-responsive">
                        <table id="violationsTable" class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="12%">Tanggal</th>
                                    <th width="20%">Siswa</th>
                                    <th width="20%">Kategori</th>
                                    <th width="10%">Tingkat</th>
                                    <th width="8%">Poin</th>
                                    <th width="10%">Status</th>
                                    <th width="10%">Dilaporkan Oleh</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($violations as $index => $violation): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <div>
                                                <?= format_indo_short($violation['violation_date']) ?>
                                            </div>
                                            <?php if ($violation['violation_time']): ?>
                                                <small class="text-muted">
                                                    <i class="mdi mdi-clock-outline"></i>
                                                    <?= date('H:i', strtotime($violation['violation_time'])) ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-2">
                                                    <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                        <?= strtoupper(substr($violation['student_name'], 0, 1)) ?>
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 font-size-14"><?= esc($violation['student_name']) ?></h6>
                                                    <small class="text-muted"><?= esc($violation['nisn']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?= esc($violation['category_name']) ?>
                                            <?php if ($violation['location']): ?>
                                                <br><small class="text-muted">
                                                    <i class="mdi mdi-map-marker"></i> <?= esc($violation['location']) ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-soft-<?= $violation['severity_level'] === 'Berat' ? 'danger' : ($violation['severity_level'] === 'Sedang' ? 'warning' : 'info') ?>">
                                                <?= esc($violation['severity_level']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="point-indicator <?= $violation['point_deduction'] >= 50 ? 'high' : ($violation['point_deduction'] >= 25 ? 'medium' : 'low') ?>">
                                                <?= $violation['point_deduction'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge status-<?= strtolower($violation['status']) ?>">
                                                <?= esc($violation['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small><?= esc($violation['reported_by_name']) ?></small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('homeroom/violations/detail/' . $violation['id']) ?>"
                                                    class="btn btn-soft-info"
                                                    data-bs-toggle="tooltip"
                                                    title="Lihat Detail">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="mdi mdi-information-outline text-muted" style="font-size: 48px;"></i>
                        <h5 class="mt-3">Tidak Ada Data Pelanggaran</h5>
                        <p class="text-muted">
                            <?php if (!empty($filters['search']) || !empty($filters['student_id']) || !empty($filters['category_id'])): ?>
                                Tidak ada data yang sesuai dengan filter yang dipilih.
                            <?php else: ?>
                                Belum ada pelanggaran yang dilaporkan untuk kelas ini.
                            <?php endif; ?>
                        </p>
                        <a href="<?= base_url('homeroom/violations/create') ?>" class="btn btn-primary mt-2">
                            <i class="mdi mdi-plus-circle me-1"></i> Laporkan Pelanggaran
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<!-- DataTables -->
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        <?php if (!empty($violations)): ?>
            const table = $('#violationsTable').DataTable({
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Semua"]
                ],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },
                order: [
                    [1, 'desc']
                ], // Sort by date
                columnDefs: [{
                        orderable: false,
                        targets: [8]
                    } // Disable sorting on action column
                ]
            });
        <?php endif; ?>

        // Toggle filter section
        $('#toggleFilter').on('click', function() {
            $('#filterSection').slideToggle();
            const icon = $(this).find('i');
            icon.toggleClass('mdi-chevron-down mdi-chevron-up');
        });

        // Auto-show filter if any filter is active
        <?php if (!empty($filters['student_id']) || !empty($filters['category_id']) || !empty($filters['severity_level']) || !empty($filters['status']) || !empty($filters['start_date']) || !empty($filters['end_date']) || !empty($filters['search'])): ?>
            $('#filterSection').show();
            $('#toggleFilter i').removeClass('mdi-chevron-down').addClass('mdi-chevron-up');
        <?php endif; ?>

        // Export buttons (disabled - akan diaktifkan di FASE 7)
        $('#exportExcel, #exportPDF').on('click', function() {
            SIBK.showAlert('Fitur export akan tersedia di fase berikutnya', 'info');
        });

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
<?php $this->endSection(); ?>