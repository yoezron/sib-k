<?php

/**
 * File Path: app/Views/layouts/partials/sidebar.php
 * 
 * Sidebar Menu
 * Menu navigasi dinamis berdasarkan role user
 * 
 * @package    SIB-K
 * @subpackage Views/Layouts/Partials
 * @category   Layout
 * @author     Development Team
 * @created    2025-01-01
 */

$user = auth_user();
$userRole = auth_role();
?>
<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div class="h-100">

        <div class="user-wid text-center py-4">
            <div class="user-img">
                <img src="<?= user_avatar($user['profile_photo'] ?? null) ?>"
                    alt="<?= esc($user['full_name']) ?>"
                    class="avatar-md mx-auto rounded-circle">
            </div>

            <div class="mt-3">
                <a href="<?= base_url('profile') ?>" class="text-body fw-medium font-size-16">
                    <?= esc($user['full_name']) ?>
                </a>
                <p class="text-muted mt-1 mb-0 font-size-13">
                    <?= esc($userRole) ?>
                </p>
            </div>
        </div>

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">

                <?php if (is_admin()): ?>
                    <!-- ADMIN MENU -->
                    <li class="menu-title">Menu Admin</li>

                    <li>
                        <a href="<?= base_url('admin/dashboard') ?>" class="waves-effect">
                            <i class="mdi mdi-view-dashboard"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="mdi mdi-account-group"></i>
                            <span>Manajemen User</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="<?= base_url('admin/users') ?>">Daftar User</a></li>
                            <li><a href="<?= base_url('admin/roles') ?>">Role & Permission</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="mdi mdi-school"></i>
                            <span>Data Akademik</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="<?= base_url('admin/academic-years') ?>">Tahun Ajaran</a></li>
                            <li><a href="<?= base_url('admin/classes') ?>">Kelas</a></li>
                            <li><a href="<?= base_url('admin/students') ?>">Siswa</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="<?= base_url('admin/settings') ?>" class="waves-effect">
                            <i class="mdi mdi-cog"></i>
                            <span>Pengaturan</span>
                        </a>
                    </li>

                <?php elseif (is_koordinator()): ?>
                    <!-- KOORDINATOR BK MENU -->
                    <li class="menu-title">Menu Koordinator</li>

                    <li>
                        <a href="<?= base_url('koordinator/dashboard') ?>" class="waves-effect">
                            <i class="mdi mdi-view-dashboard"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?= base_url('koordinator/users') ?>" class="waves-effect">
                            <i class="mdi mdi-account-multiple"></i>
                            <span>Kelola User</span>
                        </a>
                    </li>

                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="mdi mdi-clipboard-text"></i>
                            <span>Layanan BK</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="<?= base_url('koordinator/sessions') ?>">Sesi Konseling</a></li>
                            <li><a href="<?= base_url('koordinator/cases') ?>">Kasus Siswa</a></li>
                            <li><a href="<?= base_url('koordinator/assessments') ?>">Asesmen</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="<?= base_url('koordinator/reports') ?>" class="waves-effect">
                            <i class="mdi mdi-file-chart"></i>
                            <span>Laporan</span>
                        </a>
                    </li>

                <?php elseif (is_guru_bk()): ?>
                    <!-- GURU BK MENU -->
                    <li class="menu-title">Menu Guru BK</li>

                    <li>
                        <a href="<?= base_url('counselor/dashboard') ?>" class="waves-effect">
                            <i class="mdi mdi-view-dashboard"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?= base_url('counselor/students') ?>" class="waves-effect">
                            <i class="mdi mdi-account-group"></i>
                            <span>Data Siswa</span>
                        </a>
                    </li>

                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="mdi mdi-calendar-check"></i>
                            <span>Konseling</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="<?= base_url('counselor/sessions') ?>">Sesi Konseling</a></li>
                            <li><a href="<?= base_url('counselor/schedule') ?>">Jadwal</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="mdi mdi-alert-circle"></i>
                            <span>Kasus & Pelanggaran</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="<?= base_url('counselor/cases') ?>">Kasus Siswa</a></li>
                            <li><a href="<?= base_url('counselor/violations') ?>">Pelanggaran</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="<?= base_url('counselor/assessments') ?>" class="waves-effect">
                            <i class="mdi mdi-clipboard-check"></i>
                            <span>Asesmen</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?= base_url('counselor/reports') ?>" class="waves-effect">
                            <i class="mdi mdi-file-chart"></i>
                            <span>Laporan</span>
                        </a>
                    </li>

                <?php elseif (is_wali_kelas()): ?>
                    <!-- WALI KELAS MENU -->
                    <li class="menu-title">Menu Wali Kelas</li>

                    <li>
                        <a href="<?= base_url('homeroom/dashboard') ?>" class="waves-effect">
                            <i class="mdi mdi-view-dashboard"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?= base_url('homeroom/my-class') ?>" class="waves-effect">
                            <i class="mdi mdi-google-classroom"></i>
                            <span>Kelas Saya</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?= base_url('homeroom/students') ?>" class="waves-effect">
                            <i class="mdi mdi-account-group"></i>
                            <span>Daftar Siswa</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?= base_url('homeroom/violations') ?>" class="waves-effect">
                            <i class="mdi mdi-alert-circle"></i>
                            <span>Pelanggaran</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?= base_url('homeroom/reports') ?>" class="waves-effect">
                            <i class="mdi mdi-file-chart"></i>
                            <span>Laporan Kelas</span>
                        </a>
                    </li>

                <?php elseif (is_siswa()): ?>
                    <!-- SISWA MENU -->
                    <li class="menu-title">Menu Siswa</li>

                    <li>
                        <a href="<?= base_url('student/dashboard') ?>" class="waves-effect">
                            <i class="mdi mdi-view-dashboard"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?= base_url('student/profile') ?>" class="waves-effect">
                            <i class="mdi mdi-account-circle"></i>
                            <span>Profil Saya</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?= base_url('student/schedule') ?>" class="waves-effect">
                            <i class="mdi mdi-calendar"></i>
                            <span>Jadwal Konseling</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?= base_url('student/assessments') ?>" class="waves-effect">
                            <i class="mdi mdi-clipboard-check"></i>
                            <span>Asesmen</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?= base_url('student/violations') ?>" class="waves-effect">
                            <i class="mdi mdi-alert-circle"></i>
                            <span>Pelanggaran Saya</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?= base_url('student/career') ?>" class="waves-effect">
                            <i class="mdi mdi-briefcase"></i>
                            <span>Informasi Karir</span>
                        </a>
                    </li>

                <?php elseif (is_orang_tua()): ?>
                    <!-- ORANG TUA MENU -->
                    <li class="menu-title">Menu Orang Tua</li>

                    <li>
                        <a href="<?= base_url('parent/dashboard') ?>" class="waves-effect">
                            <i class="mdi mdi-view-dashboard"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?= base_url('parent/children') ?>" class="waves-effect">
                            <i class="mdi mdi-account-child"></i>
                            <span>Data Anak</span>
                        </a>
                    </li>

                <?php endif; ?>

                <!-- COMMON MENU -->
                <li class="menu-title">Menu Umum</li>

                <li>
                    <a href="<?= base_url('messages') ?>" class="waves-effect">
                        <i class="mdi mdi-email"></i>
                        <span>Pesan</span>
                    </a>
                </li>

                <li>
                    <a href="<?= base_url('notifications') ?>" class="waves-effect">
                        <i class="mdi mdi-bell"></i>
                        <span>Notifikasi</span>
                    </a>
                </li>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->