# Changelog

All notable changes to the SIB-K project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-10-05

### 🎉 FASE 1: Foundation Setup - Initial Release

#### Added
- **Framework Setup**
  - CodeIgniter 4.6.3 framework installation
  - Composer dependency management
  - Development environment configuration

- **Database Schema**
  - `roles` table - User role management
  - `permissions` table - Permission management  
  - `role_permissions` table - Role-permission relationships
  - `users` table - User accounts with authentication
  - `academic_years` table - School year management
  - `classes` table - Class management
  - `students` table - Student records
  - `parents` table - Parent/guardian information

- **Database Seeders**
  - RoleSeeder - 6 predefined roles (Admin, Koordinator BK, Guru BK, Wali Kelas, Orang Tua, Siswa)
  - PermissionSeeder - 18 base permissions
  - AdminSeeder - Default admin user (username: admin, password: admin123)

- **Authentication System**
  - Login page with gradient design
  - Session-based authentication
  - Bcrypt password hashing
  - Remember login state
  - Secure logout functionality
  - Password validation

- **Authorization System**
  - Role-Based Access Control (RBAC)
  - AuthFilter - Protect authenticated routes
  - RoleFilter - Role-based route protection
  - Permission checking system

- **Models**
  - RoleModel - with permission relationships
  - PermissionModel - permission management
  - UserModel - with password hashing and role relationships
  - StudentModel - with class and user relationships
  - ClassModel - with academic year and teacher relationships
  - AcademicYearModel - with active year management

- **Controllers**
  - AuthController - Login/logout functionality
  - Admin/DashboardController - Admin dashboard
  - Counselor/DashboardController - Counselor dashboard
  - HomeroomTeacher/DashboardController - Wali kelas dashboard
  - Student/DashboardController - Student dashboard
  - Parent/DashboardController - Parent dashboard

- **Views**
  - Login page with Bootstrap 5.3
  - Admin dashboard with sidebar navigation
  - Basic dashboards for all user roles
  - Responsive design
  - Flash message support
  - Role-specific navigation

- **Routes**
  - Public routes (login)
  - Protected admin routes
  - Protected counselor routes
  - Protected homeroom teacher routes
  - Protected student routes
  - Protected parent routes
  - Role-based route filtering

- **Security Features**
  - CSRF protection enabled
  - XSS prevention with esc() helper
  - SQL injection prevention (Query Builder)
  - Password hashing with Bcrypt (cost: 10)
  - Secure session management
  - Input validation rules

- **Documentation**
  - Comprehensive README.md
  - INSTALLATION.md guide
  - CONTRIBUTING.md guidelines
  - CHANGELOG.md (this file)
  - Inline code documentation

#### Database Structure
```
roles (6 records)
├── id
├── name
├── display_name
├── description
└── timestamps

permissions (18 records)
├── id
├── name
├── display_name
├── description
└── timestamps

role_permissions (18 records for admin)
├── id
├── role_id
├── permission_id
└── created_at

users (1 admin user)
├── id
├── role_id
├── username
├── email
├── password (hashed)
├── full_name
├── phone
├── avatar
├── is_active
├── last_login
└── timestamps

academic_years
├── id
├── year_name
├── start_date
├── end_date
├── is_active
└── timestamps

classes
├── id
├── academic_year_id
├── name
├── grade_level (X, XI, XII)
├── homeroom_teacher_id
└── timestamps

students
├── id
├── user_id
├── class_id
├── nisn (unique)
├── nis
├── full_name
├── gender (L/P)
├── birth_place
├── birth_date
├── address
├── phone
├── email
├── photo
├── admission_date
├── status (active/graduated/dropped_out/transferred)
└── timestamps

parents
├── id
├── user_id
├── student_id
├── relationship (father/mother/guardian)
├── full_name
├── phone
├── email
├── occupation
├── address
└── timestamps
```

#### User Roles
1. **Admin** - Full system access
2. **Koordinator BK** - BK coordinator, manages all counseling services
3. **Guru BK** - Counselor, provides counseling services
4. **Wali Kelas** - Homeroom teacher, monitors students
5. **Orang Tua** - Parent, monitors child progress
6. **Siswa** - Student, accesses services

#### Permissions Implemented
- User Management: view, create, edit, delete users
- Student Management: view, create, edit, delete, import students
- Session Management: view, create, edit, delete counseling sessions
- Assessment Management: view, create, take assessments
- Report Management: view, export reports

### Fixed
- Migration order for foreign key dependencies
- Student table unique index on NISN field
- Route configuration for role-based access
- Session persistence across requests

### Security
- All user inputs are validated
- Passwords are hashed using Bcrypt
- CSRF tokens on all forms
- XSS protection on all outputs
- SQL injection prevention via Query Builder
- Secure session configuration

### Technical Details
- **Framework**: CodeIgniter 4.6.3
- **PHP Version**: 8.1+
- **Database**: MySQL 8.0+
- **Frontend**: Bootstrap 5.3, Font Awesome 6.4
- **Architecture**: MVC with Service Layer pattern
- **Authentication**: Session-based
- **Password Hashing**: Bcrypt (PASSWORD_BCRYPT)

### Testing
- ✅ Database migrations tested and working
- ✅ Seeders tested and working
- ✅ Login functionality tested
- ✅ Role-based access control tested
- ✅ All dashboards accessible with correct roles

### Known Limitations
- Dashboard statistics show placeholder data (will be implemented in FASE 2)
- No CRUD operations yet (planned for FASE 2)
- Excel import/export not yet implemented (planned for FASE 2)
- Report generation not yet implemented (planned for FASE 7)

---

## [Unreleased]

### Planned for FASE 2: MODUL ADMIN
- [ ] User management CRUD operations
- [ ] Student management CRUD operations
- [ ] Excel import for student data
- [ ] Excel export for student data
- [ ] Class management CRUD operations
- [ ] Academic year management CRUD operations
- [ ] Dashboard with real statistics
- [ ] User profile management
- [ ] Password change functionality

### Planned for FASE 3: MODUL KONSELING
- [ ] Counseling session management
- [ ] Case tracking system
- [ ] Session notes and documentation
- [ ] Violation tracking
- [ ] Service categories (Personal, Social, Learning, Career)

### Planned for FASE 4: MODUL ASESMEN
- [ ] Assessment creation and management
- [ ] Question bank
- [ ] Student assessment taking interface
- [ ] Results and analysis

### Planned for FASE 5: MODUL SISWA & ORANG TUA
- [ ] Complete student portal
- [ ] Career information portal
- [ ] College/university information
- [ ] Complete parent portal
- [ ] Progress monitoring

### Planned for FASE 6: KOMUNIKASI
- [ ] Notification system
- [ ] Internal messaging
- [ ] Complaint/report submission system

### Planned for FASE 7: REPORTING
- [ ] PDF report generation (Dompdf)
- [ ] Excel export functionality (PhpSpreadsheet)
- [ ] Various report types

### Planned for FASE 8: FINALISASI
- [ ] Complete security audit
- [ ] Performance optimization
- [ ] Complete documentation
- [ ] Comprehensive testing

---

## Version History

- **1.0.0** (2025-10-05) - Initial release with foundation setup
- Future versions will be documented here

---

## Contributors

- **yoezron** - Initial work and architecture

---

## Links

- [Repository](https://github.com/yoezron/sib-k)
- [Issues](https://github.com/yoezron/sib-k/issues)
- [Pull Requests](https://github.com/yoezron/sib-k/pulls)
