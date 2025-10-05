# sib-k
Sistem Informasi Layanan Bimbingan dan Konseling

🏗️ ARSITEKTUR APLIKASI SISTEM INFORMASI LAYANAN BIMBINGAN DAN KONSELING MADRASAH ALIYAH PERSIS 31 Banjaran
A. Teknologi Mutakhir yang Digunakan
┌─────────────────────────────────────────────────────────────┐
│                    LAYER PRESENTATION                        │
│  • Bootstrap 5.3+ (Latest)                                   │
│  • Alpine.js 3.x (Lightweight Reactivity)                    │
│  • Edura Template (Premium)                                  │
│  • Chart.js 4.x (Data Visualization)                         │
└─────────────────────────────────────────────────────────────┘
                            ↓↑
┌─────────────────────────────────────────────────────────────┐
│                   LAYER APPLICATION                          │
│  • CodeIgniter 4.5.5+ (Latest Stable)                       │
│  • RESTful API Architecture                                  │
│  • JWT Authentication (optional untuk mobile)                │
│  • Redis Cache (Performance)                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓↑
┌─────────────────────────────────────────────────────────────┐
│                    LAYER BUSINESS LOGIC                      │
│  • Service Pattern (Business Logic Isolation)                │
│  • Repository Pattern (Data Abstraction)                     │
│  • Event-Driven Architecture (Notifications)                 │
└─────────────────────────────────────────────────────────────┘
                            ↓↑
┌─────────────────────────────────────────────────────────────┐
│                      LAYER DATA                              │
│  • MySQL 8.0+ / MariaDB 10.6+                               │
│  • Database Migration & Seeding                              │
│  • Query Builder (Optimized)                                 │
└─────────────────────────────────────────────────────────────┘

STRUKTUR DIREKTORI LENGKAP

