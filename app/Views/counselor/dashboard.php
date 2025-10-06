<?php

/**
 * File Path: app/Views/counselor/dashboard.php
 * 
 * Counselor Dashboard View
 * Dashboard untuk Guru BK dengan statistik, chart, dan data sesi konseling
 * 
 * @package    SIB-K
 * @subpackage Views/Counselor
 * @category   Dashboard
 * @author     Development Team
 * @created    2025-01-06
 */

$this->extend('layouts/main');
$this->section('content');
?>

<!-- Welcome Section -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Selamat Datang, <?= esc(auth_name()) ?>!</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('counselor/dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <!-- Total Sessions -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Sesi</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?= $stats['total_sessions'] ?? 0 ?>">0</span>
                        </h4>
                    </div>
                    <div class="flex-shrink-0 text-end dash-widget">
                        <div class="avatar-sm rounded-circle bg-soft-primary">
                            <span class="avatar-title bg-primary rounded-circle">
                                <i class="mdi mdi-calendar-check font-size-24 text-white"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sessions Today -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Sesi Hari Ini</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?= $stats['sessions_today'] ?? 0 ?>">0</span>
                        </h4>
                    </div>
                    <div class="flex-shrink-0 text-end dash-widget">
                        <div class="avatar-sm rounded-circle bg-soft-success">
                            <span class="avatar-title bg-success rounded-circle">
                                <i class="mdi mdi-clock-outline font-size-24 text-white"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sessions This Month -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Sesi Bulan Ini</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?= $stats['sessions_this_month'] ?? 0 ?>">0</span>
                        </h4>
                    </div>
                    <div class="flex-shrink-0 text-end dash-widget">
                        <div class="avatar-sm rounded-circle bg-soft-info">
                            <span class="avatar-title bg-info rounded-circle">
                                <i class="mdi mdi-calendar-month font-size-24 text-white"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Sessions -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Sesi Mendatang</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?= $stats['upcoming_sessions'] ?? 0 ?>">0</span>
                        </h4>
                    </div>
                    <div class="flex-shrink-0 text-end dash-widget">
                        <div class="avatar-sm rounded-circle bg-soft-warning">
                            <span class="avatar-title bg-warning rounded-circle">
                                <i class="mdi mdi-calendar-clock font-size-24 text-white"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Chart: Session Trends -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Tren Sesi Konseling (6 Bulan Terakhir)</h4>
            </div>
            <div class="card-body">
                <canvas id="sessionTrendChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Quick Actions</h4>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= base_url('counselor/sessions/create') ?>" class="btn btn-primary">
                        <i class="mdi mdi-plus-circle me-1"></i> Tambah Sesi Baru
                    </a>
                    <a href="<?= base_url('counselor/sessions') ?>" class="btn btn-outline-primary">
                        <i class="mdi mdi-calendar-check me-1"></i> Lihat Semua Sesi
                    </a>
                    <a href="<?= base_url('counselor/schedule') ?>" class="btn btn-outline-info">
                        <i class="mdi mdi-calendar me-1"></i> Jadwal Konseling
                    </a>
                    <a href="<?= base_url('counselor/cases') ?>" class="btn btn-outline-warning">
                        <i class="mdi mdi-alert-circle me-1"></i> Kelola Kasus
                    </a>
                    <a href="<?= base_url('counselor/reports') ?>" class="btn btn-outline-success">
                        <i class="mdi mdi-file-chart me-1"></i> Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Today's Sessions -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Sesi Hari Ini</h4>
                <div class="flex-shrink-0">
                    <span class="badge bg-primary"><?= count($todaySessions ?? []) ?> Sesi</span>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($todaySessions)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Waktu</th>
                                    <th>Siswa/Topik</th>
                                    <th>Jenis</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($todaySessions as $session): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-semibold">
                                                <?= $session['session_time'] ? date('H:i', strtotime($session['session_time'])) : '-' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                <h6 class="mb-0"><?= esc($session['topic']) ?></h6>
                                                <?php if ($session['student_name']): ?>
                                                    <small class="text-muted"><?= esc($session['student_name']) ?></small>
                                                <?php elseif ($session['class_name']): ?>
                                                    <small class="text-muted">Kelas <?= esc($session['class_name']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $typeClass = match ($session['session_type']) {
                                                'Individu' => 'info',
                                                'Kelompok' => 'warning',
                                                'Klasikal' => 'primary',
                                                default => 'secondary'
                                            };
                                            ?>
                                            <span class="badge bg-<?= $typeClass ?>"><?= esc($session['session_type']) ?></span>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = match ($session['status']) {
                                                'Dijadwalkan' => 'warning',
                                                'Selesai' => 'success',
                                                'Dibatalkan' => 'danger',
                                                default => 'secondary'
                                            };
                                            ?>
                                            <span class="badge bg-<?= $statusClass ?>"><?= esc($session['status']) ?></span>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('counselor/sessions/detail/' . $session['id']) ?>"
                                                class="btn btn-sm btn-soft-primary">
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
                        <i class="mdi mdi-calendar-blank text-muted" style="font-size: 48px;"></i>
                        <p class="text-muted mt-2">Tidak ada sesi konseling hari ini</p>
                        <a href="<?= base_url('counselor/sessions/create') ?>" class="btn btn-sm btn-primary">
                            <i class="mdi mdi-plus"></i> Buat Sesi Baru
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Upcoming Sessions -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Sesi Mendatang (7 Hari)</h4>
                <div class="flex-shrink-0">
                    <span class="badge bg-warning"><?= count($upcomingSessions ?? []) ?> Sesi</span>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($upcomingSessions)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($upcomingSessions as $session): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-sm">
                                            <span class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                <?= date('d', strtotime($session['session_date'])) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h6 class="mb-1 text-truncate"><?= esc($session['topic']) ?></h6>
                                        <p class="text-muted text-truncate mb-2">
                                            <i class="mdi mdi-calendar me-1"></i>
                                            <?= date('d M Y', strtotime($session['session_date'])) ?>
                                            <?php if ($session['session_time']): ?>
                                                | <i class="mdi mdi-clock-outline me-1"></i>
                                                <?= date('H:i', strtotime($session['session_time'])) ?>
                                            <?php endif; ?>
                                        </p>
                                        <?php if ($session['student_name']): ?>
                                            <small class="text-muted">
                                                <i class="mdi mdi-account me-1"></i><?= esc($session['student_name']) ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="<?= base_url('counselor/sessions/detail/' . $session['id']) ?>"
                                            class="btn btn-sm btn-soft-info">
                                            Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="mdi mdi-calendar-check text-muted" style="font-size: 48px;"></i>
                        <p class="text-muted mt-2">Tidak ada sesi mendatang</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Assigned Students -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Siswa Binaan</h4>
                <div class="flex-shrink-0">
                    <span class="badge bg-success"><?= count($assignedStudents ?? []) ?> Siswa</span>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($assignedStudents)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>NISN</th>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                    <th>Sesi</th>
                                    <th>Poin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($assignedStudents, 0, 5) as $student): ?>
                                    <tr>
                                        <td><?= esc($student['nisn']) ?></td>
                                        <td>
                                            <h6 class="mb-0"><?= esc($student['student_name']) ?></h6>
                                        </td>
                                        <td><?= esc($student['class_name'] ?? '-') ?></td>
                                        <td>
                                            <span class="badge bg-info"><?= $student['total_sessions'] ?? 0 ?></span>
                                        </td>
                                        <td>
                                            <?php
                                            $points = $student['total_violation_points'] ?? 0;
                                            $pointClass = $points > 50 ? 'danger' : ($points > 20 ? 'warning' : 'success');
                                            ?>
                                            <span class="badge bg-<?= $pointClass ?>"><?= $points ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (count($assignedStudents) > 5): ?>
                        <div class="text-center mt-3">
                            <a href="<?= base_url('counselor/students') ?>" class="btn btn-sm btn-link">
                                Lihat Semua Siswa <i class="mdi mdi-arrow-right"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="mdi mdi-account-group text-muted" style="font-size: 48px;"></i>
                        <p class="text-muted mt-2">Belum ada siswa binaan</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Activities & Pending Follow-ups -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs-custom card-header-tabs border-bottom-0" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#activities" role="tab">
                            Aktivitas Terbaru
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#pending" role="tab">
                            Perlu Follow-up
                            <?php if (!empty($pendingSessions)): ?>
                                <span class="badge bg-danger rounded-pill"><?= count($pendingSessions) ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Activities Tab -->
                    <div class="tab-pane active" id="activities" role="tabpanel">
                        <?php if (!empty($recentActivities)): ?>
                            <ul class="list-unstyled activity-wid mb-0">
                                <?php foreach ($recentActivities as $activity): ?>
                                    <li class="activity-list activity-border">
                                        <div class="activity-icon avatar-xs">
                                            <span class="avatar-title bg-soft-<?= $activity['color'] ?> text-<?= $activity['color'] ?> rounded-circle">
                                                <i class="mdi <?= $activity['icon'] ?>"></i>
                                            </span>
                                        </div>
                                        <div class="timeline-list-item">
                                            <div class="d-flex">
                                                <div class="flex-grow-1 overflow-hidden me-4">
                                                    <h6 class="font-size-14 mb-1"><?= esc($activity['title']) ?></h6>
                                                    <p class="text-truncate text-muted mb-0"><?= esc($activity['description']) ?></p>
                                                </div>
                                                <div class="flex-shrink-0 text-end">
                                                    <small class="text-muted"><?= esc($activity['time']) ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <p class="text-muted">Belum ada aktivitas terbaru</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pending Follow-ups Tab -->
                    <div class="tab-pane" id="pending" role="tabpanel">
                        <?php if (!empty($pendingSessions)): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($pendingSessions as $session): ?>
                                    <div class="list-group-item px-0">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1"><?= esc($session['topic']) ?></h6>
                                                <p class="text-muted mb-1">
                                                    <small>
                                                        <i class="mdi mdi-calendar me-1"></i>
                                                        <?= date('d M Y', strtotime($session['session_date'])) ?>
                                                    </small>
                                                </p>
                                                <?php if ($session['student_name']): ?>
                                                    <small class="text-muted">
                                                        <i class="mdi mdi-account me-1"></i><?= esc($session['student_name']) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <a href="<?= base_url('counselor/sessions/detail/' . $session['id']) ?>"
                                                    class="btn btn-sm btn-soft-warning">
                                                    <i class="mdi mdi-bell-ring"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="mdi mdi-check-all text-success" style="font-size: 48px;"></i>
                                <p class="text-muted mt-2">Semua sesi sudah ditindaklanjuti</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // Counter animation
    document.addEventListener('DOMContentLoaded', function() {
        const counters = document.querySelectorAll('.counter-value');

        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            const duration = 1000;
            const increment = target / (duration / 16);
            let current = 0;

            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    counter.textContent = Math.floor(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target;
                }
            };

            updateCounter();
        });
    });

    // Session Trend Chart
    const chartData = <?= json_encode($chartData ?? ['labels' => [], 'individual' => [], 'group' => [], 'class' => []]) ?>;

    const ctx = document.getElementById('sessionTrendChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                        label: 'Individu',
                        data: chartData.individual,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Kelompok',
                        data: chartData.group,
                        borderColor: 'rgb(255, 205, 86)',
                        backgroundColor: 'rgba(255, 205, 86, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Klasikal',
                        data: chartData.class,
                        borderColor: 'rgb(54, 162, 235)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    title: {
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
    }

    // Auto-refresh stats every 5 minutes (optional)
    setInterval(function() {
        fetch('<?= base_url('counselor/dashboard/getQuickStats') ?>', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update counter values
                    document.querySelectorAll('.counter-value').forEach((el, index) => {
                        const keys = ['total_sessions', 'sessions_today', 'sessions_this_month', 'upcoming_sessions'];
                        if (keys[index]) {
                            el.setAttribute('data-target', data.data[keys[index]] || 0);
                            el.textContent = data.data[keys[index]] || 0;
                        }
                    });
                }
            })
            .catch(error => console.error('Error refreshing stats:', error));
    }, 300000); // 5 minutes
</script>
<?php $this->endSection(); ?>