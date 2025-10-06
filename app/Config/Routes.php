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
$routes->get('test', 'Test::index');

// Profile route
$routes->get('profile', 'ProfileController::index');

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
$routes->get('verify/(:segment)', 'Auth\AuthController::verify/$1');

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
    $routes->get('users/show/(:num)', 'UserController::show/$1');
    $routes->get('users/edit/(:num)', 'UserController::edit/$1');
    $routes->post('users/update/(:num)', 'UserController::update/$1');
    $routes->post('users/delete/(:num)', 'UserController::delete/$1');
    $routes->post('users/toggle-active/(:num)', 'UserController::toggleActive/$1');
    $routes->post('users/reset-password/(:num)', 'UserController::resetPassword/$1');
    $routes->post('users/upload-photo/(:num)', 'UserController::uploadPhoto/$1');
    $routes->get('users/export', 'UserController::export');
    $routes->get('users/search', 'UserController::search');


    // Role Management
    $routes->get('roles', 'RoleController::index');
    $routes->get('roles/create', 'RoleController::create');
    $routes->post('roles/store', 'RoleController::store');
    $routes->get('roles/edit/(:num)', 'RoleController::edit/$1');
    $routes->post('roles/update/(:num)', 'RoleController::update/$1');
    $routes->post('roles/delete/(:num)', 'RoleController::delete/$1');
    $routes->get('roles/permissions/(:num)', 'RoleController::permissions/$1');
    $routes->post('roles/assign-permissions/(:num)', 'RoleController::assignPermissions/$1');

    // Academic Year Management
    $routes->get('academic-years', 'AcademicYearController::index');
    $routes->get('academic-years/create', 'AcademicYearController::create');
    $routes->post('academic-years/store', 'AcademicYearController::store');
    $routes->get('academic-years/edit/(:num)', 'AcademicYearController::edit/$1');
    $routes->post('academic-years/update/(:num)', 'AcademicYearController::update/$1');
    $routes->post('academic-years/delete/(:num)', 'AcademicYearController::delete/$1');
    $routes->post('academic-years/set-active/(:num)', 'AcademicYearController::setActive/$1');
    $routes->get('academic-years/get-suggested', 'AcademicYearController::getSuggested');
    $routes->get('academic-years/check-overlap', 'AcademicYearController::checkOverlap');
    $routes->get('academic-years/generate-year-name', 'AcademicYearController::generateYearName');

    // Class Management
    $routes->get('classes', 'ClassController::index');
    $routes->get('classes/create', 'ClassController::create');
    $routes->post('classes/store', 'ClassController::store');
    $routes->get('classes/edit/(:num)', 'ClassController::edit/$1');
    $routes->post('classes/update/(:num)', 'ClassController::update/$1');
    $routes->post('classes/delete/(:num)', 'ClassController::delete/$1');
    $routes->get('classes/detail/(:num)', 'ClassController::detail/$1');
    $routes->get('classes/get-suggested-name', 'ClassController::getSuggestedName');
    $routes->post('classes/assign-homeroom/(:num)', 'ClassController::assignHomeroom/$1');
    $routes->post('classes/assign-counselor/(:num)', 'ClassController::assignCounselor/$1');


    // Student Management
    $routes->get('students', 'StudentController::index');
    $routes->get('students/create', 'StudentController::create');
    $routes->post('students/store', 'StudentController::store');
    $routes->get('students/profile/(:num)', 'StudentController::profile/$1');
    $routes->get('students/edit/(:num)', 'StudentController::edit/$1');
    $routes->post('students/update/(:num)', 'StudentController::update/$1');
    $routes->post('students/delete/(:num)', 'StudentController::delete/$1');

    // Import & Export
    $routes->post('students/change-class/(:num)', 'StudentController::changeClass/$1');
    $routes->get('students/export', 'StudentController::export');
    $routes->get('students/search', 'StudentController::search');
    $routes->get('students/by-class/(:num)', 'StudentController::getByClass/$1');
    $routes->get('students/import', 'StudentController::import');
    $routes->post('students/do-import', 'StudentController::doImport');
    $routes->get('students/download-template', 'StudentController::downloadTemplate');
    $routes->get('students/stats', 'StudentController::getStats');


    // System Settings
    $routes->get('settings', 'SettingController::index');
    $routes->post('settings/update', 'SettingController::update');
});

/*
|--------------------------------------------------------------------------
| Koordinator BK Routes
|--------------------------------------------------------------------------



/*
|--------------------------------------------------------------------------
| Guru BK Routes (Counselor)
|--------------------------------------------------------------------------
| Filter: auth, role:Guru BK, Koordinator BK
*/
$routes->group('counselor', ['filter' => 'auth', 'namespace' => 'App\Controllers\Counselor'], function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('dashboard/getQuickStats', 'DashboardController::getQuickStats');


    // Counseling Sessions
    $routes->get('sessions', 'SessionController::index');
    $routes->get('sessions/create', 'SessionController::create');
    $routes->post('sessions/store', 'SessionController::store');
    $routes->get('sessions/detail/(:num)', 'SessionController::show/$1');
    $routes->get('sessions/edit/(:num)', 'SessionController::edit/$1');
    $routes->post('sessions/update/(:num)', 'SessionController::update/$1');
    $routes->post('sessions/delete/(:num)', 'SessionController::delete/$1');
    $routes->post('sessions/addNote/(:num)', 'SessionController::addNote/$1');
    $routes->get('sessions/students-by-class', 'SessionController::getStudentsByClass');

    // Cases & Violations
    $routes->get('cases', 'CaseController::index');
    $routes->get('cases/create', 'CaseController::create');
    $routes->post('cases/store', 'CaseController::store');
    $routes->get('cases/detail/(:num)', 'CaseController::detail/$1');
    $routes->post('cases/update/(:num)', 'CaseController::update/$1');
    $routes->post('cases/delete/(:num)', 'CaseController::delete/$1');
    $routes->post('cases/addSanction/(:num)', 'CaseController::addSanction/$1');
    $routes->post('cases/notifyParent/(:num)', 'CaseController::notifyParent/$1');
});
