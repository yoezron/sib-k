<?php

/**
 * File Path: app/Views/homeroom_teacher/reports/class_summary.php
 * * Class Summary Report View
 * Tampilan laporan kelas untuk wali kelas
 * * @package    SIB-K
 * @subpackage Views\HomeroomTeacher
 * @category   View
 * @author     Development Team
 * @created    2025-01-07
 * @version    1.1.0
 */

// --- Helper Function for Severity Badge ---
// Memusatkan logika untuk menentukan kelas badge berdasarkan tingkat pelanggaran.
// Ini membuat view lebih bersih dan mudah dikelola.
if (!function_exists('getSeverityBadgeClass')) {
    function getSeverityBadgeClass(string $severityLevel = ''): string
    {
        $map = [
            'Ringan' => 'bg-warning',
            'Sedang' => 'bg-orange', // Menggunakan kelas custom .bg-orange
            'Berat'  => 'bg-danger',
        ];
        return $map[$severityLevel] ?? 'bg-secondary';
    }
}


// --- Data Initialization ---
// Menggunakan null coalescing operator untuk memastikan variabel memiliki nilai default (array kosong atau nol).
// Ini mencegah error "undefined variable".
$report = $report ?? [];
$summary = $report['summary'] ?? [];
$studentStats = $report['student_stats'] ?? ['total' => 0];
$genderDistribution = $report['gender_distribution'] ?? ['L' => 0, 'P' => 0];
$violationStats = $report['violation_stats'] ?? ['total' => 0, 'ringan' => 0, 'sedang' => 0, 'berat' => 0];
$sessionStats = $report['session_stats'] ?? ['total' => 0, 'completed' => 0, 'scheduled' => 0];
$sessionAttendance = $report['session_attendance'] ?? ['attendance_rate' => 0, 'attended' => 0, 'total_sessions' => 0];
$studentsAtRisk = $report['students_at_risk'] ?? [];
$topViolations = $report['top_violations'] ?? [];
$recentViolations = $report['recent_violations'] ?? [];
$students = $report['students'] ?? [];
$violationTrend = $report['violation_trend'] ?? [];
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><?= esc($pageTitle ?? 'Laporan Kelas') ?></h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <?php if (!empty($breadcrumbs)): ?>
                                <?php foreach ($breadcrumbs as $breadcrumb): ?>
                                    <?php if (isset($breadcrumb['active']) && $breadcrumb['active']): ?>
                                        <li class="breadcrumb-item active"><?= esc($breadcrumb['title']) ?></li>
                                    <?php else: ?>
                                        <li class="breadcrumb-item">
                                            <a href="<?= esc($breadcrumb['url'] ?? '#', 'url') ?>"><?= esc($breadcrumb['title']) ?></a>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="card-title mb-2">
                                    <i class="mdi mdi-school text-primary me-2"></i>
                                    <?= esc($class['class_name'] ?? 'Nama Kelas') ?>
                                </h4>
                                <p class="text-muted mb-0">
                                    <i class="mdi mdi-calendar me-1"></i>
                                    T.A. <?= esc($class['year_name'] ?? '-') ?> - Semester <?= esc($class['semester'] ?? '-') ?>
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <div class="btn-group">
                                    <a href="<?= base_url('homeroom-teacher/reports/export-pdf') ?>" class="btn btn-danger">
                                        <i class="mdi mdi-file-pdf me-1"></i> Export PDF
                                    </a>
                                    <a href="<?= base_url('homeroom-teacher/reports/export-excel') ?>" class="btn btn-success">
                                        <i class="mdi mdi-file-excel me-1"></i> Export Excel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">Total Siswa</p>
                                <h4 class="mb-0"><?= number_format($studentStats['total']) ?></h4>
                            </div>
                            <div class="flex-shrink-0 avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                <span class="avatar-title rounded-circle bg-primary">
                                    <i class="mdi mdi-account-group font-size-24"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-success me-1"><i class="mdi mdi-gender-male"></i> <?= (int) $genderDistribution['L'] ?> Laki-laki</span>
                            <span class="badge bg-info"><i class="mdi mdi-gender-female"></i> <?= (int) $genderDistribution['P'] ?> Perempuan</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">Total Pelanggaran</p>
                                <h4 class="mb-0"><?= number_format($violationStats['total']) ?></h4>
                            </div>
                            <div class="flex-shrink-0 avatar-sm rounded-circle bg-danger align-self-center mini-stat-icon">
                                <span class="avatar-title rounded-circle bg-danger">
                                    <i class="mdi mdi-alert-circle font-size-24"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-warning me-1"><?= (int) $violationStats['ringan'] ?> Ringan</span>
                            <span class="badge bg-orange me-1"><?= (int) $violationStats['sedang'] ?> Sedang</span>
                            <span class="badge bg-danger"><?= (int) $violationStats['berat'] ?> Berat</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">Sesi Konseling</p>
                                <h4 class="mb-0"><?= number_format($sessionStats['total']) ?></h4>
                            </div>
                            <div class="flex-shrink-0 avatar-sm rounded-circle bg-info align-self-center mini-stat-icon">
                                <span class="avatar-title rounded-circle bg-info">
                                    <i class="mdi mdi-calendar-check font-size-24"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-success me-1"><?= (int) $sessionStats['completed'] ?> Selesai</span>
                            <span class="badge bg-warning"><?= (int) $sessionStats['scheduled'] ?> Terjadwal</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">Tingkat Penyelesaian Sesi</p>
                                <h4 class="mb-0"><?= number_format($sessionAttendance['attendance_rate'], 1) ?>%</h4>
                            </div>
                            <div class="flex-shrink-0 avatar-sm rounded-circle bg-success align-self-center mini-stat-icon">
                                <span class="avatar-title rounded-circle bg-success">
                                    <i class="mdi mdi-check-circle font-size-24"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                <?= (int) $sessionAttendance['attended'] ?> dari <?= (int) $sessionAttendance['total_sessions'] ?> sesi diselesaikan
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Distribusi Gender</h4>
                        <div class="chart-container">
                            <canvas id="genderChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Pelanggaran per Tingkat</h4>
                        <div class="chart-container">
                            <canvas id="severityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Tren Pelanggaran (6 Bulan)</h4>
                        <div class="chart-container">
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($studentsAtRisk)): ?>
            <div class="row">
                <div class="col-12">
                    <div class="card border-danger">
                        <div class="card-body">
                            <h4 class="card-title text-danger mb-3"><i class="mdi mdi-alert me-2"></i>Siswa Berisiko</h4>
                            <p class="card-subtitle text-muted mb-3">Siswa dengan poin pelanggaran tinggi (di atas atau sama dengan 50 poin).</p>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>NISN</th>
                                            <th>Nama Lengkap</th>
                                            <th class="text-center">L/P</th>
                                            <th class="text-center">Poin</th>
                                            <th class="text-center">Jml. Pelanggaran</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($studentsAtRisk as $index => $student): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= esc($student['nisn'] ?? '-') ?></td>
                                                <td>
                                                    <strong><?= esc($student['full_name'] ?? 'Nama Siswa') ?></strong>
                                                    <?php if (!empty($student['email'])): ?>
                                                        <br><small class="text-muted"><?= esc($student['email']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-<?= ($student['gender'] ?? '') === 'L' ? 'primary' : 'info' ?>">
                                                        <?= esc($student['gender'] ?? '-') ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-danger font-size-14"><?= number_format($student['violation_points'] ?? 0) ?></span>
                                                </td>
                                                <td class="text-center"><?= number_format($student['violation_count'] ?? 0) ?></td>
                                                <td class="text-center">
                                                    <a href="<?= base_url('homeroom-teacher/violations?student_id=' . esc($student['id'] ?? 0, 'url')) ?>" class="btn btn-sm btn-warning">
                                                        <i class="mdi mdi-eye me-1"></i>Detail
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

        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4"><i class="mdi mdi-chart-bar text-danger me-2"></i>Top 5 Pelanggaran</h4>
                        <?php if (!empty($topViolations)): ?>
                            <div class="table-responsive">
                                <table class="table table-centered table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Kategori Pelanggaran</th>
                                            <th>Tingkat</th>
                                            <th class="text-center">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($topViolations as $index => $violation): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= esc($violation['category_name'] ?? '-') ?></td>
                                                <td>
                                                    <span class="badge <?= getSeverityBadgeClass($violation['severity_level'] ?? '') ?>">
                                                        <?= esc($violation['severity_level'] ?? '-') ?>
                                                    </span>
                                                </td>
                                                <td class="text-center fw-bold"><?= number_format($violation['total_count'] ?? 0) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-center text-muted mt-4"><em>Tidak ada data pelanggaran untuk ditampilkan.</em></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4"><i class="mdi mdi-clock-outline text-warning me-2"></i>Pelanggaran Terbaru (30 Hari)</h4>
                        <div class="table-responsive recent-violations-container">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light" style="position: sticky; top: 0;">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Siswa</th>
                                        <th>Pelanggaran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recentViolations)): ?>
                                        <?php foreach ($recentViolations as $violation): ?>
                                            <tr>
                                                <td><small><?= date('d M Y', strtotime($violation['violation_date'] ?? '')) ?></small></td>
                                                <td><small><?= esc($violation['student_name'] ?? '-') ?></small></td>
                                                <td>
                                                    <small>
                                                        <?= esc($violation['category_name'] ?? '-') ?>
                                                        <span class="badge <?= getSeverityBadgeClass($violation['severity_level'] ?? '') ?> badge-sm">
                                                            <?= esc($violation['severity_level'] ?? '-') ?>
                                                        </span>
                                                    </small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">
                                                <em>Tidak ada pelanggaran dalam 30 hari terakhir.</em>
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

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4"><i class="mdi mdi-account-multiple text-primary me-2"></i>Daftar Lengkap Siswa</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover dt-responsive nowrap w-100" id="studentsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>NISN</th>
                                        <th>Nama Lengkap</th>
                                        <th class="text-center">L/P</th>
                                        <th class="text-center">Poin</th>
                                        <th class="text-center">Pelanggaran</th>
                                        <th class="text-center">Konseling</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($students)): ?>
                                        <?php foreach ($students as $index => $student): ?>
                                            <?php
                                            $violationPoints = $student['violation_points'] ?? 0;
                                            $pointClass = 'text-success';
                                            if ($violationPoints >= 50) $pointClass = 'text-danger';
                                            elseif ($violationPoints >= 25) $pointClass = 'text-warning';
                                            ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= esc($student['nisn'] ?? '-') ?></td>
                                                <td>
                                                    <a href="<?= base_url('homeroom-teacher/students/profile/' . esc($student['id'] ?? 0, 'url')) ?>" class="fw-bold">
                                                        <?= esc($student['full_name'] ?? 'Nama Siswa') ?>
                                                    </a>
                                                    <?php if (!empty($student['email'])): ?>
                                                        <br><small class="text-muted"><?= esc($student['email']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-<?= ($student['gender'] ?? '') === 'L' ? 'primary' : 'info' ?>">
                                                        <?= esc($student['gender'] ?? '-') ?>
                                                    </span>
                                                </td>
                                                <td class="text-center fw-bold <?= $pointClass ?>">
                                                    <?= number_format($violationPoints) ?>
                                                </td>
                                                <td class="text-center"><?= number_format($student['violation_count'] ?? 0) ?></td>
                                                <td class="text-center"><?= number_format($student['session_count'] ?? 0) ?></td>
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <a href="<?= base_url('homeroom-teacher/violations?student_id=' . esc($student['id'] ?? 0, 'url')) ?>" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Lihat Detail Pelanggaran">
                                                            <i class="mdi mdi-eye"></i>
                                                        </a>
                                                        <a href="<?= base_url('homeroom-teacher/counseling/create?student_id=' . esc($student['id'] ?? 0, 'url')) ?>" class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Jadwalkan Sesi Konseling">
                                                            <i class="mdi mdi-calendar-plus"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
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
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet" />

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

    /* Mendefinisikan kelas .bg-orange tanpa !important */
    .badge.bg-orange,
    .bg-orange {
        background-color: #fd7e14;
    }

    .badge-sm {
        font-size: 0.75rem;
        padding: 0.25rem 0.45rem;
    }

    .card {
        box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, .03);
        margin-bottom: 1.5rem;
    }

    .border-danger {
        border-left: 4px solid #f46a6a;
    }

    /* Memindahkan style inline ke dalam CSS block */
    .recent-violations-container {
        max-height: 400px;
        overflow-y: auto;
    }

    /* Memastikan chart responsif dan tidak keluar dari card */
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
    $(function() {
        'use strict';

        // 1. Initialize DataTable
        $('#studentsTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            },
            order: [
                [2, 'asc']
            ], // Sort by name
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "Semua"]
            ],
        });

        // 2. Initialize Bootstrap Tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // --- Charts Initialization ---

        // Opsi dasar untuk chart pie/doughnut untuk mengurangi duplikasi
        const commonPieOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20
                    }
                }
            }
        };

        // 3. Gender Distribution Chart (Doughnut)
        const genderCtx = document.getElementById('genderChart')?.getContext('2d');
        if (genderCtx) {
            new Chart(genderCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Laki-laki', 'Perempuan'],
                    datasets: [{
                        data: [<?= (int) ($genderDistribution['L'] ?? 0) ?>, <?= (int) ($genderDistribution['P'] ?? 0) ?>],
                        backgroundColor: ['#556ee6', '#34c38f'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: commonPieOptions
            });
        }

        // 4. Violation by Severity Chart (Pie)
        const severityCtx = document.getElementById('severityChart')?.getContext('2d');
        if (severityCtx) {
            new Chart(severityCtx, {
                type: 'pie',
                data: {
                    labels: ['Ringan', 'Sedang', 'Berat'],
                    datasets: [{
                        data: [
                            <?= (int) ($violationStats['ringan'] ?? 0) ?>,
                            <?= (int) ($violationStats['sedang'] ?? 0) ?>,
                            <?= (int) ($violationStats['berat'] ?? 0) ?>
                        ],
                        backgroundColor: ['#f1b44c', '#fd7e14', '#f46a6a'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: commonPieOptions
            });
        }

        // 5. Violation Trend Chart (Line)
        const trendCtx = document.getElementById('trendChart')?.getContext('2d');
        if (trendCtx) {
            const trendData = <?= json_encode($violationTrend) ?>;

            // Logika yang lebih robust untuk memformat label bulan dan tahun
            const trendLabels = trendData.map(item => {
                const monthKey = item.month; // format: "YYYY-MM"
                if (!monthKey || !/^\d{4}-\d{2}$/.test(monthKey)) {
                    return item.label || monthKey || 'N/A';
                }
                // Tambahkan hari agar menjadi string tanggal yang valid untuk parsing
                const date = new Date(monthKey + '-01T00:00:00');
                // Gunakan API Intl.DateTimeFormat untuk format nama bulan yang lebih baik
                return new Intl.DateTimeFormat('id-ID', {
                    month: 'short',
                    year: 'numeric'
                }).format(date);
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
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>