sib-k/
├── app/
│   ├── Config/
│   │   ├── Routes.php                    # Route definitions
│   │   ├── Filters.php                   # Auth & RBAC filters
│   │   ├── Database.php                  # DB config
│   │   ├── Events.php                    # Event handlers
│   │   ├── Services.php                  # Custom services
│   │   └── Validation.php                # Custom validation rules
│   │
│   ├── Controllers/
│   │   ├── BaseController.php            # Enhanced base controller
│   │   ├── AuthController.php            # Authentication
│   │   │
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── UserController.php
│   │   │   ├── StudentController.php
│   │   │   ├── ClassController.php
│   │   │   └── AcademicYearController.php
│   │   │
│   │   ├── Counselor/
│   │   │   ├── DashboardController.php
│   │   │   ├── SessionController.php
│   │   │   ├── CaseController.php
│   │   │   ├── AssessmentController.php
│   │   │   └── ReportController.php
│   │   │
│   │   ├── HomeroomTeacher/
│   │   │   ├── DashboardController.php
│   │   │   ├── ViolationController.php
│   │   │   └── ClassReportController.php
│   │   │
│   │   ├── Student/
│   │   │   ├── DashboardController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── ScheduleController.php
│   │   │   ├── AssessmentController.php
│   │   │   └── CareerController.php
│   │   │
│   │   ├── Parent/
│   │   │   ├── DashboardController.php
│   │   │   ├── ChildController.php
│   │   │   └── CommunicationController.php
│   │   │
│   │   └── Api/
│   │       └── v1/                       # REST API endpoints
│   │
│   ├── Database/
│   │   ├── Migrations/
│   │   │   ├── 2024-01-01-000001_create_roles_table.php
│   │   │   ├── 2024-01-01-000002_create_permissions_table.php
│   │   │   ├── 2024-01-01-000003_create_role_permissions_table.php
│   │   │   ├── 2024-01-01-000004_create_users_table.php
│   │   │   ├── 2024-01-01-000005_create_students_table.php
│   │   │   ├── 2024-01-01-000006_create_classes_table.php
│   │   │   ├── 2024-01-01-000007_create_academic_years_table.php
│   │   │   ├── 2024-01-01-000008_create_counseling_sessions_table.php
│   │   │   ├── 2024-01-01-000009_create_session_notes_table.php
│   │   │   ├── 2024-01-01-000010_create_violations_table.php
│   │   │   ├── 2024-01-01-000011_create_violation_categories_table.php
│   │   │   ├── 2024-01-01-000012_create_assessments_table.php
│   │   │   ├── 2024-01-01-000013_create_assessment_questions_table.php
│   │   │   ├── 2024-01-01-000014_create_assessment_answers_table.php
│   │   │   ├── 2024-01-01-000015_create_notifications_table.php
│   │   │   └── 2024-01-01-000016_create_messages_table.php
│   │   │
│   │   └── Seeds/
│   │       ├── RoleSeeder.php
│   │       ├── PermissionSeeder.php
│   │       ├── AdminSeeder.php
│   │       ├── ViolationCategorySeeder.php
│   │       └── DemoDataSeeder.php
│   │
│   ├── Filters/
│   │   ├── AuthFilter.php                # Authentication check
│   │   ├── RoleFilter.php                # Role-based access
│   │   ├── ThrottleFilter.php            # Rate limiting
│   │   └── CorsFilter.php                # CORS handling
│   │
│   ├── Libraries/
│   │   ├── PDFGenerator.php              # Dompdf wrapper
│   │   ├── ExcelImporter.php             # PhpSpreadsheet wrapper
│   │   ├── NotificationService.php       # Notification handler
│   │   └── EncryptionService.php         # Data encryption
│   │
│   ├── Models/
│   │   ├── RoleModel.php
│   │   ├── PermissionModel.php
│   │   ├── UserModel.php
│   │   ├── StudentModel.php
│   │   ├── ParentModel.php
│   │   ├── ClassModel.php
│   │   ├── AcademicYearModel.php
│   │   ├── CounselingSessionModel.php
│   │   ├── SessionNoteModel.php
│   │   ├── ViolationModel.php
│   │   ├── ViolationCategoryModel.php
│   │   ├── AssessmentModel.php
│   │   ├── AssessmentQuestionModel.php
│   │   ├── AssessmentAnswerModel.php
│   │   ├── NotificationModel.php
│   │   └── MessageModel.php
│   │
│   ├── Services/                         # Business Logic Layer
│   │   ├── AuthService.php
│   │   ├── StudentService.php
│   │   ├── CounselingService.php
│   │   ├── ViolationService.php
│   │   ├── AssessmentService.php
│   │   ├── ReportService.php
│   │   └── NotificationService.php
│   │
│   ├── Repositories/                     # Data Access Layer
│   │   ├── UserRepository.php
│   │   ├── StudentRepository.php
│   │   └── SessionRepository.php
│   │
│   ├── Validation/                       # Custom Validators
│   │   ├── StudentValidation.php
│   │   ├── SessionValidation.php
│   │   └── AssessmentValidation.php
│   │
│   └── Views/
│       ├── layouts/
│       │   ├── main.php                  # Master template
│       │   ├── auth.php                  # Auth layout
│       │   ├── partials/
│       │   │   ├── header.php
│       │   │   ├── sidebar.php
│       │   │   ├── footer.php
│       │   │   └── notifications.php
│       │   └── components/
│       │       ├── alert.php
│       │       ├── modal.php
│       │       └── table.php
│       │
│       ├── auth/
│       │   ├── login.php
│       │   ├── forgot_password.php
│       │   └── reset_password.php
│       │
│       ├── admin/
│       │   ├── dashboard.php
│       │   ├── users/
│       │   ├── students/
│       │   └── classes/
│       │
│       ├── counselor/
│       │   ├── dashboard.php
│       │   ├── sessions/
│       │   ├── cases/
│       │   ├── assessments/
│       │   └── reports/
│       │
│       ├── homeroom_teacher/
│       │   ├── dashboard.php
│       │   ├── violations/
│       │   └── reports/
│       │
│       ├── student/
│       │   ├── dashboard.php
│       │   ├── profile.php
│       │   ├── schedule.php
│       │   ├── assessments/
│       │   └── career/
│       │
│       └── parent/
│           ├── dashboard.php
│           ├── child_profile.php
│           └── messages/
│
├── public/
│   ├── index.php
│   ├── .htaccess
│   └── assets/
│       ├── edura/                        # Edura template files
│       │   ├── css/
│       │   ├── js/
│       │   ├── images/
│       │   └── fonts/
│       ├── custom/
│       │   ├── css/
│       │   │   └── app.css              # Custom styles
│       │   ├── js/
│       │   │   └── app.js               # Custom scripts
│       │   └── images/
│       └── uploads/
│           ├── students/
│           ├── documents/
│           └── temp/
│
├── writable/
│   ├── cache/
│   ├── logs/
│   ├── session/
│   └── uploads/
│
├── vendor/
│
├── .env
├── .gitignore
├── composer.json
└── spark

 Arsitektur & Pola Desain
A. Service Layer Pattern
// Pisahkan business logic dari Controller
// app/Services/CounselingService.php
class CounselingService {
    public function createSession($data) {
        // Validation
        // Business rules
        // Save to database
        // Send notifications
        // Return result
    }
}

