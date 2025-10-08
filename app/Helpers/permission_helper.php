<?php

/**
 * File Path: app/Helpers/permission_helper.php
 * 
 * Permission Helper Functions
 * Menyediakan fungsi-fungsi untuk check permission dan role
 * 
 * Load this helper: helper('permission');
 * 
 * @package    SIB-K
 * @subpackage Helpers
 * @category   Authorization
 * @author     Development Team
 * @created    2025-01-07
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
        return (bool) $session->get('is_logged_in');
    }
}

if (!function_exists('current_user_id')) {
    /**
     * Get current logged in user ID
     * 
     * @return int|null
     */
    function current_user_id(): ?int
    {
        $session = \Config\Services::session();
        return $session->get('user_id');
    }
}

if (!function_exists('current_user')) {
    /**
     * Get current user data from session
     * 
     * @param string|null $key Specific key to get
     * @return mixed
     */
    function current_user(?string $key = null)
    {
        $session = \Config\Services::session();

        if ($key) {
            return $session->get($key);
        }

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

if (!function_exists('current_user_name')) {
    /**
     * Get current user full name
     * 
     * @return string
     */
    function current_user_name(): string
    {
        $session = \Config\Services::session();
        return $session->get('full_name') ?? 'Guest';
    }
}

if (!function_exists('current_user_role')) {
    /**
     * Get current user role name
     * 
     * @return string|null
     */
    function current_user_role(): ?string
    {
        $session = \Config\Services::session();
        return $session->get('role_name');
    }
}

if (!function_exists('has_role')) {
    /**
     * Check if user has specific role
     * 
     * @param string|array $roles Role name(s) to check
     * @return bool
     */
    function has_role($roles): bool
    {
        $session = \Config\Services::session();
        $userRole = $session->get('role_name');

        if (!$userRole) {
            return false;
        }

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
     * @param string|array $permissions Permission(s) to check
     * @return bool
     */
    function has_permission($permissions): bool
    {
        $session = \Config\Services::session();

        // Admin has all permissions
        if ($session->get('role_name') === 'Admin') {
            return true;
        }

        $userPermissions = $session->get('permissions') ?? [];

        if (empty($userPermissions)) {
            return false;
        }

        if (is_array($permissions)) {
            // Check if user has at least one of the permissions
            foreach ($permissions as $permission) {
                if (in_array($permission, $userPermissions)) {
                    return true;
                }
            }
            return false;
        }

        return in_array($permissions, $userPermissions);
    }
}

if (!function_exists('has_all_permissions')) {
    /**
     * Check if user has all specified permissions
     * 
     * @param array $permissions Permissions to check
     * @return bool
     */
    function has_all_permissions(array $permissions): bool
    {
        $session = \Config\Services::session();

        // Admin has all permissions
        if ($session->get('role_name') === 'Admin') {
            return true;
        }

        $userPermissions = $session->get('permissions') ?? [];

        if (empty($userPermissions)) {
            return false;
        }

        foreach ($permissions as $permission) {
            if (!in_array($permission, $userPermissions)) {
                return false;
            }
        }

        return true;
    }
}

if (!function_exists('is_admin')) {
    /**
     * Check if current user is Admin
     * 
     * @return bool
     */
    function is_admin(): bool
    {
        return has_role('Admin');
    }
}

if (!function_exists('is_coordinator')) {
    /**
     * Check if current user is Koordinator BK
     * 
     * @return bool
     */
    function is_coordinator(): bool
    {
        return has_role('Koordinator BK');
    }
}

if (!function_exists('is_counselor')) {
    /**
     * Check if current user is Guru BK
     * 
     * @return bool
     */
    function is_counselor(): bool
    {
        return has_role('Guru BK');
    }
}

if (!function_exists('is_homeroom_teacher')) {
    /**
     * Check if current user is Wali Kelas
     * 
     * @return bool
     */
    function is_homeroom_teacher(): bool
    {
        return has_role('Wali Kelas');
    }
}

if (!function_exists('is_student')) {
    /**
     * Check if current user is Siswa
     * 
     * @return bool
     */
    function is_student(): bool
    {
        return has_role('Siswa');
    }
}

if (!function_exists('is_parent')) {
    /**
     * Check if current user is Orang Tua
     * 
     * @return bool
     */
    function is_parent(): bool
    {
        return has_role('Orang Tua');
    }
}

if (!function_exists('is_bk_staff')) {
    /**
     * Check if user is BK staff (Koordinator BK or Guru BK)
     * 
     * @return bool
     */
    function is_bk_staff(): bool
    {
        return has_role(['Koordinator BK', 'Guru BK']);
    }
}

if (!function_exists('is_teacher')) {
    /**
     * Check if user is teacher (Guru BK, Wali Kelas, or has teacher role)
     * 
     * @return bool
     */
    function is_teacher(): bool
    {
        return has_role(['Guru BK', 'Wali Kelas', 'Koordinator BK']);
    }
}

if (!function_exists('can_manage_users')) {
    /**
     * Check if user can manage users
     * 
     * @return bool
     */
    function can_manage_users(): bool
    {
        return has_permission('manage_users');
    }
}

if (!function_exists('can_manage_students')) {
    /**
     * Check if user can manage students
     * 
     * @return bool
     */
    function can_manage_students(): bool
    {
        return has_permission('manage_students');
    }
}

if (!function_exists('can_view_sessions')) {
    /**
     * Check if user can view counseling sessions
     * 
     * @return bool
     */
    function can_view_sessions(): bool
    {
        return has_permission(['view_sessions', 'manage_sessions']);
    }
}

if (!function_exists('can_manage_sessions')) {
    /**
     * Check if user can manage counseling sessions
     * 
     * @return bool
     */
    function can_manage_sessions(): bool
    {
        return has_permission('manage_sessions');
    }
}

if (!function_exists('can_manage_violations')) {
    /**
     * Check if user can manage violations
     * 
     * @return bool
     */
    function can_manage_violations(): bool
    {
        return has_permission('manage_violations');
    }
}

if (!function_exists('can_manage_assessments')) {
    /**
     * Check if user can manage assessments
     * 
     * @return bool
     */
    function can_manage_assessments(): bool
    {
        return has_permission('manage_assessments');
    }
}

if (!function_exists('can_view_reports')) {
    /**
     * Check if user can view reports
     * 
     * @return bool
     */
    function can_view_reports(): bool
    {
        return has_permission(['view_reports', 'generate_reports']);
    }
}

if (!function_exists('can_generate_reports')) {
    /**
     * Check if user can generate reports
     * 
     * @return bool
     */
    function can_generate_reports(): bool
    {
        return has_permission('generate_reports');
    }
}

if (!function_exists('owns_resource')) {
    /**
     * Check if current user owns a resource
     * 
     * @param int $resource_user_id User ID of resource owner
     * @return bool
     */
    function owns_resource(int $resource_user_id): bool
    {
        return current_user_id() === $resource_user_id;
    }
}

if (!function_exists('can_edit')) {
    /**
     * Check if user can edit a resource
     * Admin can edit all, owner can edit own
     * 
     * @param string   $resource_type Type of resource
     * @param int|null $owner_id      Owner user ID
     * @return bool
     */
    function can_edit(string $resource_type, ?int $owner_id = null): bool
    {
        // Admin can edit everything
        if (is_admin()) {
            return true;
        }

        // If owner_id provided, check ownership
        if ($owner_id !== null && owns_resource($owner_id)) {
            return true;
        }

        // Check specific permission
        $permission_map = [
            'user'       => 'manage_users',
            'student'    => 'manage_students',
            'session'    => 'manage_sessions',
            'violation'  => 'manage_violations',
            'assessment' => 'manage_assessments',
            'class'      => 'manage_classes',
        ];

        $permission = $permission_map[$resource_type] ?? null;

        return $permission ? has_permission($permission) : false;
    }
}

if (!function_exists('can_delete')) {
    /**
     * Check if user can delete a resource
     * Same logic as can_edit
     * 
     * @param string   $resource_type Type of resource
     * @param int|null $owner_id      Owner user ID
     * @return bool
     */
    function can_delete(string $resource_type, ?int $owner_id = null): bool
    {
        return can_edit($resource_type, $owner_id);
    }
}

if (!function_exists('can_view')) {
    /**
     * Check if user can view a resource
     * More permissive than edit/delete
     * 
     * @param string   $resource_type Type of resource
     * @param int|null $owner_id      Owner user ID
     * @return bool
     */
    function can_view(string $resource_type, ?int $owner_id = null): bool
    {
        // Admin can view everything
        if (is_admin()) {
            return true;
        }

        // Owner can view own resources
        if ($owner_id !== null && owns_resource($owner_id)) {
            return true;
        }

        // Check specific view permission
        $permission_map = [
            'user'       => ['view_users', 'manage_users'],
            'student'    => ['view_students', 'manage_students'],
            'session'    => ['view_sessions', 'manage_sessions'],
            'violation'  => ['view_violations', 'manage_violations'],
            'assessment' => ['view_assessments', 'manage_assessments'],
            'report'     => ['view_reports', 'generate_reports'],
        ];

        $permissions = $permission_map[$resource_type] ?? null;

        return $permissions ? has_permission($permissions) : false;
    }
}

if (!function_exists('get_user_permissions')) {
    /**
     * Get all permissions for current user
     * 
     * @return array
     */
    function get_user_permissions(): array
    {
        $session = \Config\Services::session();
        return $session->get('permissions') ?? [];
    }
}

if (!function_exists('check_access')) {
    /**
     * Check access and redirect if not authorized
     * 
     * @param string|array $roles       Required roles
     * @param string|array $permissions Required permissions
     * @param string       $redirect_to Redirect URL if not authorized
     * @return void
     */
    function check_access($roles = null, $permissions = null, string $redirect_to = null): void
    {
        $hasAccess = true;

        // Check roles
        if ($roles !== null && !has_role($roles)) {
            $hasAccess = false;
        }

        // Check permissions
        if ($permissions !== null && !has_permission($permissions)) {
            $hasAccess = false;
        }

        if (!$hasAccess) {
            $url = $redirect_to ?? base_url();
            redirect()->to($url)->with('error', 'Anda tidak memiliki akses ke halaman tersebut.')->send();
            exit;
        }
    }
}

if (!function_exists('get_dashboard_url')) {
    /**
     * Get dashboard URL based on user role
     * 
     * @return string
     */
    function get_dashboard_url(): string
    {
        $role = current_user_role();

        $dashboard_map = [
            'Admin'           => '/admin/dashboard',
            'Koordinator BK'  => '/koordinator/dashboard',
            'Guru BK'         => '/counselor/dashboard',
            'Wali Kelas'      => '/homeroom/dashboard',
            'Siswa'           => '/student/dashboard',
            'Orang Tua'       => '/parent/dashboard',
        ];

        return base_url($dashboard_map[$role] ?? '/');
    }
}

if (!function_exists('user_avatar')) {
    /**
     * Get user avatar URL or initials
     * 
     * @param array|null $user User data (optional, uses current user if null)
     * @param string     $size Size (sm, md, lg)
     * @return string HTML for avatar
     */
    function user_avatar(?array $user = null, string $size = 'md'): string
    {
        if ($user === null) {
            $user = current_user();
        }

        $sizes = [
            'sm' => '32',
            'md' => '40',
            'lg' => '56',
            'xl' => '80',
        ];

        $px = $sizes[$size] ?? '40';
        $photo = $user['profile_photo'] ?? null;

        if ($photo && file_exists(FCPATH . 'uploads/users/' . $photo)) {
            $url = base_url('uploads/users/' . $photo);
            return '<img src="' . $url . '" alt="' . esc($user['full_name']) . '" class="rounded-circle" width="' . $px . '" height="' . $px . '">';
        }

        // Generate initials
        $name = $user['full_name'] ?? 'U';
        $words = explode(' ', $name);
        $initials = '';

        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
            if (strlen($initials) >= 2) break;
        }

        $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
        $color = $colors[array_sum(str_split(ord($initials[0] ?? 'A'))) % count($colors)];

        return '<div class="avatar-' . $size . ' d-inline-block">
                    <span class="avatar-title rounded-circle bg-soft-' . $color . ' text-' . $color . '" style="width: ' . $px . 'px; height: ' . $px . 'px; line-height: ' . $px . 'px;">
                        ' . esc($initials) . '
                    </span>
                </div>';
    }
}

if (!function_exists('require_auth')) {
    /**
     * Require authentication, redirect to login if not logged in
     * 
     * @return void
     */
    function require_auth(): void
    {
        if (!is_logged_in()) {
            redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.')->send();
            exit;
        }
    }
}

if (!function_exists('require_role')) {
    /**
     * Require specific role, redirect if not authorized
     * 
     * @param string|array $roles Required role(s)
     * @return void
     */
    function require_role($roles): void
    {
        require_auth();

        if (!has_role($roles)) {
            $url = get_dashboard_url();
            redirect()->to($url)->with('error', 'Anda tidak memiliki akses ke halaman tersebut.')->send();
            exit;
        }
    }
}

if (!function_exists('require_permission')) {
    /**
     * Require specific permission, redirect if not authorized
     * 
     * @param string|array $permissions Required permission(s)
     * @return void
     */
    function require_permission($permissions): void
    {
        require_auth();

        if (!has_permission($permissions)) {
            $url = get_dashboard_url();
            redirect()->to($url)->with('error', 'Anda tidak memiliki izin untuk mengakses halaman tersebut.')->send();
            exit;
        }
    }
}
