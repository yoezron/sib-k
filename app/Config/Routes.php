<?php

/**
 * File Path: app/Config/Routes.php
 * 
 * Routes Configuration
 * Mendefinisikan semua routing aplikasi dengan grup berbasis role
 * 
 * @package    SIB-K
 * @subpackage Config
 * @category   Configuration
 * @author     Development Team
 * @created    2025-01-01
 */

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default controller
$routes->get('/', 'Home::index');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
$routes->group('', ['filter' => 'csrf'], function ($routes) {
    $routes->get('login', 'Auth\AuthController::index');
    $routes->post('login', 'Auth\AuthController::login');
    $routes->get('logout', 'Auth\AuthController::logout');
    $routes->get('register', 'Auth\AuthController::register');
    $routes->post('register', 'Auth\AuthController::doRegister');
    $routes->get('forgot-password', 'Auth\AuthController::forgotPassword');
    $routes->post('forgot-password', 'Auth\AuthController::sendResetLink');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| Filter: auth, role:Admin
*/
$routes->group('admin', ['filter' => 'auth', 'namespace' => 'App\Controllers\Admin'], function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');

    // User Management
    $routes->get('users', 'UserController::index');
    $routes->get('users/create', 'UserController::create');
    $routes->post('users/store', 'UserController::store');
    $routes->get('users/edit/(:num)', 'UserController::edit/$1');
    $routes->post('users/update/(:num)', 'UserController::update/$1');
    $routes->post('users/delete/(:num)', 'UserController::delete/$1');
    $routes->post('users/toggle-active/(:num)', 'UserController::toggleActive/$1');

    // Role Management
    $routes->get('roles', 'RoleController::index');
    $routes->get('roles/create', 'RoleController::create');
    $routes->post('roles/store', 'RoleController::store');
    $routes->get('roles/edit/(:num)', 'RoleController::edit/$1');
    $routes->post('roles/update/(:num)', 'RoleController::update/$1');
    $routes->post('roles/delete/(:num)', 'RoleController::delete/$1');
    $routes->get('roles/permissions/(:num)', 'RoleController::permissions/$1');
    $routes->post('roles/assign-permissions/(:num)', 'RoleController::assignPermissions/$1');

    // Permission Management
    $routes->get('permissions', 'PermissionController::index');
    $routes->get('permissions/create', 'PermissionController::create');
    $routes->post('permissions/store', 'PermissionController::store');
    $routes->get('permissions/edit/(:num)', 'PermissionController::edit/$1');
    $routes->post('permissions/update/(:num)', 'PermissionController::update/$1');
    $routes->post('permissions/delete/(:num)', 'PermissionController::delete/$1');

    // Academic Year Management
    $routes->get('academic-years', 'AcademicYearController::index');
    $routes->get('academic-years/create', 'AcademicYearController::create');
    $routes->post('academic-years/store', 'AcademicYearController::store');
    $routes->get('academic-years/edit/(:num)', 'AcademicYearController::edit/$1');
    $routes->post('academic-years/update/(:num)', 'AcademicYearController::update/$1');
    $routes->post('academic-years/delete/(:num)', 'AcademicYearController::delete/$1');
    $routes->post('academic-years/set-active/(:num)', 'AcademicYearController::setActive/$1');

    // Class Management
    $routes->get('classes', 'ClassController::index');
    $routes->get('classes/create', 'ClassController::create');
    $routes->post('classes/store', 'ClassController::store');
    $routes->get('classes/edit/(:num)', 'ClassController::edit/$1');
    $routes->post('classes/update/(:num)', 'ClassController::update/$1');
    $routes->post('classes/delete/(:num)', 'ClassController::delete/$1');
    $routes->get('classes/detail/(:num)', 'ClassController::detail/$1');

    // Student Management
    $routes->get('students', 'StudentController::index');
    $routes->get('students/create', 'StudentController::create');
    $routes->post('students/store', 'StudentController::store');
    $routes->get('students/edit/(:num)', 'StudentController::edit/$1');
    $routes->post('students/update/(:num)', 'StudentController::update/$1');
    $routes->post('students/delete/(:num)', 'StudentController::delete/$1');
    $routes->get('students/detail/(:num)', 'StudentController::detail/$1');

    // Import & Export
    $routes->get('students/import', 'StudentController::import');
    $routes->post('students/do-import', 'StudentController::doImport');
    $routes->get('students/download-template', 'StudentController::downloadTemplate');
    $routes->get('students/export', 'StudentController::export');

    // System Settings
    $routes->get('settings', 'SettingController::index');
    $routes->post('settings/update', 'SettingController::update');

    // Activity Logs
    $routes->get('logs', 'LogController::index');
    $routes->get('logs/detail/(:num)', 'LogController::detail/$1');
    $routes->post('logs/clear', 'LogController::clear');

    // AJAX Routes
    $routes->get('students/search', 'StudentController::search');
    $routes->get('students/by-class/(:num)', 'StudentController::getByClass/$1');
});

/*
|--------------------------------------------------------------------------
| Koordinator BK Routes
|--------------------------------------------------------------------------
| Filter: auth, role:Koordinator BK
*/
$routes->group('koordinator', ['filter' => 'auth', 'namespace' => 'App\Controllers\Koordinator'], function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');

    // User Management (Limited)
    $routes->get('users', 'UserController::index');
    $routes->get('users/create', 'UserController::create');
    $routes->post('users/store', 'UserController::store');

    // Academic Data
    $routes->get('academic-years', 'AcademicYearController::index');
    $routes->get('classes', 'ClassController::index');
    $routes->get('students', 'StudentController::index');

    // Counseling Management
    $routes->get('sessions', 'SessionController::index');
    $routes->get('cases', 'CaseController::index');
    $routes->get('assessments', 'AssessmentController::index');

    // Reports
    $routes->get('reports', 'ReportController::index');
    $routes->get('reports/generate', 'ReportController::generate');
});