B. Repository Pattern
// Abstraksi data access
// app/Repositories/StudentRepository.php
class StudentRepository {
    public function findWithRelations($id) {
        return $this->model
            ->with('counselingSessions')
            ->with('violations')
            ->find($id);
    }
}

C. Event-Driven Architecture

// app/Config/Events.php
Events::on('session_created', function($data) {
    // Send notification to student
    // Send notification to parent
    // Log activity
});

Keamanan Tingkat Lanjut
// A. Input Sanitization dengan Custom Rules
// app/Validation/StudentValidation.php

// B. Data Encryption untuk Catatan Sensitif
$encrypted = service('encrypter')->encrypt($sensitiveData);

// C. Rate Limiting
// app/Filters/ThrottleFilter.php

// D. SQL Injection Prevention (Always use Query Builder)
$this->db->table('users')->where('id', $id)->get();

// E. CSRF Token (Auto-enabled di CI4)

// F. XSS Protection
<?= esc($userData) ?>

// G. Password Policy
- Minimum 8 karakter
- Kombinasi huruf besar, kecil, angka, simbol
- Hash menggunakan PASSWORD_BCRYPT

Performance Optimization
// A. Query Optimization
- Eager Loading untuk relasi
- Index pada kolom yang sering di-query
- Batasi SELECT hanya kolom yang diperlukan

// B. Caching Strategy
$cache = \Config\Services::cache();
$cache->save('key', $data, 3600);

// C. Database Indexing
ALTER TABLE students ADD INDEX idx_nisn (nisn);
ALTER TABLE counseling_sessions ADD INDEX idx_student_date (student_id, session_date);

// D. Lazy Loading untuk Data Besar
// Load data on-demand, bukan semua sekaligus

// E. Asset Optimization
- Minify CSS/JS
- Compress images
- Use CDN untuk library eksternal

API Development (Future Mobile App)
// app/Controllers/Api/v1/SessionController.php
class SessionController extends BaseController {
    public function index() {
        return $this->respond([
            'status' => 'success',
            'data' => $sessions,
            'meta' => [
                'page' => 1,
                'per_page' => 20,
                'total' => 100
            ]
        ]);
    }
}

Logging & Monitoring

// Custom Logging
log_message('info', 'User login: ' . $userId);
log_message('error', 'Failed to save session: ' . $error);

// Activity Logging
- Track semua perubahan data penting
- Record user actions
- Store IP address dan user agent

URUTAN FILE KODE - EKSEKUSI BERTAHAP

FASE 1: SETUP & FONDASI (Sprint 1-2)
Tahap 1.1: Instalasi & Konfigurasi Awal

# File Execution Order:
1. composer.json (Setup dependencies)
2. .env (Environment configuration)
3. app/Config/Database.php
4. app/Config/App.php
5. app/Config/Autoload.php

Tahap 1.2: Database Schema
# Migration Files (Run in order):
1. 2024-01-01-000001_create_roles_table.php
2. 2024-01-01-000002_create_permissions_table.php
3. 2024-01-01-000003_create_role_permissions_table.php
4. 2024-01-01-000004_create_users_table.php
5. 2024-01-01-000005_create_students_table.php
6. 2024-01-01-000006_create_classes_table.php
7. 2024-01-01-000007_create_academic_years_table.php

# Command:
php spark migrate

Tahap 1.3: Seeding Data Awal
# Seeder Files (Run in order):
1. RoleSeeder.php
2. PermissionSeeder.php
3. AdminSeeder.php

# Command:
php spark db:seed RoleSeeder
php spark db:seed PermissionSeeder
php spark db:seed AdminSeeder

Tahap 1.4: Models Dasar
# Create in order:
1. app/Models/RoleModel.php
2. app/Models/PermissionModel.php
3. app/Models/UserModel.php
4. app/Models/StudentModel.php
5. app/Models/ClassModel.php
6. app/Models/AcademicYearModel.php

Tahap 1.5: Authentication System
# Create in order:
1. app/Filters/AuthFilter.php
2. app/Filters/RoleFilter.php
3. app/Config/Filters.php (Register filters)
4. app/Libraries/AuthLibrary.php (Helper functions)
5. app/Controllers/AuthController.php
6. app/Views/auth/login.php
7. app/Config/Routes.php (Auth routes)

Tahap 1.6: Layout Template
# Create in order:
1. app/Views/layouts/main.php
2. app/Views/layouts/partials/header.php
3. app/Views/layouts/partials/sidebar.php
4. app/Views/layouts/partials/footer.php
5. public/assets/custom/css/app.css
6. public/assets/custom/js/app.js

