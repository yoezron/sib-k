<?php

/**
 * File Path: app/Libraries/NotificationService.php
 * 
 * Notification Service
 * Menyediakan service untuk mengelola notifikasi internal sistem
 * 
 * @package    SIB-K
 * @subpackage Libraries
 * @category   Communication
 * @author     Development Team
 * @created    2025-01-01
 */

namespace App\Libraries;

class NotificationService
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Send notification to user(s)
     * 
     * @param int|array $userIds
     * @param string $title
     * @param string $message
     * @param string $type (info, success, warning, danger)
     * @param string|null $link
     * @param array|null $metadata
     * @return bool
     */
    public function send($userIds, $title, $message, $type = 'info', $link = null, $metadata = null)
    {
        if (!is_array($userIds)) {
            $userIds = [$userIds];
        }

        $data = [];
        $timestamp = date('Y-m-d H:i:s');

        foreach ($userIds as $userId) {
            $data[] = [
                'user_id'    => $userId,
                'title'      => $title,
                'message'    => $message,
                'type'       => $type,
                'link'       => $link,
                'metadata'   => $metadata ? json_encode($metadata) : null,
                'is_read'    => 0,
                'created_at' => $timestamp,
            ];
        }

        // Check if notifications table exists
        if (!$this->db->tableExists('notifications')) {
            return false;
        }

        return $this->db->table('notifications')->insertBatch($data);
    }

    /**
     * Send notification to all users with specific role
     * 
     * @param string $roleName
     * @param string $title
     * @param string $message
     * @param string $type
     * @param string|null $link
     * @return bool
     */
    public function sendToRole($roleName, $title, $message, $type = 'info', $link = null)
    {
        $users = $this->db->table('users')
            ->select('users.id')
            ->join('roles', 'roles.id = users.role_id')
            ->where('roles.role_name', $roleName)
            ->where('users.is_active', 1)
            ->get()
            ->getResultArray();

        if (empty($users)) {
            return false;
        }

        $userIds = array_column($users, 'id');

        return $this->send($userIds, $title, $message, $type, $link);
    }

    /**
     * Send notification to all users
     * 
     * @param string $title
     * @param string $message
     * @param string $type
     * @param string|null $link
     * @return bool
     */
    public function sendToAll($title, $message, $type = 'info', $link = null)
    {
        $users = $this->db->table('users')
            ->select('id')
            ->where('is_active', 1)
            ->get()
            ->getResultArray();

        if (empty($users)) {
            return false;
        }

        $userIds = array_column($users, 'id');

        return $this->send($userIds, $title, $message, $type, $link);
    }

    /**
     * Get unread notifications for user
     * 
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getUnread($userId, $limit = 10)
    {
        if (!$this->db->tableExists('notifications')) {
            return [];
        }

        return $this->db->table('notifications')
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Get all notifications for user
     * 
     * @param int $userId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll($userId, $limit = 20, $offset = 0)
    {
        if (!$this->db->tableExists('notifications')) {
            return [];
        }

        return $this->db->table('notifications')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    /**
     * Get unread count for user
     * 
     * @param int $userId
     * @return int
     */
    public function getUnreadCount($userId)
    {
        if (!$this->db->tableExists('notifications')) {
            return 0;
        }

        return $this->db->table('notifications')
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();
    }

    /**
     * Mark notification as read
     * 
     * @param int $notificationId
     * @return bool
     */
    public function markAsRead($notificationId)
    {
        if (!$this->db->tableExists('notifications')) {
            return false;
        }

        return $this->db->table('notifications')
            ->where('id', $notificationId)
            ->update(['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Mark all notifications as read for user
     * 
     * @param int $userId
     * @return bool
     */
    public function markAllAsRead($userId)
    {
        if (!$this->db->tableExists('notifications')) {
            return false;
        }

        return $this->db->table('notifications')
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->update(['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Delete notification
     * 
     * @param int $notificationId
     * @return bool
     */
    public function delete($notificationId)
    {
        if (!$this->db->tableExists('notifications')) {
            return false;
        }

        return $this->db->table('notifications')
            ->where('id', $notificationId)
            ->delete();
    }

    /**
     * Delete all notifications for user
     * 
     * @param int $userId
     * @return bool
     */
    public function deleteAll($userId)
    {
        if (!$this->db->tableExists('notifications')) {
            return false;
        }

        return $this->db->table('notifications')
            ->where('user_id', $userId)
            ->delete();
    }

    /**
     * Clean old notifications (older than X days)
     * 
     * @param int $days
     * @return bool
     */
    public function cleanOldNotifications($days = 30)
    {
        if (!$this->db->tableExists('notifications')) {
            return false;
        }

        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        return $this->db->table('notifications')
            ->where('created_at <', $cutoffDate)
            ->where('is_read', 1)
            ->delete();
    }

    /**
     * Send session reminder notification
     * 
     * @param int $studentId
     * @param array $sessionData
     * @return bool
     */
    public function sendSessionReminder($studentId, $sessionData)
    {
        $title = 'Pengingat Sesi Konseling';
        $message = "Anda memiliki sesi konseling pada {$sessionData['session_date']} pukul {$sessionData['session_time']}";
        $link = '/student/schedule';

        return $this->send($studentId, $title, $message, 'info', $link, $sessionData);
    }

    /**
     * Send violation notification
     * 
     * @param int $studentId
     * @param array $violationData
     * @return bool
     */
    public function sendViolationNotification($studentId, $violationData)
    {
        $title = 'Pelanggaran Tercatat';
        $message = "Pelanggaran baru telah dicatat: {$violationData['violation_type']} (+{$violationData['points']} poin)";
        $link = '/student/violations';

        // Send to student
        $this->send($studentId, $title, $message, 'warning', $link, $violationData);

        // Send to parent if exists
        $student = $this->db->table('students')
            ->where('user_id', $studentId)
            ->get()
            ->getRowArray();

        if ($student && $student['parent_id']) {
            $parentMessage = "Anak Anda telah melakukan pelanggaran: {$violationData['violation_type']} (+{$violationData['points']} poin)";
            $this->send($student['parent_id'], $title, $parentMessage, 'warning', '/parent/violations', $violationData);
        }

        return true;
    }

    /**
     * Send assessment notification
     * 
     * @param int $studentId
     * @param array $assessmentData
     * @return bool
     */
    public function sendAssessmentNotification($studentId, $assessmentData)
    {
        $title = 'Asesmen Baru';
        $message = "Asesmen baru tersedia: {$assessmentData['assessment_title']}. Deadline: {$assessmentData['deadline']}";
        $link = '/student/assessments';

        return $this->send($studentId, $title, $message, 'info', $link, $assessmentData);
    }

    /**
     * Send message notification
     * 
     * @param int $recipientId
     * @param string $senderName
     * @param string $messagePreview
     * @return bool
     */
    public function sendMessageNotification($recipientId, $senderName, $messagePreview)
    {
        $title = 'Pesan Baru';
        $message = "Pesan baru dari {$senderName}: {$messagePreview}";
        $link = '/messages';

        return $this->send($recipientId, $title, $message, 'info', $link);
    }

    /**
     * Get notification statistics for user
     * 
     * @param int $userId
     * @return array
     */
    public function getStatistics($userId)
    {
        if (!$this->db->tableExists('notifications')) {
            return [
                'total'  => 0,
                'unread' => 0,
                'read'   => 0,
            ];
        }

        $total = $this->db->table('notifications')
            ->where('user_id', $userId)
            ->countAllResults();

        $unread = $this->db->table('notifications')
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();

        return [
            'total'  => $total,
            'unread' => $unread,
            'read'   => $total - $unread,
        ];
    }
}
