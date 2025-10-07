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


/**
 * File Path: app/Config/Routes.php (Tambahkan ke file Routes yang sudah ada)
 * 
 * Routes Configuration - Assessment Module
 * Route definitions untuk modul asesmen Guru BK
 * 
 * @package    SIB-K
 * @subpackage Config
 * @category   Routes
 * @author     Development Team
 * @created    2025-01-06
 */

// =====================================================
// COUNSELOR ROUTES - ASSESSMENT MODULE
// =====================================================
$routes->group('counselor', ['filter' => 'auth', 'namespace' => 'App\Controllers\Counselor'], function ($routes) {

    // Assessment Dashboard & CRUD
    $routes->get('assessments', 'AssessmentController::index', ['as' => 'counselor.assessments']);
    $routes->get('assessments/create', 'AssessmentController::create', ['as' => 'counselor.assessments.create']);
    $routes->post('assessments/store', 'AssessmentController::store', ['as' => 'counselor.assessments.store']);
    $routes->get('assessments/(:num)', 'AssessmentController::show/$1', ['as' => 'counselor.assessments.show']);
    $routes->get('assessments/(:num)/edit', 'AssessmentController::edit/$1', ['as' => 'counselor.assessments.edit']);
    $routes->post('assessments/(:num)/update', 'AssessmentController::update/$1', ['as' => 'counselor.assessments.update']);
    $routes->put('assessments/(:num)', 'AssessmentController::update/$1');
    $routes->delete('assessments/(:num)', 'AssessmentController::delete/$1', ['as' => 'counselor.assessments.delete']);
    $routes->post('assessments/(:num)/delete', 'AssessmentController::delete/$1');

    // Question Management
    $routes->get('assessments/(:num)/questions', 'AssessmentController::questions/$1', ['as' => 'counselor.assessments.questions']);
    $routes->post('assessments/(:num)/questions/add', 'AssessmentController::addQuestion/$1', ['as' => 'counselor.assessments.questions.add']);
    $routes->post('assessments/(:num)/questions/(:num)/update', 'AssessmentController::updateQuestion/$1/$2', ['as' => 'counselor.assessments.questions.update']);
    $routes->delete('assessments/(:num)/questions/(:num)', 'AssessmentController::deleteQuestion/$1/$2', ['as' => 'counselor.assessments.questions.delete']);
    $routes->post('assessments/(:num)/questions/(:num)/delete', 'AssessmentController::deleteQuestion/$1/$2');

    // Assignment
    $routes->get('assessments/(:num)/assign', 'AssessmentController::assign/$1', ['as' => 'counselor.assessments.assign']);
    $routes->post('assessments/(:num)/assign/process', 'AssessmentController::processAssign/$1', ['as' => 'counselor.assessments.assign.process']);

    // Results & Grading
    $routes->get('assessments/(:num)/results', 'AssessmentController::results/$1', ['as' => 'counselor.assessments.results']);
    $routes->get('assessments/(:num)/results/(:num)', 'AssessmentController::resultDetail/$1/$2', ['as' => 'counselor.assessments.results.detail']);
    $routes->get('assessments/(:num)/results/(:num)/grade', 'AssessmentController::resultDetail/$1/$2');
    $routes->get('assessments/(:num)/grading', 'AssessmentController::grading/$1', ['as' => 'counselor.assessments.grading']);
    $routes->post('assessments/grade/submit', 'AssessmentController::submitGrade', ['as' => 'counselor.assessments.grade.submit']);

    // Publishing Actions
    $routes->get('assessments/(:num)/publish', 'AssessmentController::publish/$1', ['as' => 'counselor.assessments.publish']);
    $routes->post('assessments/(:num)/publish', 'AssessmentController::publish/$1');
    $routes->get('assessments/(:num)/unpublish', 'AssessmentController::unpublish/$1', ['as' => 'counselor.assessments.unpublish']);
    $routes->post('assessments/(:num)/unpublish', 'AssessmentController::unpublish/$1');

    // Duplicate
    $routes->get('assessments/(:num)/duplicate', 'AssessmentController::duplicate/$1', ['as' => 'counselor.assessments.duplicate']);
    $routes->post('assessments/(:num)/duplicate', 'AssessmentController::duplicate/$1');
});

// =====================================================
// STUDENT ROUTES - ASSESSMENT MODULE (Untuk Fase 5.1)
// =====================================================
$routes->group('student', ['filter' => 'auth', 'namespace' => 'App\Controllers\Student'], function ($routes) {

    // Available Assessments
    $routes->get('assessments', 'AssessmentController::index', ['as' => 'student.assessments']);
    $routes->get('assessments/available', 'AssessmentController::available', ['as' => 'student.assessments.available']);

    // Take Assessment
    $routes->get('assessments/(:num)/start', 'AssessmentController::start/$1', ['as' => 'student.assessments.start']);
    $routes->get('assessments/(:num)/take', 'AssessmentController::take/$1', ['as' => 'student.assessments.take']);
    $routes->post('assessments/(:num)/answer', 'AssessmentController::submitAnswer/$1', ['as' => 'student.assessments.answer']);
    $routes->post('assessments/(:num)/submit', 'AssessmentController::submitAssessment/$1', ['as' => 'student.assessments.submit']);

    // Results
    $routes->get('assessments/results', 'AssessmentController::results', ['as' => 'student.assessments.results']);
    $routes->get('assessments/(:num)/result', 'AssessmentController::viewResult/$1', ['as' => 'student.assessments.result']);
    $routes->get('assessments/(:num)/review', 'AssessmentController::reviewAnswers/$1', ['as' => 'student.assessments.review']);
});

// =====================================================
// API ROUTES - ASSESSMENT MODULE (Optional - untuk AJAX)
// =====================================================
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {

    // Assessment API
    $routes->group('assessments', ['filter' => 'auth'], function ($routes) {
        // Get assessment data
        $routes->get('(:num)', 'AssessmentApiController::show/$1');
        $routes->get('(:num)/questions', 'AssessmentApiController::getQuestions/$1');
        $routes->get('(:num)/statistics', 'AssessmentApiController::getStatistics/$1');

        // Student progress
        $routes->get('(:num)/progress/(:num)', 'AssessmentApiController::getProgress/$1/$2'); // assessment_id/student_id

        // Submit answer (AJAX)
        $routes->post('answer', 'AssessmentApiController::saveAnswer');

        // Auto-save draft
        $routes->post('(:num)/autosave', 'AssessmentApiController::autosave/$1');
    });
});

/*
|--------------------------------------------------------------------------
| Wali Kelas Routes (Homeroom Teacher)
|--------------------------------------------------------------------------
| Filter: auth, role:Wali Kelas
*/
$routes->group('homeroom-teacher', ['filter' => 'auth', 'namespace' => 'App\Controllers\HomeroomTeacher'], function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('dashboard/getStats', 'DashboardController::getStats');

    // Violations
    $routes->get('violations', 'ViolationController::index');
    $routes->get('violations/create', 'ViolationController::create');
    $routes->post('violations/store', 'ViolationController::store');
    $routes->get('violations/detail/(:num)', 'ViolationController::detail/$1');

    // Class Reports
    $routes->get('reports', 'ClassReportController::index');
    $routes->get('reports/export-pdf', 'ClassReportController::exportPDF');
    $routes->get('reports/export-excel', 'ClassReportController::exportExcel');
    $routes->get('reports/data', 'ClassReportController::getReportData');
});
