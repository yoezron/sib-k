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

<!-- Class Info Card -->
<div class="row">
    <div class="col-12">
        <div class="card bg-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-md flex-shrink-0 me-4">
                        <span class="avatar-title bg-white bg-soft rounded-circle font-size-24">
                            <i class="bx bxs-graduation text-primary"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 text-white">
                        <h4 class="text-white mb-2"><?= esc($homeroom_class['class_name']) ?></h4>
                        <p class="mb-0">
                            <i class="bx bx-calendar me-1"></i>
                            <?= esc($homeroom_class['year_name']) ?> - Semester <?= esc($homeroom_class['semester']) ?>
                        </p>
                    </div>
                    <div>
                        <a href="<?= route_to('homeroom.violations.create') ?>" class="btn btn-light">
                            <i class="bx bx-plus-circle me-1"></i> Catat Pelanggaran
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
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium mb-2">Total Siswa</p>
                        <h4 class="mb-0"><?= $statistics['total_students'] ?></h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                            <span class="avatar-title rounded-circle bg-primary">
                                <i class="bx bx-user font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium mb-2">Pelanggaran Bulan Ini</p>
                        <h4 class="mb-0"><?= $statistics['violations_this_month'] ?></h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="avatar-sm rounded-circle bg-danger mini-stat-icon">
                            <span class="avatar-title rounded-circle bg-danger">
                                <i class="bx bx-error font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium mb-2">Konseling Bulan Ini</p>
                        <h4 class="mb-0"><?= $statistics['counseling_sessions'] ?></h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="avatar-sm rounded-circle bg-info mini-stat-icon">
                            <span class="avatar-title rounded-circle bg-info">
                                <i class="bx bx-conversation font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium mb-2">Rata-rata Poin</p>
                        <h4 class="mb-0"><?= $statistics['average_violation_points'] ?></h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="avatar-sm rounded-circle bg-warning mini-stat-icon">
                            <span class="avatar-title rounded-circle bg-warning">
                                <i class="bx bx-trending-up font-size-24"></i>
                            </span>
                        </div>
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
                    <a href="<?= route_to('homeroom.violations.index') ?>" class="btn btn-sm btn-soft-primary">
                        <i class="bx bx-list-ul align-middle"></i> Lihat Semua
                    </a>
                </div>
            </div>

            <div class="card-body">
                <?php if (empty($recent_violations)): ?>
                    <div class="text-center py-4">
                        <i class="bx bx-check-circle text-success font-size-48"></i>
                        <p class="text-muted mt-3 mb-0">Tidak ada pelanggaran baru</p>
                    </div>
                <?php else: ?>
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
                                <?php foreach ($recent_violations as $v): ?>
                                    <tr>
                                        <td>
                                            <span class="text-nowrap">
                                                <?= date('d/m/Y', strtotime($v['violation_date'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                <a href="<?= route_to('homeroom.violations.detail', $v['id']) ?>"
                                                    class="text-body fw-bold">
                                                    <?= esc($v['student_name']) ?>
                                                </a>
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
                                            <span class="badge bg-danger rounded-pill"><?= $v['points'] ?></span>
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
                                            <span class="badge <?= $statusBadge ?> font-size-11">
                                                <?= esc($v['status']) ?>
                                            </span>
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

    <!-- Students Need Attention -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Siswa Perlu Perhatian</h4>
            </div>

            <div class="card-body">
                <?php if (empty($students_need_attention)): ?>
                    <div class="text-center py-4">
                        <i class="bx bx-smile text-success font-size-36"></i>
                        <p class="text-muted mt-2 mb-0 font-size-13">Semua siswa dalam kondisi baik</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-borderless table-hover mb-0">
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
                <?php if (empty($top_violations)): ?>
                    <div class="text-center py-4">
                        <p class="text-muted">Belum ada data pelanggaran</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-borderless table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Kategori</th>
                                    <th>Tingkat</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Total Poin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_violations as $tv): ?>
                                    <tr>
                                        <td><?= esc($tv['category_name']) ?></td>
                                        <td>
                                            <?php
                                            $badgeClass = match ($tv['severity_level']) {
                                                'Ringan' => 'bg-info',
                                                'Sedang' => 'bg-warning',
                                                'Berat' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $badgeClass ?> font-size-11">
                                                <?= esc($tv['severity_level']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary"><?= $tv['violation_count'] ?>x</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger"><?= $tv['total_points'] ?></span>
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

    <!-- Upcoming Sessions -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Sesi Konseling Mendatang</h4>
            </div>

            <div class="card-body">
                <?php if (empty($upcoming_sessions)): ?>
                    <div class="text-center py-4">
                        <i class="bx bx-calendar-x text-muted font-size-36"></i>
                        <p class="text-muted mt-2 mb-0">Tidak ada sesi terjadwal</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-borderless table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Siswa</th>
                                    <th>Konselor</th>
                                    <th>Jenis</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcoming_sessions as $session): ?>
                                    <tr>
                                        <td>
                                            <span class="text-nowrap">
                                                <?= date('d/m/Y', strtotime($session['session_date'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?= esc($session['student_name']) ?></strong>
                                                <div class="text-muted font-size-11"><?= esc($session['nisn']) ?></div>
                                            </div>
                                        </td>
                                        <td><?= esc($session['counselor_name'] ?? 'N/A') ?></td>
                                        <td>
                                            <span class="badge bg-info font-size-11">
                                                <?= esc($session['session_type']) ?>
                                            </span>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Optional: Add chart for violation trend
        <?php if (!empty($monthly_violation_trend)): ?>
            const trendData = <?= json_encode($monthly_violation_trend) ?>;

            const labels = trendData.map(item => {
                const [year, month] = item.month.split('-');
                const date = new Date(year, month - 1);
                return date.toLocaleDateString('id-ID', {
                    month: 'short',
                    year: 'numeric'
                });
            });

            const values = trendData.map(item => item.violation_count);

            console.log('Trend data loaded:', {
                labels,
                values
            });
            // You can implement chart here using Chart.js or ApexCharts
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>