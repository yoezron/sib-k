<?php

/**
 * File Path: app/Config/Routes.php
 * 
 * Complete Routes Configuration
 * Mendefinisikan SEMUA routing aplikasi dengan grup berbasis role
 * 
 * Template: Qovex Admin Template
 * Framework: CodeIgniter 4
 * 
 * @package    SIB-K
 * @subpackage Config
 * @category   Configuration
 * @author     Development Team
 * @created    2025-01-01
 * @updated    2025-01-07 - Complete rewrite with all modules
 */

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

/*
|--------------------------------------------------------------------------
| Default Routes
|--------------------------------------------------------------------------
*/
$routes->get('/', 'Home::index');
$routes->get('test', 'Test::index');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
| Public routes untuk login, register, forgot password
*/
$routes->group('', ['filter' => 'csrf'], function ($routes) {
    $routes->get('login', 'Auth\AuthController::index', ['as' => 'login']);
    $routes->post('login', 'Auth\AuthController::login', ['as' => 'login.submit']);
    $routes->get('logout', 'Auth\AuthController::logout', ['as' => 'logout']);
    $routes->get('register', 'Auth\AuthController::register', ['as' => 'register']);
    $routes->post('register', 'Auth\AuthController::doRegister', ['as' => 'register.submit']);
    $routes->get('forgot-password', 'Auth\AuthController::forgotPassword', ['as' => 'password.forgot']);
    $routes->post('forgot-password', 'Auth\AuthController::sendResetLink', ['as' => 'password.email']);
    $routes->get('reset-password/(:segment)', 'Auth\AuthController::resetPassword/$1', ['as' => 'password.reset']);
    $routes->post('reset-password', 'Auth\AuthController::doResetPassword', ['as' => 'password.update']);
});

// Email verification
$routes->get('verify/(:segment)', 'Auth\AuthController::verify/$1', ['as' => 'verification.verify']);

/*
|--------------------------------------------------------------------------
| Profile Routes (All authenticated users)
|--------------------------------------------------------------------------
*/
$routes->group('profile', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ProfileController::index', ['as' => 'profile']);
    $routes->get('edit', 'ProfileController::edit', ['as' => 'profile.edit']);
    $routes->post('update', 'ProfileController::update', ['as' => 'profile.update']);
    $routes->post('change-password', 'ProfileController::changePassword', ['as' => 'profile.password']);
    $routes->post('upload-photo', 'ProfileController::uploadPhoto', ['as' => 'profile.photo']);
});

