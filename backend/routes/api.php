<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\PriorityController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\AttachmentController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\SlaPolicyController;

Route::middleware('throttle:10,1')->post('/auth/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'active'])->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
    Route::put('/tickets/{ticket}', [TicketController::class, 'update']);
    Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus']);
    Route::get('/tickets/{ticket}/messages', [MessageController::class, 'index']);
    Route::post('/tickets/{ticket}/messages', [MessageController::class, 'store']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'read']);
    Route::get('/tickets/{ticket}/attachments', [AttachmentController::class, 'index']);
    Route::post('/tickets/{ticket}/attachments', [AttachmentController::class, 'store']);
    Route::get('/attachments/{attachment}/download', [AttachmentController::class, 'download']);
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
    Route::get('/reports/tickets', [DashboardController::class, 'report']);

    Route::middleware('role:Admin,Koordinator')->group(function () {
        Route::post('/tickets/{ticket}/assignments', [AssignmentController::class, 'store']);
        Route::get('/users/assignees', [UserController::class, 'assignees']);
    });
    Route::post('/assignments/{assignment}/accept', [AssignmentController::class, 'accept']);

    Route::middleware('role:Admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive']);
        Route::get('/departments', [DepartmentController::class, 'index']);
        Route::post('/departments', [DepartmentController::class, 'store']);
        Route::get('/departments/{department}', [DepartmentController::class, 'show']);
        Route::get('/departments/{department}/members', [DepartmentController::class, 'members']);
        Route::put('/departments/{department}', [DepartmentController::class, 'update']);
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::get('/categories/{category}', [CategoryController::class, 'show']);
        Route::patch('/categories/{category}/toggle-active', [CategoryController::class, 'toggleActive']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::get('/priorities', [PriorityController::class, 'index']);
        Route::post('/priorities', [PriorityController::class, 'store']);
        Route::get('/priorities/{priority}', [PriorityController::class, 'show']);
        Route::patch('/priorities/{priority}/toggle-active', [PriorityController::class, 'toggleActive']);
        Route::put('/priorities/{priority}', [PriorityController::class, 'update']);
        Route::get('/sla-policies', [SlaPolicyController::class, 'index']);
        Route::post('/sla-policies', [SlaPolicyController::class, 'store']);
        Route::get('/sla-policies/{slaPolicy}', [SlaPolicyController::class, 'show']);
        Route::put('/sla-policies/{slaPolicy}', [SlaPolicyController::class, 'update']);
    });
});
