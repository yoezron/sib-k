<?php

/**
 * File Path: app/Views/admin/classes/index.php
 * 
 * Classes List View
 * Menampilkan daftar kelas dengan filter dan pagination
 * 
 * @package    SIB-K
 * @subpackage Views/Admin/Classes
 * @category   Class Management
 * @author     Development Team
 * @created    2025-01-06
 */
?>

<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18"><?= $page_title ?></h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <?php foreach ($breadcrumb as $item): ?>
                        <?php if ($item['link']): ?>
                            <li class="breadcrumb-item">
                                <a href="<?= $item['link'] ?>"><?= $item['title'] ?></a>
                            </li>
                        <?php else: ?>
                            <li class="breadcrumb-item active"><?= $item['title'] ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    </div>
</div>

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

<?php if (session()->getFlashdata('info')): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="mdi mdi-information me-2"></i>
        <?= session()->getFlashdata('info') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Kelas</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?= $stats['total'] ?>">0</span>
                        </h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                            <span class="avatar-title">
                                <i class="mdi mdi-google-classroom font-size-24"></i>
                            </span>
                        </div>
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
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Kelas Aktif</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?= $stats['active'] ?>">0</span>
                        </h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                            <span class="avatar-title">
                                <i class="mdi mdi-check-circle font-size-24"></i>
                            </span>
                        </div>
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
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Kelas X</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?= $stats['by_grade']['X'] ?? 0 ?>">0</span>
                        </h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                            <span class="avatar-title">
                                <i class="mdi mdi-numeric-10-box font-size-24"></i>
                            </span>
                        </div>
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
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Kelas XI & XII</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?= ($stats['by_grade']['XI'] ?? 0) + ($stats['by_grade']['XII'] ?? 0) ?>">0</span>
                        </h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                            <span class="avatar-title">
                                <i class="mdi mdi-school font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Classes Table Card -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Header with Action Buttons -->
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h4 class="card-title">
                            <i class="mdi mdi-google-classroom me-2"></i>Daftar Kelas
                        </h4>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-sm-end">
                            <a href="<?= base_url('admin/classes/create') ?>" class="btn btn-success btn-rounded waves-effect waves-light mb-2">
                                <i class="mdi mdi-plus me-1"></i> Tambah Kelas
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Filter Form -->
                <div class="row mb-3">
                    <div class="col-12">
                        <form action="<?= base_url('admin/classes') ?>" method="get" class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">Tahun Ajaran</label>
                                <select name="academic_year_id" class="form-select form-select-sm">
                                    <option value="">Semua</option>
                                    <?php foreach ($academic_years as $year): ?>
                                        <option value="<?= $year['id'] ?>" <?= $filters['academic_year_id'] == $year['id'] ? 'selected' : '' ?>>
                                            <?= $year['year_name'] ?> - <?= $year['semester'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Tingkat</label>
                                <select name="grade_level" class="form-select form-select-sm">
                                    <option value="">Semua Tingkat</option>
                                    <?php foreach ($grade_levels as $key => $value): ?>
                                        <option value="<?= $key ?>" <?= $filters['grade_level'] == $key ? 'selected' : '' ?>>
                                            <?= $value ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Jurusan</label>
                                <select name="major" class="form-select form-select-sm">
                                    <option value="">Semua Jurusan</option>
                                    <?php foreach ($majors as $key => $value): ?>
                                        <option value="<?= $key ?>" <?= $filters['major'] == $key ? 'selected' : '' ?>>
                                            <?= $key ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="is_active" class="form-select form-select-sm">
                                    <option value="">Semua Status</option>
                                    <?php foreach ($status_options as $key => $value): ?>
                                        <option value="<?= $key ?>" <?= $filters['is_active'] == $key ? 'selected' : '' ?>>
                                            <?= $value ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Pencarian</label>
                                <input type="text" name="search" class="form-control form-control-sm"
                                    placeholder="Nama kelas, wali kelas..."
                                    value="<?= $filters['search'] ?>">
                            </div>

                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-sm d-block w-100">
                                    <i class="mdi mdi-magnify"></i> Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Kelas</th>
                                <th>Tahun Ajaran</th>
                                <th>Tingkat</th>
                                <th>Jurusan</th>
                                <th>Wali Kelas</th>
                                <th>Guru BK</th>
                                <th width="10%" class="text-center">Siswa</th>
                                <th width="8%" class="text-center">Status</th>
                                <th width="12%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($classes)): ?>
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <i class="mdi mdi-information-outline font-size-24 text-muted d-block mb-2"></i>
                                        <span class="text-muted">Tidak ada data kelas</span>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php
                                $no = 1 + (($current_page - 1) * $per_page);
                                foreach ($classes as $class):
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <strong><?= esc($class['class_name']) ?></strong>
                                            <?php if ($class['is_active']): ?>
                                                <span class="badge bg-success ms-2">Aktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= esc($class['year_name']) ?>
                                            <small class="text-muted d-block"><?= esc($class['semester']) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary"><?= esc($class['grade_level']) ?></span>
                                        </td>
                                        <td><?= esc($class['major'] ?? '-') ?></td>
                                        <td>
                                            <?php if ($class['homeroom_name']): ?>
                                                <i class="mdi mdi-account-tie text-primary me-1"></i>
                                                <?= esc($class['homeroom_name']) ?>
                                            <?php else: ?>
                                                <span class="text-muted">- Belum ditugaskan -</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($class['counselor_name']): ?>
                                                <i class="mdi mdi-account-heart text-success me-1"></i>
                                                <?= esc($class['counselor_name']) ?>
                                            <?php else: ?>
                                                <span class="text-muted">- Belum ditugaskan -</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info font-size-12">
                                                <?= $class['student_count'] ?> / <?= $class['max_students'] ?? 36 ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($class['is_active']): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Tidak Aktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url('admin/classes/detail/' . $class['id']) ?>"
                                                    class="btn btn-sm btn-info"
                                                    data-bs-toggle="tooltip"
                                                    title="Detail">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="<?= base_url('admin/classes/edit/' . $class['id']) ?>"
                                                    class="btn btn-sm btn-primary"
                                                    data-bs-toggle="tooltip"
                                                    title="Edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="confirmDelete(<?= $class['id'] ?>, '<?= esc($class['class_name']) ?>')"
                                                    data-bs-toggle="tooltip"
                                                    title="Hapus">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($pager): ?>
                    <div class="row mt-4">
                        <div class="col-sm-12 col-md-5">
                            <div class="dataTables_info">
                                Menampilkan <?= (($current_page - 1) * $per_page) + 1 ?>
                                sampai <?= min($current_page * $per_page, $total) ?>
                                dari <?= $total ?> data
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="dataTables_paginate paging_simple_numbers float-end">
                                <?= $pager->links('default', 'default_full') ?>
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
                <p>Apakah Anda yakin ingin menghapus kelas <strong id="className"></strong>?</p>
                <div class="alert alert-warning" role="alert">
                    <i class="mdi mdi-alert me-2"></i>
                    <strong>Perhatian:</strong> Kelas yang memiliki siswa tidak dapat dihapus.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="post" style="display: inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="mdi mdi-delete me-1"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Counter animation
        const counters = document.querySelectorAll('.counter-value');
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-target');
            const increment = target / 100;

            const updateCounter = () => {
                const current = +counter.innerText;
                if (current < target) {
                    counter.innerText = Math.ceil(current + increment);
                    setTimeout(updateCounter, 10);
                } else {
                    counter.innerText = target;
                }
            };

            updateCounter();
        });
    });

    // Confirm delete
    function confirmDelete(id, className) {
        document.getElementById('className').textContent = className;
        document.getElementById('deleteForm').action = '<?= base_url('admin/classes/delete/') ?>' + id;

        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>

<?= $this->endSection() ?>