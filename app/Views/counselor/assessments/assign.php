<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="page-title mb-0">
                <i class="fas fa-user-plus me-2"></i>
                Tugaskan Asesmen
            </h2>
            <p class="text-muted mb-0">
                Pilih siswa yang akan mengerjakan asesmen:
                <strong><?= esc($assessment['title']) ?></strong>
            </p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="<?= base_url('counselor/assessments/' . $assessment['id']) ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Kembali
            </a>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form method="post" action="<?= base_url('counselor/assessments/' . $assessment['id'] . '/assign/process') ?>" id="assignForm">
    <?= csrf_field() ?>

    <div class="row">
        <div class="col-lg-8">
            <!-- Students Selection -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2 text-primary"></i>
                            Pilih Siswa
                        </h5>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary" onclick="selectAll()">
                                <i class="fas fa-check-double me-1"></i>Pilih Semua
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="deselectAll()">
                                <i class="fas fa-times me-1"></i>Batalkan Semua
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search Filter -->
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchStudent"
                                placeholder="Cari siswa berdasarkan nama atau NISN...">
                        </div>
                    </div>

                    <!-- Class Filter Tabs -->
                    <ul class="nav nav-pills mb-4" id="classFilterTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="all-tab" data-bs-toggle="pill"
                                data-bs-target="#all" type="button" role="tab"
                                onclick="filterByClass('all')">
                                Semua Kelas
                                <span class="badge bg-primary ms-1" id="count-all">0</span>
                            </button>
                        </li>
                        <?php $classIndex = 0; ?>
                        <?php foreach ($students_by_class as $className => $students): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="class-<?= $classIndex ?>-tab"
                                    data-bs-toggle="pill" data-bs-target="#class-<?= $classIndex ?>"
                                    type="button" role="tab"
                                    onclick="filterByClass('<?= esc($className) ?>')">
                                    <?= esc($className) ?>
                                    <span class="badge bg-secondary ms-1" id="count-<?= esc($className) ?>"><?= count($students) ?></span>
                                </button>
                            </li>
                            <?php $classIndex++; ?>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Students List -->
                    <div class="student-list">
                        <?php if (empty($students_by_class)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Tidak ada siswa aktif</h5>
                                <p class="text-muted">Pastikan ada siswa yang terdaftar dan aktif</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($students_by_class as $className => $students): ?>
                                <div class="class-group" data-class="<?= esc($className) ?>">
                                    <h6 class="text-muted mb-3 fw-bold">
                                        <i class="fas fa-graduation-cap me-2"></i>
                                        <?= esc($className) ?>
                                    </h6>

                                    <div class="row g-3 mb-4">
                                        <?php foreach ($students as $student): ?>
                                            <div class="col-md-6 student-item"
                                                data-class="<?= esc($className) ?>"
                                                data-name="<?= strtolower($student['full_name']) ?>"
                                                data-nisn="<?= $student['nisn'] ?>">
                                                <div class="card border h-100 student-card">
                                                    <div class="card-body p-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input student-checkbox"
                                                                type="checkbox"
                                                                name="student_ids[]"
                                                                value="<?= $student['id'] ?>"
                                                                id="student-<?= $student['id'] ?>"
                                                                data-class="<?= esc($className) ?>">
                                                            <label class="form-check-label w-100 cursor-pointer"
                                                                for="student-<?= $student['id'] ?>">
                                                                <div class="d-flex align-items-start">
                                                                    <div class="avatar-circle bg-primary bg-opacity-10 text-primary me-3">
                                                                        <?= strtoupper(substr($student['full_name'], 0, 2)) ?>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <h6 class="mb-1"><?= esc($student['full_name']) ?></h6>
                                                                        <div class="small text-muted">
                                                                            <i class="fas fa-id-card me-1"></i>
                                                                            <?= esc($student['nisn']) ?>
                                                                        </div>
                                                                        <?php if (!empty($student['class_name'])): ?>
                                                                            <div class="small text-muted">
                                                                                <i class="fas fa-school me-1"></i>
                                                                                <?= esc($student['class_name']) ?>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="col-lg-4">
            <!-- Assessment Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2 text-primary"></i>
                        Info Asesmen
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="small text-muted mb-1">Judul</label>
                        <div class="fw-bold"><?= esc($assessment['title']) ?></div>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted mb-1">Tipe Asesmen</label>
                        <div>
                            <span class="badge bg-info"><?= esc($assessment['assessment_type']) ?></span>
                        </div>
                    </div>

                    <?php if (!empty($assessment['description'])): ?>
                        <div class="mb-3">
                            <label class="small text-muted mb-1">Deskripsi</label>
                            <div class="small"><?= esc($assessment['description']) ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="small text-muted mb-1">Total Pertanyaan</label>
                        <div>
                            <span class="badge bg-primary"><?= $assessment['total_questions'] ?> Soal</span>
                        </div>
                    </div>

                    <?php if ($assessment['duration_minutes']): ?>
                        <div class="mb-3">
                            <label class="small text-muted mb-1">Durasi</label>
                            <div>
                                <i class="fas fa-clock me-1"></i>
                                <?= $assessment['duration_minutes'] ?> Menit
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($assessment['passing_score']): ?>
                        <div class="mb-3">
                            <label class="small text-muted mb-1">Nilai Lulus</label>
                            <div>
                                <i class="fas fa-chart-line me-1"></i>
                                <?= $assessment['passing_score'] ?>%
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mb-0">
                        <label class="small text-muted mb-1">Maksimal Percobaan</label>
                        <div>
                            <i class="fas fa-redo me-1"></i>
                            <?= $assessment['max_attempts'] ?> Kali
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selection Summary -->
            <div class="card border-0 shadow-sm bg-light mb-4">
                <div class="card-body">
                    <h6 class="mb-3">
                        <i class="fas fa-check-circle me-2 text-success"></i>
                        Ringkasan Pilihan
                    </h6>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Siswa Dipilih:</span>
                        <span class="badge bg-success" id="selectedCount">0</span>
                    </div>
                    <div id="selectedSummary" class="small text-muted">
                        Belum ada siswa yang dipilih
                    </div>
                </div>
            </div>

            <!-- Quick Select -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2 text-warning"></i>
                        Pilihan Cepat
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php foreach ($classes as $class): ?>
                            <button type="button" class="btn btn-outline-primary btn-sm text-start"
                                onclick="selectByClass('<?= esc($class['class_name']) ?>')">
                                <i class="fas fa-check me-2"></i>
                                Pilih semua <?= esc($class['class_name']) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 btn-lg" id="submitBtn" disabled>
                        <i class="fas fa-paper-plane me-2"></i>
                        Tugaskan Asesmen
                    </button>
                    <div class="small text-muted text-center mt-2">
                        Pilih minimal satu siswa untuk melanjutkan
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    let selectedStudents = new Set();

    // Update selection count and summary
    function updateSelection() {
        const checkboxes = document.querySelectorAll('.student-checkbox:checked');
        const count = checkboxes.length;
        const submitBtn = document.getElementById('submitBtn');
        const selectedCount = document.getElementById('selectedCount');
        const selectedSummary = document.getElementById('selectedSummary');

        selectedCount.textContent = count;
        submitBtn.disabled = count === 0;

        if (count === 0) {
            selectedSummary.textContent = 'Belum ada siswa yang dipilih';
        } else {
            selectedSummary.textContent = `${count} siswa siap ditugaskan`;
        }

        // Update class counts
        updateClassCounts();
    }

    // Update counts per class
    function updateClassCounts() {
        const allCheckboxes = document.querySelectorAll('.student-checkbox');
        const classCounts = {};
        let totalChecked = 0;

        allCheckboxes.forEach(cb => {
            const className = cb.dataset.class;
            if (!classCounts[className]) {
                classCounts[className] = {
                    total: 0,
                    checked: 0
                };
            }
            classCounts[className].total++;
            if (cb.checked) {
                classCounts[className].checked++;
                totalChecked++;
            }
        });

        // Update all count
        document.getElementById('count-all').textContent = totalChecked;

        // Update class counts
        for (const className in classCounts) {
            const badge = document.getElementById(`count-${className}`);
            if (badge) {
                badge.textContent = classCounts[className].checked;
            }
        }
    }

    // Select all students
    function selectAll() {
        const visibleCheckboxes = document.querySelectorAll('.student-item:not([style*="display: none"]) .student-checkbox');
        visibleCheckboxes.forEach(cb => cb.checked = true);
        updateSelection();
    }

    // Deselect all students
    function deselectAll() {
        const visibleCheckboxes = document.querySelectorAll('.student-item:not([style*="display: none"]) .student-checkbox');
        visibleCheckboxes.forEach(cb => cb.checked = false);
        updateSelection();
    }

    // Select by class
    function selectByClass(className) {
        const classCheckboxes = document.querySelectorAll(`.student-checkbox[data-class="${className}"]`);
        classCheckboxes.forEach(cb => cb.checked = true);
        updateSelection();
    }

    // Filter by class
    function filterByClass(className) {
        const allItems = document.querySelectorAll('.student-item');
        const allGroups = document.querySelectorAll('.class-group');

        if (className === 'all') {
            allItems.forEach(item => item.style.display = '');
            allGroups.forEach(group => group.style.display = '');
        } else {
            allGroups.forEach(group => {
                if (group.dataset.class === className) {
                    group.style.display = '';
                } else {
                    group.style.display = 'none';
                }
            });
        }
    }

    // Search functionality
    document.getElementById('searchStudent').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const studentItems = document.querySelectorAll('.student-item');

        studentItems.forEach(item => {
            const name = item.dataset.name;
            const nisn = item.dataset.nisn;

            if (name.includes(searchTerm) || nisn.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Listen to checkbox changes
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateSelection);
        });

        // Initial update
        updateSelection();

        // Form validation
        const form = document.getElementById('assignForm');
        form.addEventListener('submit', function(e) {
            const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;

            if (checkedCount === 0) {
                e.preventDefault();
                alert('Pilih minimal satu siswa untuk ditugaskan');
                return false;
            }

            return confirm(`Tugaskan asesmen ini kepada ${checkedCount} siswa?`);
        });
    });
</script>

<style>
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    .student-card {
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .student-card:hover {
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .form-check-input:checked~.form-check-label .student-card {
        border-color: #0d6efd;
        background-color: #f8f9fa;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    .nav-pills .nav-link {
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }

    .nav-pills .nav-link.active {
        background-color: #0d6efd;
    }

    .class-group {
        margin-bottom: 2rem;
    }

    .student-list {
        max-height: 800px;
        overflow-y: auto;
    }

    .form-check-input {
        cursor: pointer;
        width: 1.25rem;
        height: 1.25rem;
    }

    .badge {
        font-size: 0.75rem;
    }
</style>

<?= $this->endSection() ?>