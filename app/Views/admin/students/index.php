<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
/**
 * File Path: app/Views/admin/students/index.php
 * 
 * Students List View
 * Menampilkan daftar siswa dengan filter dan actions
 * 
 * @package    SIB-K
 * @subpackage Views/Admin/Students
 * @category   Student Management
 * @author     Development Team
 * @created    2025-01-05
 */
?>

<!-- Start Page Content -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Manajemen Siswa</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Admin</a></li>
                    <li class="breadcrumb-item active">Siswa</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="mdi mdi-check-circle me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="mdi mdi-alert-circle me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-md-3">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Total Siswa</p>
                        <h4 class="mb-0"><?= number_format($stats['total']) ?></h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                            <span class="avatar-title">
                                <i class="mdi mdi-school font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Siswa Aktif</p>
                        <h4 class="mb-0"><?= number_format($stats['active']) ?></h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                            <span class="avatar-title">
                                <i class="mdi mdi-account-check font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Alumni</p>
                        <h4 class="mb-0"><?= number_format($stats['alumni']) ?></h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                            <span class="avatar-title">
                                <i class="mdi mdi-school-outline font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Pindah/Keluar</p>
                        <h4 class="mb-0"><?= number_format($stats['moved'] + $stats['dropped']) ?></h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                            <span class="avatar-title">
                                <i class="mdi mdi-account-off font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Students Table Card -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Header with Action Buttons -->
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h4 class="card-title">
                            <i class="mdi mdi-account-group me-2"></i>Daftar Siswa
                        </h4>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-sm-end">
                            <a href="<?= base_url('admin/students/create') ?>" class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2">
                                <i class="mdi mdi-plus me-1"></i> Tambah Siswa
                            </a>
                            <a href="<?= base_url('admin/students/import') ?>" class="btn btn-info btn-rounded waves-effect waves-light mb-2 me-2">
                                <i class="mdi mdi-upload me-1"></i> Import
                            </a>
                            <a href="<?= base_url('admin/students/export') . '?' . http_build_query($filters) ?>" class="btn btn-primary btn-rounded waves-effect waves-light mb-2">
                                <i class="mdi mdi-download me-1"></i> Export CSV
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Filter Form -->
                <div class="row mb-3">
                    <div class="col-12">
                        <form action="<?= base_url('admin/students') ?>" method="get" class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">Kelas</label>
                                <select name="class_id" class="form-select form-select-sm">
                                    <option value="">Semua Kelas</option>
                                    <?php foreach ($classes as $class): ?>
                                        <option value="<?= $class['id'] ?>" <?= $filters['class_id'] == $class['id'] ? 'selected' : '' ?>>
                                            <?= esc($class['grade_level']) ?> - <?= esc($class['class_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tingkat</label>
                                <select name="grade_level" class="form-select form-select-sm">
                                    <option value="">Semua Tingkat</option>
                                    <option value="X" <?= $filters['grade_level'] == 'X' ? 'selected' : '' ?>>X</option>
                                    <option value="XI" <?= $filters['grade_level'] == 'XI' ? 'selected' : '' ?>>XI</option>
                                    <option value="XII" <?= $filters['grade_level'] == 'XII' ? 'selected' : '' ?>>XII</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">Semua Status</option>
                                    <?php foreach ($status_options as $status): ?>
                                        <option value="<?= $status ?>" <?= $filters['status'] == $status ? 'selected' : '' ?>>
                                            <?= $status ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select form-select-sm">
                                    <option value="">Semua</option>
                                    <?php foreach ($gender_options as $key => $value): ?>
                                        <option value="<?= $key ?>" <?= $filters['gender'] == $key ? 'selected' : '' ?>>
                                            <?= $value ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pencarian</label>
                                <input type="text" name="search" class="form-control form-control-sm" placeholder="NISN, NIS, Nama, Email..." value="<?= esc($filters['search']) ?>">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="mdi mdi-magnify"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Students Table -->
                <div class="table-responsive">
                    <table class="table table-centered table-nowrap table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Siswa</th>
                                <th>NISN</th>
                                <th>NIS</th>
                                <th>Kelas</th>
                                <th>Gender</th>
                                <th>Status</th>
                                <th>Poin</th>
                                <th style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($students)): ?>
                                <?php
                                $no = 1 + (($pager->getCurrentPage() - 1) * $pager->getPerPage());
                                foreach ($students as $student):
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-3">
                                                    <img src="<?= user_avatar($student['profile_photo']) ?>"
                                                        alt="<?= esc($student['full_name']) ?>"
                                                        class="avatar-xs rounded-circle">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="font-size-14 mb-0">
                                                        <?= esc($student['full_name']) ?>
                                                    </h5>
                                                    <p class="text-muted mb-0 font-size-12">
                                                        <?= esc($student['email']) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td><code><?= esc($student['nisn']) ?></code></td>
                                        <td><code><?= esc($student['nis']) ?></code></td>
                                        <td>
                                            <?php if ($student['class_name']): ?>
                                                <span class="badge bg-primary">
                                                    <?= esc($student['grade_level']) ?> - <?= esc($student['class_name']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Belum Ada</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($student['gender'] == 'L'): ?>
                                                <i class="mdi mdi-gender-male text-primary"></i> L
                                            <?php else: ?>
                                                <i class="mdi mdi-gender-female text-danger"></i> P
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusColors = [
                                                'Aktif' => 'success',
                                                'Alumni' => 'info',
                                                'Pindah' => 'warning',
                                                'Keluar' => 'danger'
                                            ];
                                            $statusColor = $statusColors[$student['status']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $statusColor ?> font-size-12">
                                                <?= esc($student['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($student['total_violation_points'] > 0): ?>
                                                <span class="badge bg-danger font-size-12">
                                                    <?= $student['total_violation_points'] ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-success font-size-12">0</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url('admin/students/profile/' . $student['id']) ?>"
                                                    class="btn btn-sm btn-info"
                                                    data-bs-toggle="tooltip"
                                                    title="Profil">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="<?= base_url('admin/students/edit/' . $student['id']) ?>"
                                                    class="btn btn-sm btn-primary"
                                                    data-bs-toggle="tooltip"
                                                    title="Edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <button type="button"
                                                    class="btn btn-sm btn-danger btn-delete"
                                                    data-id="<?= $student['id'] ?>"
                                                    data-name="<?= esc($student['full_name']) ?>"
                                                    data-bs-toggle="tooltip"
                                                    title="Hapus">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="mdi mdi-account-off font-size-24 d-block mb-2"></i>
                                        Tidak ada data siswa
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($pager->getPageCount() > 1): ?>
                    <div class="row mt-3">
                        <div class="col-sm-12 col-md-5">
                            <div class="dataTables_info">
                                Menampilkan <?= ($pager->getCurrentPage() - 1) * $pager->getPerPage() + 1 ?>
                                sampai <?= min($pager->getCurrentPage() * $pager->getPerPage(), $pager->getTotal()) ?>
                                dari <?= $pager->getTotal() ?> data
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="dataTables_paginate paging_simple_numbers float-end">
                                <?= $pager->links('default', 'bootstrap_pagination') ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="mdi mdi-alert-circle text-danger me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data siswa <strong id="studentName"></strong>?</p>
                <p class="text-danger mb-0">
                    <i class="mdi mdi-information me-1"></i>
                    Data yang sudah dihapus tidak dapat dikembalikan!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i>Batal
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="mdi mdi-delete me-1"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Delete Student
        $('.btn-delete').on('click', function() {
            const studentId = $(this).data('id');
            const studentName = $(this).data('name');

            $('#studentName').text(studentName);
            $('#deleteForm').attr('action', '<?= base_url('admin/students/delete') ?>/' + studentId);

            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
<?= $this->endSection() ?>