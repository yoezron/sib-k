<?php

/**
 * File Path: app/Filters/AuthFilter.php
 * 
 * Auth Filter
 * Middleware untuk memastikan user sudah login sebelum mengakses halaman
 * 
 * @package    SIB-K
 * @subpackage Filters
 * @category   Authentication
 * @author     Development Team
 * @created    2025-01-01
 */

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = \Config\Services::session();

        // Check if user is logged in
        if (!$session->get('is_logged_in')) {
            // Store the intended URL to redirect after login
            $session->set('redirect_url', current_url());

            // Redirect to login page with error message
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        }

        // Check if user account is still active
        $userId = $session->get('user_id');
        if ($userId) {
            $userModel = new \App\Models\UserModel();
            $user = $userModel->find($userId);

            if (!$user || $user['is_active'] != 1) {
                // User is inactive or deleted
                $session->destroy();
                return redirect()->to('/login')->with('error', 'Akun Anda tidak aktif. Silakan hubungi administrator.');
            }
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
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
}
