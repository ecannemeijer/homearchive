<?php
/**
 * Entry point van applicatie
 */

require_once __DIR__ . '/../config/helpers.php';

use App\Router;

$router = new Router();

// Auth routes
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@handle_login');
$router->get('/register', 'AuthController@register');
$router->post('/register', 'AuthController@handle_register');
$router->get('/logout', 'AuthController@logout');

// Dashboard
$router->get('/dashboard', 'DashboardController@index');
$router->get('/', 'DashboardController@index');

// Subscriptions
$router->get('/subscriptions', 'SubscriptionController@index');
$router->get('/subscriptions/create', 'SubscriptionController@create');
$router->post('/subscriptions', 'SubscriptionController@store');
$router->get('/subscriptions/{id}', 'SubscriptionController@show');
$router->get('/subscriptions/{id}/edit', 'SubscriptionController@edit');
$router->post('/subscriptions/{id}', 'SubscriptionController@update');
$router->post('/subscriptions/{id}/delete', 'SubscriptionController@delete');

// Costs
$router->get('/costs', 'CostsController@overview');
$router->get('/costs/api/trend', 'CostsController@monthly_trend_api');
$router->get('/costs/export/csv', 'CostsController@export_csv');

// Password Vault
$router->get('/password-vault', 'PasswordVaultController@index');
$router->get('/password-vault/create', 'PasswordVaultController@create');
$router->post('/password-vault', 'PasswordVaultController@store');
$router->get('/password-vault/{id}', 'PasswordVaultController@show');
$router->get('/password-vault/{id}/edit', 'PasswordVaultController@edit');
$router->post('/password-vault/{id}', 'PasswordVaultController@update');
$router->post('/password-vault/{id}/delete', 'PasswordVaultController@delete');
$router->get('/api/password/generate', 'PasswordVaultController@generate_password_api');

// Documents
$router->post('/api/documents/upload', 'DocumentController@upload');
$router->get('/documents/{id}/download', 'DocumentController@download');
$router->post('/api/documents/{id}/delete', 'DocumentController@delete');
$router->get('/documents/{id}/preview', 'DocumentController@preview');

// Categories
$router->get('/categories', 'CategoryController@index');
$router->post('/api/categories', 'CategoryController@store');
$router->post('/api/categories/{id}', 'CategoryController@update');
$router->post('/api/categories/{id}/delete', 'CategoryController@delete');

// API Endpoints
$router->get('/api/stats', 'ApiController@stats');
$router->get('/api/notifications', 'ApiController@notifications');
$router->post('/api/notifications/{id}/read', 'ApiController@read_notification');
$router->get('/api/check-expiring', 'ApiController@check_expiring');
$router->get('/api/search', 'ApiController@search');
$router->get('/api/cost-trend', 'ApiController@cost_trend');
$router->get('/api/health', 'ApiController@health');

// User Management (admin only)
$router->get('/users', 'UserManagementController@index');
$router->get('/users/create', 'UserManagementController@create');
$router->post('/users', 'UserManagementController@store');
$router->get('/users/{id}/edit', 'UserManagementController@edit');
$router->post('/users/{id}', 'UserManagementController@update');
$router->post('/users/{id}/delete', 'UserManagementController@delete');

// Password & Account
$router->get('/change-password', 'UserManagementController@change_password');
$router->post('/change-password', 'UserManagementController@update_password');

// Dispatch request
$router->dispatch();
