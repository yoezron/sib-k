<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Dashboard Wali Kelas</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<!-- Class Info Banner -->
<div class="row">
    <div class="col-12">
        <div class="card bg-primary bg-soft">
            <div class="card-body">
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
                    <div class="flex-shrink-0">
                        <a href="<?= base_url('homeroom-teacher/reports/class-summary') ?>" class="btn btn-primary btn-sm">
                            <i class="bx bx-file me-1"></i> Lihat Laporan Lengkap
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block">Total Siswa</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?= $statistics['total_students'] ?>">0</span>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-success-subtle text-success">
                                <i class="bx bx-check-circle align-middle"></i>
                                <?= $statistics['violation_free_students'] ?> Bebas Pelanggaran
                            </span>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <div id="mini-chart1" data-colors='["--bs-primary"]' class="apex-charts mb-2"></div>
                        <span class="text-muted">Siswa Aktif</span>
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
                        <span class="text-muted mb-3 lh-1 d-block">Pelanggaran Bulan Ini</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?= $statistics['violations_this_month'] ?>">0</span>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-danger-subtle text-danger">
                                <i class="bx bx-user align-middle"></i>
                                <?= $statistics['students_with_violations'] ?> Siswa
                            </span>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <div id="mini-chart2" data-colors='["--bs-danger"]' class="apex-charts mb-2"></div>
                        <span class="text-muted">Total Kasus</span>
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
                        <span class="text-muted mb-3 lh-1 d-block">Sesi Konseling</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?= $statistics['counseling_sessions'] ?>">0</span>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-info-subtle text-info">
                                <i class="bx bx-calendar-event align-middle"></i>
                                Bulan Ini
                            </span>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <div id="mini-chart3" data-colors='["--bs-info"]' class="apex-charts mb-2"></div>
                        <span class="text-muted">Sesi Terjadwal</span>
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
                        <span class="text-muted mb-3 lh-1 d-block">Rata-rata Poin</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?= $statistics['average_violation_points'] ?>">0</span>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-warning-subtle text-warning">
                                <i class="bx bx-trending-up align-middle"></i>
                                Per Siswa
                            </span>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <div id="mini-chart4" data-colors='["--bs-warning"]' class="apex-charts mb-2"></div>
                        <span class="text-muted">Poin Pelanggaran</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Violations -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Pelanggaran Terbaru</h4>
                <div class="flex-shrink-0">
                    <a href="<?= base_url('homeroom-teacher/violations') ?>" class="btn btn-sm btn-soft-primary">
                        <i class="bx bx-list-ul align-middle"></i> Lihat Semua
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless table-hover table-nowrap align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Tanggal</th>
                                <th scope="col">Siswa</th>
                                <th scope="col">Kategori</th>
                                <th scope="col">Tingkat</th>
                                <th scope="col" class="text-center">Poin</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_violations)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bx bx-check-circle font-size-24 d-block mb-2"></i>
                                            Belum ada pelanggaran bulan ini
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recent_violations as $violation): ?>
                                    <tr>
                                        <td>
                                            <div class="font-size-13 text-muted">
                                                <?= date('d/m/Y', strtotime($violation['violation_date'])) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <h5 class="font-size-14 mb-0"><?= esc($violation['student_name']) ?></h5>
                                            <p class="text-muted mb-0 font-size-12"><?= esc($violation['nisn']) ?></p>
                                        </td>
                                        <td>
                                            <span class="text-dark"><?= esc($violation['category_name']) ?></span>
                                        </td>
                                        <td>
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

    <!-- Students Need Attention -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Siswa Perlu Perhatian</h4>
                <div class="flex-shrink-0">
                    <span class="badge bg-danger"><?= count($students_need_attention) ?></span>
                </div>
            </div>

            <div class="card-body">
                <?php if (empty($students_need_attention)): ?>
                    <div class="text-center py-4">
                        <i class="bx bx-smile font-size-24 text-success mb-2"></i>
                        <p class="text-muted mb-0">Semua siswa dalam kondisi baik</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive" style="max-height: 350px;">
                        <table class="table table-borderless table-sm mb-0">
                            <tbody>
                                <?php foreach ($students_need_attention as $student): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-3 flex-shrink-0">
                                                    <span class="avatar-title rounded-circle bg-danger bg-soft text-danger font-size-18">
                                                        <?= strtoupper(substr($student['full_name'], 0, 1)) ?>
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="font-size-13 mb-0"><?= esc($student['full_name']) ?></h5>
                                                    <p class="text-muted mb-0 font-size-11"><?= esc($student['nisn']) ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-danger rounded-pill"><?= $student['total_points'] ?> Poin</span>
                                            <div class="text-muted font-size-11"><?= $student['violation_count'] ?> kasus</div>
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

