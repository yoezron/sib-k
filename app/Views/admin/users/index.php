<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
/**
 * File Path: app/Views/admin/users/index.php
 * 
 * Users List View
 * Menampilkan daftar users dengan filter, search, dan actions
 * 
 * @package    SIB-K
 * @subpackage Views/Admin/Users
 * @category   User Management
 * @author     Development Team
 * @created    2025-01-05
 */
?>

<!-- Start Page Content -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Manajemen Pengguna</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Admin</a></li>
                    <li class="breadcrumb-item active">Pengguna</li>
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
    <div class="col-md-4">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Total Pengguna</p>
                        <h4 class="mb-0"><?= number_format($stats['total']) ?></h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                            <span class="avatar-title">
                                <i class="mdi mdi-account-group font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Pengguna Aktif</p>
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

    <div class="col-md-4">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Pengguna Nonaktif</p>
                        <h4 class="mb-0"><?= number_format($stats['inactive']) ?></h4>
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

<!-- Users Table Card -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Header with Add Button -->
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h4 class="card-title">
                            <i class="mdi mdi-account-group me-2"></i>Daftar Pengguna
                        </h4>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-sm-end">
                            <a href="<?= base_url('admin/users/create') ?>" class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2">
                                <i class="mdi mdi-plus me-1"></i> Tambah Pengguna
                            </a>
                            <a href="<?= base_url('admin/users/export') . '?' . http_build_query($filters) ?>" class="btn btn-primary btn-rounded waves-effect waves-light mb-2">
                                <i class="mdi mdi-download me-1"></i> Export CSV
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Filter Form -->
                <div class="row mb-3">
                    <div class="col-12">
                        <form action="<?= base_url('admin/users') ?>" method="get" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Role</label>
                                <select name="role_id" class="form-select">
                                    <option value="">Semua Role</option>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= $role['id'] ?>" <?= $filters['role_id'] == $role['id'] ? 'selected' : '' ?>>
                                            <?= esc($role['role_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="is_active" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="1" <?= $filters['is_active'] === '1' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="0" <?= $filters['is_active'] === '0' ? 'selected' : '' ?>>Nonaktif</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pencarian</label>
                                <input type="text" name="search" class="form-control" placeholder="Username, email, atau nama..." value="<?= esc($filters['search']) ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-magnify me-1"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="table-responsive">
                    <table class="table table-centered table-nowrap table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Pengguna</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Telepon</th>
                                <th>Status</th>
                                <th>Terakhir Login</th>
                                <th style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)): ?>
                                <?php
                                $no = 1 + (($pager->getCurrentPage() - 1) * $pager->getPerPage());
                                foreach ($users as $user):
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-3">
                                                    <img src="<?= user_avatar($user['profile_photo']) ?>"
                                                        alt="<?= esc($user['full_name']) ?>"
                                                        class="avatar-xs rounded-circle">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="font-size-14 mb-0">
                                                        <?= esc($user['full_name']) ?>
                                                    </h5>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= esc($user['username']) ?></td>
                                        <td><?= esc($user['email']) ?></td>
                                        <td>
                                            <span class="badge bg-info font-size-12">
                                                <?= esc($user['role_name']) ?>
                                            </span>
                                        </td>
                                        <td><?= esc($user['phone'] ?? '-') ?></td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input toggle-active"
                                                    type="checkbox"
                                                    data-id="<?= $user['id'] ?>"
                                                    <?= $user['is_active'] == 1 ? 'checked' : '' ?>
                                                    <?= $user['id'] == session()->get('user_id') ? 'disabled' : '' ?>>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($user['last_login']): ?>
                                                <small><?= date('d/m/Y H:i', strtotime($user['last_login'])) ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">Belum pernah</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url('admin/users/show/' . $user['id']) ?>"
                                                    class="btn btn-sm btn-info"
                                                    data-bs-toggle="tooltip"
                                                    title="Detail">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>"
                                                    class="btn btn-sm btn-primary"
                                                    data-bs-toggle="tooltip"
                                                    title="Edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <?php if ($user['id'] != session()->get('user_id')): ?>
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger btn-delete"
                                                        data-id="<?= $user['id'] ?>"
                                                        data-name="<?= esc($user['full_name']) ?>"
                                                        data-bs-toggle="tooltip"
                                                        title="Hapus">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="mdi mdi-account-off font-size-24 d-block mb-2"></i>
                                        Tidak ada data pengguna
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
                <p>Apakah Anda yakin ingin menghapus pengguna <strong id="userName"></strong>?</p>
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

        // Toggle Active Status
        $('.toggle-active').on('change', function() {
            const userId = $(this).data('id');
            const isChecked = $(this).is(':checked');
            const checkbox = $(this);

            // Confirm action
            if (!confirm('Apakah Anda yakin ingin mengubah status pengguna ini?')) {
                // Revert checkbox
                checkbox.prop('checked', !isChecked);
                return;
            }

            // Send AJAX request
            $.ajax({
                url: '<?= base_url('admin/users/toggle-active') ?>/' + userId,
                type: 'POST',
                data: {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        // Show error and revert checkbox
                        checkbox.prop('checked', !isChecked);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    // Revert checkbox
                    checkbox.prop('checked', !isChecked);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan sistem'
                    });
                }
            });
        });

        // Delete User
        $('.btn-delete').on('click', function() {
            const userId = $(this).data('id');
            const userName = $(this).data('name');

            $('#userName').text(userName);
            $('#deleteForm').attr('action', '<?= base_url('admin/users/delete') ?>/' + userId);

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