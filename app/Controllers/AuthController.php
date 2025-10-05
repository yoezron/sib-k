<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    public function login()
    {
        // If already logged in, redirect to dashboard
        if (session()->get('logged_in')) {
            return redirect()->to($this->getDashboardByRole(session()->get('role_name')));
        }

        return view('auth/login');
    }

    public function attemptLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->where('username', $username)
            ->orWhere('email', $username)
            ->first();

        if ($user && password_verify($password, $user->password)) {
            if ($user->is_active == 0) {
                return redirect()->back()->with('error', 'Akun Anda tidak aktif. Hubungi administrator.');
            }

            // Update last login
            $userModel->update($user->id, ['last_login' => date('Y-m-d H:i:s')]);

            // Get user with role
            $userWithRole = $userModel->getWithRole($user->id);

            // Set session
            session()->set([
                'user_id' => $user->id,
                'username' => $user->username,
                'full_name' => $user->full_name,
                'role_id' => $user->role_id,
                'role_name' => $userWithRole->role_name,
                'role_display_name' => $userWithRole->role_display_name,
                'logged_in' => true
            ]);

            return redirect()->to($this->getDashboardByRole($userWithRole->role_name))
                ->with('success', 'Selamat datang, ' . $user->full_name);
        }

        return redirect()->back()->with('error', 'Username atau password salah.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil logout.');
    }

    private function getDashboardByRole($roleName)
    {
        $dashboards = [
            'admin' => '/admin/dashboard',
            'koordinator_bk' => '/counselor/dashboard',
            'guru_bk' => '/counselor/dashboard',
            'wali_kelas' => '/homeroom/dashboard',
            'orang_tua' => '/parent/dashboard',
            'siswa' => '/student/dashboard',
        ];

        return $dashboards[$roleName] ?? '/';
    }
}
