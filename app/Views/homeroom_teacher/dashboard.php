<?php

/**
 * File Path: app/Views/homeroom_teacher/dashboard.php
 * 
 * Homeroom Teacher Dashboard View
 * Dashboard utama untuk Wali Kelas
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

<?php if (!$hasClass): ?>
    <!-- No Class Assigned -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-alert-circle-outline text-warning" style="font-size: 64px;"></i>
                    <h4 class="mt-3">Belum Ada Kelas yang Ditugaskan</h4>
                    <p class="text-muted"><?= esc($message) ?></p>
                    <a href="<?= base_url('/') ?>" class="btn btn-primary mt-3">
                        <i class="mdi mdi-home me-1"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>

    <!-- Welcome Card -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card welcome-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="text-white mb-2">Selamat Datang, <?= esc($currentUser['full_name']) ?>!</h4>
                            <p class="text-white-50 mb-0">
                                Anda adalah Wali Kelas <strong><?= esc($class['class_name']) ?></strong> -
                                Tahun Ajaran <?= esc($class['year_name']) ?> Semester <?= esc($class['semester']) ?>
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="<?= base_url('homeroom/violations/create') ?>" class="btn btn-light">
                                <i class="mdi mdi-plus-circle me-1"></i> Laporkan Pelanggaran
                            </a>
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
                            <h4 class="mb-0 counter"><?= number_format($stats['total_students']) ?></h4>
                        </div>
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-soft-primary align-self-center">
                            <span class="avatar-title">
                                <i class="mdi mdi-account-group font-size-24 text-primary"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="mdi mdi-gender-male text-info"></i> <?= $stats['gender_distribution']['male'] ?? 0 ?> Laki-laki
                            <span class="mx-2">|</span>
                            <i class="mdi mdi-gender-female text-danger"></i> <?= $stats['gender_distribution']['female'] ?? 0 ?> Perempuan
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Violations This Month -->
        <div class="col-xl-3 col-md-6">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Pelanggaran Bulan Ini</p>
                            <h4 class="mb-0 counter"><?= number_format($stats['violations_this_month']) ?></h4>
                        </div>
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-soft-danger align-self-center">
                            <span class="avatar-title">
                                <i class="mdi mdi-alert-circle-outline font-size-24 text-danger"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <?php if ($stats['violation_trend'] === 'up'): ?>
                            <small class="text-danger">
                                <i class="mdi mdi-arrow-up"></i> <?= abs($stats['violation_change_percentage']) ?>% dari bulan lalu
                            </small>
                        <?php elseif ($stats['violation_trend'] === 'down'): ?>
                            <small class="text-success">
                                <i class="mdi mdi-arrow-down"></i> <?= abs($stats['violation_change_percentage']) ?>% dari bulan lalu
                            </small>
                        <?php else: ?>
                            <small class="text-muted">
                                <i class="mdi mdi-minus"></i> Tidak ada perubahan
                            </small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Violations This Week -->
        <div class="col-xl-3 col-md-6">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Pelanggaran Minggu Ini</p>
                            <h4 class="mb-0 counter"><?= number_format($stats['violations_this_week']) ?></h4>
                        </div>
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-soft-warning align-self-center">
                            <span class="avatar-title">
                                <i class="mdi mdi-alert font-size-24 text-warning"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">
                            <?= $stats['students_with_violations'] ?> siswa terlibat
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students in Counseling -->
        <div class="col-xl-3 col-md-6">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Dalam Konseling</p>
                            <h4 class="mb-0 counter"><?= number_format($stats['students_in_counseling']) ?></h4>
                        </div>
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-soft-success align-self-center">
                            <span class="avatar-title">
                                <i class="mdi mdi-account-voice font-size-24 text-success"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">
                            Siswa bulan ini
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Tables Row -->
    <div class="row">
        <!-- Violation Trends Chart -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="mdi mdi-chart-line text-primary me-2"></i>Tren Pelanggaran (6 Bulan Terakhir)
                    </h5>
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="violationTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Violation by Category -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="mdi mdi-chart-donut text-info me-2"></i>Pelanggaran per Kategori
                    </h5>
                    <div class="chart-container" style="height: 300px;">
                        <?php if (!empty($violationByCategory)): ?>
                            <canvas id="categoryChart"></canvas>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="mdi mdi-information-outline font-size-24 text-muted"></i>
                                <p class="text-muted mt-2">Belum ada data pelanggaran</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Violations and Top Violators Row -->
    <div class="row">
        <!-- Recent Violations -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-clipboard-alert text-danger me-2"></i>Pelanggaran Terbaru (7 Hari)
                        </h5>
                        <a href="<?= base_url('homeroom/violations') ?>" class="btn btn-sm btn-soft-primary">
                            Lihat Semua <i class="mdi mdi-arrow-right ms-1"></i>
                        </a>
                    </div>

                    <?php if (!empty($recentViolations)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Siswa</th>
                                        <th>Kategori</th>
                                        <th>Poin</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($recentViolations, 0, 5) as $violation): ?>
                                        <tr>
                                            <td>
                                                <small><?= format_indo_short($violation['violation_date']) ?></small>
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
                                                <span class="badge badge-soft-<?= $violation['severity_level'] === 'Berat' ? 'danger' : ($violation['severity_level'] === 'Sedang' ? 'warning' : 'info') ?>">
                                                    <?= esc($violation['category_name']) ?>
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
                                                <a href="<?= base_url('homeroom/violations/detail/' . $violation['id']) ?>"
                                                    class="btn btn-sm btn-soft-info"
                                                    data-bs-toggle="tooltip"
                                                    title="Lihat Detail">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="mdi mdi-emoticon-happy-outline text-success font-size-48"></i>
                            <p class="text-muted mt-2">Tidak ada pelanggaran dalam 7 hari terakhir</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Top Violators & Quick Actions -->
        <div class="col-xl-4">
            <!-- Top Violators -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="mdi mdi-account-alert text-warning me-2"></i>Siswa dengan Poin Tertinggi
                    </h5>

                    <?php if (!empty($topViolators)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($topViolators as $index => $student): ?>
                                <div class="list-group-item px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar-sm">
                                                <span class="avatar-title rounded-circle bg-soft-<?= $index === 0 ? 'danger' : 'warning' ?> text-<?= $index === 0 ? 'danger' : 'warning' ?> font-size-16 fw-bold">
                                                    #<?= $index + 1 ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 font-size-14"><?= esc($student['full_name']) ?></h6>
                                            <small class="text-muted"><?= esc($student['nisn']) ?></small>
                                        </div>
                                        <div class="flex-shrink-0 text-end">
                                            <h6 class="mb-0 text-<?= $student['total_points'] >= 75 ? 'danger' : 'warning' ?>">
                                                <?= $student['total_points'] ?> poin
                                            </h6>
                                            <small class="text-muted"><?= $student['violation_count'] ?> pelanggaran</small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="mdi mdi-emoticon-happy text-success font-size-36"></i>
                            <p class="text-muted mt-2 mb-0">Tidak ada data</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="mdi mdi-flash text-primary me-2"></i>Aksi Cepat
                    </h5>
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('homeroom/violations/create') ?>" class="btn btn-danger">
                            <i class="mdi mdi-alert-circle-outline me-1"></i> Laporkan Pelanggaran
                        </a>
                        <a href="<?= base_url('homeroom/violations') ?>" class="btn btn-info">
                            <i class="mdi mdi-format-list-bulleted me-1"></i> Lihat Semua Pelanggaran
                        </a>
                        <a href="<?= base_url('homeroom/reports') ?>" class="btn btn-success">
                            <i class="mdi mdi-file-chart me-1"></i> Lihat Laporan Kelas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        <?php if ($hasClass): ?>

            // Violation Trend Chart
            const violationTrendData = <?= json_encode($violationTrends) ?>;
            const trendLabels = violationTrendData.map(item => item.month);
            const trendCounts = violationTrendData.map(item => item.count);

            const ctx1 = document.getElementById('violationTrendChart');
            if (ctx1) {
                new Chart(ctx1, {
                    type: 'line',
                    data: {
                        labels: trendLabels,
                        datasets: [{
                            label: 'Jumlah Pelanggaran',
                            data: trendCounts,
                            borderColor: '#f46a6a',
                            backgroundColor: 'rgba(244, 106, 106, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#f46a6a',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
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
            }

            // Category Chart
            <?php if (!empty($violationByCategory)): ?>
                const categoryData = <?= json_encode($violationByCategory) ?>;
                const categoryLabels = categoryData.map(item => item.category_name);
                const categoryCounts = categoryData.map(item => item.count);

                const ctx2 = document.getElementById('categoryChart');
                if (ctx2) {
                    new Chart(ctx2, {
                        type: 'doughnut',
                        data: {
                            labels: categoryLabels,
                            datasets: [{
                                data: categoryCounts,
                                backgroundColor: [
                                    'rgba(244, 106, 106, 0.8)',
                                    'rgba(241, 180, 76, 0.8)',
                                    'rgba(80, 165, 241, 0.8)',
                                    'rgba(52, 195, 143, 0.8)',
                                    'rgba(85, 110, 230, 0.8)'
                                ],
                                borderWidth: 2,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 10,
                                        font: {
                                            size: 11
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            <?php endif; ?>

        <?php endif; ?>

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
<?php $this->endSection(); ?>