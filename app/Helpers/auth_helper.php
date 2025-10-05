<?php

/**
 * File Path: app/Helpers/auth_helper.php
 * 
 * Authentication Helper
 * Menyediakan helper functions untuk authentication yang dapat digunakan
 * di views dan controllers
 * 
 * @package    SIB-K
 * @subpackage Helpers
 * @category   Authentication
 * @author     Development Team
 * @created    2025-01-01
 */

if (!function_exists('is_logged_in')) {
    /**
     * Check if user is logged in
     * 
     * @return bool
     */
    function is_logged_in(): bool
    {
        $session = \Config\Services::session();
        return $session->get('is_logged_in') === true;
    }
}

if (!function_exists('auth_user')) {
    /**
     * Get current authenticated user data
     * 
     * @return array|null
     */
    function auth_user(): ?array
    {
        if (!is_logged_in()) {
            return null;
        }

        $session = \Config\Services::session();

        return [
            'id'            => $session->get('user_id'),
            'username'      => $session->get('username'),
            'email'         => $session->get('email'),
            'full_name'     => $session->get('full_name'),
            'role_id'       => $session->get('role_id'),
            'role_name'     => $session->get('role_name'),
            'permissions'   => $session->get('permissions') ?? [],
            'profile_photo' => $session->get('profile_photo'),
        ];
    }
}

if (!function_exists('auth_id')) {
    /**
     * Get current user ID
     * 
     * @return int|null
     */
    function auth_id(): ?int
    {
        $session = \Config\Services::session();
        return $session->get('user_id');
    }
}

if (!function_exists('auth_role')) {
    /**
     * Get current user role
     * 
     * @return string|null
     */
    function auth_role(): ?string
    {
        $session = \Config\Services::session();
        return $session->get('role_name');
    }
}

if (!function_exists('auth_name')) {
    /**
     * Get current user full name
     * 
     * @return string|null
     */
    function auth_name(): ?string
    {
        $session = \Config\Services::session();
        return $session->get('full_name');
    }
}

if (!function_exists('has_role')) {
    /**
     * Check if user has specific role
     * 
     * @param string|array $roles
     * @return bool
     */
    function has_role($roles): bool
    {
        if (!is_logged_in()) {
            return false;
        }

        $session = \Config\Services::session();
        $userRole = $session->get('role_name');

        if (is_array($roles)) {
            return in_array($userRole, $roles);
        }

        return $userRole === $roles;
    }
}

if (!function_exists('has_permission')) {
    /**
     * Check if user has specific permission
     * 
     * @param string|array $permissions
     * @return bool
     */
    function has_permission($permissions): bool
    {
        if (!is_logged_in()) {
            return false;
        }

        $session = \Config\Services::session();
        $userPermissions = $session->get('permissions') ?? [];

        // Admin has all permissions
        if ($session->get('role_name') === 'Admin') {
            return true;
        }

        if (is_array($permissions)) {
            // Check if user has ANY of the permissions
            return count(array_intersect($permissions, $userPermissions)) > 0;
        }

        return in_array($permissions, $userPermissions);
    }
}

if (!function_exists('is_admin')) {
    /**
     * Check if user is admin
     * 
     * @return bool
     */
    function is_admin(): bool
    {
        return has_role('Admin');
    }
}

if (!function_exists('is_koordinator')) {
    /**
     * Check if user is Koordinator BK
     * 
     * @return bool
     */
    function is_koordinator(): bool
    {
        return has_role('Koordinator BK');
    }
}

if (!function_exists('is_guru_bk')) {
    /**
     * Check if user is Guru BK
     * 
     * @return bool
     */
    function is_guru_bk(): bool
    {
        return has_role('Guru BK');
    }
}

if (!function_exists('is_wali_kelas')) {
    /**
     * Check if user is Wali Kelas
     * 
     * @return bool
     */
    function is_wali_kelas(): bool
    {
        return has_role('Wali Kelas');
    }
}

