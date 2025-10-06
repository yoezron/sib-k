<?php

/**
 * File Path: app/Views/admin/academic_years/index.php
 * 
 * Academic Years List View
 * Menampilkan daftar tahun ajaran dengan filter dan pagination
 * 
 * @package    SIB-K
 * @subpackage Views/Admin/AcademicYears
 * @category   Academic Year Management
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

<!-- Active Academic Year Info -->
<?php if ($active_year): ?>
    <div class="alert alert-success border-0 mb-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <i class="mdi mdi-calendar-check font-size-24"></i>
            </div>
            <div class="flex-grow-1 ms-3">
                <strong>Tahun Ajaran Aktif:</strong>
                <?= esc($active_year['year_name']) ?> - <?= esc($active_year['semester']) ?>
                <span class="text-muted ms-2">
                    (<?= date('d M Y', strtotime($active_year['start_date'])) ?> - <?= date('d M Y', strtotime($active_year['end_date'])) ?>)
                </span>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-warning border-0 mb-4" role="alert">
        <i class="mdi mdi-alert me-2"></i>
        <strong>Perhatian:</strong> Tidak ada tahun ajaran yang aktif saat ini.
    </div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-xl-4 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Tahun Ajaran</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?= $stats['total'] ?>">0</span>
                        </h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                            <span class="avatar-title">
                                <i class="mdi mdi-calendar-multiple font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Semester Ganjil</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?= $stats['by_semester']['Ganjil'] ?? 0 ?>">0</span>
                        </h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                            <span class="avatar-title">
                                <i class="mdi mdi-calendar-arrow-right font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Semester Genap</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?= $stats['by_semester']['Genap'] ?? 0 ?>">0</span>
                        </h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                            <span class="avatar-title">
                                <i class="mdi mdi-calendar-arrow-left font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Academic Years Table Card -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Header with Action Buttons -->
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h4 class="card-title">
                            <i class="mdi mdi-calendar-multiple me-2"></i>Daftar Tahun Ajaran
                        </h4>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-sm-end">
                            <a href="<?= base_url('admin/academic-years/create') ?>" class="btn btn-success btn-rounded waves-effect waves-light mb-2">
                                <i class="mdi mdi-plus me-1"></i> Tambah Tahun Ajaran
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Filter Form -->
                <div class="row mb-3">
                    <div class="col-12">
                        <form action="<?= base_url('admin/academic-years') ?>" method="get" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Semester</label>
                                <select name="semester" class="form-select form-select-sm">
                                    <option value="">Semua Semester</option>
                                    <?php foreach ($semester_options as $key => $value): ?>
                                        <option value="<?= $key ?>" <?= $filters['semester'] == $key ? 'selected' : '' ?>>
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
                                        <option value="<?= $key ?>" <?= $filters['is_active'] === (string)$key ? 'selected' : '' ?>>
                                            <?= $value ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Pencarian</label>
                                <input type="text" name="search" class="form-control form-control-sm"
                                    placeholder="Cari tahun ajaran..."
                                    value="<?= $filters['search'] ?>">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                        <i class="mdi mdi-magnify"></i> Filter
                                    </button>
                                    <a href="<?= base_url('admin/academic-years') ?>" class="btn btn-secondary btn-sm">
                                        <i class="mdi mdi-refresh"></i> Reset
                                    </a>
                                </div>
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
                                <th>Tahun Ajaran</th>
                                <th>Semester</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th width="10%" class="text-center">Durasi</th>
                                <th width="10%" class="text-center">Jumlah Kelas</th>
                                <th width="10%" class="text-center">Status</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($academic_years)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="mdi mdi-information-outline font-size-24 text-muted d-block mb-2"></i>
                                        <span class="text-muted">Tidak ada data tahun ajaran</span>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php
                                $no = 1 + (($current_page - 1) * $per_page);
                                foreach ($academic_years as $year):
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <strong><?= esc($year['year_name']) ?></strong>
                                            <?php if ($year['is_active']): ?>
                                                <span class="badge bg-success ms-2">Aktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($year['semester'] === 'Ganjil'): ?>
                                                <span class="badge bg-info"><?= esc($year['semester']) ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-primary"><?= esc($year['semester']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d M Y', strtotime($year['start_date'])) ?></td>
                                        <td><?= date('d M Y', strtotime($year['end_date'])) ?></td>
                                        <td class="text-center">
                                            <?php
                                            $duration = (strtotime($year['end_date']) - strtotime($year['start_date'])) / (60 * 60 * 24);
                                            $months = round($duration / 30);
                                            ?>
                                            <span class="badge bg-secondary"><?= $months ?> bulan</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info font-size-12"><?= $year['class_count'] ?> kelas</span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($year['is_active']): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Tidak Aktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <?php if (!$year['is_active']): ?>
                                                    <button type="button"
                                                        class="btn btn-sm btn-success"
                                                        onclick="confirmSetActive(<?= $year['id'] ?>, '<?= esc($year['year_name']) ?>')"
                                                        data-bs-toggle="tooltip"
                                                        title="Aktifkan">
                                                        <i class="mdi mdi-check-circle"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <a href="<?= base_url('admin/academic-years/edit/' . $year['id']) ?>"
                                                    class="btn btn-sm btn-primary"
                                                    data-bs-toggle="tooltip"
                                                    title="Edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <?php if ($year['class_count'] == 0): ?>
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="confirmDelete(<?= $year['id'] ?>, '<?= esc($year['year_name']) ?>')"
                                                        data-bs-toggle="tooltip"
                                                        title="Hapus">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="button"
                                                        class="btn btn-sm btn-secondary"
                                                        disabled
                                                        data-bs-toggle="tooltip"
                                                        title="Tidak bisa dihapus (ada kelas)">
                                                        <i class="mdi mdi-lock"></i>
                                                    </button>
                                                <?php endif; ?>
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

<!-- Set Active Confirmation Modal -->
<div class="modal fade" id="setActiveModal" tabindex="-1" aria-labelledby="setActiveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="setActiveModalLabel">
                    <i class="mdi mdi-check-circle text-success me-2"></i>Konfirmasi Aktifkan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin mengaktifkan tahun ajaran <strong id="activeYearName"></strong>?</p>
                <div class="alert alert-info" role="alert">
                    <i class="mdi mdi-information me-2"></i>
                    Tahun ajaran yang sedang aktif akan otomatis dinonaktifkan.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="setActiveForm" method="post" style="display: inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-success">
                        <i class="mdi mdi-check-circle me-1"></i> Aktifkan
                    </button>
                </form>
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
                <p>Apakah Anda yakin ingin menghapus tahun ajaran <strong id="deleteYearName"></strong>?</p>
                <div class="alert alert-warning" role="alert">
                    <i class="mdi mdi-alert me-2"></i>
                    <strong>Perhatian:</strong> Tahun ajaran yang memiliki kelas tidak dapat dihapus.
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

    // Confirm set active
    function confirmSetActive(id, yearName) {
        document.getElementById('activeYearName').textContent = yearName;
        document.getElementById('setActiveForm').action = '<?= base_url('admin/academic-years/set-active/') ?>' + id;

        var setActiveModal = new bootstrap.Modal(document.getElementById('setActiveModal'));
        setActiveModal.show();
    }

    // Confirm delete
    function confirmDelete(id, yearName) {
        document.getElementById('deleteYearName').textContent = yearName;
        document.getElementById('deleteForm').action = '<?= base_url('admin/academic-years/delete/') ?>' + id;

        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>

<?= $this->endSection() ?>