/*
|--------------------------------------------------------------------------
| ADMIN Routes
|--------------------------------------------------------------------------
| Prefix: admin
| Filter: auth
| Role: Admin only (enforced in controllers)
*/
$routes->group('admin', ['filter' => 'auth', 'namespace' => 'App\Controllers\Admin'], function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'DashboardController::index', ['as' => 'admin.dashboard']);
    $routes->get('dashboard/stats', 'DashboardController::getStats', ['as' => 'admin.dashboard.stats']);

    // ========== USER MANAGEMENT ==========
    $routes->group('users', function ($routes) {
        $routes->get('/', 'UserController::index', ['as' => 'admin.users']);
        $routes->get('create', 'UserController::create', ['as' => 'admin.users.create']);
        $routes->post('store', 'UserController::store', ['as' => 'admin.users.store']);
        $routes->get('show/(:num)', 'UserController::show/$1', ['as' => 'admin.users.show']);
        $routes->get('edit/(:num)', 'UserController::edit/$1', ['as' => 'admin.users.edit']);
        $routes->post('update/(:num)', 'UserController::update/$1', ['as' => 'admin.users.update']);
        $routes->post('delete/(:num)', 'UserController::delete/$1', ['as' => 'admin.users.delete']);
        $routes->post('toggle-active/(:num)', 'UserController::toggleActive/$1', ['as' => 'admin.users.toggle']);
        $routes->post('reset-password/(:num)', 'UserController::resetPassword/$1', ['as' => 'admin.users.reset']);
        $routes->post('upload-photo/(:num)', 'UserController::uploadPhoto/$1', ['as' => 'admin.users.photo']);
        $routes->get('export', 'UserController::export', ['as' => 'admin.users.export']);
        $routes->get('search', 'UserController::search', ['as' => 'admin.users.search']);
    });

    // ========== ROLE MANAGEMENT ==========
    $routes->group('roles', function ($routes) {
        $routes->get('/', 'RoleController::index', ['as' => 'admin.roles']);
        $routes->get('create', 'RoleController::create', ['as' => 'admin.roles.create']);
        $routes->post('store', 'RoleController::store', ['as' => 'admin.roles.store']);
        $routes->get('edit/(:num)', 'RoleController::edit/$1', ['as' => 'admin.roles.edit']);
        $routes->post('update/(:num)', 'RoleController::update/$1', ['as' => 'admin.roles.update']);
        $routes->post('delete/(:num)', 'RoleController::delete/$1', ['as' => 'admin.roles.delete']);
        $routes->get('permissions/(:num)', 'RoleController::permissions/$1', ['as' => 'admin.roles.permissions']);
        $routes->post('assign-permissions/(:num)', 'RoleController::assignPermissions/$1', ['as' => 'admin.roles.assign']);
    });

    // ========== ACADEMIC YEAR MANAGEMENT ==========
    $routes->group('academic-years', function ($routes) {
        $routes->get('/', 'AcademicYearController::index', ['as' => 'admin.academic_years']);
        $routes->get('create', 'AcademicYearController::create', ['as' => 'admin.academic_years.create']);
        $routes->post('store', 'AcademicYearController::store', ['as' => 'admin.academic_years.store']);
        $routes->get('edit/(:num)', 'AcademicYearController::edit/$1', ['as' => 'admin.academic_years.edit']);
        $routes->post('update/(:num)', 'AcademicYearController::update/$1', ['as' => 'admin.academic_years.update']);
        $routes->post('delete/(:num)', 'AcademicYearController::delete/$1', ['as' => 'admin.academic_years.delete']);
        $routes->post('set-active/(:num)', 'AcademicYearController::setActive/$1', ['as' => 'admin.academic_years.activate']);
        $routes->get('get-suggested', 'AcademicYearController::getSuggested', ['as' => 'admin.academic_years.suggested']);
        $routes->get('check-overlap', 'AcademicYearController::checkOverlap', ['as' => 'admin.academic_years.check']);
        $routes->get('generate-year-name', 'AcademicYearController::generateYearName', ['as' => 'admin.academic_years.generate']);
    });

    // ========== CLASS MANAGEMENT ==========
    $routes->group('classes', function ($routes) {
        $routes->get('/', 'ClassController::index', ['as' => 'admin.classes']);
        $routes->get('create', 'ClassController::create', ['as' => 'admin.classes.create']);
        $routes->post('store', 'ClassController::store', ['as' => 'admin.classes.store']);
        $routes->get('edit/(:num)', 'ClassController::edit/$1', ['as' => 'admin.classes.edit']);
        $routes->post('update/(:num)', 'ClassController::update/$1', ['as' => 'admin.classes.update']);
        $routes->post('delete/(:num)', 'ClassController::delete/$1', ['as' => 'admin.classes.delete']);
        $routes->get('detail/(:num)', 'ClassController::detail/$1', ['as' => 'admin.classes.detail']);
        $routes->get('get-suggested-name', 'ClassController::getSuggestedName', ['as' => 'admin.classes.suggested']);
        $routes->post('assign-homeroom/(:num)', 'ClassController::assignHomeroom/$1', ['as' => 'admin.classes.homeroom']);
        $routes->post('assign-counselor/(:num)', 'ClassController::assignCounselor/$1', ['as' => 'admin.classes.counselor']);
    });

    // ========== STUDENT MANAGEMENT ==========
    $routes->group('students', function ($routes) {
        $routes->get('/', 'StudentController::index', ['as' => 'admin.students']);
        $routes->get('create', 'StudentController::create', ['as' => 'admin.students.create']);
        $routes->post('store', 'StudentController::store', ['as' => 'admin.students.store']);
        $routes->get('profile/(:num)', 'StudentController::profile/$1', ['as' => 'admin.students.profile']);
        $routes->get('edit/(:num)', 'StudentController::edit/$1', ['as' => 'admin.students.edit']);
        $routes->post('update/(:num)', 'StudentController::update/$1', ['as' => 'admin.students.update']);
        $routes->post('delete/(:num)', 'StudentController::delete/$1', ['as' => 'admin.students.delete']);
        $routes->post('change-class/(:num)', 'StudentController::changeClass/$1', ['as' => 'admin.students.change_class']);
        $routes->get('export', 'StudentController::export', ['as' => 'admin.students.export']);
        $routes->get('search', 'StudentController::search', ['as' => 'admin.students.search']);
        $routes->get('by-class/(:num)', 'StudentController::getByClass/$1', ['as' => 'admin.students.by_class']);
        $routes->get('stats', 'StudentController::getStats', ['as' => 'admin.students.stats']);

        // Import
        $routes->get('import', 'StudentController::import', ['as' => 'admin.students.import']);
        $routes->post('do-import', 'StudentController::doImport', ['as' => 'admin.students.do_import']);
        $routes->get('download-template', 'StudentController::downloadTemplate', ['as' => 'admin.students.template']);
    });

    // ========== SYSTEM SETTINGS ==========
    $routes->group('settings', function ($routes) {
        $routes->get('/', 'SettingController::index', ['as' => 'admin.settings']);
        $routes->post('update', 'SettingController::update', ['as' => 'admin.settings.update']);
    });
});

