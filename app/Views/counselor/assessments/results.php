<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="page-title mb-0">
                <i class="fas fa-chart-bar me-2"></i>
                Hasil Asesmen
            </h2>
            <p class="text-muted mb-0">
                <strong><?= esc($assessment['title']) ?></strong>
            </p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="<?= base_url('counselor/assessments/' . $assessment['id']) ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Kembali
            </a>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                            <i class="fas fa-users text-primary fa-2x"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total Peserta</h6>
                        <h3 class="mb-0"><?= $statistics['total_participants'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="fas fa-check-circle text-success fa-2x"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Selesai</h6>
                        <h3 class="mb-0"><?= $statistics['completed'] ?></h3>
                        <small class="text-muted">
                            <?= $statistics['total_participants'] > 0 ? round(($statistics['completed'] / $statistics['total_participants']) * 100, 1) : 0 ?>%
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3">
                            <i class="fas fa-chart-line text-info fa-2x"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Nilai Rata-rata</h6>
                        <h3 class="mb-0"><?= number_format($statistics['average_score'], 1) ?></h3>
                        <small class="text-muted">
                            Range: <?= number_format($statistics['lowest_score'], 1) ?> - <?= number_format($statistics['highest_score'], 1) ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                            <i class="fas fa-trophy text-warning fa-2x"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Tingkat Kelulusan</h6>
                        <h3 class="mb-0"><?= number_format($statistics['pass_rate'], 1) ?>%</h3>
                        <small class="text-muted">
                            <?= $statistics['passed'] ?> lulus / <?= $statistics['failed'] ?> tidak lulus
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="get" action="<?= base_url('counselor/assessments/' . $assessment['id'] . '/results') ?>">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status Pengerjaan</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="In Progress" <?= ($filters['status'] == 'In Progress') ? 'selected' : '' ?>>Sedang Dikerjakan</option>
                        <option value="Completed" <?= ($filters['status'] == 'Completed') ? 'selected' : '' ?>>Selesai</option>
                        <option value="Graded" <?= ($filters['status'] == 'Graded') ? 'selected' : '' ?>>Dinilai</option>
                        <option value="Expired" <?= ($filters['status'] == 'Expired') ? 'selected' : '' ?>>Kadaluarsa</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Kelas</label>
                    <select name="class_id" class="form-select">
                        <option value="">Semua Kelas</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= $class['id'] ?>" <?= ($filters['class_id'] == $class['id']) ? 'selected' : '' ?>>
                                <?= esc($class['class_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Hasil</label>
                    <select name="is_passed" class="form-select">
                        <option value="">Semua Hasil</option>
                        <option value="1" <?= ($filters['is_passed'] === '1') ? 'selected' : '' ?>>Lulus</option>
                        <option value="0" <?= ($filters['is_passed'] === '0') ? 'selected' : '' ?>>Tidak Lulus</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Pencarian</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Nama / NISN..." value="<?= esc($filters['search'] ?? '') ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter me-2"></i>Terapkan Filter
                </button>
                <a href="<?= base_url('counselor/assessments/' . $assessment['id'] . '/results') ?>" class="btn btn-secondary">
                    <i class="fas fa-redo me-2"></i>Reset
                </a>
                <button type="button" class="btn btn-success" onclick="exportToExcel()">
                    <i class="fas fa-file-excel me-2"></i>Export Excel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Results Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Hasil Siswa</h5>
            <?php if (!empty($results)): ?>
                <span class="badge bg-primary"><?= count($results) ?> Hasil</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (empty($results)): ?>
            <div class="text-center py-5">
                <i class="fas fa-chart-bar fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Belum ada hasil</h5>
                <p class="text-muted">Hasil akan muncul setelah siswa menyelesaikan asesmen</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="resultsTable">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Siswa</th>
                            <th>NISN</th>
                            <th>Kelas</th>
                            <th class="text-center">Percobaan</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Nilai</th>
                            <th class="text-center">Hasil</th>
                            <th class="text-center">Waktu Selesai</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($results as $result): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle me-2 d-flex align-items-center justify-content-center fw-bold">
                                            <?= strtoupper(substr($result['student_name'], 0, 2)) ?>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?= esc($result['student_name']) ?></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted"><?= esc($result['nisn']) ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($result['class_name'])): ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                            <?= esc($result['class_name']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">#<?= $result['attempt_number'] ?></span>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $statusBadge = [
                                        'In Progress' => 'warning',
                                        'Completed' => 'info',
                                        'Graded' => 'success',
                                        'Expired' => 'danger'
                                    ];
                                    $statusIcon = [
                                        'In Progress' => 'fa-spinner',
                                        'Completed' => 'fa-check',
                                        'Graded' => 'fa-check-double',
                                        'Expired' => 'fa-times'
                                    ];
                                    $badgeClass = $statusBadge[$result['status']] ?? 'secondary';
                                    $iconClass = $statusIcon[$result['status']] ?? 'fa-question';
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?>">
                                        <i class="fas <?= $iconClass ?> me-1"></i>
                                        <?= esc($result['status']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($result['status'] == 'Graded'): ?>
                                        <div class="fw-bold fs-5">
                                            <?= number_format($result['percentage'], 1) ?>%
                                        </div>
                                        <small class="text-muted">
                                            <?= number_format($result['total_score'], 1) ?> / <?= number_format($result['max_score'], 1) ?>
                                        </small>
                                    <?php elseif ($result['status'] == 'Completed'): ?>
                                        <span class="text-warning">
                                            <i class="fas fa-clock me-1"></i>Menunggu Penilaian
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($result['is_passed'] === 1): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Lulus
                                        </span>
                                    <?php elseif ($result['is_passed'] === 0): ?>
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle me-1"></i>Tidak Lulus
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($result['completed_at']): ?>
                                        <div class="small">
                                            <?= date('d/m/Y', strtotime($result['completed_at'])) ?>
                                        </div>
                                        <div class="small text-muted">
                                            <?= date('H:i', strtotime($result['completed_at'])) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('counselor/assessments/' . $assessment['id'] . '/results/' . $result['id']) ?>"
                                            class="btn btn-primary" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($result['status'] == 'Completed'): ?>
                                            <a href="<?= base_url('counselor/assessments/' . $assessment['id'] . '/results/' . $result['id'] . '/grade') ?>"
                                                class="btn btn-warning" title="Nilai Jawaban">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php endif; ?>
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

<!-- Score Distribution Chart -->
<?php if (!empty($results) && $statistics['completed'] > 0): ?>
    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2 text-primary"></i>
                        Distribusi Nilai
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="scoreDistributionChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2 text-primary"></i>
                        Status Pengerjaan
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    // Export to Excel functionality
    function exportToExcel() {
        const table = document.getElementById('resultsTable');
        let html = table.outerHTML;
        const url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
        const downloadLink = document.createElement("a");

        document.body.appendChild(downloadLink);
        downloadLink.href = url;
        downloadLink.download = '<?= esc($assessment['title']) ?>_results.xls';
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }

    // Initialize charts if Chart.js is available
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!empty($results) && $statistics['completed'] > 0): ?>

            // Calculate score distribution
            const scores = <?= json_encode(array_column(array_filter($results, function ($r) {
                                return $r['status'] == 'Graded';
                            }), 'percentage')) ?>;

            const ranges = {
                '0-20': 0,
                '21-40': 0,
                '41-60': 0,
                '61-80': 0,
                '81-100': 0
            };

            scores.forEach(score => {
                if (score <= 20) ranges['0-20']++;
                else if (score <= 40) ranges['21-40']++;
                else if (score <= 60) ranges['41-60']++;
                else if (score <= 80) ranges['61-80']++;
                else ranges['81-100']++;
            });

            // Score Distribution Chart
            const ctx1 = document.getElementById('scoreDistributionChart');
            if (ctx1 && typeof Chart !== 'undefined') {
                new Chart(ctx1, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(ranges),
                        datasets: [{
                            label: 'Jumlah Siswa',
                            data: Object.values(ranges),
                            backgroundColor: [
                                'rgba(220, 53, 69, 0.8)',
                                'rgba(253, 126, 20, 0.8)',
                                'rgba(255, 193, 7, 0.8)',
                                'rgba(13, 202, 240, 0.8)',
                                'rgba(25, 135, 84, 0.8)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
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
            }

            // Status Chart
            const ctx2 = document.getElementById('statusChart');
            if (ctx2 && typeof Chart !== 'undefined') {
                new Chart(ctx2, {
                    type: 'doughnut',
                    data: {
                        labels: ['Selesai & Dinilai', 'Sedang Dikerjakan', 'Belum Mulai'],
                        datasets: [{
                            data: [
                                <?= $statistics['completed'] ?>,
                                <?= $statistics['in_progress'] ?>,
                                <?= $statistics['total_participants'] - $statistics['completed'] - $statistics['in_progress'] ?>
                            ],
                            backgroundColor: [
                                'rgba(25, 135, 84, 0.8)',
                                'rgba(255, 193, 7, 0.8)',
                                'rgba(108, 117, 125, 0.8)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true
                    }
                });
            }

        <?php endif; ?>
    });
</script>

<style>
    .avatar-sm {
        width: 35px;
        height: 35px;
        font-size: 0.875rem;
    }

    .table> :not(caption)>*>* {
        padding: 0.75rem 0.5rem;
    }

    .btn-group-sm>.btn {
        padding: 0.25rem 0.5rem;
    }

    canvas {
        max-height: 300px;
    }
</style>

<?= $this->endSection() ?>