/*
|--------------------------------------------------------------------------
| Guru BK Routes (Counselor)
|--------------------------------------------------------------------------
| Filter: auth, role:Guru BK, Koordinator BK
*/
$routes->group('counselor', ['filter' => 'auth', 'namespace' => 'App\Controllers\Counselor'], function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');

    // Student Data (Read Only)
    $routes->get('students', 'StudentController::index');
    $routes->get('students/detail/(:num)', 'StudentController::detail/$1');

    // Counseling Sessions
    $routes->get('sessions', 'SessionController::index');
    $routes->get('sessions/create', 'SessionController::create');
    $routes->post('sessions/store', 'SessionController::store');
    $routes->get('sessions/edit/(:num)', 'SessionController::edit/$1');
    $routes->post('sessions/update/(:num)', 'SessionController::update/$1');
    $routes->post('sessions/delete/(:num)', 'SessionController::delete/$1');
    $routes->get('sessions/detail/(:num)', 'SessionController::detail/$1');

    // Cases & Violations
    $routes->get('cases', 'CaseController::index');
    $routes->get('cases/create', 'CaseController::create');
    $routes->post('cases/store', 'CaseController::store');
    $routes->get('cases/detail/(:num)', 'CaseController::detail/$1');

    $routes->get('violations', 'ViolationController::index');
    $routes->get('violations/create', 'ViolationController::create');
    $routes->post('violations/store', 'ViolationController::store');

    // Assessments
    $routes->get('assessments', 'AssessmentController::index');
    $routes->get('assessments/create', 'AssessmentController::create');
    $routes->post('assessments/store', 'AssessmentController::store');
    $routes->get('assessments/assign', 'AssessmentController::assign');
    $routes->post('assessments/do-assign', 'AssessmentController::doAssign');
    $routes->get('assessments/results/(:num)', 'AssessmentController::results/$1');

    // Reports
    $routes->get('reports', 'ReportController::index');
    $routes->get('reports/generate', 'ReportController::generate');
    $routes->get('reports/download/(:segment)', 'ReportController::download/$1');

    // Schedule
    $routes->get('schedule', 'ScheduleController::index');
    $routes->get('schedule/calendar', 'ScheduleController::calendar');
});