FASE 2: MODUL ADMIN (Sprint 3-4)
Tahap 2.1: Admin Dashboard
1. app/Controllers/Admin/DashboardController.php
2. app/Views/admin/dashboard.php
3. app/Config/Routes.php (Add admin routes group)

Tahap 2.2: User Management (CRUD)
1. app/Validation/UserValidation.php
2. app/Services/UserService.php
3. app/Controllers/Admin/UserController.php
4. app/Views/admin/users/index.php
5. app/Views/admin/users/create.php
6. app/Views/admin/users/edit.php
7. app/Views/admin/users/show.php

Tahap 2.3: Student Management
1. app/Validation/StudentValidation.php
2. app/Services/StudentService.php
3. app/Controllers/Admin/StudentController.php
4. app/Views/admin/students/index.php
5. app/Views/admin/students/create.php
6. app/Views/admin/students/edit.php
7. app/Views/admin/students/profile.php

Tahap 2.4: Bulk Student Import
1. composer require phpoffice/phpspreadsheet
2. app/Libraries/ExcelImporter.php
3. app/Controllers/Admin/ImportController.php
4. app/Views/admin/students/import.php
5. public/templates/student_import_template.xlsx

Tahap 2.5: Class & Academic Year Management
1. app/Controllers/Admin/ClassController.php
2. app/Views/admin/classes/index.php
3. app/Views/admin/classes/form.php
4. app/Controllers/Admin/AcademicYearController.php
5. app/Views/admin/academic_years/index.php
6. app/Views/admin/academic_years/form.php

FASE 3: MODUL GURU BK (Sprint 5-7)
Tahap 3.1: Database untuk Counseling
# Migrations:
1. 2024-01-01-000008_create_counseling_sessions_table.php
2. 2024-01-01-000009_create_session_notes_table.php
3. 2024-01-01-000010_create_session_participants_table.php

# Models:
4. app/Models/CounselingSessionModel.php
5. app/Models/SessionNoteModel.php
6. app/Models/SessionParticipantModel.php

Tahap 3.2: Counselor Dashboard
1. app/Services/CounselingService.php
2. app/Controllers/Counselor/DashboardController.php
3. app/Views/counselor/dashboard.php

Tahap 3.3: Session Management
1. app/Validation/SessionValidation.php
2. app/Controllers/Counselor/SessionController.php
3. app/Views/counselor/sessions/index.php
4. app/Views/counselor/sessions/create.php
5. app/Views/counselor/sessions/edit.php
6. app/Views/counselor/sessions/detail.php
7. app/Views/counselor/sessions/add_note.php

Tahap 3.4: Case & Violation Management
# Migrations:
1. 2024-01-01-000011_create_violations_table.php
2. 2024-01-01-000012_create_violation_categories_table.php
3. 2024-01-01-000013_create_sanctions_table.php

# Seeders:
4. ViolationCategorySeeder.php

# Models:
5. app/Models/ViolationModel.php
6. app/Models/ViolationCategoryModel.php
7. app/Models/SanctionModel.php

# Controllers & Views:
8. app/Services/ViolationService.php
9. app/Controllers/Counselor/CaseController.php
10. app/Views/counselor/cases/index.php
11. app/Views/counselor/cases/create.php
12. app/Views/counselor/cases/detail.php

Tahap 3.5: Assessment Module
# Migrations:
1. 2024-01-01-000014_create_assessments_table.php
2. 2024-01-01-000015_create_assessment_questions_table.php
3. 2024-01-01-000016_create_assessment_answers_table.php
4. 2024-01-01-000017_create_assessment_results_table.php

# Models:
5. app/Models/AssessmentModel.php
6. app/Models/AssessmentQuestionModel.php
7. app/Models/AssessmentAnswerModel.php
8. app/Models/AssessmentResultModel.php

# Controllers & Views:
9. app/Services/AssessmentService.php
10. app/Controllers/Counselor/AssessmentController.php
11. app/Views/counselor/assessments/index.php
12. app/Views/counselor/assessments/create.php
13. app/Views/counselor/assessments/assign.php
14. app/Views/counselor/assessments/results.php

FASE 4: MODUL WALI KELAS (Sprint 8)
Tahap 4.1: Homeroom Teacher Module
1. app/Controllers/HomeroomTeacher/DashboardController.php
2. app/Views/homeroom_teacher/dashboard.php
3. app/Controllers/HomeroomTeacher/ViolationController.php
4. app/Views/homeroom_teacher/violations/create.php
5. app/Views/homeroom_teacher/violations/index.php
6. app/Controllers/HomeroomTeacher/ClassReportController.php
7. app/Views/homeroom_teacher/reports/class_summary.php

