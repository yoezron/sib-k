<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Catat Pelanggaran Siswa</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('homeroom-teacher/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('homeroom-teacher/violations') ?>">Pelanggaran</a></li>
                    <li class="breadcrumb-item active">Catat Pelanggaran</li>
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

<form method="post" action="<?= base_url('homeroom-teacher/violations/store') ?>" id="violationForm">
    <?= csrf_field() ?>

    <div class="row">
        <div class="col-lg-8">
            <!-- Main Form -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Informasi Pelanggaran</h4>
                </div>
                <div class="card-body">
                    <!-- Student Selection -->
                    <div class="mb-3">
                        <label for="student_id" class="form-label">
                            Pilih Siswa <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="student_id" name="student_id" required onchange="loadStudentInfo()">
                            <option value="">-- Pilih Siswa --</option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?= $student['id'] ?>"
                                    data-nisn="<?= esc($student['nisn']) ?>"
                                    data-gender="<?= esc($student['gender']) ?>"
                                    <?= old('student_id') == $student['id'] ? 'selected' : '' ?>>
                                    <?= esc($student['full_name']) ?> - <?= esc($student['nisn']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Pilih siswa yang melakukan pelanggaran</div>
                    </div>

                    <!-- Student Info Card (Hidden by default) -->
                    <div id="studentInfoCard" style="display: none;">
                        <div class="alert alert-info border-0" role="alert">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <i class="bx bx-user-circle font-size-24"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="alert-heading font-size-14" id="studentName">-</h5>
                                    <p class="mb-0">
                                        <strong>NISN:</strong> <span id="studentNisn">-</span> |
                                        <strong>Jenis Kelamin:</strong> <span id="studentGender">-</span>
                                    </p>
                                    <div id="violationHistory" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Violation Date -->
                    <div class="mb-3">
                        <label for="violation_date" class="form-label">
                            Tanggal Kejadian <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" id="violation_date" name="violation_date"
                            max="<?= date('Y-m-d') ?>"
                            value="<?= old('violation_date', date('Y-m-d')) ?>"
                            required>
                        <div class="form-text">Tanggal terjadinya pelanggaran</div>
                    </div>

                    <!-- Category Selection -->
                    <div class="mb-3">
                        <label for="category_id" class="form-label">
                            Kategori Pelanggaran <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="category_id" name="category_id" required onchange="loadCategoryInfo()">
                            <option value="">-- Pilih Kategori --</option>
                            <?php
                            $groupedCategories = [];
                            foreach ($categories as $category) {
                                $groupedCategories[$category['severity_level']][] = $category;
                            }
                            ?>
                            <?php foreach (['Ringan', 'Sedang', 'Berat'] as $severity): ?>
                                <?php if (isset($groupedCategories[$severity])): ?>
                                    <optgroup label="<?= $severity ?>">
                                        <?php foreach ($groupedCategories[$severity] as $category): ?>
                                            <option value="<?= $category['id'] ?>"
                                                data-severity="<?= esc($category['severity_level']) ?>"
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
                        <div class="form-text">Pilih kategori yang sesuai dengan pelanggaran</div>
                    </div>

                    <!-- Category Info Card (Hidden by default) -->
                    <div id="categoryInfoCard" style="display: none;">
                        <div class="alert border-0" role="alert" id="categoryAlert">
                            <h5 class="alert-heading font-size-14">Informasi Kategori</h5>
                            <p class="mb-2">
                                <strong>Tingkat:</strong>
                                <span class="badge" id="severityBadge">-</span>
                            </p>
                            <p class="mb-0">
                                <strong>Poin:</strong> <span id="categoryPoints">-</span> poin<br>
                                <strong>Deskripsi:</strong> <span id="categoryDescription">-</span>
                            </p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">
                            Deskripsi Kejadian <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="description" name="description" rows="5"
                            required minlength="10"
                            placeholder="Jelaskan secara detail kronologi kejadian pelanggaran..."><?= old('description') ?></textarea>
                        <div class="form-text">
                            <span id="charCount">0</span> / 500 karakter (Minimal 10 karakter)
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="mb-3">
                        <label for="location" class="form-label">Lokasi Kejadian</label>
                        <input type="text" class="form-control" id="location" name="location"
                            value="<?= old('location') ?>"
                            placeholder="Contoh: Kantin, Ruang Kelas, Lapangan, dll">
                        <div class="form-text">Tempat terjadinya pelanggaran</div>
                    </div>

                    <!-- Witness -->
                    <div class="mb-3">
                        <label for="witness" class="form-label">Saksi</label>
                        <input type="text" class="form-control" id="witness" name="witness"
                            value="<?= old('witness') ?>"
                            placeholder="Nama saksi atau pihak lain yang menyaksikan">
                        <div class="form-text">Nama saksi atau pihak yang mengetahui kejadian (jika ada)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="col-lg-4">
            <!-- Help Card -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="bx bx-help-circle text-info"></i> Panduan
                    </h4>
                    <div class="mb-3">
                        <h6 class="font-size-13">Tingkat Pelanggaran:</h6>
                        <ul class="ps-3 mb-3 font-size-13">
                            <li class="mb-2">
                                <span class="badge bg-success-subtle text-success">Ringan</span>
                                - Tidak berdampak serius, pembinaan ringan
                            </li>
                            <li class="mb-2">
                                <span class="badge bg-warning-subtle text-warning">Sedang</span>
                                - Perlu perhatian khusus, konseling
                            </li>
                            <li class="mb-2">
                                <span class="badge bg-danger-subtle text-danger">Berat</span>
                                - Sangat serius, sanksi tegas
                            </li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <h6 class="font-size-13">Catatan Penting:</h6>
                        <ul class="ps-3 mb-0 font-size-13 text-muted">
                            <li class="mb-2">Pastikan informasi yang diisi akurat dan objektif</li>
                            <li class="mb-2">Jelaskan kronologi secara detail dan jelas</li>
                            <li class="mb-2">Pelanggaran berat akan otomatis diteruskan ke orang tua</li>
                            <li class="mb-2">Pelanggar berulang akan mendapat perhatian khusus dari BK</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card bg-warning bg-soft">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-xs flex-shrink-0 me-3">
                            <span class="avatar-title bg-warning text-white rounded-circle">
                                <i class="bx bx-info-circle"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 font-size-13">Informasi</h6>
                            <p class="text-muted mb-0 font-size-12">
                                Data pelanggaran akan tercatat dalam sistem dan dapat dilihat oleh Guru BK
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                            <i class="bx bx-save font-size-16 align-middle me-2"></i>
                            Simpan Pelanggaran
                        </button>
                        <a href="<?= base_url('homeroom-teacher/violations') ?>" class="btn btn-light waves-effect">
                            <i class="bx bx-x font-size-16 align-middle me-2"></i>
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    // Character counter for description
    document.getElementById('description').addEventListener('input', function() {
        const count = this.value.length;
        document.getElementById('charCount').textContent = count;

        if (count < 10) {
            document.getElementById('charCount').classList.add('text-danger');
        } else {
            document.getElementById('charCount').classList.remove('text-danger');
        }
    });

    // Load student info when selected
    function loadStudentInfo() {
        const select = document.getElementById('student_id');
        const option = select.options[select.selectedIndex];
        const card = document.getElementById('studentInfoCard');

        if (select.value) {
            const nisn = option.getAttribute('data-nisn');
            const gender = option.getAttribute('data-gender');

            document.getElementById('studentName').textContent = option.text.split(' - ')[0];
            document.getElementById('studentNisn').textContent = nisn;
            document.getElementById('studentGender').textContent = gender === 'L' ? 'Laki-laki' : 'Perempuan';

            card.style.display = 'block';

            // Load violation history via AJAX (optional)
            loadViolationHistory(select.value);
        } else {
            card.style.display = 'none';
        }
    }

    // Load category info when selected
    function loadCategoryInfo() {
        const select = document.getElementById('category_id');
        const option = select.options[select.selectedIndex];
        const card = document.getElementById('categoryInfoCard');

        if (select.value) {
            const severity = option.getAttribute('data-severity');
            const points = option.getAttribute('data-points');
            const description = option.getAttribute('data-description');

            // Set badge color based on severity
            const badge = document.getElementById('severityBadge');
            const alert = document.getElementById('categoryAlert');

            badge.textContent = severity;
            badge.className = 'badge';
            alert.className = 'alert border-0';

            if (severity === 'Ringan') {
                badge.classList.add('bg-success-subtle', 'text-success');
                alert.classList.add('alert-success');
            } else if (severity === 'Sedang') {
                badge.classList.add('bg-warning-subtle', 'text-warning');
                alert.classList.add('alert-warning');
            } else if (severity === 'Berat') {
                badge.classList.add('bg-danger-subtle', 'text-danger');
                alert.classList.add('alert-danger');
            }

            document.getElementById('categoryPoints').textContent = points;
            document.getElementById('categoryDescription').textContent = description || '-';

            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    }

    // Load violation history (AJAX - optional)
    function loadViolationHistory(studentId) {
        // You can implement AJAX call to get student's violation history
        // For now, just show a placeholder
        const historyDiv = document.getElementById('violationHistory');
        historyDiv.innerHTML = '<small class="text-muted"><i class="bx bx-loader bx-spin"></i> Memuat riwayat...</small>';

        // Simulate loading
        setTimeout(() => {
            historyDiv.innerHTML = '<small class="text-muted">Riwayat pelanggaran akan ditampilkan di sini</small>';
        }, 500);
    }

    // Form validation
    document.getElementById('violationForm').addEventListener('submit', function(e) {
        const student = document.getElementById('student_id').value;
        const category = document.getElementById('category_id').value;
        const description = document.getElementById('description').value;

        if (!student || !category || description.length < 10) {
            e.preventDefault();
            alert('Mohon lengkapi semua field yang wajib diisi');
            return false;
        }

        // Confirm submission
        const option = document.getElementById('category_id').options[document.getElementById('category_id').selectedIndex];
        const severity = option.getAttribute('data-severity');

        if (severity === 'Berat') {
            if (!confirm('Pelanggaran berat akan diteruskan ke orang tua. Lanjutkan?')) {
                e.preventDefault();
                return false;
            }
        }
    });

    // Auto-dismiss alerts
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                // Don't auto-close error alerts
                if (!alert.classList.contains('alert-danger')) {
                    bsAlert.close();
                }
            });
        }, 5000);
    });
</script>

<?= $this->endSection() ?>