<?php

/**
 * File Path: app/Views/homeroom_teacher/reports/class_summary.php
 * 
 * Class Summary Report View
 * Tampilan laporan kelas untuk wali kelas
 * 
 * @package    SIB-K
 * @subpackage Views\HomeroomTeacher
 * @category   View
 * @author     Development Team
 * @created    2025-01-07
 * @version    1.0.0
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-content">
    <div class="container-fluid">

        <!-- Page Title & Breadcrumb -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><?= esc($pageTitle) ?></h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <?php foreach ($breadcrumbs as $breadcrumb): ?>
                                <?php if (isset($breadcrumb['active']) && $breadcrumb['active']): ?>
                                    <li class="breadcrumb-item active"><?= esc($breadcrumb['title']) ?></li>
                                <?php else: ?>
                                    <li class="breadcrumb-item">
                                        <a href="<?= esc($breadcrumb['url']) ?>"><?= esc($breadcrumb['title']) ?></a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Class Info Header -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="card-title mb-2">
                                    <i class="mdi mdi-school text-primary me-2"></i>
                                    <?= esc($class['class_name']) ?>
                                </h4>
                                <p class="text-muted mb-0">
                                    <i class="mdi mdi-calendar me-1"></i>
                                    <?= esc($class['year_name']) ?> - Semester <?= esc($class['semester']) ?>
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <div class="btn-group">
                                    <a href="<?= base_url('homeroom-teacher/reports/export-pdf') ?>"
                                        class="btn btn-danger">
                                        <i class="mdi mdi-file-pdf me-1"></i> Export PDF
                                    </a>
                                    <a href="<?= base_url('homeroom-teacher/reports/export-excel') ?>"
                                        class="btn btn-success">
                                        <i class="mdi mdi-file-excel me-1"></i> Export Excel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <!-- Total Students -->
            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">Total Siswa</p>
                                <h4 class="mb-0"><?= number_format($report['student_stats']['total']) ?></h4>
                            </div>
                            <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                <span class="avatar-title rounded-circle bg-primary">
                                    <i class="mdi mdi-account-group font-size-24"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-success me-1">
                                <i class="mdi mdi-gender-male"></i>
                                <?= $report['gender_distribution']['L'] ?> L
                            </span>
                            <span class="badge bg-info">
                                <i class="mdi mdi-gender-female"></i>
                                <?= $report['gender_distribution']['P'] ?> P
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Violations -->
            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">Total Pelanggaran</p>
                                <h4 class="mb-0"><?= number_format($report['violation_stats']['total']) ?></h4>
                            </div>
                            <div class="avatar-sm rounded-circle bg-danger align-self-center mini-stat-icon">
                                <span class="avatar-title rounded-circle bg-danger">
                                    <i class="mdi mdi-alert-circle font-size-24"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-warning me-1"><?= $report['violation_stats']['ringan'] ?> Ringan</span>
                            <span class="badge bg-orange me-1"><?= $report['violation_stats']['sedang'] ?> Sedang</span>
                            <span class="badge bg-danger"><?= $report['violation_stats']['berat'] ?> Berat</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Counseling Sessions -->
            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">Sesi Konseling</p>
                                <h4 class="mb-0"><?= number_format($report['session_stats']['total']) ?></h4>
                            </div>
                            <div class="avatar-sm rounded-circle bg-info align-self-center mini-stat-icon">
                                <span class="avatar-title rounded-circle bg-info">
                                    <i class="mdi mdi-calendar-check font-size-24"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-success me-1"><?= $report['session_stats']['completed'] ?> Selesai</span>
                            <span class="badge bg-warning"><?= $report['session_stats']['scheduled'] ?> Terjadwal</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Rate -->
            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">Tingkat Kehadiran</p>
                                <h4 class="mb-0"><?= number_format($report['session_attendance']['attendance_rate'], 1) ?>%</h4>
                            </div>
                            <div class="avatar-sm rounded-circle bg-success align-self-center mini-stat-icon">
                                <span class="avatar-title rounded-circle bg-success">
                                    <i class="mdi mdi-check-circle font-size-24"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                <?= $report['session_attendance']['attended'] ?> dari <?= $report['session_attendance']['total_sessions'] ?> sesi
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <!-- Gender Distribution Chart -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Distribusi Gender</h4>
                        <canvas id="genderChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Violation by Severity Chart -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Pelanggaran per Tingkat</h4>
                        <canvas id="severityChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Violation Trend Chart -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Trend Pelanggaran (6 Bulan)</h4>
                        <canvas id="trendChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students at Risk -->
        <?php if (!empty($report['students_at_risk'])): ?>
            <div class="row">
                <div class="col-12">
                    <div class="card border-danger">
                        <div class="card-body">
                            <h4 class="card-title text-danger">
                                <i class="mdi mdi-alert me-2"></i>Siswa Berisiko
                            </h4>
                            <p class="text-muted">Siswa dengan poin pelanggaran tinggi (≥ 50 poin)</p>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="15%">NISN</th>
                                            <th>Nama Lengkap</th>
                                            <th width="10%" class="text-center">L/P</th>
                                            <th width="15%" class="text-center">Poin</th>
                                            <th width="15%" class="text-center">Pelanggaran</th>
                                            <th width="15%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($report['students_at_risk'] as $index => $student): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= esc($student['nisn']) ?></td>
                                                <td>
                                                    <strong><?= esc($student['full_name']) ?></strong>
                                                    <?php if ($student['email']): ?>
                                                        <br><small class="text-muted"><?= esc($student['email']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($student['gender'] == 'L'): ?>
                                                        <span class="badge bg-primary">L</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-info">P</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-danger font-size-14">
                                                        <?= number_format($student['violation_points']) ?> poin
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <?= $student['violation_count'] ?> kasus
                                                </td>
                                                <td>
                                                    <a href="<?= base_url('homeroom-teacher/violations?student_id=' . $student['id']) ?>"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="mdi mdi-eye me-1"></i>Lihat
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Top Violations -->
        <?php if (!empty($report['top_violations'])): ?>
            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">
                                <i class="mdi mdi-chart-bar text-danger me-2"></i>
                                Top 5 Pelanggaran
                            </h4>

                            <div class="table-responsive">
                                <table class="table table-centered table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Kategori Pelanggaran</th>
                                            <th width="20%">Tingkat</th>
                                            <th width="15%" class="text-center">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($report['top_violations'] as $index => $violation): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= esc($violation['category_name']) ?></td>
                                                <td>
                                                    <?php
                                                    $badgeClass = 'bg-secondary';
                                                    if ($violation['severity_level'] == 'Ringan') $badgeClass = 'bg-warning';
                                                    elseif ($violation['severity_level'] == 'Sedang') $badgeClass = 'bg-orange';
                                                    elseif ($violation['severity_level'] == 'Berat') $badgeClass = 'bg-danger';
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>">
                                                        <?= esc($violation['severity_level']) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <strong><?= $violation['total_count'] ?></strong>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Violations -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">
                                <i class="mdi mdi-clock-outline text-warning me-2"></i>
                                Pelanggaran Terbaru (30 Hari)
                            </h4>

                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Siswa</th>
                                            <th>Pelanggaran</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($report['recent_violations'])): ?>
                                            <?php foreach ($report['recent_violations'] as $violation): ?>
                                                <tr>
                                                    <td>
                                                        <small><?= date('d/m/Y', strtotime($violation['violation_date'])) ?></small>
                                                    </td>
                                                    <td>
                                                        <small><?= esc($violation['student_name']) ?></small>
                                                    </td>
                                                    <td>
                                                        <small>
                                                            <?= esc($violation['category_name']) ?>
                                                            <?php
                                                            $badgeClass = 'bg-secondary';
                                                            if ($violation['severity_level'] == 'Ringan') $badgeClass = 'bg-warning';
                                                            elseif ($violation['severity_level'] == 'Sedang') $badgeClass = 'bg-orange';
                                                            elseif ($violation['severity_level'] == 'Berat') $badgeClass = 'bg-danger';
                                                            ?>
                                                            <span class="badge <?= $badgeClass ?> badge-sm">
                                                                <?= esc($violation['severity_level']) ?>
                                                            </span>
                                                        </small>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">
                                                    <em>Tidak ada pelanggaran dalam 30 hari terakhir</em>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- All Students Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">
                            <i class="mdi mdi-account-multiple text-primary me-2"></i>
                            Daftar Lengkap Siswa
                        </h4>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover dt-responsive nowrap w-100" id="studentsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="12%">NISN</th>
                                        <th>Nama Lengkap</th>
                                        <th width="8%" class="text-center">L/P</th>
                                        <th width="10%" class="text-center">Poin</th>
                                        <th width="10%" class="text-center">Pelanggaran</th>
                                        <th width="10%" class="text-center">Konseling</th>
                                        <th width="10%">Status</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($report['students'])): ?>
                                        <?php foreach ($report['students'] as $index => $student): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= esc($student['nisn']) ?></td>
                                                <td>
                                                    <strong><?= esc($student['full_name']) ?></strong>
                                                    <?php if ($student['email']): ?>
                                                        <br><small class="text-muted"><?= esc($student['email']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($student['gender'] == 'L'): ?>
                                                        <span class="badge bg-primary">L</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-info">P</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php
                                                    $pointClass = 'text-success';
                                                    if ($student['violation_points'] >= 50) $pointClass = 'text-danger';
                                                    elseif ($student['violation_points'] >= 30) $pointClass = 'text-warning';
                                                    ?>
                                                    <strong class="<?= $pointClass ?>">
                                                        <?= number_format($student['violation_points']) ?>
                                                    </strong>
                                                </td>
                                                <td class="text-center"><?= $student['violation_count'] ?></td>
                                                <td class="text-center"><?= $student['session_count'] ?></td>
                                                <td>
                                                    <?php
                                                    $statusClass = 'bg-success';
                                                    if ($student['status'] != 'Aktif') $statusClass = 'bg-secondary';
                                                    ?>
                                                    <span class="badge <?= $statusClass ?>">
                                                        <?= esc($student['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="<?= base_url('homeroom-teacher/violations?student_id=' . $student['id']) ?>"
                                                            class="btn btn-sm btn-outline-primary"
                                                            data-bs-toggle="tooltip"
                                                            title="Lihat Pelanggaran">
                                                            <i class="mdi mdi-eye"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">
                                                <em>Tidak ada data siswa</em>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .mini-stats-wid .card-body {
        padding: 1.5rem;
    }

    .mini-stat-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .bg-orange {
        background-color: #fd7e14 !important;
    }

    .badge-sm {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }

    .card {
        box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, .03);
        margin-bottom: 1.5rem;
    }

    .border-danger {
        border-left: 4px solid #f46a6a !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<!-- DataTables -->
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#studentsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            },
            order: [
                [2, 'asc']
            ], // Sort by name
            pageLength: 25,
            responsive: true
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Gender Distribution Chart
        const genderCtx = document.getElementById('genderChart').getContext('2d');
        new Chart(genderCtx, {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    data: [
                        <?= $report['gender_distribution']['L'] ?>,
                        <?= $report['gender_distribution']['P'] ?>
                    ],
                    backgroundColor: ['#556ee6', '#f1b44c'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Violation by Severity Chart
        const severityCtx = document.getElementById('severityChart').getContext('2d');
        new Chart(severityCtx, {
            type: 'pie',
            data: {
                labels: ['Ringan', 'Sedang', 'Berat'],
                datasets: [{
                    data: [
                        <?= $report['violation_stats']['ringan'] ?>,
                        <?= $report['violation_stats']['sedang'] ?>,
                        <?= $report['violation_stats']['berat'] ?>
                    ],
                    backgroundColor: ['#f1b44c', '#fd7e14', '#f46a6a'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Violation Trend Chart
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        const trendData = <?= json_encode($report['violation_trend']) ?>;

        // Prepare data for chart
        const trendLabels = trendData.map(item => {
            const [year, month] = item.month.split('-');
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            return monthNames[parseInt(month) - 1] + ' ' + year;
        });
        const trendValues = trendData.map(item => item.total_count);

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Jumlah Pelanggaran',
                    data: trendValues,
                    borderColor: '#f46a6a',
                    backgroundColor: 'rgba(244, 106, 106, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>