if (!function_exists('is_siswa')) {
    /**
     * Check if user is Siswa
     * 
     * @return bool
     */
    function is_siswa(): bool
    {
        return has_role('Siswa');
    }
}

if (!function_exists('is_orang_tua')) {
    /**
     * Check if user is Orang Tua
     * 
     * @return bool
     */
    function is_orang_tua(): bool
    {
        return has_role('Orang Tua');
    }
}

if (!function_exists('user_avatar')) {
    /**
     * Get user avatar URL
     * 
     * @param string|null $photo
     * @return string
     */
    function user_avatar(?string $photo = null): string
    {
        if (!$photo) {
            $session = \Config\Services::session();
            $photo = $session->get('profile_photo');
        }

        if ($photo && file_exists(FCPATH . 'uploads/profiles/' . $photo)) {
            return base_url('uploads/profiles/' . $photo);
        }

        // Default avatar
        return base_url('assets/images/default-avatar.png');
    }
}

if (!function_exists('redirect_to_login')) {
    /**
     * Redirect to login page
     * 
     * @param string|null $message
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    function redirect_to_login(?string $message = null)
    {
        $session = \Config\Services::session();

        // Store intended URL
        $session->set('redirect_url', current_url());

        if ($message) {
            $session->setFlashdata('error', $message);
        }

        return redirect()->to('/login');
    }
}

if (!function_exists('redirect_to_dashboard')) {
    /**
     * Redirect to appropriate dashboard based on user role
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    function redirect_to_dashboard()
    {
        $role = auth_role();

        $dashboards = [
            'Admin'          => '/admin/dashboard',
            'Koordinator BK' => '/koordinator/dashboard',
            'Guru BK'        => '/counselor/dashboard',
            'Wali Kelas'     => '/homeroom/dashboard',
            'Siswa'          => '/student/dashboard',
            'Orang Tua'      => '/parent/dashboard',
        ];

        $url = $dashboards[$role] ?? '/';

        return redirect()->to($url);
    }
}

if (!function_exists('check_permission')) {
    /**
     * Check permission and redirect if not authorized
     * 
     * @param string|array $permissions
     * @return void
     */
    function check_permission($permissions): void
    {
        if (!has_permission($permissions)) {
            $session = \Config\Services::session();
            $session->setFlashdata('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
            redirect_to_dashboard()->send();
            exit;
        }
    }
}

if (!function_exists('check_role')) {
    /**
     * Check role and redirect if not authorized
     * 
     * @param string|array $roles
     * @return void
     */
    function check_role($roles): void
    {
        if (!has_role($roles)) {
            $session = \Config\Services::session();
            $session->setFlashdata('error', 'Akses ditolak. Halaman ini hanya untuk role tertentu.');
            redirect_to_dashboard()->send();
            exit;
        }
    }
}

if (!function_exists('get_user_initials')) {
    /**
     * Get user initials from full name
     * 
     * @param string|null $name
     * @return string
     */
    function get_user_initials(?string $name = null): string
    {
        if (!$name) {
            $name = auth_name();
        }

        if (!$name) {
            return 'U';
        }

        $words = explode(' ', $name);
        $initials = '';

        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }

        return substr($initials, 0, 2);
    }
}

if (!function_exists('format_role_badge')) {
    /**
     * Format role as HTML badge
     * 
     * @param string $role
     * @return string
     */
    function format_role_badge(string $role): string
    {
        $badges = [
            'Admin'          => '<span class="badge bg-danger">Admin</span>',
            'Koordinator BK' => '<span class="badge bg-primary">Koordinator BK</span>',
            'Guru BK'        => '<span class="badge bg-info">Guru BK</span>',
            'Wali Kelas'     => '<span class="badge bg-warning">Wali Kelas</span>',
            'Siswa'          => '<span class="badge bg-success">Siswa</span>',
            'Orang Tua'      => '<span class="badge bg-secondary">Orang Tua</span>',
        ];

        return $badges[$role] ?? '<span class="badge bg-dark">' . esc($role) . '</span>';
    }
}
