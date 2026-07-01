<?php

/** @var App\Core\Router $router */

// Auth Routes
$router->post('/api/auth/login', [App\Controllers\AuthController::class, 'login']);
$router->post('/api/auth/logout', 'AuthController@logout');

// Staff Management
$router->get('/api/staff', 'StaffController@list');
$router->post('/api/staff', 'StaffController@create');
$router->post('/api/staff/update', 'StaffController@update');
$router->post('/api/staff/delete', 'StaffController@delete');

// Project Management
$router->get('/api/projects', 'ProjectController@list');
$router->post('/api/projects', 'ProjectController@create');
$router->post('/api/projects/update', 'ProjectController@update');
$router->post('/api/projects/delete', 'ProjectController@delete');

// Task Management
$router->get('/api/tasks', 'TaskController@list');
$router->post('/api/tasks', 'TaskController@create');
$router->post('/api/tasks/update', 'TaskController@update');
$router->post('/api/tasks/update-status', 'TaskController@updateStatus');
$router->post('/api/tasks/complete-staff', 'TaskController@completeStaff');
$router->get('/api/tasks/overdue', 'TaskController@getOverdue');
$router->post('/api/tasks/delete', 'TaskController@delete');

// Recurring Task Management
$router->post('/api/tasks/recurring/enable', 'TaskController@enableRecurring');
$router->post('/api/tasks/recurring/disable', 'TaskController@disableRecurring');
$router->get('/api/tasks/recurring/logs', 'TaskController@recurringLogs');
$router->post('/api/tasks/recurring/process', 'TaskController@processRecurring');

// Todo Module
$router->get('/api/todos', 'TodoController@list');
$router->get('/api/todos/overdue', 'TodoController@getOverdue');
$router->get('/admin/todos', 'TodoController@listAdmin');
$router->get('/staff/todos', 'TodoController@listStaff');
$router->post('/api/todos/create', 'TodoController@create');
$router->post('/staff/todos/create', 'TodoController@createStaff');
$router->post('/api/todos/update', 'TodoController@update');
$router->put('/todos/update', 'TodoController@update');
$router->post('/api/todos/delete', 'TodoController@delete');
$router->delete('/todos/delete', 'TodoController@delete');
$router->post('/api/todos/reset_pinned', 'TodoController@resetPinned');

// Dashboard & Analytics
$router->get('/api/dashboard/charts', 'DashboardController@getChartData');
$router->get('/api/dashboard/priority-tasks', 'DashboardController@getPriorityTasks');
$router->get('/api/dashboard/alerts', 'DashboardController@getAlerts');
$router->post('/api/dashboard/alerts/read', 'DashboardController@markAlertRead');
$router->get('/api/dashboard/notifications', 'DashboardController@getNotifications');

// Profile & Settings
$router->post('/api/profile/update', 'ProfileController@update');

// KPI Management
$router->get('/api/admin/kpi/daily-record', 'KPIController@getDailyRecord');
$router->get('/api/admin/kpi/monthly-report', 'KPIController@getMonthlyReport');
$router->get('/api/admin/kpi/staff-report-data', 'KPIController@getStaffReportData');
$router->post('/api/admin/kpi/save-daily', 'KPIController@saveDaily');
$router->post('/api/admin/kpi/log-report', 'KPIController@logReport');

// Leave Management
$router->get('/api/leaves/list', 'LeaveController@getList');
$router->post('/api/leaves/submit', 'LeaveController@submitRequest');
$router->post('/api/leaves/update-status', 'LeaveController@updateStatus');
$router->post('/api/leaves/cancel', 'LeaveController@cancelRequest');

// Publishing Report Management
$router->get('/api/publishing/fetch-report', 'PublishingController@fetchReport');
$router->post('/api/publishing/save-report', 'PublishingController@saveReport');
$router->post('/api/publishing/delete-table', 'PublishingController@deleteTable');
$router->post('/api/publishing/create-table', 'PublishingController@createTable');
$router->post('/api/publishing/cell-update', 'PublishingController@updateCellStatus');
$router->post('/api/publishing/update-assignment', 'PublishingController@updateAssignment');
$router->get('/api/publishing/sync-changes', 'PublishingController@syncChanges');

// Daily Report APIs
$router->get('/api/daily-report/today', 'DailyReportController@fetchTodayReport');
$router->get('/staff/api/daily-report/today', 'DailyReportController@fetchTodayReport');
$router->get('/api/daily-report/fetch', 'DailyReportController@fetchReport');
$router->post('/api/daily-report/save', 'DailyReportController@saveReport');
$router->post('/api/daily-report/update/{id}', 'DailyReportController@updateReport');
$router->get('/api/daily-report/admin-list', 'DailyReportController@fetchAdminReports');
$router->get('/admin/api/daily-report/user/{id}', 'DailyReportController@fetchReportsByUser');
$router->get('/admin/api/daily-report/user/{id}/{date}', 'DailyReportController@fetchReportByUserDate');
$router->post('/admin/api/daily-report/save', 'DailyReportController@saveReport');
$router->post('/staff/api/daily-report/save', 'DailyReportController@saveReport');

// SOP Management
$router->get('/api/sops', 'SopController@list');
$router->post('/api/sops/save', 'SopController@store');
$router->post('/api/sops/delete', 'SopController@delete');