/*
|--------------------------------------------------------------------------
| KOORDINATOR BK Routes
|--------------------------------------------------------------------------
| Prefix: koordinator
| Filter: auth
| Role: Koordinator BK
*/
$routes->group('koordinator', ['filter' => 'auth', 'namespace' => 'App\Controllers\Koordinator'], function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'DashboardController::index', ['as' => 'koordinator.dashboard']);

    // Additional koordinator-specific routes will be added here in future phases
    // Currently koordinator has same access as counselor routes
});

/*
|--------------------------------------------------------------------------
| COUNSELOR Routes (Guru BK)
|--------------------------------------------------------------------------
| Prefix: counselor
| Filter: auth
| Role: Guru BK, Koordinator BK
*/
$routes->group('counselor', ['filter' => 'auth', 'namespace' => 'App\Controllers\Counselor'], function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'DashboardController::index', ['as' => 'counselor.dashboard']);
    $routes->get('dashboard/getQuickStats', 'DashboardController::getQuickStats', ['as' => 'counselor.dashboard.stats']);

    // ========== COUNSELING SESSIONS ==========
    $routes->group('sessions', function ($routes) {
        $routes->get('/', 'SessionController::index', ['as' => 'counselor.sessions']);
        $routes->get('create', 'SessionController::create', ['as' => 'counselor.sessions.create']);
        $routes->post('store', 'SessionController::store', ['as' => 'counselor.sessions.store']);
        $routes->get('detail/(:num)', 'SessionController::show/$1', ['as' => 'counselor.sessions.detail']);
        $routes->get('edit/(:num)', 'SessionController::edit/$1', ['as' => 'counselor.sessions.edit']);
        $routes->post('update/(:num)', 'SessionController::update/$1', ['as' => 'counselor.sessions.update']);
        $routes->post('delete/(:num)', 'SessionController::delete/$1', ['as' => 'counselor.sessions.delete']);
        $routes->post('addNote/(:num)', 'SessionController::addNote/$1', ['as' => 'counselor.sessions.note']);
        $routes->get('students-by-class', 'SessionController::getStudentsByClass', ['as' => 'counselor.sessions.students']);
    });

    // ========== CASES & VIOLATIONS ==========
    $routes->group('cases', function ($routes) {
        $routes->get('/', 'CaseController::index', ['as' => 'counselor.cases']);
        $routes->get('create', 'CaseController::create', ['as' => 'counselor.cases.create']);
        $routes->post('store', 'CaseController::store', ['as' => 'counselor.cases.store']);
        $routes->get('detail/(:num)', 'CaseController::detail/$1', ['as' => 'counselor.cases.detail']);
        $routes->post('update/(:num)', 'CaseController::update/$1', ['as' => 'counselor.cases.update']);
        $routes->post('delete/(:num)', 'CaseController::delete/$1', ['as' => 'counselor.cases.delete']);
        $routes->post('addSanction/(:num)', 'CaseController::addSanction/$1', ['as' => 'counselor.cases.sanction']);
        $routes->post('notifyParent/(:num)', 'CaseController::notifyParent/$1', ['as' => 'counselor.cases.notify']);
    });

    // ========== ASSESSMENTS ==========
    $routes->group('assessments', function ($routes) {
        // List & CRUD
        $routes->get('/', 'AssessmentController::index', ['as' => 'counselor.assessments']);
        $routes->get('create', 'AssessmentController::create', ['as' => 'counselor.assessments.create']);
        $routes->post('store', 'AssessmentController::store', ['as' => 'counselor.assessments.store']);
        $routes->get('(:num)', 'AssessmentController::show/$1', ['as' => 'counselor.assessments.show']);
        $routes->get('(:num)/edit', 'AssessmentController::edit/$1', ['as' => 'counselor.assessments.edit']);
        $routes->post('(:num)/update', 'AssessmentController::update/$1', ['as' => 'counselor.assessments.update']);
        $routes->post('(:num)/delete', 'AssessmentController::delete/$1', ['as' => 'counselor.assessments.delete']);

        // Questions Management
        $routes->get('(:num)/questions', 'AssessmentController::questions/$1', ['as' => 'counselor.assessments.questions']);
        $routes->post('(:num)/questions/add', 'AssessmentController::addQuestion/$1', ['as' => 'counselor.assessments.questions.add']);
        $routes->post('(:num)/questions/(:num)/update', 'AssessmentController::updateQuestion/$1/$2', ['as' => 'counselor.assessments.questions.update']);
        $routes->post('(:num)/questions/(:num)/delete', 'AssessmentController::deleteQuestion/$1/$2', ['as' => 'counselor.assessments.questions.delete']);

        // Assignment
        $routes->get('(:num)/assign', 'AssessmentController::assign/$1', ['as' => 'counselor.assessments.assign']);
        $routes->post('(:num)/assign/process', 'AssessmentController::processAssign/$1', ['as' => 'counselor.assessments.assign.process']);

        // Results & Grading
        $routes->get('(:num)/results', 'AssessmentController::results/$1', ['as' => 'counselor.assessments.results']);
        $routes->get('(:num)/results/(:num)', 'AssessmentController::resultDetail/$1/$2', ['as' => 'counselor.assessments.result.detail']);
        $routes->get('(:num)/grading', 'AssessmentController::grading/$1', ['as' => 'counselor.assessments.grading']);
        $routes->post('grade/submit', 'AssessmentController::submitGrade', ['as' => 'counselor.assessments.grade.submit']);

        // Publishing
        $routes->post('(:num)/publish', 'AssessmentController::publish/$1', ['as' => 'counselor.assessments.publish']);
        $routes->post('(:num)/unpublish', 'AssessmentController::unpublish/$1', ['as' => 'counselor.assessments.unpublish']);
        $routes->post('(:num)/duplicate', 'AssessmentController::duplicate/$1', ['as' => 'counselor.assessments.duplicate']);
    });

    // ========== REPORTS ==========
    $routes->group('reports', function ($routes) {
        $routes->get('/', 'ReportController::index', ['as' => 'counselor.reports']);
        $routes->get('student/(:num)', 'ReportController::studentReport/$1', ['as' => 'counselor.reports.student']);
        $routes->get('session-summary', 'ReportController::sessionSummary', ['as' => 'counselor.reports.session']);
        $routes->get('violation-report', 'ReportController::violationReport', ['as' => 'counselor.reports.violation']);
        $routes->post('generate-pdf', 'ReportController::generatePDF', ['as' => 'counselor.reports.pdf']);
        $routes->post('generate-excel', 'ReportController::generateExcel', ['as' => 'counselor.reports.excel']);
    });
});

