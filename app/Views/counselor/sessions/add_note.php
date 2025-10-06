<?php

/**
 * File Path: app/Views/counselor/sessions/add_note.php
 * 
 * Add Note Modal
 * Modal untuk menambahkan catatan ke sesi konseling
 * 
 * @package    SIB-K
 * @subpackage Views/Counselor/Sessions
 * @category   View
 * @author     Development Team
 * @created    2025-01-06
 */
?>

<!-- Add Note Modal -->
<div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="<?= base_url('counselor/sessions/addNote/' . $session['id']) ?>" method="POST" id="addNoteForm">
                <?= csrf_field() ?>

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addNoteModalLabel">
                        <i class="mdi mdi-note-plus-outline me-2"></i>Tambah Catatan Sesi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Session Info Summary -->
                    <div class="alert alert-info border-0 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="mdi mdi-information-outline fs-3"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1"><?= esc($session['topic']) ?></h6>
                                <small class="text-muted">
                                    <i class="mdi mdi-calendar me-1"></i><?= date('d M Y', strtotime($session['session_date'])) ?>
                                    <?php if ($session['student_name']): ?>
                                        | <i class="mdi mdi-account me-1"></i><?= esc($session['student_name']) ?>
                                    <?php elseif ($session['class_name']): ?>
                                        | <i class="mdi mdi-google-classroom me-1"></i>Kelas <?= esc($session['class_name']) ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Note Content -->
                    <div class="mb-3">
                        <label for="noteContent" class="form-label required">
                            <i class="mdi mdi-text-box-outline me-1"></i>Isi Catatan
                        </label>
                        <textarea
                            name="note_content"
                            id="noteContent"
                            class="form-control"
                            rows="6"
                            placeholder="Tuliskan catatan tentang sesi konseling ini... (perkembangan siswa, observasi, hasil diskusi, dll)"
                            required><?= old('note_content') ?></textarea>
                        <div class="form-text">
                            <i class="mdi mdi-information-outline me-1"></i>
                            Catatan akan tersimpan dengan timestamp dan nama Anda secara otomatis
                        </div>
                    </div>

                    <!-- Important Flag -->
                    <div class="mb-3">
                        <div class="form-check form-switch form-switch-lg">
                            <input
                                type="checkbox"
                                name="is_important"
                                class="form-check-input"
                                id="isImportantCheck"
                                value="1"
                                <?= old('is_important') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isImportantCheck">
                                <i class="mdi mdi-star text-danger me-1"></i>
                                <strong>Tandai sebagai Catatan Penting</strong>
                            </label>
                        </div>
                        <small class="text-muted ms-5">
                            Catatan penting akan ditampilkan dengan badge khusus dan mudah diidentifikasi
                        </small>
                    </div>

                    <!-- Tips Section -->
                    <div class="alert alert-success border-0 mb-0">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="mdi mdi-lightbulb-on-outline fs-5"></i>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <strong class="d-block mb-2">Tips Menulis Catatan:</strong>
                                <ul class="mb-0 ps-3">
                                    <li>Catat perkembangan atau perubahan perilaku siswa</li>
                                    <li>Dokumentasikan keputusan atau kesepakatan penting</li>
                                    <li>Tulis observasi yang relevan untuk follow-up</li>
                                    <li>Gunakan bahasa yang jelas dan profesional</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-success" id="submitNoteBtn">
                        <i class="mdi mdi-content-save me-1"></i>Simpan Catatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Form Validation and Submit Handler
    document.addEventListener('DOMContentLoaded', function() {
        const addNoteForm = document.getElementById('addNoteForm');
        const submitBtn = document.getElementById('submitNoteBtn');
        const noteContent = document.getElementById('noteContent');

        if (addNoteForm) {
            addNoteForm.addEventListener('submit', function(e) {
                // Validate note content
                if (noteContent.value.trim().length < 10) {
                    e.preventDefault();
                    alert('Catatan harus minimal 10 karakter');
                    noteContent.focus();
                    return false;
                }

                // Disable submit button to prevent double submission
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i>Menyimpan...';
            });

            // Character counter (optional)
            noteContent.addEventListener('input', function() {
                const length = this.value.length;
                const maxLength = 1000; // Set max length if needed

                // You can add character counter display here if needed
                if (length > maxLength) {
                    this.value = this.value.substring(0, maxLength);
                }
            });
        }

        // Reset form when modal is hidden
        const addNoteModal = document.getElementById('addNoteModal');
        if (addNoteModal) {
            addNoteModal.addEventListener('hidden.bs.modal', function() {
                addNoteForm.reset();
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="mdi mdi-content-save me-1"></i>Simpan Catatan';
            });
        }

        // Auto-focus on textarea when modal opens
        if (addNoteModal) {
            addNoteModal.addEventListener('shown.bs.modal', function() {
                noteContent.focus();
            });
        }
    });
</script>

<style>
    /* Modal Specific Styles */
    #addNoteModal .form-check-input {
        cursor: pointer;
    }

    #addNoteModal .form-check-label {
        cursor: pointer;
    }

    #addNoteModal .form-switch .form-check-input {
        width: 3rem;
        height: 1.5rem;
    }

    #addNoteModal textarea {
        resize: vertical;
        min-height: 150px;
    }

    /* Required Field Indicator */
    .required::after {
        content: " *";
        color: #f46a6a;
        font-weight: bold;
    }

    /* Alert Icon Alignment */
    .alert .mdi {
        font-size: 1.25rem;
    }

    /* Tips List Styling */
    .alert ul li {
        margin-bottom: 0.25rem;
        font-size: 0.9rem;
    }

    .alert ul li:last-child {
        margin-bottom: 0;
    }
</style>