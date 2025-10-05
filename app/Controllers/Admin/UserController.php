<?php

/**
 * File Path: app/Controllers/Admin/UserController.php
 * 
 * User Controller
 * Handle CRUD operations untuk User management
 * 
 * @package    SIB-K
 * @subpackage Controllers/Admin
 * @category   User Management
 * @author     Development Team
 * @created    2025-01-05
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\UserService;
use App\Validation\UserValidation;

class UserController extends BaseController
{
    protected $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * Display users list
     * 
     * @return string
     */
    public function index()
    {
        // Get filters from request
        $filters = [
            'role_id' => $this->request->getGet('role_id'),
            'is_active' => $this->request->getGet('is_active'),
            'search' => $this->request->getGet('search'),
            'order_by' => $this->request->getGet('order_by') ?? 'users.created_at',
            'order_dir' => $this->request->getGet('order_dir') ?? 'DESC',
        ];

        // Get users with pagination
        $perPage = 10;
        $usersData = $this->userService->getAllUsers($filters, $perPage);

        // Get roles for filter dropdown
        $roles = $this->userService->getRoles();

        // Get statistics
        $stats = $this->userService->getUserStatistics();

        $data = [
            'title' => 'Manajemen Pengguna',
            'page_title' => 'Daftar Pengguna',
            'breadcrumb' => [
                ['title' => 'Admin', 'link' => base_url('admin/dashboard')],
                ['title' => 'Pengguna', 'link' => null],
            ],
            'users' => $usersData['users'],
            'pager' => $usersData['pager'],
            'roles' => $roles,
            'stats' => $stats,
            'filters' => $filters,
        ];

        return view('admin/users/index', $data);
    }

    /**
     * Display create user form
     * 
     * @return string
     */
    public function create()
    {
        // Get roles for dropdown
        $roles = $this->userService->getRoles();

        $data = [
            'title' => 'Tambah Pengguna Baru',
            'page_title' => 'Tambah Pengguna',
            'breadcrumb' => [
                ['title' => 'Admin', 'link' => base_url('admin/dashboard')],
                ['title' => 'Pengguna', 'link' => base_url('admin/users')],
                ['title' => 'Tambah', 'link' => null],
            ],
            'roles' => $roles,
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/users/create', $data);
    }

    /**
     * Store new user
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function store()
    {
        // Validate input
        $rules = UserValidation::createRules();

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Sanitize input
        $data = UserValidation::sanitizeInput($this->request->getPost());

        // Create user
        $result = $this->userService->createUser($data);

        if (!$result['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $result['message']);
        }

        return redirect()->to('admin/users')
            ->with('success', $result['message']);
    }

    /**
     * Display user detail
     * 
     * @param int $id
     * @return string|\CodeIgniter\HTTP\RedirectResponse
     */
    public function show($id)
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return redirect()->to('admin/users')
                ->with('error', 'User tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Pengguna',
            'page_title' => 'Detail Pengguna',
            'breadcrumb' => [
                ['title' => 'Admin', 'link' => base_url('admin/dashboard')],
                ['title' => 'Pengguna', 'link' => base_url('admin/users')],
                ['title' => 'Detail', 'link' => null],
            ],
            'user' => $user,
        ];

        return view('admin/users/show', $data);
    }

    /**
     * Display edit user form
     * 
     * @param int $id
     * @return string|\CodeIgniter\HTTP\RedirectResponse
     */
    public function edit($id)
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return redirect()->to('admin/users')
                ->with('error', 'User tidak ditemukan');
        }

        // Get roles for dropdown
        $roles = $this->userService->getRoles();

        $data = [
            'title' => 'Edit Pengguna',
            'page_title' => 'Edit Pengguna',
            'breadcrumb' => [
                ['title' => 'Admin', 'link' => base_url('admin/dashboard')],
                ['title' => 'Pengguna', 'link' => base_url('admin/users')],
                ['title' => 'Edit', 'link' => null],
            ],
            'user' => $user,
            'roles' => $roles,
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/users/edit', $data);
    }

    /**
     * Update user data
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update($id)
    {
        // Check if user exists
        $user = $this->userService->getUserById($id);
        if (!$user) {
            return redirect()->to('admin/users')
                ->with('error', 'User tidak ditemukan');
        }

        // Validate input
        $rules = UserValidation::updateRules($id);

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Sanitize input
        $data = UserValidation::sanitizeInput($this->request->getPost());

        // Update user
        $result = $this->userService->updateUser($id, $data);

        if (!$result['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $result['message']);
        }

        return redirect()->to('admin/users')
            ->with('success', $result['message']);
    }

    /**
     * Delete user
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete($id)
    {
        // Delete user
        $result = $this->userService->deleteUser($id);

        if (!$result['success']) {
            return redirect()->back()
                ->with('error', $result['message']);
        }

        return redirect()->to('admin/users')
            ->with('success', $result['message']);
    }

    /**
     * Toggle user active status via AJAX
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function toggleActive($id)
    {
        // Toggle active status
        $result = $this->userService->toggleActive($id);

        return $this->response->setJSON($result);
    }

    /**
     * Reset user password
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function resetPassword($id)
    {
        // Check if user exists
        $user = $this->userService->getUserById($id);
        if (!$user) {
            return redirect()->to('admin/users')
                ->with('error', 'User tidak ditemukan');
        }

        // Generate random password
        $newPassword = $this->generateRandomPassword();

        // Change password
        $result = $this->userService->changePassword($id, $newPassword);

        if (!$result['success']) {
            return redirect()->back()
                ->with('error', $result['message']);
        }

        return redirect()->back()
            ->with('success', "Password berhasil direset. Password baru: <strong>{$newPassword}</strong>. Harap catat dan sampaikan kepada user.");
    }

    /**
     * Upload profile photo
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function uploadPhoto($id)
    {
        // Validate file
        $rules = UserValidation::profilePhotoRules();

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->with('error', implode(', ', $this->validator->getErrors()));
        }

        // Get uploaded file
        $file = $this->request->getFile('profile_photo');

        // Upload photo
        $result = $this->userService->uploadProfilePhoto($id, $file);

        if (!$result['success']) {
            return redirect()->back()
                ->with('error', $result['message']);
        }

        return redirect()->back()
            ->with('success', $result['message']);
    }

    /**
     * Export users to Excel
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function export()
    {
        // Get all users
        $filters = [
            'role_id' => $this->request->getGet('role_id'),
            'is_active' => $this->request->getGet('is_active'),
            'search' => $this->request->getGet('search'),
        ];

        $usersData = $this->userService->getAllUsers($filters, 10000); // Get all

        // Prepare data for export
        $exportData = [];
        foreach ($usersData['users'] as $user) {
            $exportData[] = [
                'ID' => $user['id'],
                'Username' => $user['username'],
                'Email' => $user['email'],
                'Nama Lengkap' => $user['full_name'],
                'Role' => $user['role_name'],
                'Telepon' => $user['phone'] ?? '-',
                'Status' => $user['is_active'] == 1 ? 'Aktif' : 'Nonaktif',
                'Terakhir Login' => $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : '-',
                'Dibuat' => date('d/m/Y H:i', strtotime($user['created_at'])),
            ];
        }

        // Create CSV
        $filename = 'users_export_' . date('Y-m-d_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Add BOM for Excel UTF-8 support
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Add headers
        if (!empty($exportData)) {
            fputcsv($output, array_keys($exportData[0]));
        }

        // Add data
        foreach ($exportData as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    /**
     * Search users via AJAX
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function search()
    {
        $keyword = $this->request->getGet('q');

        if (empty($keyword)) {
            return $this->response->setJSON([
                'results' => []
            ]);
        }

        $filters = [
            'search' => $keyword,
        ];

        $usersData = $this->userService->getAllUsers($filters, 10);

        $results = [];
        foreach ($usersData['users'] as $user) {
            $results[] = [
                'id' => $user['id'],
                'text' => $user['full_name'] . ' (' . $user['username'] . ')',
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role_name'],
            ];
        }

        return $this->response->setJSON([
            'results' => $results
        ]);
    }

    /**
     * Generate random password
     * 
     * @param int $length
     * @return string
     */
    private function generateRandomPassword($length = 8)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%';
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $password;
    }
}