FASE 5: MODUL SISWA & ORANG TUA (Sprint 9-10)
Tahap 5.1: Student Portal
1. app/Controllers/Student/DashboardController.php
2. app/Views/student/dashboard.php
3. app/Controllers/Student/ProfileController.php
4. app/Views/student/profile.php
5. app/Controllers/Student/ScheduleController.php
6. app/Views/student/schedule/index.php
7. app/Views/student/schedule/request.php
8. app/Controllers/Student/AssessmentController.php
9. app/Views/student/assessments/available.php
10. app/Views/student/assessments/take.php
11. app/Views/student/assessments/results.php

Tahap 5.2: Career Information Portal
# Migrations:
1. 2024-01-01-000018_create_career_options_table.php
2. 2024-01-01-000019_create_university_info_table.php

# Models:
3. app/Models/CareerOptionModel.php
4. app/Models/UniversityInfoModel.php

# Controllers & Views:
5. app/Controllers/Student/CareerController.php
6. app/Views/student/career/explore.php
7. app/Views/student/career/detail.php
8. app/Views/student/career/saved.php

Tahap 5.3: Parent Portal
1. app/Controllers/Parent/DashboardController.php
2. app/Views/parent/dashboard.php
3. app/Controllers/Parent/ChildController.php
4. app/Views/parent/child/profile.php
5. app/Views/parent/child/violations.php
6. app/Views/parent/child/sessions.php

FASE 6: KOMUNIKASI & NOTIFIKASI (Sprint 11)
Tahap 6.1: Notification System
# Migrations:
1. 2024-01-01-000020_create_notifications_table.php

# Models & Services:
2. app/Models/NotificationModel.php
3. app/Libraries/NotificationService.php
4. app/Config/Events.php (Setup event listeners)

# Views:
5. app/Views/layouts/partials/notifications.php

Tahap 6.2: Internal Messaging
# Migrations:
1. 2024-01-01-000021_create_messages_table.php
2. 2024-01-01-000022_create_message_participants_table.php

# Models:
3. app/Models/MessageModel.php
4. app/Models/MessageParticipantModel.php

# Controllers & Views:
5. app/Controllers/MessageController.php
6. app/Views/messages/inbox.php
7. app/Views/messages/compose.php
8. app/Views/messages/detail.php

FASE 7: REPORTING (Sprint 12)
Tahap 7.1: PDF Report Generation
1. composer require dompdf/dompdf
2. app/Libraries/PDFGenerator.php
3. app/Services/ReportService.php
4. app/Controllers/Counselor/ReportController.php
5. app/Views/reports/student_individual.php
6. app/Views/reports/session_summary.php
7. app/Views/reports/violation_report.php
8. app/Views/reports/class_aggregate.php

Tahap 7.2: Export to Excel
1. app/Controllers/ExportController.php
2. app/Views/admin/export/options.php

FASE 8: FINALISASI (Sprint 13-14)
Tahap 8.1: Testing & Security Audit
1. app/Tests/ (Unit & Integration tests)
2. Security review checklist
3. Performance optimization
4. Cross-browser testing

Tahap 8.2: Documentation
1. README.md
2. INSTALLATION.md
3. USER_MANUAL.md
4. API_DOCUMENTATION.md
5. DEPLOYMENT_GUIDE.md

Tahap 8.3: Deployment Preparation
1. .htaccess configuration
2. Server requirements check
3. Backup scripts
4. Monitoring setup

SECURITY CHECKLIST
✅ Password hashing dengan bcrypt
✅ CSRF protection enabled
✅ XSS filtering aktif
✅ SQL injection prevention (Query Builder)
✅ Input validation & sanitization
✅ Session security (httponly, secure flags)
✅ Rate limiting untuk login
✅ Enkripsi data sensitif
✅ File upload validation
✅ HTTPS enforcement (production)
✅ Security headers (CSP, X-Frame-Options)
✅ Error handling yang aman
✅ Audit logging
✅ Role-based access control
✅ Database backup otomatis

PERFORMANCE METRICS
Target Performance:
- Page Load: < 2 detik
- Database Queries: < 50ms per query
- API Response: < 200ms
- Concurrent Users: 500+
- Uptime: 99.9%

DEPLOYMENT STRATEGY
Development → Staging → Production

1. Version Control (Git)
2. Automated Testing (PHPUnit)
3. Code Review Process
4. Staging Environment Testing
5. Blue-Green Deployment
6. Rollback Plan
7. Monitoring & Alerting
