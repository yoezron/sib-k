<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Authentication Routes
$routes->get('/', 'AuthController::login');
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::attemptLogin');
$routes->get('/logout', 'AuthController::logout');

// Admin Routes
$routes->group('admin', ['filter' => 'role:admin'], function($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index');
});

// Counselor Routes
$routes->group('counselor', ['filter' => 'role:koordinator_bk,guru_bk'], function($routes) {
    $routes->get('dashboard', 'Counselor\DashboardController::index');
});

// Homeroom Teacher Routes
$routes->group('homeroom', ['filter' => 'role:wali_kelas'], function($routes) {
    $routes->get('dashboard', 'HomeroomTeacher\DashboardController::index');
});

// Student Routes
$routes->group('student', ['filter' => 'role:siswa'], function($routes) {
    $routes->get('dashboard', 'Student\DashboardController::index');
});

// Parent Routes
$routes->group('parent', ['filter' => 'role:orang_tua'], function($routes) {
    $routes->get('dashboard', 'Parent\DashboardController::index');
});