/*
|--------------------------------------------------------------------------
| HOMEROOM TEACHER Routes (Wali Kelas)
|--------------------------------------------------------------------------
| Prefix: homeroom
| Filter: auth
| Role: Wali Kelas
*/
$routes->group('homeroom', ['filter' => 'auth', 'namespace' => 'App\Controllers\HomeroomTeacher'], function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'DashboardController::index', ['as' => 'homeroom.dashboard']);
    $routes->get('dashboard/stats', 'DashboardController::getStats', ['as' => 'homeroom.dashboard.stats']);

    // ========== VIOLATIONS ==========
    $routes->group('violations', function ($routes) {
        $routes->get('/', 'ViolationController::index', ['as' => 'homeroom.violations']);
        $routes->get('create', 'ViolationController::create', ['as' => 'homeroom.violations.create']);
        $routes->post('store', 'ViolationController::store', ['as' => 'homeroom.violations.store']);
        $routes->get('detail/(:num)', 'ViolationController::detail/$1', ['as' => 'homeroom.violations.detail']);
    });

    // ========== CLASS REPORTS ==========
    $routes->group('reports', function ($routes) {
        $routes->get('/', 'ClassReportController::index', ['as' => 'homeroom.reports']);
        $routes->get('data', 'ClassReportController::getReportData', ['as' => 'homeroom.reports.data']);
        $routes->get('export-pdf', 'ClassReportController::exportPDF', ['as' => 'homeroom.reports.pdf']);
        $routes->get('export-excel', 'ClassReportController::exportExcel', ['as' => 'homeroom.reports.excel']);
    });
});

