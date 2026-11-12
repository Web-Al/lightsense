<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureRole;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Everyone who is logged in (viewer, staff, administrator) can look at these
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'overview'])->name('dashboard');
    Route::get('/dashboard/energy', [DashboardController::class, 'energy'])->name('dashboard.energy');
    Route::get('/dashboard/schedules', [ScheduleController::class, 'index'])->name('dashboard.schedules');
    Route::get('/dashboard/activity', [DashboardController::class, 'activity'])->name('dashboard.activity');
    Route::get('/dashboard/devices', [DeviceController::class, 'index'])->name('dashboard.devices');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Staff and administrators can change schedules
    Route::middleware(EnsureRole::class . ':staff')->group(function () {
        Route::post('/dashboard/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
        Route::patch('/dashboard/schedules/{schedule}/toggle', [ScheduleController::class, 'toggle'])->name('schedules.toggle');
        Route::delete('/dashboard/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
    });

    // Only administrators can manage users
    Route::middleware(EnsureRole::class . ':admin')->group(function () {
        Route::get('/dashboard/users', [UserController::class, 'index'])->name('dashboard.users');
        Route::patch('/dashboard/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/dashboard/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::post('/dashboard/devices', [DeviceController::class, 'store'])->name('devices.store');
        Route::patch('/dashboard/devices/{device}', [DeviceController::class, 'update'])->name('devices.update');
        Route::post('/dashboard/devices/{device}/key', [DeviceController::class, 'regenerateKey'])->name('devices.key');
        Route::delete('/dashboard/devices/{device}', [DeviceController::class, 'destroy'])->name('devices.destroy');
    });
});
