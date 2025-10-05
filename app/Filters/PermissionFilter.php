<?php

/**
 * File Path: app/Filters/PermissionFilter.php
 * 
 * Permission Filter
 * Middleware untuk memeriksa permission user sebelum mengakses halaman tertentu
 * 
 * @package    SIB-K
 * @subpackage Filters
 * @category   Authorization
 * @author     Development Team
 * @created    2025-01-01
 */

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PermissionFilter implements FilterInterface
{
    /**
     * Check if user has required permission
     *
     * @param RequestInterface $request
     * @param array|null       $arguments (array of required permissions)
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = \Config\Services::session();

        // Check if user is logged in
        if (!$session->get('is_logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // If no specific permissions required, allow access
        if (empty($arguments)) {
            return;
        }

        $userPermissions = $session->get('permissions') ?? [];

        // Admin has all permissions
        if ($session->get('role_name') === 'Admin') {
            return;
        }

        // Check if user has at least one of the required permissions
        $hasPermission = false;
        foreach ($arguments as $permission) {
            if (in_array($permission, $userPermissions)) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            // Log unauthorized access attempt
            log_message('warning', "Permission denied for user {$session->get('username')} to " . uri_string() . ". Required: " . implode(', ', $arguments));

            // Redirect to appropriate dashboard based on user role
            $userRole = $session->get('role_name');
            return redirect()->to($this->getRedirectPath($userRole))
                ->with('error', 'Anda tidak memiliki izin untuk mengakses halaman tersebut.');
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }

    /**
     * Get redirect path based on user role
     *
     * @param string $role
     * @return string
     */
    private function getRedirectPath($role)
    {
        $paths = [
            'Admin'           => '/admin/dashboard',
            'Koordinator BK'  => '/koordinator/dashboard',
            'Guru BK'         => '/counselor/dashboard',
            'Wali Kelas'      => '/homeroom/dashboard',
            'Siswa'           => '/student/dashboard',
            'Orang Tua'       => '/parent/dashboard',
        ];

        return $paths[$role] ?? '/';
    }
}