<div class="row">
    <!-- Top Violations Chart -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Kategori Pelanggaran Terbanyak</h4>
            </div>

            <div class="card-body">
                <div id="top-violations-chart" class="apex-charts" dir="ltr"></div>
            </div>
        </div>
    </div>

    <!-- Monthly Trend Chart -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Tren Pelanggaran 6 Bulan Terakhir</h4>
            </div>

            <div class="card-body">
                <div id="monthly-trend-chart" class="apex-charts" dir="ltr"></div>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Sessions -->
<?php if (!empty($upcoming_sessions)): ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Jadwal Konseling Mendatang</h4>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-hover table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Tanggal</th>
                                    <th scope="col">Waktu</th>
                                    <th scope="col">Tipe</th>
                                    <th scope="col">Siswa/Topik</th>
                                    <th scope="col">Konselor</th>
                                    <th scope="col">Lokasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcoming_sessions as $session): ?>
                                    <tr>
                                        <td><?= date('d M Y', strtotime($session['session_date'])) ?></td>
                                        <td><?= $session['session_time'] ? date('H:i', strtotime($session['session_time'])) : '-' ?></td>
                                        <td>
                                            <span class="badge bg-<?= $session['session_type'] === 'Individual' ? 'primary' : 'info' ?>-subtle text-<?= $session['session_type'] === 'Individual' ? 'primary' : 'info' ?>">
                                                <?= esc($session['session_type']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($session['session_type'] === 'Individual'): ?>
                                                <strong><?= esc($session['student_name']) ?></strong>
                                            <?php else: ?>
                                                <?= esc($session['topic']) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($session['counselor_name']) ?></td>
                                        <td><?= esc($session['location'] ?? '-') ?></td>
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

<!-- ApexCharts -->
<script src="<?= base_url('assets/libs/apexcharts/apexcharts.min.js') ?>"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Counter Animation
        document.querySelectorAll('.counter-value').forEach(function(element) {
            const target = parseInt(element.getAttribute('data-target'));
            let count = 0;
            const increment = target / 50;

            const timer = setInterval(function() {
                count += increment;
                if (count >= target) {
                    element.textContent = target;
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(count);
                }
            }, 20);
        });

        // Top Violations Chart
        const topViolationsData = <?= json_encode($top_violations) ?>;
        const categories = topViolationsData.map(v => v.category_name);
        const counts = topViolationsData.map(v => parseInt(v.violation_count));

        const topViolationsOptions = {
            series: [{
                name: 'Jumlah Pelanggaran',
                data: counts
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4,
                    distributed: true
                }
            },
            colors: ['#556ee6', '#f46a6a', '#34c38f', '#f1b44c', '#50a5f1'],
            dataLabels: {
                enabled: true
            },
            xaxis: {
                categories: categories
            },
            legend: {
                show: false
            }
        };

        const topViolationsChart = new ApexCharts(
            document.querySelector("#top-violations-chart"),
            topViolationsOptions
        );
        topViolationsChart.render();

        // Monthly Trend Chart
        const monthlyTrendData = <?= json_encode($monthly_violation_trend) ?>;
        const months = monthlyTrendData.map(t => t.month);
        const trendCounts = monthlyTrendData.map(t => parseInt(t.count));

        const monthlyTrendOptions = {
            series: [{
                name: 'Jumlah Pelanggaran',
                data: trendCounts
            }],
            chart: {
                type: 'area',
                height: 300,
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.1,
                }
            },
            colors: ['#f46a6a'],
            xaxis: {
                categories: months
            },
            yaxis: {
                title: {
                    text: 'Jumlah Kasus'
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " kasus";
                    }
                }
            }
        };

        const monthlyTrendChart = new ApexCharts(
            document.querySelector("#monthly-trend-chart"),
            monthlyTrendOptions
        );
        monthlyTrendChart.render();

        // Mini Charts (Sparklines)
        const sparklineOptions = {
            chart: {
                type: 'line',
                width: 80,
                height: 35,
                sparkline: {
                    enabled: true
                }
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            tooltip: {
                enabled: false
            }
        };

        // Mini Chart 1
        new ApexCharts(document.querySelector("#mini-chart1"), {
            ...sparklineOptions,
            series: [{
                data: [<?= $statistics['total_students'] ?>, <?= $statistics['violation_free_students'] ?>]
            }],
            colors: ['#556ee6']
        }).render();

        // Mini Chart 2
        new ApexCharts(document.querySelector("#mini-chart2"), {
            ...sparklineOptions,
            series: [{
                data: trendCounts.slice(-7)
            }],
            colors: ['#f46a6a']
        }).render();

        // Mini Chart 3
        new ApexCharts(document.querySelector("#mini-chart3"), {
            ...sparklineOptions,
            series: [{
                data: [<?= $statistics['counseling_sessions'] ?>, <?= count($upcoming_sessions) ?>]
            }],
            colors: ['#50a5f1']
        }).render();

        // Mini Chart 4
        new ApexCharts(document.querySelector("#mini-chart4"), {
            ...sparklineOptions,
            series: [{
                data: [<?= $statistics['average_violation_points'] ?>]
            }],
            colors: ['#f1b44c']
        }).render();
    });
</script>

<?= $this->endSection() ?>