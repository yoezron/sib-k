<?php

/**
 * File Path: app/Views/homeroom_teacher/violations/create.php
 * 
 * Homeroom Teacher Violations Create View
 * Form untuk melaporkan pelanggaran baru
 * 
 * @package    SIB-K
 * @subpackage Views/HomeroomTeacher
 * @category   View
 * @author     Development Team
 * @created    2025-01-07
 */

$this->extend('layouts/main');
$this->section('content');
?>

<!-- Start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18"><?= esc($pageTitle) ?></h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <?php foreach ($breadcrumbs as $breadcrumb): ?>
                        <?php if (isset($breadcrumb['active']) && $breadcrumb['active']): ?>
                            <li class="breadcrumb-item active"><?= esc($breadcrumb['title']) ?></li>
                        <?php else: ?>
                            <li class="breadcrumb-item"><a href="<?= esc($breadcrumb['url']) ?>"><?= esc($breadcrumb['title']) ?></a></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- End page title -->

<!-- Alert Messages -->
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="mdi mdi-alert-circle me-2"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="mdi mdi-alert-circle me-2"></i>
        <strong>Terdapat kesalahan pada form:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-12">
        <!-- Class Info -->
        <div class="card">
            <div class="card-body bg-soft-primary">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm me-3">
                        <span class="avatar-title rounded-circle bg-primary text-white font-size-16">
                            <i class="mdi mdi-google-classroom"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="mb-1"><?= esc($class['class_name']) ?></h5>
                        <p class="text-muted mb-0">
                            Tahun Ajaran <?= esc($class['year_name']) ?> - Semester <?= esc($class['semester']) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="mdi mdi-clipboard-alert text-danger me-2"></i>
                    Formulir Laporan Pelanggaran
                </h5>
                <p class="text-muted mb-4">
                    Lengkapi form di bawah ini untuk melaporkan pelanggaran siswa.
                    Semua informasi yang dilaporkan akan tercatat dalam sistem.
                </p>

                <form action="<?= base_url('homeroom/violations/store') ?>" method="POST" id="violationForm" class="needs-validation" novalidate>
                    <?= csrf_field() ?>

                    <div class="row">
                        <!-- Student Selection -->
                        <div class="col-md-6 mb-3">
                            <label for="student_id" class="form-label required">Nama Siswa</label>
                            <select class="form-select <?= session('errors.student_id') ? 'is-invalid' : '' ?>"
                                id="student_id"
                                name="student_id"
                                required>
                                <option value="">-- Pilih Siswa --</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?= $student['id'] ?>" <?= old('student_id') == $student['id'] ? 'selected' : '' ?>>
                                        <?= esc($student['nisn']) ?> - <?= esc($student['full_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (session('errors.student_id')): ?>
                                <div class="invalid-feedback d-block">
                                    <?= session('errors.student_id') ?>
                                </div>
                            <?php else: ?>
                                <div class="invalid-feedback">
                                    Silakan pilih siswa yang melakukan pelanggaran.
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Violation Category -->
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label required">Kategori Pelanggaran</label>
                            <select class="form-select <?= session('errors.category_id') ? 'is-invalid' : '' ?>"
                                id="category_id"
                                name="category_id"
                                required>
                                <option value="">-- Pilih Kategori --</option>

                                <?php foreach ($groupedCategories as $severity => $cats): ?>
                                    <?php if (!empty($cats)): ?>
                                        <optgroup label="<?= esc($severity) ?>">
                                            <?php foreach ($cats as $category): ?>
                                                <option value="<?= $category['id'] ?>"
                                                    data-points="<?= $category['points'] ?>"
                                                    data-description="<?= esc($category['description']) ?>"
                                                    <?= old('category_id') == $category['id'] ? 'selected' : '' ?>>
                                                    <?= esc($category['category_name']) ?> (<?= $category['points'] ?> poin)
                                                </option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <?php if (session('errors.category_id')): ?>
                                <div class="invalid-feedback d-block">
                                    <?= session('errors.category_id') ?>
                                </div>
                            <?php else: ?>
                                <div class="invalid-feedback">
                                    Silakan pilih kategori pelanggaran.
                                </div>
                            <?php endif; ?>
                            <div id="categoryInfo" class="mt-2 p-2 bg-soft-info rounded" style="display: none;">
                                <small class="text-muted" id="categoryDescription"></small>
                            </div>
                        </div>

                        <!-- Violation Date -->
                        <div class="col-md-6 mb-3">
                            <label for="violation_date" class="form-label required">Tanggal Pelanggaran</label>
                            <input type="date"
                                class="form-control <?= session('errors.violation_date') ? 'is-invalid' : '' ?>"
                                id="violation_date"
                                name="violation_date"
                                value="<?= old('violation_date', date('Y-m-d')) ?>"
                                max="<?= date('Y-m-d') ?>"
                                required>
                            <?php if (session('errors.violation_date')): ?>
                                <div class="invalid-feedback d-block">
                                    <?= session('errors.violation_date') ?>
                                </div>
                            <?php else: ?>
                                <div class="invalid-feedback">
                                    Silakan pilih tanggal pelanggaran.
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Violation Time -->
                        <div class="col-md-6 mb-3">
                            <label for="violation_time" class="form-label">Waktu Pelanggaran (Opsional)</label>
                            <input type="time"
                                class="form-control <?= session('errors.violation_time') ? 'is-invalid' : '' ?>"
                                id="violation_time"
                                name="violation_time"
                                value="<?= old('violation_time') ?>">
                            <?php if (session('errors.violation_time')): ?>
                                <div class="invalid-feedback d-block">
                                    <?= session('errors.violation_time') ?>
                                </div>
                            <?php endif; ?>
                            <small class="text-muted">Contoh: 08:30</small>
                        </div>

                        <!-- Location -->
                        <div class="col-md-12 mb-3">
                            <label for="location" class="form-label">Lokasi Kejadian (Opsional)</label>
                            <input type="text"
                                class="form-control <?= session('errors.location') ? 'is-invalid' : '' ?>"
                                id="location"
                                name="location"
                                placeholder="Contoh: Ruang Kelas X IPA 1, Kantin, Lapangan"
                                value="<?= old('location') ?>"
                                maxlength="200">
                            <?php if (session('errors.location')): ?>
                                <div class="invalid-feedback d-block">
                                    <?= session('errors.location') ?>
                                </div>
                            <?php endif; ?>
                            <small class="text-muted">Maksimal 200 karakter</small>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label required">Deskripsi Pelanggaran</label>
                            <textarea class="form-control <?= session('errors.description') ? 'is-invalid' : '' ?>"
                                id="description"
                                name="description"
                                rows="5"
                                placeholder="Jelaskan secara detail apa yang terjadi, termasuk konteks dan situasi saat pelanggaran dilakukan..."
                                required
                                minlength="10"><?= old('description') ?></textarea>
                            <?php if (session('errors.description')): ?>
                                <div class="invalid-feedback d-block">
                                    <?= session('errors.description') ?>
                                </div>
                            <?php else: ?>
                                <div class="invalid-feedback">
                                    Deskripsi pelanggaran minimal 10 karakter.
                                </div>
                            <?php endif; ?>
                            <small class="text-muted">
                                <span id="charCount">0</span> karakter
                            </small>
                        </div>

                        <!-- Witness -->
                        <div class="col-md-12 mb-3">
                            <label for="witness" class="form-label">Saksi (Opsional)</label>
                            <input type="text"
                                class="form-control <?= session('errors.witness') ? 'is-invalid' : '' ?>"
                                id="witness"
                                name="witness"
                                placeholder="Nama saksi yang melihat kejadian (jika ada)"
                                value="<?= old('witness') ?>"
                                maxlength="200">
                            <?php if (session('errors.witness')): ?>
                                <div class="invalid-feedback d-block">
                                    <?= session('errors.witness') ?>
                                </div>
                            <?php endif; ?>
                            <small class="text-muted">Maksimal 200 karakter</small>
                        </div>
                    </div>

                    <!-- Important Notes -->
                    <div class="alert alert-warning" role="alert">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <i class="mdi mdi-information font-size-24"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="alert-heading">Penting untuk Diperhatikan:</h6>
                                <ul class="mb-0">
                                    <li>Pastikan informasi yang dilaporkan akurat dan sesuai fakta</li>
                                    <li>Laporan ini akan tercatat dalam sistem dan dapat dilihat oleh Guru BK</li>
                                    <li>Orang tua siswa akan menerima notifikasi tentang pelanggaran ini</li>
                                    <li>Laporkan dengan objektif dan profesional</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('homeroom/violations') ?>" class="btn btn-light">
                                    <i class="mdi mdi-arrow-left me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-danger" id="submitBtn">
                                    <i class="mdi mdi-send me-1"></i> Laporkan Pelanggaran
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        // Show category description
        $('#category_id').on('change', function() {
            const selectedOption = $(this).find(':selected');
            const description = selectedOption.data('description');
            const points = selectedOption.data('points');

            if (description) {
                $('#categoryDescription').html(
                    '<i class="mdi mdi-information-outline me-1"></i>' +
                    '<strong>Deskripsi:</strong> ' + description +
                    ' <span class="badge bg-danger ms-2">' + points + ' Poin</span>'
                );
                $('#categoryInfo').slideDown();
            } else {
                $('#categoryInfo').slideUp();
            }
        });

        // Character counter for description
        $('#description').on('keyup', function() {
            const charCount = $(this).val().length;
            $('#charCount').text(charCount);

            if (charCount < 10) {
                $('#charCount').parent().addClass('text-danger').removeClass('text-muted');
            } else {
                $('#charCount').parent().addClass('text-muted').removeClass('text-danger');
            }
        });

        // Initialize character count
        $('#charCount').text($('#description').val().length);

        // Form validation
        const form = document.getElementById('violationForm');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            } else {
                // Disable submit button to prevent double submission
                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...'
                );
            }
            form.classList.add('was-validated');
        }, false);

        // Confirm before leaving if form has changes
        let formChanged = false;
        $('#violationForm :input').on('change', function() {
            formChanged = true;
        });

        $(window).on('beforeunload', function() {
            if (formChanged && !$('#violationForm').data('submitted')) {
                return 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?';
            }
        });

        $('#violationForm').on('submit', function() {
            $(this).data('submitted', true);
            formChanged = false;
        });

        // Select2 for better dropdowns (optional, if available)
        if ($.fn.select2) {
            $('#student_id, #category_id').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: function() {
                    return $(this).find('option:first').text();
                }
            });
        }

        // Auto-hide alerts
        setTimeout(function() {
            $('.alert:not(.alert-warning)').fadeOut('slow');
        }, 5000);
    });
</script>
<?php $this->endSection(); ?>