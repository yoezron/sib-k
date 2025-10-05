<?php

/**
 * File Path: app/Views/layouts/partials/topbar.php
 * 
 * Topbar / Header
 * Navigation bar dengan logo, search, notifications, dan user menu
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
<header id="page-topbar">
    <div class="navbar-header">
        <div class="container-fluid">
            <div class="float-end">

                <div class="dropdown d-inline-block d-lg-none ms-2">
                    <button type="button" class="btn header-item noti-icon waves-effect"
                        id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <i class="mdi mdi-magnify"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                        aria-labelledby="page-header-search-dropdown">
                        <form class="p-3">
                            <div class="m-0">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Cari...">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="mdi mdi-magnify"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="dropdown d-none d-lg-inline-block ms-1">
                    <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                        <i class="mdi mdi-fullscreen"></i>
                    </button>
                </div>

                <div class="dropdown d-inline-block">
                    <button type="button" class="btn header-item noti-icon waves-effect"
                        id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <i class="mdi mdi-bell-outline"></i>
                        <span class="badge rounded-pill bg-danger" id="notification-badge">0</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                        aria-labelledby="page-header-notifications-dropdown">
                        <div class="p-3">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0">Notifikasi</h6>
                                </div>
                                <div class="col-auto">
                                    <a href="<?= base_url('notifications') ?>" class="small">Lihat Semua</a>
                                </div>
                            </div>
                        </div>
                        <div data-simplebar style="max-height: 230px;" id="notification-list">
                            <a href="#" class="text-reset notification-item">
                                <div class="d-flex align-items-start">
                                    <div class="avatar-xs me-3">
                                        <span class="avatar-title bg-primary rounded-circle font-size-16">
                                            <i class="bx bx-info-circle"></i>
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <h6 class="mt-0 mb-1">Tidak ada notifikasi</h6>
                                        <div class="font-size-12 text-muted">
                                            <p class="mb-0">Anda tidak memiliki notifikasi baru</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="p-2 border-top d-grid">
                            <a class="btn btn-sm btn-link font-size-14" href="<?= base_url('notifications') ?>">
                                <i class="mdi mdi-arrow-right-circle me-1"></i> Lihat Semua
                            </a>
                        </div>
                    </div>
                </div>

                <div class="dropdown d-inline-block">
                    <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img class="rounded-circle header-profile-user"
                            src="<?= user_avatar($user['profile_photo'] ?? null) ?>"
                            alt="<?= esc($user['full_name']) ?>">
                        <span class="d-none d-xl-inline-block ms-1"><?= esc($user['full_name']) ?></span>
                        <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <div class="dropdown-header">
                            <h6 class="mb-0"><?= esc($user['full_name']) ?></h6>
                            <small class="text-muted"><?= format_role_badge($userRole) ?></small>
                        </div>
                        <div class="dropdown-divider"></div>

                        <a class="dropdown-item" href="<?= base_url('profile') ?>">
                            <i class="bx bx-user font-size-16 align-middle me-1"></i> Profil Saya
                        </a>

                        <?php if (has_permission('view_dashboard')): ?>
                            <a class="dropdown-item" href="<?= base_url($user['role_name'] === 'Admin' ? 'admin/dashboard' : 'dashboard') ?>">
                                <i class="bx bx-home-circle font-size-16 align-middle me-1"></i> Dashboard
                            </a>
                        <?php endif; ?>

                        <a class="dropdown-item" href="<?= base_url('messages') ?>">
                            <i class="bx bx-envelope font-size-16 align-middle me-1"></i> Pesan
                        </a>

                        <div class="dropdown-divider"></div>

                        <a class="dropdown-item text-danger" href="<?= base_url('logout') ?>">
                            <i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> Logout
                        </a>
                    </div>
                </div>

                <div class="dropdown d-inline-block">
                    <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect"
                        data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
                        <i class="mdi mdi-settings-outline"></i>
                    </button>
                </div>

            </div>
            <div>
                <!-- LOGO -->
                <div class="navbar-brand-box">
                    <a href="<?= base_url('/') ?>" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="<?= base_url('assets/images/logo-sm.png') ?>" alt="logo" height="20">
                        </span>
                        <span class="logo-lg">
                            <img src="<?= base_url('assets/images/logo-dark.png') ?>" alt="logo" height="17">
                        </span>
                    </a>

                    <a href="<?= base_url('/') ?>" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="<?= base_url('assets/images/logo-sm.png') ?>" alt="logo" height="20">
                        </span>
                        <span class="logo-lg">
                            <img src="<?= base_url('assets/images/logo-light.png') ?>" alt="logo" height="19">
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 font-size-16 header-item toggle-btn waves-effect"
                    id="vertical-menu-btn">
                    <i class="fa fa-fw fa-bars"></i>
                </button>

                <!-- App Search-->
                <form class="app-search d-none d-lg-inline-block">
                    <div class="position-relative">
                        <input type="text" class="form-control" placeholder="Cari...">
                        <span class="bx bx-search-alt"></span>
                    </div>
                </form>

            </div>
        </div>
    </div>
</header>