/*
|--------------------------------------------------------------------------
| STUDENT Routes
|--------------------------------------------------------------------------
| Prefix: student
| Filter: auth
| Role: Siswa
*/
$routes->group('student', ['filter' => 'auth', 'namespace' => 'App\Controllers\Student'], function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'DashboardController::index', ['as' => 'student.dashboard']);

    // ========== PROFILE ==========
    $routes->group('profile', function ($routes) {
        $routes->get('/', 'ProfileController::index', ['as' => 'student.profile']);
        $routes->get('edit', 'ProfileController::edit', ['as' => 'student.profile.edit']);
        $routes->post('update', 'ProfileController::update', ['as' => 'student.profile.update']);
    });

    // ========== SCHEDULE & COUNSELING REQUEST ==========
    $routes->group('schedule', function ($routes) {
        $routes->get('/', 'ScheduleController::index', ['as' => 'student.schedule']);
        $routes->get('request', 'ScheduleController::request', ['as' => 'student.schedule.request']);
        $routes->post('submit-request', 'ScheduleController::submitRequest', ['as' => 'student.schedule.submit']);
        $routes->get('history', 'ScheduleController::history', ['as' => 'student.schedule.history']);
    });

    // ========== ASSESSMENTS ==========
    $routes->group('assessments', function ($routes) {
        $routes->get('/', 'AssessmentController::index', ['as' => 'student.assessments']);
        $routes->get('available', 'AssessmentController::available', ['as' => 'student.assessments.available']);
        $routes->get('(:num)/start', 'AssessmentController::start/$1', ['as' => 'student.assessments.start']);
        $routes->get('(:num)/take', 'AssessmentController::take/$1', ['as' => 'student.assessments.take']);
        $routes->post('(:num)/answer', 'AssessmentController::submitAnswer/$1', ['as' => 'student.assessments.answer']);
        $routes->post('(:num)/submit', 'AssessmentController::submitAssessment/$1', ['as' => 'student.assessments.submit']);
        $routes->get('results', 'AssessmentController::results', ['as' => 'student.assessments.results']);
        $routes->get('(:num)/result', 'AssessmentController::viewResult/$1', ['as' => 'student.assessments.result']);
        $routes->get('(:num)/review', 'AssessmentController::reviewAnswers/$1', ['as' => 'student.assessments.review']);
    });

    // ========== CAREER EXPLORATION ==========
    $routes->group('career', function ($routes) {
        $routes->get('/', 'CareerController::index', ['as' => 'student.career']);
        $routes->get('explore', 'CareerController::explore', ['as' => 'student.career.explore']);
        $routes->get('detail/(:num)', 'CareerController::detail/$1', ['as' => 'student.career.detail']);
        $routes->post('save/(:num)', 'CareerController::save/$1', ['as' => 'student.career.save']);
        $routes->get('saved', 'CareerController::saved', ['as' => 'student.career.saved']);
    });
});

