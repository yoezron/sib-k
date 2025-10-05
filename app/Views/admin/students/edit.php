<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
/**
 * File Path: app/Views/admin/students/edit.php
 * 
 * Edit Student Form View
 * Form untuk mengedit data siswa
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
            <h4 class="mb-0">Edit Data Siswa</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Admin</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/students') ?>">Siswa</a></li>
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
    <!-- Student Info Card -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">
                    <i class="mdi mdi-account-circle me-2"></i>Info Siswa
                </h4>

                <div class="text-center">
                    <img src="<?= user_avatar($student['profile_photo']) ?>"
                        alt="<?= esc($student['full_name']) ?>"
                        class="avatar-lg rounded-circle mb-3">

                    <h5 class="mb-1"><?= esc($student['full_name']) ?></h5>
                    <p class="text-muted mb-2">@<?= esc($student['username']) ?></p>

                    <?php if ($student['class_name']): ?>
                        <span class="badge bg-primary font-size-12">
                            <?= esc($student['grade_level']) ?> - <?= esc($student['class_name']) ?>
                        </span>
                    <?php else: ?>
                        <span class="badge bg-secondary font-size-12">Belum Ada Kelas</span>
                    <?php endif; ?>

                    <span class="badge bg-<?= $student['status'] == 'Aktif' ? 'success' : 'secondary' ?> font-size-12 ms-1">
                        <?= esc($student['status']) ?>
                    </span>
                </div>

                <hr class="my-4">

                <div class="table-responsive">
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted">Student ID:</td>
                                <td class="text-end"><strong>#<?= $student['id'] ?></strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">NISN:</td>
                                <td class="text-end"><code><?= esc($student['nisn']) ?></code></td>
                            </tr>
                            <tr>
                                <td class="text-muted">NIS:</td>
                                <td class="text-end"><code><?= esc($student['nis']) ?></code></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Poin Pelanggaran:</td>
                                <td class="text-end">
                                    <span class="badge bg-<?= $student['total_violation_points'] > 0 ? 'danger' : 'success' ?>">
                                        <?= $student['total_violation_points'] ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Terdaftar:</td>
                                <td class="text-end">
                                    <small><?= date('d/m/Y', strtotime($student['created_at'])) ?></small>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr class="my-4">

                <!-- Quick Actions -->
                <div class="d-grid gap-2">
                    <a href="<?= base_url('admin/students/profile/' . $student['id']) ?>" class="btn btn-info">
                        <i class="mdi mdi-eye me-1"></i> Lihat Profil Lengkap
                    </a>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#changeClassModal">
                        <i class="mdi mdi-swap-horizontal me-1"></i> Pindah Kelas
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="mdi mdi-delete me-1"></i> Hapus Siswa
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">
                    <i class="mdi mdi-account-edit me-2"></i>Edit Data Siswa
                </h4>

                <form action="<?= base_url('admin/students/update/' . $student['id']) ?>" method="POST" class="needs-validation" novalidate>
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nisn" class="form-label">
                                    NISN <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" name="nisn"
                                    value="<?= old('nisn') ?? esc($student['nisn']) ?>"
                                    placeholder="10-20 digit" required>
                                <small class="text-muted">Nomor Induk Siswa Nasional</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nis" class="form-label">
                                    NIS <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" name="nis"
                                    value="<?= old('nis') ?? esc($student['nis']) ?>"
                                    placeholder="5-20 karakter" required>
                                <small class="text-muted">Nomor Induk Siswa</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="gender" class="form-label">
                                    Jenis Kelamin <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" name="gender" required>
                                    <option value="">Pilih</option>
                                    <?php foreach ($gender_options as $key => $value): ?>
                                        <option value="<?= $key ?>" <?= (old('gender') ?? $student['gender']) == $key ? 'selected' : '' ?>>
                                            <?= $value ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="birth_place" class="form-label">Tempat Lahir</label>
                                <input type="text" class="form-control" name="birth_place"
                                    value="<?= old('birth_place') ?? esc($student['birth_place']) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="birth_date" class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control" name="birth_date"
                                    value="<?= old('birth_date') ?? $student['birth_date'] ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="religion" class="form-label">Agama</label>
                                <select class="form-select" name="religion">
                                    <option value="">Pilih</option>
                                    <?php foreach ($religion_options as $religion): ?>
                                        <option value="<?= $religion ?>" <?= (old('religion') ?? $student['religion']) == $religion ? 'selected' : '' ?>>
                                            <?= $religion ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="class_id" class="form-label">Kelas</label>
                                <select class="form-select" name="class_id">
                                    <option value="">Pilih Kelas</option>
                                    <?php foreach ($classes as $class): ?>
                                        <option value="<?= $class['id'] ?>" <?= (old('class_id') ?? $student['class_id']) == $class['id'] ? 'selected' : '' ?>>
                                            <?= esc($class['grade_level']) ?> - <?= esc($class['class_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="admission_date" class="form-label">Tanggal Masuk</label>
                                <input type="date" class="form-control" name="admission_date"
                                    value="<?= old('admission_date') ?? $student['admission_date'] ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="address" class="form-label">Alamat Lengkap</label>
                                <textarea class="form-control" name="address" rows="3"><?= old('address') ?? esc($student['address']) ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="parent_id" class="form-label">Orang Tua/Wali</label>
                                <select class="form-select" name="parent_id">
                                    <option value="">Pilih Orang Tua</option>
                                    <?php foreach ($parents as $parent): ?>
                                        <option value="<?= $parent['id'] ?>" <?= (old('parent_id') ?? $student['parent_id']) == $parent['id'] ? 'selected' : '' ?>>
                                            <?= esc($parent['full_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($student['parent_name']): ?>
                                    <small class="text-muted">Saat ini: <?= esc($student['parent_name']) ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <?php foreach ($status_options as $status): ?>
                                        <option value="<?= $status ?>" <?= (old('status') ?? $student['status']) == $status ? 'selected' : '' ?>>
                                            <?= $status ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="alert alert-warning" role="alert">
                        <i class="mdi mdi-alert-outline me-2"></i>
                        <strong>Perhatian:</strong> Untuk mengubah data user account (username, email, password),
                        silakan edit melalui menu <a href="<?= base_url('admin/users/edit/' . $student['user_id']) ?>">Manajemen User</a>.
                    </div>

                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('admin/students') ?>" class="btn btn-secondary">
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

<!-- Change Class Modal -->
<div class="modal fade" id="changeClassModal" tabindex="-1" aria-labelledby="changeClassModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeClassModalLabel">
                    <i class="mdi mdi-swap-horizontal text-warning me-2"></i>Pindah Kelas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/students/change-class/' . $student['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new_class_id" class="form-label">Kelas Baru <span class="text-danger">*</span></label>
                        <select class="form-select" id="new_class_id" name="class_id" required>
                            <option value="">Pilih Kelas Baru</option>
                            <?php foreach ($classes as $class): ?>
                                <?php if ($class['id'] != $student['class_id']): ?>
                                    <option value="<?= $class['id'] ?>">
                                        <?= esc($class['grade_level']) ?> - <?= esc($class['class_name']) ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p class="text-muted mb-0">
                        Kelas saat ini:
                        <strong>
                            <?php if ($student['class_name']): ?>
                                <?= esc($student['grade_level']) ?> - <?= esc($student['class_name']) ?>
                            <?php else: ?>
                                Belum Ada Kelas
                            <?php endif; ?>
                        </strong>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="mdi mdi-swap-horizontal me-1"></i>Pindahkan
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
            <form action="<?= base_url('admin/students/delete/' . $student['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data siswa <strong><?= esc($student['full_name']) ?></strong>?</p>
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
        // NISN Validation (Numbers only)
        $('input[name="nisn"]').on('keyup', function() {
            const value = $(this).val();
            $(this).val(value.replace(/[^0-9]/g, ''));
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