/*
|--------------------------------------------------------------------------
| Wali Kelas Routes (Homeroom Teacher)
|--------------------------------------------------------------------------
| Filter: auth, role:Wali Kelas
*/
$routes->group('homeroom', ['filter' => 'auth', 'namespace' => 'App\Controllers\HomeroomTeacher'], function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');

    // Class Management
    $routes->get('my-class', 'ClassController::myClass');
    $routes->get('students', 'StudentController::index');
    $routes->get('students/detail/(:num)', 'StudentController::detail/$1');

    // Violations
    $routes->get('violations', 'ViolationController::index');
    $routes->get('violations/create', 'ViolationController::create');
    $routes->post('violations/store', 'ViolationController::store');
    $routes->get('violations/detail/(:num)', 'ViolationController::detail/$1');

    // Reports
    $routes->get('reports', 'ReportController::index');
    $routes->get('reports/class-summary', 'ReportController::classSummary');

    // Messages
    $routes->get('messages', 'MessageController::index');
});

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
| Filter: auth, role:Siswa
*/
$routes->group('student', ['filter' => 'auth', 'namespace' => 'App\Controllers\Student'], function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');

    // Profile
    $routes->get('profile', 'ProfileController::index');
    $routes->post('profile/update', 'ProfileController::update');
    $routes->post('profile/change-password', 'ProfileController::changePassword');

    // Schedule
    $routes->get('schedule', 'ScheduleController::index');
    $routes->get('schedule/request', 'ScheduleController::request');
    $routes->post('schedule/submit-request', 'ScheduleController::submitRequest');

    // Assessments
    $routes->get('assessments', 'AssessmentController::index');
    $routes->get('assessments/take/(:num)', 'AssessmentController::take/$1');
    $routes->post('assessments/submit/(:num)', 'AssessmentController::submit/$1');
    $routes->get('assessments/results', 'AssessmentController::results');

    // Violations
    $routes->get('violations', 'ViolationController::index');

    // Career Information
    $routes->get('career', 'CareerController::index');
    $routes->get('career/explore', 'CareerController::explore');
    $routes->get('career/detail/(:num)', 'CareerController::detail/$1');
    $routes->get('career/universities', 'CareerController::universities');

    // Messages
    $routes->get('messages', 'MessageController::index');
    $routes->get('messages/compose', 'MessageController::compose');
    $routes->post('messages/send', 'MessageController::send');
});

/*
|--------------------------------------------------------------------------
| Parent Routes
|--------------------------------------------------------------------------
| Filter: auth, role:Orang Tua
*/
$routes->group('parent', ['filter' => 'auth', 'namespace' => 'App\Controllers\Parent'], function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');

    // Children Profile
    $routes->get('children', 'ChildController::index');
    $routes->get('children/profile/(:num)', 'ChildController::profile/$1');
    $routes->get('children/violations/(:num)', 'ChildController::violations/$1');
    $routes->get('children/sessions/(:num)', 'ChildController::sessions/$1');

    // Messages
    $routes->get('messages', 'MessageController::index');
    $routes->get('messages/compose', 'MessageController::compose');
    $routes->post('messages/send', 'MessageController::send');
});

/*
|--------------------------------------------------------------------------
| Common Routes (Authenticated Users)
|--------------------------------------------------------------------------
*/
$routes->group('', ['filter' => 'auth'], function ($routes) {
    // Dashboard redirect
    $routes->get('dashboard', 'DashboardController::redirect');

    // Profile
    $routes->get('profile', 'ProfileController::index');
    $routes->post('profile/update', 'ProfileController::update');
    $routes->post('profile/upload-photo', 'ProfileController::uploadPhoto');
    $routes->post('profile/change-password', 'ProfileController::changePassword');

    // Notifications
    $routes->get('notifications', 'NotificationController::index');
    $routes->post('notifications/mark-read/(:num)', 'NotificationController::markRead/$1');
    $routes->post('notifications/mark-all-read', 'NotificationController::markAllRead');
    $routes->post('notifications/delete/(:num)', 'NotificationController::delete/$1');

    // Messages
    $routes->get('messages', 'MessageController::inbox');
    $routes->get('messages/sent', 'MessageController::sent');
    $routes->get('messages/read/(:num)', 'MessageController::read/$1');
    $routes->get('messages/compose', 'MessageController::compose');
    $routes->post('messages/send', 'MessageController::send');
    $routes->post('messages/delete/(:num)', 'MessageController::delete/$1');
});

/*
|--------------------------------------------------------------------------
| API Routes (Optional for AJAX requests)
|--------------------------------------------------------------------------
*/
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    $routes->post('notifications/count', 'NotificationController::getUnreadCount', ['filter' => 'auth']);
    $routes->get('students/search', 'StudentController::search', ['filter' => 'auth']);
    $routes->get('users/search', 'UserController::search', ['filter' => 'auth']);
});
