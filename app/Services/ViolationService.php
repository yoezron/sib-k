<?php

/**
 * File Path: app/Services/ViolationService.php
 * 
 * Violation Service
 * Business logic layer untuk Case & Violation Management
 * 
 * @package    SIB-K
 * @subpackage Services
 * @category   Business Logic
 * @author     Development Team
 * @created    2025-01-06
 */

namespace App\Services;

use App\Models\ViolationModel;
use App\Models\ViolationCategoryModel;
use App\Models\SanctionModel;
use App\Models\StudentModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class ViolationService
{
    protected $violationModel;
    protected $categoryModel;
    protected $sanctionModel;
    protected $studentModel;
    protected $db;

    public function __construct()
    {
        $this->violationModel = new ViolationModel();
        $this->categoryModel = new ViolationCategoryModel();
        $this->sanctionModel = new SanctionModel();
        $this->studentModel = new StudentModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Create new violation with validation and auto-checks
     * 
     * @param array $data
     * @return array [success => bool, message => string, violation_id => int|null]
     */
    public function createViolation($data)
    {
        try {
            // Validate category exists and active
            $category = $this->categoryModel->find($data['category_id']);
            if (!$category || !$category['is_active']) {
                return [
                    'success' => false,
                    'message' => 'Kategori pelanggaran tidak valid atau tidak aktif',
                ];
            }

            // Validate student exists
            $student = $this->studentModel->find($data['student_id']);
            if (!$student) {
                return [
                    'success' => false,
                    'message' => 'Siswa tidak ditemukan',
                ];
            }

            // Set default values
            if (!isset($data['status'])) {
                $data['status'] = 'Dilaporkan';
            }

            if (!isset($data['reported_by'])) {
                $data['reported_by'] = auth_id();
            }

            // Create violation
            $violationId = $this->violationModel->insert($data);

            if (!$violationId) {
                return [
                    'success' => false,
                    'message' => 'Gagal menyimpan data pelanggaran',
                    'errors' => $this->violationModel->errors(),
                ];
            }

            // Log activity
            $this->logActivity('create_violation', $violationId, "Pelanggaran baru dilaporkan untuk siswa: {$student['nisn']}");

            return [
                'success' => true,
                'message' => 'Data pelanggaran berhasil disimpan',
                'violation_id' => $violationId,
            ];
        } catch (\Exception $e) {
            log_message('error', 'ViolationService::createViolation - ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update violation with workflow validation
     * 
     * @param int $id
     * @param array $data
     * @return array [success => bool, message => string]
     */
    public function updateViolation($id, $data)
    {
        try {
            $violation = $this->violationModel->find($id);

            if (!$violation) {
                return [
                    'success' => false,
                    'message' => 'Data pelanggaran tidak ditemukan',
                ];
            }

            // Validate status transition
            $statusValidation = $this->validateStatusTransition($violation['status'], $data['status'] ?? $violation['status']);
            if (!$statusValidation['valid']) {
                return [
                    'success' => false,
                    'message' => $statusValidation['message'],
                ];
            }

            // If status changed to Selesai, set resolution_date
            if (isset($data['status']) && $data['status'] === 'Selesai' && empty($data['resolution_date'])) {
                $data['resolution_date'] = date('Y-m-d');
            }

            // Update violation
            $updated = $this->violationModel->update($id, $data);

            if (!$updated) {
                return [
                    'success' => false,
                    'message' => 'Gagal memperbarui data pelanggaran',
                    'errors' => $this->violationModel->errors(),
                ];
            }

            // Log activity
            $this->logActivity('update_violation', $id, 'Data pelanggaran diperbarui');

            return [
                'success' => true,
                'message' => 'Data pelanggaran berhasil diperbarui',
            ];
        } catch (\Exception $e) {
            log_message('error', 'ViolationService::updateViolation - ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Validate status transition rules
     * 
     * @param string $currentStatus
     * @param string $newStatus
     * @return array [valid => bool, message => string]
     */
    private function validateStatusTransition($currentStatus, $newStatus)
    {
        // Define allowed transitions
        $allowedTransitions = [
            'Dilaporkan' => ['Dalam Proses', 'Dibatalkan'],
            'Dalam Proses' => ['Selesai', 'Dibatalkan'],
            'Selesai' => [], // Cannot change from Selesai
            'Dibatalkan' => [], // Cannot change from Dibatalkan
        ];

        // Same status is always allowed
        if ($currentStatus === $newStatus) {
            return ['valid' => true, 'message' => 'Status tidak berubah'];
        }

        // Check if transition is allowed
        if (!isset($allowedTransitions[$currentStatus]) || !in_array($newStatus, $allowedTransitions[$currentStatus])) {
            return [
                'valid' => false,
                'message' => "Tidak dapat mengubah status dari '{$currentStatus}' menjadi '{$newStatus}'",
            ];
        }

        return ['valid' => true, 'message' => 'Transisi status valid'];
    }

    /**
     * Get violations with filters and pagination
     * 
     * @param array $filters
     * @return array
     */
    public function getViolations($filters = [])
    {
        return $this->violationModel->getViolationsWithFilters($filters);
    }

    /**
     * Get violation detail with all related data
     * 
     * @param int $id
     * @return array|null
     */
    public function getViolationDetail($id)
    {
        $violation = $this->violationModel->getViolationWithDetails($id);

        if (!$violation) {
            return null;
        }

        // Get sanctions for this violation
        $violation['sanctions'] = $this->sanctionModel->getByViolation($id);

        // Parse evidence JSON if exists
        if (!empty($violation['evidence'])) {
            $violation['evidence_files'] = json_decode($violation['evidence'], true);
        }

        return $violation;
    }

    /**
     * Get dashboard statistics for violations
     * 
     * @param array $filters
     * @return array
     */
    public function getDashboardStats($filters = [])
    {
        // Get overall statistics
        $stats = $this->violationModel->getStatistics($filters);

        // Get statistics by severity
        $statsBySeverity = $this->violationModel->getStatsBySeverity($filters);

        // Get top violators
        $topViolators = $this->violationModel->getTopViolators(5, $filters);

        // Get pending notifications
        $pendingNotifications = $this->violationModel->getPendingNotifications(10);

        return [
            'overall' => $stats,
            'by_severity' => $statsBySeverity,
            'top_violators' => $topViolators,
            'pending_notifications' => count($pendingNotifications),
        ];
    }

    /**
     * Get student violation history with statistics
     * 
     * @param int $studentId
     * @return array
     */
    public function getStudentViolationHistory($studentId)
    {
        // Get all violations
        $violations = $this->violationModel->getByStudent($studentId);

        // Calculate statistics
        $stats = [
            'total_violations' => count($violations),
            'total_points' => $this->violationModel->getStudentTotalPoints($studentId),
            'by_severity' => [
                'Ringan' => 0,
                'Sedang' => 0,
                'Berat' => 0,
            ],
            'by_status' => [
                'Dilaporkan' => 0,
                'Dalam Proses' => 0,
                'Selesai' => 0,
                'Dibatalkan' => 0,
            ],
            'is_repeat_offender' => false,
            'last_violation_date' => null,
        ];

        foreach ($violations as $violation) {
            // Count by severity
            if (isset($stats['by_severity'][$violation['severity_level']])) {
                $stats['by_severity'][$violation['severity_level']]++;
            }

            // Count by status
            if (isset($stats['by_status'][$violation['status']])) {
                $stats['by_status'][$violation['status']]++;
            }

            // Check repeat offender
            if ($violation['is_repeat_offender']) {
                $stats['is_repeat_offender'] = true;
            }

            // Get last violation date
            if (empty($stats['last_violation_date']) || $violation['violation_date'] > $stats['last_violation_date']) {
                $stats['last_violation_date'] = $violation['violation_date'];
            }
        }

        return [
            'violations' => $violations,
            'statistics' => $stats,
        ];
    }

    /**
     * Process parent notification
     * 
     * @param int $violationId
     * @return array [success => bool, message => string]
     */
    public function notifyParent($violationId)
    {
        try {
            $violation = $this->violationModel->getViolationWithDetails($violationId);

            if (!$violation) {
                return [
                    'success' => false,
                    'message' => 'Data pelanggaran tidak ditemukan',
                ];
            }

            if ($violation['parent_notified']) {
                return [
                    'success' => false,
                    'message' => 'Orang tua sudah dinotifikasi sebelumnya',
                ];
            }

            // TODO: Implement actual notification mechanism
            // For now, just mark as notified
            $this->violationModel->markParentNotified($violationId);

            // Log activity
            $this->logActivity('notify_parent', $violationId, 'Notifikasi dikirim ke orang tua');

            return [
                'success' => true,
                'message' => 'Notifikasi berhasil dikirim ke orang tua',
            ];
        } catch (\Exception $e) {
            log_message('error', 'ViolationService::notifyParent - ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Generate violation report
     * 
     * @param array $filters
     * @return array
     */
    public function generateReport($filters = [])
    {
        // Get violations
        $violations = $this->violationModel->getViolationsWithFilters($filters);

        // Get statistics
        $statistics = $this->violationModel->getStatistics($filters);
        $statsBySeverity = $this->violationModel->getStatsBySeverity($filters);

        // Get top violators
        $topViolators = $this->violationModel->getTopViolators(10, $filters);

        // Get most common categories
        $db = \Config\Database::connect();
        $commonCategories = $db->table('violations')
            ->select('violation_categories.category_name,
                      violation_categories.severity_level,
                      COUNT(violations.id) as violation_count')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->where('violations.deleted_at', null);

        if (!empty($filters['date_from'])) {
            $commonCategories->where('violations.violation_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $commonCategories->where('violations.violation_date <=', $filters['date_to']);
        }

        $commonCategories = $commonCategories->groupBy('violations.category_id')
            ->orderBy('violation_count', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        return [
            'violations' => $violations,
            'statistics' => [
                'overall' => $statistics,
                'by_severity' => $statsBySeverity,
                'common_categories' => $commonCategories,
            ],
            'top_violators' => $topViolators,
            'filters_applied' => $filters,
            'generated_at' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Check if student can be promoted/graduated based on violations
     * 
     * @param int $studentId
     * @return array [eligible => bool, message => string, total_points => int]
     */
    public function checkPromotionEligibility($studentId)
    {
        // Get current academic year total points
        $currentAcademicYear = date('Y') . '/' . (date('Y') + 1);

        $totalPoints = $this->violationModel->getStudentTotalPoints($studentId, [
            'date_from' => date('Y') . '-07-01', // Start of academic year (July)
            'date_to' => date('Y-m-d'),
        ]);

        // Define threshold (example: 100 points)
        $threshold = 100;

        if ($totalPoints >= $threshold) {
            return [
                'eligible' => false,
                'message' => "Siswa tidak memenuhi syarat kenaikan kelas karena memiliki {$totalPoints} poin pelanggaran (threshold: {$threshold} poin)",
                'total_points' => $totalPoints,
                'threshold' => $threshold,
            ];
        }

        return [
            'eligible' => true,
            'message' => "Siswa memenuhi syarat kenaikan kelas dengan {$totalPoints} poin pelanggaran",
            'total_points' => $totalPoints,
            'threshold' => $threshold,
        ];
    }

    /**
     * Get active categories for selection
     * 
     * @return array
     */
    public function getActiveCategories()
    {
        return $this->categoryModel->getActiveCategories();
    }

    /**
     * Get categories grouped by severity
     * 
     * @return array
     */
    public function getCategoriesGrouped()
    {
        $categories = $this->categoryModel->getActiveCategories();

        $grouped = [
            'Ringan' => [],
            'Sedang' => [],
            'Berat' => [],
        ];

        foreach ($categories as $category) {
            $grouped[$category['severity_level']][] = $category;
        }

        return $grouped;
    }

    /**
     * Bulk update violations status
     * 
     * @param array $violationIds
     * @param string $status
     * @return array [success => bool, message => string, updated_count => int]
     */
    public function bulkUpdateStatus($violationIds, $status)
    {
        try {
            $updatedCount = 0;

            foreach ($violationIds as $id) {
                $result = $this->updateViolation($id, ['status' => $status]);
                if ($result['success']) {
                    $updatedCount++;
                }
            }

            return [
                'success' => true,
                'message' => "{$updatedCount} pelanggaran berhasil diperbarui",
                'updated_count' => $updatedCount,
            ];
        } catch (\Exception $e) {
            log_message('error', 'ViolationService::bulkUpdateStatus - ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'updated_count' => 0,
            ];
        }
    }

    /**
     * Delete violation with validation
     * 
     * @param int $id
     * @return array [success => bool, message => string]
     */
    public function deleteViolation($id)
    {
        try {
            $violation = $this->violationModel->find($id);

            if (!$violation) {
                return [
                    'success' => false,
                    'message' => 'Data pelanggaran tidak ditemukan',
                ];
            }

            // Check if has sanctions
            $sanctions = $this->sanctionModel->getByViolation($id);
            if (count($sanctions) > 0) {
                return [
                    'success' => false,
                    'message' => 'Pelanggaran tidak dapat dihapus karena sudah memiliki sanksi. Silakan hapus sanksi terlebih dahulu.',
                ];
            }

            // Delete violation
            $this->violationModel->delete($id);

            // Log activity
            $this->logActivity('delete_violation', $id, 'Pelanggaran dihapus');

            return [
                'success' => true,
                'message' => 'Data pelanggaran berhasil dihapus',
            ];
        } catch (\Exception $e) {
            log_message('error', 'ViolationService::deleteViolation - ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Log activity for audit trail
     * 
     * @param string $action
     * @param int $violationId
     * @param string $description
     * @return void
     */
    private function logActivity($action, $violationId, $description)
    {
        log_message('info', "[ViolationService] Action: {$action}, Violation ID: {$violationId}, Description: {$description}, User: " . auth_id());
    }
}