/*
|--------------------------------------------------------------------------
| PARENT Routes (Orang Tua)
|--------------------------------------------------------------------------
| Prefix: parent
| Filter: auth
| Role: Orang Tua
*/
$routes->group('parent', ['filter' => 'auth', 'namespace' => 'App\Controllers\Parent'], function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'DashboardController::index', ['as' => 'parent.dashboard']);

    // ========== CHILD MANAGEMENT ==========
    $routes->group('child', function ($routes) {
        $routes->get('profile/(:num)', 'ChildController::profile/$1', ['as' => 'parent.child.profile']);
        $routes->get('violations/(:num)', 'ChildController::violations/$1', ['as' => 'parent.child.violations']);
        $routes->get('sessions/(:num)', 'ChildController::sessions/$1', ['as' => 'parent.child.sessions']);
        $routes->get('assessments/(:num)', 'ChildController::assessments/$1', ['as' => 'parent.child.assessments']);
    });

    // ========== COMMUNICATION ==========
    $routes->group('communication', function ($routes) {
        $routes->get('/', 'CommunicationController::index', ['as' => 'parent.communication']);
        $routes->post('send-message', 'CommunicationController::sendMessage', ['as' => 'parent.communication.send']);
    });
});

/*
|--------------------------------------------------------------------------
| MESSAGING Routes (All authenticated users)
|--------------------------------------------------------------------------
| Prefix: messages
| Filter: auth
*/
$routes->group('messages', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'MessageController::index', ['as' => 'messages.inbox']);
    $routes->get('inbox', 'MessageController::inbox', ['as' => 'messages.inbox']);
    $routes->get('sent', 'MessageController::sent', ['as' => 'messages.sent']);
    $routes->get('compose', 'MessageController::compose', ['as' => 'messages.compose']);
    $routes->post('send', 'MessageController::send', ['as' => 'messages.send']);
    $routes->get('detail/(:num)', 'MessageController::detail/$1', ['as' => 'messages.detail']);
    $routes->post('reply/(:num)', 'MessageController::reply/$1', ['as' => 'messages.reply']);
    $routes->post('delete/(:num)', 'MessageController::delete/$1', ['as' => 'messages.delete']);
    $routes->post('mark-read/(:num)', 'MessageController::markAsRead/$1', ['as' => 'messages.read']);
});

