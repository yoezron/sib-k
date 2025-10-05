# 📘 Panduan Instalasi SIB-K

## Sistem Informasi Layanan Bimbingan dan Konseling
Madrasah Aliyah Persis 31 Banjaran

---

## 📋 Prasyarat Sistem

Sebelum menginstall aplikasi, pastikan sistem Anda memiliki:

### Perangkat Lunak yang Diperlukan:
- **PHP** versi 8.1 atau lebih tinggi
- **MySQL** versi 8.0 atau lebih tinggi
- **Composer** (PHP Dependency Manager)
- **Apache/Nginx** Web Server (opsional untuk development)

### Extension PHP yang Diperlukan:
- `intl`
- `mbstring`
- `json`
- `mysqlnd`
- `curl`

---

## 🚀 Langkah Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/yoezron/sib-k.git
cd sib-k
```

### 2. Install Dependencies

```bash
composer install
```

Tunggu hingga semua dependencies terinstall dengan sempurna.

### 3. Konfigurasi Database

#### a. Buat Database MySQL

```bash
# Login ke MySQL
mysql -u root -p

# Buat database
CREATE DATABASE sib_k CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Buat user khusus (opsional tapi direkomendasikan)
CREATE USER 'sibk_user'@'localhost' IDENTIFIED BY 'password_anda';
GRANT ALL PRIVILEGES ON sib_k.* TO 'sibk_user'@'localhost';
FLUSH PRIVILEGES;
exit;
```

#### b. Konfigurasi File Environment

```bash
# Copy file environment template
cp env .env

# Edit file .env
nano .env
```

Ubah konfigurasi database di file `.env`:

```ini
# Environment
CI_ENVIRONMENT = development

# Database Configuration
database.default.hostname = localhost
database.default.database = sib_k
database.default.username = sibk_user
database.default.password = password_anda
database.default.DBDriver = MySQLi
database.default.DBPrefix = 

# Base URL (sesuaikan dengan domain Anda)
app.baseURL = http://localhost:8080/
```

### 4. Jalankan Migrasi Database

```bash
php spark migrate
```

Output yang diharapkan:
```
Running all new migrations...
    Running: (App) 2025-10-05-023246_App\Database\Migrations\CreateRolesTable
    Running: (App) 2025-10-05-023320_App\Database\Migrations\CreatePermissionsTable
    ...
Migrations complete.
```

### 5. Jalankan Seeder (Data Awal)

```bash
# Seed roles
php spark db:seed RoleSeeder

# Seed permissions
php spark db:seed PermissionSeeder

# Seed admin user
php spark db:seed AdminSeeder
```

### 6. Set Permissions untuk Writable Directory

```bash
chmod -R 777 writable/
chmod -R 777 public/assets/uploads/
```

### 7. Jalankan Development Server

```bash
php spark serve
```

Atau jika ingin menggunakan port dan host tertentu:

```bash
php spark serve --host=0.0.0.0 --port=8080
```

### 8. Akses Aplikasi

Buka browser dan akses:
```
http://localhost:8080
```

**Login Credentials:**
- **Username**: `admin`
- **Password**: `admin123`

---

## 🔐 Keamanan

### Untuk Production Environment:

1. **Ubah Environment Mode**
```ini
CI_ENVIRONMENT = production
```

2. **Ganti Password Admin Default**
   - Login sebagai admin
   - Buka menu Profile
   - Ubah password

3. **Set Proper File Permissions**
```bash
# Set ownership ke web server user
chown -R www-data:www-data /path/to/sib-k

# Set directory permissions
find . -type d -exec chmod 755 {} \;

# Set file permissions
find . -type f -exec chmod 644 {} \;

# Writable directories
chmod -R 775 writable/
chmod -R 775 public/assets/uploads/
```

4. **Aktifkan HTTPS**
   - Install SSL Certificate
   - Configure Web Server untuk HTTPS
   - Update `app.baseURL` di `.env`

5. **Backup Database Secara Berkala**
```bash
# Contoh backup MySQL
mysqldump -u sibk_user -p sib_k > backup_$(date +%Y%m%d).sql
```

---

## 🌐 Konfigurasi Web Server

### Apache Configuration

Buat file `.htaccess` di root directory (sudah disediakan):

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php/$1 [L]
</IfModule>
```

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name sibk.example.com;
    root /path/to/sib-k/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

---

## 🛠️ Troubleshooting

### Problem: Error "Table not found"
**Solusi**: Pastikan migrations telah dijalankan dengan benar
```bash
php spark migrate:status
php spark migrate
```

### Problem: Error "Permission denied" di writable/
**Solusi**: Set permissions yang tepat
```bash
chmod -R 777 writable/
```

### Problem: Error "Database connection failed"
**Solusi**: 
1. Pastikan MySQL service berjalan
2. Cek credentials di file `.env`
3. Test koneksi database:
```bash
mysql -u sibk_user -p sib_k
```

### Problem: Error "Class not found"
**Solusi**: Clear cache dan regenerate autoloader
```bash
composer dump-autoload
php spark cache:clear
```

---

## 📞 Support

Jika mengalami kesulitan dalam instalasi, silakan:
1. Buka issue di GitHub repository
2. Hubungi developer: yoezron@github.com

---

## 📝 Versi

- **Current Version**: 1.0.0 (FASE 1)
- **CodeIgniter Version**: 4.6.3
- **PHP Version**: 8.1+
- **MySQL Version**: 8.0+

---

## ✅ Checklist Setelah Instalasi

- [ ] Database berhasil dibuat
- [ ] Migrations berhasil dijalankan
- [ ] Seeders berhasil dijalankan
- [ ] Bisa login dengan admin/admin123
- [ ] Dashboard admin tampil dengan benar
- [ ] Password admin default sudah diganti
- [ ] File permissions sudah diset dengan benar
- [ ] Backup database sudah dikonfigurasi

---

**Terakhir diupdate**: Oktober 2025
