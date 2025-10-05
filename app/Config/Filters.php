<?php

/**
 * File Path: app/Config/Filters.php
 * 
 * Filters Configuration
 * Mendaftarkan semua filter yang akan digunakan di aplikasi
 * 
 * @package    SIB-K
 * @subpackage Config
 * @category   Configuration
 * @author     Development Team
 * @created    2025-01-01
 */

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseConfig
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     *
     * @var array<string, class-string|list<class-string>> [filter_name => classname]
     *                                                      or [filter_name => [classname1, classname2, ...]]
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,

        // Custom Filters
        'auth'          => \App\Filters\AuthFilter::class,
        'role'          => \App\Filters\RoleFilter::class,
        'permission'    => \App\Filters\PermissionFilter::class,
        'cors'          => \App\Filters\CorsFilter::class,
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     *
     * @var array<string, array<string, array<string, string>>>|array<string, list<string>>
     */
    public array $globals = [
        'before' => [
            // Enable CSRF protection globally
            'csrf' => ['except' => [
                'api/*',  // Exclude API routes if needed
            ]],

            // Honeypot protection
            // 'honeypot',

            // CORS filter
            // 'cors',

            // Invalid characters filter
            'invalidchars',
        ],
        'after' => [
            'toolbar',
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * 'post' => ['foo', 'bar']
     *
     * If you use this, you should disable auto-routing because auto-routing
     * permits any HTTP method to access a controller. Accessing the controller
     * with a method you don't expect could bypass the filter.
     */
    public array $methods = [];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     */
    public array $filters = [
        // Auth filter for protected routes
        'auth' => [
            'before' => [
                'admin/*',
                'koordinator/*',
                'counselor/*',
                'homeroom/*',
                'student/*',
                'parent/*',
                'dashboard',
                'profile/*',
            ],
        ],

        // Role-based filters
        'role' => [
            'before' => [
                'admin/*' => ['arguments' => ['Admin']],
                'koordinator/*' => ['arguments' => ['Koordinator BK']],
                'counselor/*' => ['arguments' => ['Guru BK', 'Koordinator BK']],
                'homeroom/*' => ['arguments' => ['Wali Kelas']],
                'student/*' => ['arguments' => ['Siswa']],
                'parent/*' => ['arguments' => ['Orang Tua']],
            ],
        ],
    ];
}
