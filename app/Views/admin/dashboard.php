<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SIB-K' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap"></i> SIB-K
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?= session()->get('full_name') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2">
                <div class="list-group">
                    <a href="<?= base_url('admin/dashboard') ?>" class="list-group-item list-group-item-action active">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-users"></i> Manajemen User
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-user-graduate"></i> Manajemen Siswa
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-school"></i> Manajemen Kelas
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-calendar-alt"></i> Tahun Ajaran
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-file-alt"></i> Laporan
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <h2>Selamat Datang, <?= session()->get('full_name') ?>!</h2>
                <p class="text-muted">Role: <?= session()->get('role_display_name') ?></p>

                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-users"></i> Total User</h5>
                                <h2 class="card-text">-</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-user-graduate"></i> Total Siswa</h5>
                                <h2 class="card-text">-</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-school"></i> Total Kelas</h5>
                                <h2 class="card-text">-</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-comments"></i> Sesi Konseling</h5>
                                <h2 class="card-text">-</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Tentang Sistem</h5>
                    </div>
                    <div class="card-body">
                        <h6>Sistem Informasi Layanan Bimbingan dan Konseling (SIB-K)</h6>
                        <p>Aplikasi komprehensif untuk mengelola layanan Bimbingan dan Konseling di Madrasah Aliyah Persis 31 Banjaran.</p>
                        
                        <h6 class="mt-3">Fitur Utama:</h6>
                        <ul>
                            <li>✅ Manajemen User dengan Role-Based Access Control</li>
                            <li>✅ Manajemen Siswa dengan Import Excel</li>
                            <li>✅ Layanan Konseling (Pribadi, Sosial, Belajar, Karir)</li>
                            <li>✅ Asesmen dan Evaluasi Siswa</li>
                            <li>✅ Tracking Pelanggaran Siswa</li>
                            <li>✅ Komunikasi Internal dan Notifikasi</li>
                            <li>✅ Laporan dalam Format PDF dan Excel</li>
                        </ul>

                        <div class="alert alert-info mt-3">
                            <strong>Status:</strong> Sistem telah berhasil diinisialisasi dan siap dikembangkan lebih lanjut.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
