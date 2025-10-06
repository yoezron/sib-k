<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2 class="page-title mb-0">
                <i class="fas fa-plus-circle me-2"></i>
                Buat Asesmen Baru
            </h2>
            <p class="text-muted mb-0">Buat asesmen psikologi atau minat bakat untuk siswa</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="<?= base_url('counselor/assessments') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Kembali
            </a>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>Terdapat kesalahan:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Form Card -->
<form method="post" action="<?= base_url('counselor/assessments/store') ?>" id="assessmentForm">
    <?= csrf_field() ?>

    <div class="row">
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2 text-primary"></i>
                        Informasi Dasar
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">
                            Judul Asesmen <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="title" name="title"
                            placeholder="Contoh: Asesmen Minat Bakat Siswa Kelas X"
                            value="<?= old('title') ?>" required>
                        <div class="form-text">Berikan judul yang jelas dan deskriptif</div>
                    </div>

                    <div class="mb-3">
                        <label for="assessment_type" class="form-label">
                            Tipe Asesmen <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="assessment_type" name="assessment_type" required>
                            <option value="">-- Pilih Tipe Asesmen --</option>
                            <?php foreach ($assessment_types as $key => $value): ?>
                                <option value="<?= esc($key) ?>" <?= old('assessment_type') == $key ? 'selected' : '' ?>>
                                    <?= esc($value) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="4"
                            placeholder="Jelaskan tujuan dan manfaat asesmen ini..."><?= old('description') ?></textarea>
                        <div class="form-text">Deskripsi akan membantu siswa memahami tujuan asesmen</div>
                    </div>

                    <div class="mb-3">
                        <label for="instructions" class="form-label">Instruksi Pengerjaan</label>
                        <textarea class="form-control" id="instructions" name="instructions" rows="4"
                            placeholder="Berikan instruksi lengkap untuk siswa..."><?= old('instructions') ?></textarea>
                        <div class="form-text">Panduan yang jelas akan membantu siswa mengerjakan dengan baik</div>
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-cog me-2 text-primary"></i>
                        Pengaturan Asesmen
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="duration_minutes" class="form-label">
                                Durasi (Menit)
                            </label>
                            <input type="number" class="form-control" id="duration_minutes" name="duration_minutes"
                                placeholder="60" value="<?= old('duration_minutes') ?>" min="0">
                            <div class="form-text">Kosongkan jika tidak ada batasan waktu</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="passing_score" class="form-label">
                                Nilai Lulus (%)
                            </label>
                            <input type="number" class="form-control" id="passing_score" name="passing_score"
                                placeholder="75" value="<?= old('passing_score', 75) ?>" min="0" max="100" step="0.01">
                            <div class="form-text">Minimal nilai untuk dinyatakan lulus</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="max_attempts" class="form-label">
                                Maksimal Percobaan
                            </label>
                            <input type="number" class="form-control" id="max_attempts" name="max_attempts"
                                value="<?= old('max_attempts', 1) ?>" min="1" max="10">
                            <div class="form-text">Berapa kali siswa dapat mengerjakan</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label d-block">Opsi Tambahan</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="show_result_immediately"
                                    name="show_result_immediately" value="1"
                                    <?= old('show_result_immediately') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="show_result_immediately">
                                    Tampilkan hasil langsung
                                </label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="allow_review"
                                    name="allow_review" value="1"
                                    <?= old('allow_review', true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="allow_review">
                                    Izinkan review jawaban
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar me-2 text-primary"></i>
                        Jadwal Pelaksanaan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">
                                Tanggal Mulai
                            </label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                value="<?= old('start_date') ?>">
                            <div class="form-text">Kosongkan jika mulai sekarang</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">
                                Tanggal Berakhir
                            </label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                value="<?= old('end_date') ?>">
                            <div class="form-text">Kosongkan jika tidak ada batas akhir</div>
                        </div>
                    </div>

                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Info:</strong> Siswa hanya dapat mengakses asesmen dalam rentang tanggal yang ditentukan.
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="col-lg-4">
            <!-- Target Audience -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2 text-primary"></i>
                        Target Peserta
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="target_audience" class="form-label">
                            Target <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="target_audience" name="target_audience" required onchange="handleTargetChange()">
                            <option value="">-- Pilih Target --</option>
                            <option value="Individual" <?= old('target_audience') == 'Individual' ? 'selected' : '' ?>>Individual</option>
                            <option value="Class" <?= old('target_audience') == 'Class' ? 'selected' : '' ?>>Kelas Tertentu</option>
                            <option value="Grade" <?= old('target_audience') == 'Grade' ? 'selected' : '' ?>>Tingkat Tertentu</option>
                            <option value="All" <?= old('target_audience') == 'All' ? 'selected' : '' ?>>Semua Siswa</option>
                        </select>
                    </div>

                    <div class="mb-3" id="classField" style="display: none;">
                        <label for="target_class_id" class="form-label">Pilih Kelas</label>
                        <select class="form-select" id="target_class_id" name="target_class_id">
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?= $class['id'] ?>" <?= old('target_class_id') == $class['id'] ? 'selected' : '' ?>>
                                    <?= esc($class['class_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3" id="gradeField" style="display: none;">
                        <label for="target_grade" class="form-label">Pilih Tingkat</label>
                        <select class="form-select" id="target_grade" name="target_grade">
                            <option value="">-- Pilih Tingkat --</option>
                            <option value="10" <?= old('target_grade') == '10' ? 'selected' : '' ?>>Kelas 10</option>
                            <option value="11" <?= old('target_grade') == '11' ? 'selected' : '' ?>>Kelas 11</option>
                            <option value="12" <?= old('target_grade') == '12' ? 'selected' : '' ?>>Kelas 12</option>
                        </select>
                    </div>

                    <div class="alert alert-warning mb-0" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <small>Target peserta dapat diubah nanti saat penugasan</small>
                    </div>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body">
                    <h6 class="mb-3">
                        <i class="fas fa-question-circle me-2 text-info"></i>
                        Bantuan
                    </h6>
                    <ul class="small mb-0 ps-3">
                        <li class="mb-2">Asesmen akan disimpan sebagai <strong>Draft</strong> terlebih dahulu</li>
                        <li class="mb-2">Tambahkan pertanyaan setelah membuat asesmen</li>
                        <li class="mb-2"><strong>Publikasi</strong> asesmen setelah semua pertanyaan siap</li>
                        <li class="mb-0">Asesmen yang dipublikasi dapat ditugaskan ke siswa</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('counselor/assessments') ?>" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan & Lanjut ke Pertanyaan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    // Handle target audience change
    function handleTargetChange() {
        const target = document.getElementById('target_audience').value;
        const classField = document.getElementById('classField');
        const gradeField = document.getElementById('gradeField');

        classField.style.display = 'none';
        gradeField.style.display = 'none';

        if (target === 'Class') {
            classField.style.display = 'block';
        } else if (target === 'Grade') {
            gradeField.style.display = 'block';
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        handleTargetChange();

        // Validate end date is after start date
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');

        startDate.addEventListener('change', function() {
            endDate.min = this.value;
        });

        endDate.addEventListener('change', function() {
            if (startDate.value && this.value < startDate.value) {
                alert('Tanggal berakhir tidak boleh lebih awal dari tanggal mulai');
                this.value = '';
            }
        });

        // Form validation
        const form = document.getElementById('assessmentForm');
        form.addEventListener('submit', function(e) {
            const target = document.getElementById('target_audience').value;

            if (target === 'Class') {
                const classId = document.getElementById('target_class_id').value;
                if (!classId) {
                    e.preventDefault();
                    alert('Pilih kelas target');
                    return false;
                }
            } else if (target === 'Grade') {
                const grade = document.getElementById('target_grade').value;
                if (!grade) {
                    e.preventDefault();
                    alert('Pilih tingkat target');
                    return false;
                }
            }
        });
    });
</script>

<style>
    .form-label {
        font-weight: 500;
        color: #495057;
    }

    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .card {
        transition: box-shadow 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }

    .alert {
        border-left: 4px solid;
    }

    .alert-info {
        border-left-color: #0dcaf0;
    }

    .alert-warning {
        border-left-color: #ffc107;
    }
</style>

<?= $this->endSection() ?>