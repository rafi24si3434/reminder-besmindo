<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Default root redirects to Dashboard or Login
$routes->get('/', 'Dashboard::index', ['filter' => 'auth']);

// 1. Install & Database Setup (Accessible without login for initial setup)
$routes->get('install', 'Install::index');
$routes->post('install/run', 'Install::run');

// 2. Authentication
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/attemptLogin', 'Auth::attemptLogin');
$routes->get('auth/logout', 'Auth::logout');

// 3. API & Cron Trigger Endpoints
$routes->get('api/check-reminders', 'Api::checkReminders');

// 4. Protected Routes (Manager Only)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // Dashboard (Fitur A)
    $routes->get('dashboard', 'Dashboard::index');

    // Manajemen Crew (Fitur B)
    $routes->get('crew', 'Crew::index');
    $routes->get('crew/create', 'Crew::create');
    $routes->get('crew/edit/(:num)', 'Crew::edit/$1');
    $routes->post('crew/save', 'Crew::save');
    $routes->get('crew/toggleStatus/(:num)', 'Crew::toggleStatus/$1');
    $routes->get('crew/delete/(:num)', 'Crew::delete/$1');

    // Master Data Rig
    $routes->get('rig', 'Rig::index');
    $routes->get('rig/create', 'Rig::create');
    $routes->get('rig/edit/(:num)', 'Rig::edit/$1');
    $routes->post('rig/save', 'Rig::save');
    $routes->get('rig/delete/(:num)', 'Rig::delete/$1');

    // Manajemen Jadwal Meeting & Meeting Mingguan (Fitur C & D)
    $routes->get('meeting', 'Meeting::index');
    $routes->get('meeting/create', 'Meeting::create');
    $routes->get('meeting/edit/(:num)', 'Meeting::edit/$1');
    $routes->get('meeting/detail/(:num)', 'Meeting::detail/$1');
    $routes->post('meeting/save', 'Meeting::save');
    $routes->post('meeting/quickReschedule', 'Meeting::quickReschedule');
    $routes->get('meeting/updateStatus/(:num)/(:segment)', 'Meeting::updateStatus/$1/$2');
    $routes->get('meeting/delete/(:num)', 'Meeting::delete/$1');
    $routes->get('meeting/getCrewsByRigJson/(:num)', 'Meeting::getCrewsByRigJson/$1');

    // Undangan & Reminder WhatsApp (Fitur E & F)
    $routes->get('reminder', 'Reminder::index');
    $routes->get('reminder/broadcast/(:num)', 'Reminder::broadcast/$1');
    $routes->post('reminder/send', 'Reminder::send');
    $routes->get('reminder/log', 'Reminder::log');

    // Monitoring Kehadiran & Teams Simulator (Fitur G, H, I)
    $routes->get('attendance/live', 'Attendance::live');
    $routes->get('attendance/live/(:num)', 'Attendance::live/$1');
    $routes->get('attendance/simulator', 'Attendance::simulator');
    $routes->post('attendance/simulateJoin', 'Attendance::simulateJoin');
    $routes->post('attendance/updateStatusManual', 'Attendance::updateStatusManual');
    $routes->get('attendance/remindNotPresent/(:num)', 'Attendance::remindNotPresent/$1');
    $routes->post('attendance/importCsv', 'Attendance::importCsv');

    // Rekap & Laporan Analitik (Fitur J)
    $routes->get('report', 'Report::index');
    $routes->get('report/print', 'Report::print');
    $routes->get('report/exportExcel', 'Report::exportExcel');
});
