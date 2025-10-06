<?php

/**
 * File Path: app/Views/counselor/cases/add_sanction.php
 * 
 * Add Sanction Modal
 * Modal untuk menambahkan sanksi ke pelanggaran siswa
 * 
 * @package    SIB-K
 * @subpackage Views/Counselor/Cases
 * @category   View
 * @author     Development Team
 * @created    2025-01-06
 */
?>

<!-- Add Sanction Modal -->
<div class="modal fade" id="addSanctionModal" tabindex="-1" aria-labelledby="addSanctionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="<?= base_url('counselor/cases/addSanction/' . $violation['id']) ?>" method="POST" id="addSanctionForm">
                <?= csrf_field() ?>

                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="addSanctionModalLabel">
                        <i class="mdi mdi-gavel me-2"></i>Tambah Sanksi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Violation Info Summary -->
                    <div class="alert alert-info border-0 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="mdi mdi-information-outline fs-3"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1"><?= esc($violation['category_name']) ?></h6>
                                <small class="text-muted">
                                    <i class="mdi mdi-calendar me-1"></i><?= date('d M Y', strtotime($violation['violation_date'])) ?>
                                    | <i class="mdi mdi-account me-1"></i><?= esc($violation['student_name']) ?>
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Sanction Type -->
                    <div class="mb-3">
                        <label for="sanctionType" class="form-label required">
                            <i class="mdi mdi-format-list-bulleted me-1"></i>Jenis Sanksi
                        </label>
                        <select name="sanction_type" id="sanctionType" class="form-select" required>
                            <option value="">-- Pilih Jenis Sanksi --</option>
                            <?php foreach ($sanction_types as $type): ?>
                                <option value="<?= esc($type) ?>" <?= old('sanction_type') === $type ? 'selected' : '' ?>>
                                    <?= esc($type) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">
                            <i class="mdi mdi-information-outline me-1"></i>
                            Pilih jenis sanksi yang akan diberikan kepada siswa
                        </div>
                    </div>

                    <!-- Sanction Date -->
                    <div class="mb-3">
                        <label for="sanctionDate" class="form-label required">
                            <i class="mdi mdi-calendar me-1"></i>Tanggal Pemberian Sanksi
                        </label>
                        <input
                            type="date"
                            name="sanction_date"
                            id="sanctionDate"
                            class="form-control"
                            value="<?= old('sanction_date', date('Y-m-d')) ?>"
                            max="<?= date('Y-m-d') ?>"
                            required>
                        <div class="form-text">
                            Tanggal sanksi diberikan/diputuskan
                        </div>
                    </div>

                    <!-- Execution Period (Optional) -->
                    <div class="border rounded p-3 mb-3 bg-light">
                        <h6 class="mb-3">
                            <i class="mdi mdi-calendar-range me-1"></i>Periode Pelaksanaan
                            <small class="text-muted">(Opsional - untuk skorsing, pembinaan berkala, dll)</small>
                        </h6>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="startDate" class="form-label">Tanggal Mulai</label>
                                <input
                                    type="date"
                                    name="start_date"
                                    id="startDate"
                                    class="form-control"
                                    value="<?= old('start_date') ?>"
                                    onchange="calculateDuration()">
                            </div>
                            <div class="col-md-6">
                                <label for="endDate" class="form-label">Tanggal Selesai</label>
                                <input
                                    type="date"
                                    name="end_date"
                                    id="endDate"
                                    class="form-control"
                                    value="<?= old('end_date') ?>"
                                    onchange="calculateDuration()">
                            </div>
                        </div>

                        <div id="durationDisplay" class="mt-2" style="display: none;">
                            <div class="alert alert-success mb-0">
                                <i class="mdi mdi-information-outline me-1"></i>
                                Durasi sanksi: <strong id="durationText">0 hari</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="sanctionDescription" class="form-label required">
                            <i class="mdi mdi-text-box-outline me-1"></i>Deskripsi Sanksi
                        </label>
                        <textarea
                            name="description"
                            id="sanctionDescription"
                            class="form-control"
                            rows="4"
                            placeholder="Jelaskan detail sanksi yang diberikan, prosedur pelaksanaan, atau syarat-syarat yang harus dipenuhi siswa..."
                            required><?= old('description') ?></textarea>
                        <div class="form-text">
                            Detail lengkap tentang sanksi yang diberikan (minimal 10 karakter)
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label for="sanctionStatus" class="form-label">
                            <i class="mdi mdi-progress-check me-1"></i>Status Sanksi
                        </label>
                        <select name="status" id="sanctionStatus" class="form-select">
                            <option value="Dijadwalkan" selected>Dijadwalkan</option>
                            <option value="Sedang Berjalan">Sedang Berjalan</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                        <div class="form-text">
                            Status pelaksanaan sanksi saat ini
                        </div>
                    </div>

                    <!-- Tips Section -->
                    <div class="alert alert-success border-0 mb-0">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="mdi mdi-lightbulb-on-outline fs-5"></i>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <strong class="d-block mb-2">Tips Pemberian Sanksi:</strong>
                                <ul class="mb-0 ps-3">
                                    <li>Sesuaikan sanksi dengan tingkat keparahan pelanggaran</li>
                                    <li>Jelaskan dengan jelas apa yang harus dilakukan siswa</li>
                                    <li>Sanksi harus edukatif dan tidak melanggar HAM</li>
                                    <li>Dokumentasikan pelaksanaan sanksi dengan baik</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-warning" id="submitSanctionBtn">
                        <i class="mdi mdi-content-save me-1"></i>Simpan Sanksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Calculate duration between start and end date
    function calculateDuration() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const durationDisplay = document.getElementById('durationDisplay');
        const durationText = document.getElementById('durationText');

        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);

            // Calculate difference in days
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (end >= start) {
                durationText.textContent = diffDays + ' hari';
                durationDisplay.style.display = 'block';
                durationDisplay.querySelector('.alert').classList.remove('alert-danger');
                durationDisplay.querySelector('.alert').classList.add('alert-success');
            } else {
                durationText.textContent = 'Tanggal selesai harus setelah tanggal mulai!';
                durationDisplay.style.display = 'block';
                durationDisplay.querySelector('.alert').classList.remove('alert-success');
                durationDisplay.querySelector('.alert').classList.add('alert-danger');
            }
        } else {
            durationDisplay.style.display = 'none';
        }
    }

    // Form validation and submit handler
    document.addEventListener('DOMContentLoaded', function() {
        const addSanctionForm = document.getElementById('addSanctionForm');
        const submitBtn = document.getElementById('submitSanctionBtn');
        const sanctionDescription = document.getElementById('sanctionDescription');

        if (addSanctionForm) {
            addSanctionForm.addEventListener('submit', function(e) {
                // Validate description length
                if (sanctionDescription.value.trim().length < 10) {
                    e.preventDefault();
                    alert('Deskripsi sanksi harus minimal 10 karakter');
                    sanctionDescription.focus();
                    return false;
                }

                // Validate date range if provided
                const startDate = document.getElementById('startDate').value;
                const endDate = document.getElementById('endDate').value;

                if (startDate && endDate) {
                    const start = new Date(startDate);
                    const end = new Date(endDate);

                    if (end < start) {
                        e.preventDefault();
                        alert('Tanggal selesai harus setelah atau sama dengan tanggal mulai');
                        return false;
                    }
                }

                // Disable submit button to prevent double submission
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i>Menyimpan...';
            });
        }

        // Reset form when modal is hidden
        const addSanctionModal = document.getElementById('addSanctionModal');
        if (addSanctionModal) {
            addSanctionModal.addEventListener('hidden.bs.modal', function() {
                addSanctionForm.reset();
                document.getElementById('durationDisplay').style.display = 'none';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="mdi mdi-content-save me-1"></i>Simpan Sanksi';
            });
        }

        // Auto-focus on sanction type when modal opens
        if (addSanctionModal) {
            addSanctionModal.addEventListener('shown.bs.modal', function() {
                document.getElementById('sanctionType').focus();
            });
        }
    });
</script>

<style>
    /* Modal Specific Styles */
    #addSanctionModal .form-label.required::after {
        content: " *";
        color: #f46a6a;
        font-weight: bold;
    }

    #addSanctionModal textarea {
        resize: vertical;
        min-height: 100px;
    }

    /* Alert Icon Alignment */
    #addSanctionModal .alert .mdi {
        font-size: 1.25rem;
    }

    /* Tips List Styling */
    #addSanctionModal .alert ul li {
        margin-bottom: 0.25rem;
        font-size: 0.9rem;
    }

    #addSanctionModal .alert ul li:last-child {
        margin-bottom: 0;
    }

    /* Duration Display Animation */
    #durationDisplay {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>