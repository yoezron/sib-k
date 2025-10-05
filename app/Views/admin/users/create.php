<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
/**
 * File Path: app/Views/admin/users/create.php
 * 
 * Create User Form View
 * Form untuk menambah pengguna baru
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
            <h4 class="mb-0">Tambah Pengguna Baru</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Admin</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/users') ?>">Pengguna</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Error Messages -->
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

<!-- Create User Form -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">
                    <i class="mdi mdi-account-plus me-2"></i>Informasi Pengguna
                </h4>

                <form action="<?= base_url('admin/users/store') ?>" method="POST" class="needs-validation" novalidate>
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
                                        <option value="<?= $role['id'] ?>" <?= old('role_id') == $role['id'] ? 'selected' : '' ?>>
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
                                    value="<?= old('full_name') ?>"
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
                                    value="<?= old('username') ?>"
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
                                    value="<?= old('email') ?>"
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
                        <!-- Password -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                        class="form-control <?= session()->getFlashdata('errors')['password'] ?? false ? 'is-invalid' : '' ?>"
                                        id="password"
                                        name="password"
                                        placeholder="Minimal 6 karakter"
                                        required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="mdi mdi-eye-outline" id="eyeIcon"></i>
                                    </button>
                                </div>
                                <small class="form-text text-muted">
                                    Minimal 6 karakter
                                </small>
                                <?php if (isset(session()->getFlashdata('errors')['password'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <?= session()->getFlashdata('errors')['password'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Password Confirmation -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">
                                    Konfirmasi Password <span class="text-danger">*</span>
                                </label>
                                <input type="password"
                                    class="form-control <?= session()->getFlashdata('errors')['password_confirm'] ?? false ? 'is-invalid' : '' ?>"
                                    id="password_confirm"
                                    name="password_confirm"
                                    placeholder="Ulangi password"
                                    required>
                                <?php if (isset(session()->getFlashdata('errors')['password_confirm'])): ?>
                                    <div class="invalid-feedback">
                                        <?= session()->getFlashdata('errors')['password_confirm'] ?>
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
                                    value="<?= old('phone') ?>"
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
                                        <?= old('is_active', '1') == '1' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_active">
                                        Aktifkan pengguna setelah dibuat
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="alert alert-info" role="alert">
                        <i class="mdi mdi-information me-2"></i>
                        <strong>Informasi:</strong> Password akan di-enkripsi secara otomatis.
                        Pastikan untuk mencatat password dan menyampaikannya kepada pengguna.
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
                                        <i class="mdi mdi-content-save me-1"></i> Simpan
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

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Toggle Password Visibility
        $('#togglePassword').on('click', function() {
            const passwordField = $('#password');
            const eyeIcon = $('#eyeIcon');

            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                eyeIcon.removeClass('mdi-eye-outline').addClass('mdi-eye-off-outline');
            } else {
                passwordField.attr('type', 'password');
                eyeIcon.removeClass('mdi-eye-off-outline').addClass('mdi-eye-outline');
            }
        });

        // Real-time Password Match Validation
        $('#password_confirm').on('keyup', function() {
            const password = $('#password').val();
            const passwordConfirm = $(this).val();

            if (passwordConfirm !== '') {
                if (password === passwordConfirm) {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                } else {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                }
            } else {
                $(this).removeClass('is-valid is-invalid');
            }
        });

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

            if (!alphanumeric.test(value)) {
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