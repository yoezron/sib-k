<?php

/**
 * File Path: app/Filters/RoleFilter.php
 * 
 * Role Filter
 * Middleware untuk memeriksa role user sebelum mengakses halaman tertentu
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

class RoleFilter implements FilterInterface
{
    /**
     * Check if user has required role
     *
     * @param RequestInterface $request
     * @param array|null       $arguments (array of allowed roles)
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

        $userRole = $session->get('role_name');

        // If no specific roles required, allow access
        if (empty($arguments)) {
            return;
        }

        // Check if user has one of the allowed roles
        if (!in_array($userRole, $arguments)) {
            // Log unauthorized access attempt
            log_message('warning', "Unauthorized access attempt by user {$session->get('username')} (Role: {$userRole}) to " . uri_string());

            // Redirect to appropriate dashboard based on user role
            return redirect()->to($this->getRedirectPath($userRole))
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
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
