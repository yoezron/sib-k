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

<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="mdi mdi-block-helper me-2"></i>
        <strong>Terdapat kesalahan:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="mdi mdi-block-helper me-2"></i>
        <?= session('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Class Info -->
<div class="row">
    <div class="col-12">
        <div class="card bg-primary bg-soft">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm flex-shrink-0 me-3">
                        <span class="avatar-title bg-primary rounded-circle font-size-18">
                            <i class="bx bxs-graduation"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="font-size-16 mb-1"><?= esc($homeroom_class['class_name']) ?></h5>
                        <p class="text-muted mb-0">
                            <i class="bx bx-calendar me-1"></i>
                            <?= esc($homeroom_class['year_name']) ?> - Semester <?= esc($homeroom_class['semester']) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form Card -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="bx bx-edit me-1"></i> Form Pencatatan Pelanggaran
                </h4>
            </div>
            <div class="card-body">
                <form action="<?= route_to('homeroom.violations.store') ?>" method="POST" enctype="multipart/form-data" id="violationForm">
                    <?= csrf_field() ?>

                    <div class="row">
                        <!-- Student Selection -->
                        <div class="col-md-6 mb-3">
                            <label for="student_id" class="form-label">
                                Siswa <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="student_id" name="student_id" required>
                                <option value="">-- Pilih Siswa --</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?= $student['id'] ?>"
                                        data-nisn="<?= esc($student['nisn']) ?>"
                                        <?= old('student_id') == $student['id'] ? 'selected' : '' ?>>
                                        <?= esc($student['full_name']) ?> - <?= esc($student['nisn']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Violation Date -->
                        <div class="col-md-6 mb-3">
                            <label for="violation_date" class="form-label">
                                Tanggal Pelanggaran <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                class="form-control"
                                id="violation_date"
                                name="violation_date"
                                value="<?= old('violation_date', date('Y-m-d')) ?>"
                                max="<?= date('Y-m-d') ?>"
                                required>
                        </div>

                        <!-- Category Selection -->
                        <div class="col-md-12 mb-3">
                            <label for="category_id" class="form-label">
                                Kategori Pelanggaran <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">-- Pilih Kategori Pelanggaran --</option>

                                <?php if (isset($grouped_categories['Ringan']) && !empty($grouped_categories['Ringan'])): ?>
                                    <optgroup label="Pelanggaran Ringan">
                                        <?php foreach ($grouped_categories['Ringan'] as $cat): ?>
                                            <option value="<?= $cat['id'] ?>"
                                                data-points="<?= $cat['points'] ?>"
                                                data-severity="Ringan"
                                                <?= old('category_id') == $cat['id'] ? 'selected' : '' ?>>
                                                <?= esc($cat['category_name']) ?> (<?= $cat['points'] ?> poin)
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endif; ?>

                                <?php if (isset($grouped_categories['Sedang']) && !empty($grouped_categories['Sedang'])): ?>
                                    <optgroup label="Pelanggaran Sedang">
                                        <?php foreach ($grouped_categories['Sedang'] as $cat): ?>
                                            <option value="<?= $cat['id'] ?>"
                                                data-points="<?= $cat['points'] ?>"
                                                data-severity="Sedang"
                                                <?= old('category_id') == $cat['id'] ? 'selected' : '' ?>>
                                                <?= esc($cat['category_name']) ?> (<?= $cat['points'] ?> poin)
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endif; ?>

                                <?php if (isset($grouped_categories['Berat']) && !empty($grouped_categories['Berat'])): ?>
                                    <optgroup label="Pelanggaran Berat">
                                        <?php foreach ($grouped_categories['Berat'] as $cat): ?>
                                            <option value="<?= $cat['id'] ?>"
                                                data-points="<?= $cat['points'] ?>"
                                                data-severity="Berat"
                                                <?= old('category_id') == $cat['id'] ? 'selected' : '' ?>>
                                                <?= esc($cat['category_name']) ?> (<?= $cat['points'] ?> poin)
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endif; ?>
                            </select>
                            <div id="categoryInfo" class="mt-2"></div>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">
                                Deskripsi Pelanggaran <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control"
                                id="description"
                                name="description"
                                rows="4"
                                placeholder="Jelaskan kronologi dan detail pelanggaran yang terjadi..."
                                required><?= old('description') ?></textarea>
                            <small class="text-muted">Minimal 10 karakter</small>
                        </div>

                        <!-- Location -->
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">Lokasi Kejadian</label>
                            <input type="text"
                                class="form-control"
                                id="location"
                                name="location"
                                placeholder="Contoh: Ruang Kelas XI-A, Kantin, dll"
                                value="<?= old('location') ?>">
                        </div>

                        <!-- Witness -->
                        <div class="col-md-6 mb-3">
                            <label for="witness" class="form-label">Saksi</label>
                            <input type="text"
                                class="form-control"
                                id="witness"
                                name="witness"
                                placeholder="Nama saksi yang melihat kejadian"
                                value="<?= old('witness') ?>">
                        </div>

                        <!-- Evidence Upload -->
                        <div class="col-md-12 mb-3">
                            <label for="evidence" class="form-label">Bukti / Evidence</label>
                            <input type="file"
                                class="form-control"
                                id="evidence"
                                name="evidence"
                                accept="image/*,.pdf">
                            <small class="text-muted">
                                Format: JPG, PNG, PDF. Maksimal 2MB
                            </small>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-md-12">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="<?= route_to('homeroom.violations.index') ?>" class="btn btn-secondary">
                                    <i class="bx bx-x me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="bx bx-save me-1"></i> Simpan Pelanggaran
                                </button>
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
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category_id');
        const categoryInfo = document.getElementById('categoryInfo');
        const form = document.getElementById('violationForm');
        const submitBtn = document.getElementById('submitBtn');

        // Show category info when selected
        categorySelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (this.value) {
                const points = selectedOption.dataset.points;
                const severity = selectedOption.dataset.severity;

                let badgeClass = severity === 'Ringan' ? 'bg-info' :
                    severity === 'Sedang' ? 'bg-warning' : 'bg-danger';

                categoryInfo.innerHTML = `
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-information me-2"></i>
                    <strong>Tingkat: </strong><span class="badge ${badgeClass}">${severity}</span>
                    <strong class="ms-2">Poin: </strong><span class="badge bg-danger">${points} Poin</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            } else {
                categoryInfo.innerHTML = '';
            }
        });

        // Form validation
        form.addEventListener('submit', function(e) {
            const description = document.getElementById('description').value;

            if (description.length < 10) {
                e.preventDefault();
                alert('Deskripsi pelanggaran minimal 10 karakter');
                return false;
            }

            // Disable submit button to prevent double submission
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bx bx-loader bx-spin me-1"></i> Menyimpan...';
        });

        // File size validation
        document.getElementById('evidence').addEventListener('change', function() {
            const file = this.files[0];
            if (file && file.size > 2097152) { // 2MB in bytes
                alert('Ukuran file terlalu besar. Maksimal 2MB');
                this.value = '';
            }
        });
    });
</script>
<?= $this->endSection() ?>