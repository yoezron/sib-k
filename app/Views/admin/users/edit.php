<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
/**
 * File Path: app/Views/admin/users/edit.php
 * 
 * Edit User Form View
 * Form untuk mengedit data pengguna
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
            <h4 class="mb-0">Edit Pengguna</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Admin</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/users') ?>">Pengguna</a></li>
                    <li class="breadcrumb-item active">Edit</li>
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

<?php if (session()->getFlashdata('errors')): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="mdi mdi-alert-circle me-2"></i>
                <strong>Terdapat kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row">
    <!-- User Info Card -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">
                    <i class="mdi mdi-account-circle me-2"></i>Profil Pengguna
                </h4>

                <div class="text-center">
                    <img src="<?= user_avatar($user['profile_photo']) ?>"
                        alt="<?= esc($user['full_name']) ?>"
                        class="avatar-lg rounded-circle mb-3">

                    <h5 class="mb-1"><?= esc($user['full_name']) ?></h5>
                    <p class="text-muted mb-2">@<?= esc($user['username']) ?></p>
                    <span class="badge bg-info font-size-12"><?= esc($user['role_name']) ?></span>
                </div>

                <hr class="my-4">

                <div class="table-responsive">
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted">User ID:</td>
                                <td class="text-end"><strong>#<?= $user['id'] ?></strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status:</td>
                                <td class="text-end">
                                    <?php if ($user['is_active'] == 1): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Terakhir Login:</td>
                                <td class="text-end">
                                    <?php if ($user['last_login']): ?>
                                        <small><?= date('d/m/Y H:i', strtotime($user['last_login'])) ?></small>
                                    <?php else: ?>
                                        <small>-</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Terdaftar:</td>
                                <td class="text-end">
                                    <small><?= date('d/m/Y', strtotime($user['created_at'])) ?></small>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr class="my-4">

                <!-- Action Buttons -->
                <div class="d-grid gap-2">
                    <a href="<?= base_url('admin/users/show/' . $user['id']) ?>" class="btn btn-info">
                        <i class="mdi mdi-eye me-1"></i> Lihat Detail
                    </a>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                        <i class="mdi mdi-key-variant me-1"></i> Reset Password
                    </button>
                    <?php if ($user['id'] != session()->get('user_id')): ?>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="mdi mdi-delete me-1"></i> Hapus User
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">
                    <i class="mdi mdi-account-edit me-2"></i>Edit Informasi Pengguna
                </h4>

                <form action="<?= base_url('admin/users/update/' . $user['id']) ?>" method="POST" class="needs-validation" novalidate>
                    <?= csrf_field() ?>

                    <div class="row">
                        <!-- Role Selection -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role_id" class="form-label">
                                    Role <span class="text-danger">*</span>
                                </label>
                                <select class="form-select <?= session()->getFlashdata('errors')['role_id'] ?? false ? 'is-invalid' : '' ?>"
                                    id="role_id"
                                    name="role_id"
                                    required>
                                    <option value="">Pilih Role</option>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= $role['id'] ?>"
                                            <?= (old('role_id') ?? $user['role_id']) == $role['id'] ? 'selected' : '' ?>>
                                            <?= esc($role['role_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset(session()->getFlashdata('errors')['role_id'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <?= session()->getFlashdata('errors')['role_id'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Full Name -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="full_name" class="form-label">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control <?= session()->getFlashdata('errors')['full_name'] ?? false ? 'is-invalid' : '' ?>"
                                    id="full_name"
                                    name="full_name"
                                    value="<?= old('full_name') ?? esc($user['full_name']) ?>"
                                    placeholder="Masukkan nama lengkap"
                                    required>
                                <?php if (isset(session()->getFlashdata('errors')['full_name'])): ?>
                                    <div class="invalid-feedback">
                                        <?= session()->getFlashdata('errors')['full_name'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Username -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    Username <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control <?= session()->getFlashdata('errors')['username'] ?? false ? 'is-invalid' : '' ?>"
                                    id="username"
                                    name="username"
                                    value="<?= old('username') ?? esc($user['username']) ?>"
                                    placeholder="Masukkan username (alfanumerik)"
                                    required>
                                <small class="form-text text-muted">
                                    Username hanya boleh berisi huruf dan angka, minimal 3 karakter
                                </small>
                                <?php if (isset(session()->getFlashdata('errors')['username'])): ?>
                                    <div class="invalid-feedback">
                                        <?= session()->getFlashdata('errors')['username'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email"
                                    class="form-control <?= session()->getFlashdata('errors')['email'] ?? false ? 'is-invalid' : '' ?>"
                                    id="email"
                                    name="email"
                                    value="<?= old('email') ?? esc($user['email']) ?>"
                                    placeholder="contoh@email.com"
                                    required>
                                <?php if (isset(session()->getFlashdata('errors')['email'])): ?>
                                    <div class="invalid-feedback">
                                        <?= session()->getFlashdata('errors')['email'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Phone -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Nomor Telepon</label>
                                <input type="text"
                                    class="form-control <?= session()->getFlashdata('errors')['phone'] ?? false ? 'is-invalid' : '' ?>"
                                    id="phone"
                                    name="phone"
                                    value="<?= old('phone') ?? esc($user['phone']) ?>"
                                    placeholder="08xxxxxxxxxx">
                                <small class="form-text text-muted">
                                    Opsional, minimal 10 digit
                                </small>
                                <?php if (isset(session()->getFlashdata('errors')['phone'])): ?>
                                    <div class="invalid-feedback">
                                        <?= session()->getFlashdata('errors')['phone'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Status Active -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <div class="form-check form-switch form-switch-lg mt-2">
                                    <input class="form-check-input"
                                        type="checkbox"
                                        id="is_active"
                                        name="is_active"
                                        value="1"
                                        <?= (old('is_active') ?? $user['is_active']) == 1 ? 'checked' : '' ?>
                                        <?= $user['id'] == session()->get('user_id') ? 'disabled' : '' ?>>
                                    <label class="form-check-label" for="is_active">
                                        <?php if ($user['id'] == session()->get('user_id')): ?>
                                            Anda tidak dapat menonaktifkan akun sendiri
                                        <?php else: ?>
                                            User aktif
                                        <?php endif; ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="alert alert-warning" role="alert">
                        <i class="mdi mdi-alert-outline me-2"></i>
                        <strong>Perhatian:</strong> Untuk mengubah password, gunakan tombol "Reset Password" di samping kiri.
                    </div>

                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">
                                    <i class="mdi mdi-arrow-left me-1"></i> Kembali
                                </a>
                                <div>
                                    <button type="reset" class="btn btn-light me-2">
                                        <i class="mdi mdi-refresh me-1"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-content-save me-1"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetPasswordModalLabel">
                    <i class="mdi mdi-key-variant text-warning me-2"></i>Reset Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/users/reset-password/' . $user['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin mereset password untuk pengguna <strong><?= esc($user['full_name']) ?></strong>?</p>
                    <p class="text-warning mb-0">
                        <i class="mdi mdi-information me-1"></i>
                        Password baru akan dibuat secara otomatis. Pastikan untuk mencatat dan menyampaikannya kepada user.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="mdi mdi-key-variant me-1"></i>Reset Password
                    </button>
                </div>
            </form>
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
            <form action="<?= base_url('admin/users/delete/' . $user['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus pengguna <strong><?= esc($user['full_name']) ?></strong>?</p>
                    <p class="text-danger mb-0">
                        <i class="mdi mdi-information me-1"></i>
                        Data yang sudah dihapus tidak dapat dikembalikan!
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="mdi mdi-delete me-1"></i>Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Username Validation (Alphanumeric only)
        $('#username').on('keyup', function() {
            const value = $(this).val();
            const alphanumeric = /^[a-zA-Z0-9]*$/;

            if (!alphanumeric.test(value)) {
                $(this).val(value.replace(/[^a-zA-Z0-9]/g, ''));
            }
        });

        // Phone Number Validation (Numbers only)
        $('#phone').on('keyup', function() {
            const value = $(this).val();
            const numeric = /^[0-9]*$/;

            if (!numeric.test(value)) {
                $(this).val(value.replace(/[^0-9]/g, ''));
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Form Validation
        (function() {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');

            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    });
</script>
<?= $this->endSection() ?>