/*
|--------------------------------------------------------------------------
| NOTIFICATION Routes (All authenticated users)
|--------------------------------------------------------------------------
| Prefix: notifications
| Filter: auth
*/
$routes->group('notifications', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'NotificationController::index', ['as' => 'notifications']);
    $routes->get('unread', 'NotificationController::unread', ['as' => 'notifications.unread']);
    $routes->post('mark-read/(:num)', 'NotificationController::markAsRead/$1', ['as' => 'notifications.read']);
    $routes->post('mark-all-read', 'NotificationController::markAllAsRead', ['as' => 'notifications.read_all']);
    $routes->post('delete/(:num)', 'NotificationController::delete/$1', ['as' => 'notifications.delete']);
    $routes->get('count', 'NotificationController::getUnreadCount', ['as' => 'notifications.count']);
});

/*
|--------------------------------------------------------------------------
| API Routes (AJAX & REST API)
|--------------------------------------------------------------------------
| Prefix: api
| Filter: auth
*/
$routes->group('api', ['filter' => 'auth', 'namespace' => 'App\Controllers\Api'], function ($routes) {

    // Dashboard Stats
    $routes->get('stats/admin', 'StatsController::adminStats');
    $routes->get('stats/counselor', 'StatsController::counselorStats');
    $routes->get('stats/student', 'StatsController::studentStats');

    // Students API
    $routes->group('students', function ($routes) {
        $routes->get('search', 'StudentApiController::search');
        $routes->get('by-class/(:num)', 'StudentApiController::getByClass/$1');
        $routes->get('(:num)', 'StudentApiController::show/$1');
    });

    // Classes API
    $routes->group('classes', function ($routes) {
        $routes->get('active', 'ClassApiController::getActive');
        $routes->get('(:num)/students', 'ClassApiController::getStudents/$1');
    });

    // Assessments API
    $routes->group('assessments', function ($routes) {
        $routes->get('(:num)', 'AssessmentApiController::show/$1');
        $routes->get('(:num)/questions', 'AssessmentApiController::getQuestions/$1');
        $routes->get('(:num)/statistics', 'AssessmentApiController::getStatistics/$1');
        $routes->get('(:num)/progress/(:num)', 'AssessmentApiController::getProgress/$1/$2');
        $routes->post('answer', 'AssessmentApiController::saveAnswer');
        $routes->post('(:num)/autosave', 'AssessmentApiController::autosave/$1');
    });

    // Notifications API
    $routes->group('notifications', function ($routes) {
        $routes->get('latest', 'NotificationApiController::getLatest');
        $routes->get('count', 'NotificationApiController::getUnreadCount');
        $routes->post('(:num)/read', 'NotificationApiController::markAsRead/$1');
    });

    // Messages API
    $routes->group('messages', function ($routes) {
        $routes->get('unread-count', 'MessageApiController::getUnreadCount');
        $routes->get('latest', 'MessageApiController::getLatest');
    });
});

/*
|--------------------------------------------------------------------------
| File Upload Routes (Authenticated users only)
|--------------------------------------------------------------------------
*/
$routes->group('upload', ['filter' => 'auth'], function ($routes) {
    $routes->post('profile-photo', 'UploadController::profilePhoto', ['as' => 'upload.photo']);
    $routes->post('document', 'UploadController::document', ['as' => 'upload.document']);
    $routes->post('temp', 'UploadController::temp', ['as' => 'upload.temp']);
});

/*
|--------------------------------------------------------------------------
| Download Routes (Authenticated users only)
|--------------------------------------------------------------------------
*/
$routes->group('download', ['filter' => 'auth'], function ($routes) {
    $routes->get('template/student-import', 'DownloadController::studentTemplate', ['as' => 'download.template.student']);
    $routes->get('report/(:segment)', 'DownloadController::report/$1', ['as' => 'download.report']);
    $routes->get('document/(:segment)', 'DownloadController::document/$1', ['as' => 'download.document']);
});

/*
|--------------------------------------------------------------------------
| 404 Override
|--------------------------------------------------------------------------
*/
$routes->set404Override(function () {
    return view('errors/html/error_404');
});
