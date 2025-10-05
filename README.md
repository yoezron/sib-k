# sib-k
Sistem Informasi Layanan Bimbingan dan Konseling

🏗️ ARSITEKTUR APLIKASI SISTEM INFORMASI LAYANAN BIMBINGAN DAN KONSELING MADRASAH ALIYAH PERSIS 31 Banjaran

## Deskripsi Sistem
Aplikasi Sistem Informasi Layanan Bimbingan dan Konseling (BK) sekolah komprehensif dan dinamis dengan Framework Codeigniter 4 yang dapat di akses oleh:
- **Koordinator BK** - Mengelola keseluruhan layanan BK
- **Guru BK** - Melakukan sesi konseling dan dokumentasi
- **Wali Kelas** - Memantau dan melaporkan pelanggaran siswa
- **Orang Tua** - Memantau perkembangan anak
- **Siswa** - Mengakses layanan dan informasi

### Layanan yang Mencakup:
✅ Layanan **Pribadi** - Konseling pribadi siswa  
✅ Layanan **Sosial** - Pengembangan keterampilan sosial  
✅ Layanan **Belajar** - Bimbingan akademik  
✅ Layanan **Karir** - Perencanaan karir dan kuliah  

### Fitur Utama:
- 📋 Administrasi lengkap dengan role-based access control
- 📊 Laporan yang dapat diunduh (PDF & Excel)
- 📤 Upload daftar siswa via Excel
- 📝 Asesmen siswa dengan berbagai jenis tes
- 🎓 Pilihan karir dan informasi perguruan tinggi
- 👤 Biodata lengkap siswa
- 📖 Riwayat kasus dan sesi konseling
- 💬 Sistem keluhan dan komunikasi internal
- 🔔 Notifikasi real-time

## Technology Stack

### Backend
- CodeIgniter 4.6.x (Latest Stable)
- PHP 8.1+
- MySQL 8.0+

### Frontend
- Bootstrap 5.3+
- Alpine.js 3.x
- Chart.js 4.x
- Edura Template

### Libraries
- Dompdf (PDF Generation)
- PhpSpreadsheet (Excel Import/Export)

## Installation

1. Clone repository
```bash
git clone https://github.com/yoezron/sib-k.git
cd sib-k
```

2. Install dependencies
```bash
composer install
```

3. Setup environment
```bash
cp env .env
# Edit .env with your database credentials
```

4. Run migrations
```bash
php spark migrate
```

5. Run seeders
```bash
php spark db:seed RoleSeeder
php spark db:seed PermissionSeeder
php spark db:seed AdminSeeder
```

6. Start development server
```bash
php spark serve
```

## Default Login Credentials

After running seeders, you can login with:
- **Username**: admin
- **Password**: admin123

## Features Implementation Roadmap

### FASE 1: SETUP & FONDASI ✅
- [x] Install CodeIgniter 4 framework
- [x] Configure environment
- [ ] Create database migrations
- [ ] Create seeders
- [ ] Implement authentication system
- [ ] Create base models
- [ ] Setup layout templates

### FASE 2: MODUL ADMIN
- [ ] Admin dashboard
- [ ] User management
- [ ] Student management with Excel import
- [ ] Class management
- [ ] Academic year management

### FASE 3: MODUL KONSELING
- [ ] Counseling session management
- [ ] Case tracking
- [ ] Violation tracking
- [ ] Service categories

### FASE 4: MODUL ASESMEN
- [ ] Assessment creation
- [ ] Question bank
- [ ] Student assessment taking
- [ ] Results and analysis

### FASE 5: MODUL SISWA & ORANG TUA
- [ ] Student portal
- [ ] Career information
- [ ] Parent portal

### FASE 6: KOMUNIKASI
- [ ] Notification system
- [ ] Internal messaging
- [ ] Complaint submission

### FASE 7: REPORTING
- [ ] PDF reports
- [ ] Excel exports

### FASE 8: FINALISASI
- [ ] Security audit
- [ ] Performance optimization
- [ ] Documentation

## License

MIT License

## Developer

Developed for Madrasah Aliyah Persis 31 Banjaran
