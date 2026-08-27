<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DivisionController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VisitController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KioskController;
use Illuminate\Support\Facades\Route;

Route::get('/', [KioskController::class, 'home'])->name('kiosk.home');
Route::get('/pilih-lokasi', [KioskController::class, 'locationPicker'])->name('kiosk.location.picker');
Route::get('/lokasi/{location:slug}', [KioskController::class, 'selectLocation'])->name('kiosk.location.select');
Route::get('/kunjungan/baru', [KioskController::class, 'create'])->name('kiosk.visit.create');
Route::post('/kunjungan', [KioskController::class, 'store'])->name('kiosk.visit.store');
Route::get('/status-kunjungan', [KioskController::class, 'status'])->name('kiosk.status');
Route::get('/survey-kepuasan', [KioskController::class, 'surveyIndex'])->name('kiosk.survey.index');
Route::get('/survey-kepuasan/{visit:visit_number}', [KioskController::class, 'survey'])->name('kiosk.survey.show');
Route::post('/survey-kepuasan/{visit:visit_number}', [KioskController::class, 'submitSurvey'])->name('kiosk.survey.store');
Route::get('/kunjungan/berhasil/{visit:visit_number}', [KioskController::class, 'success'])->name('kiosk.visit.success');

Route::middleware('guest')->group(function () {
    Route::get('/petugas/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/petugas/login', [AuthController::class, 'login'])->name('login.process');
});
Route::post('/petugas/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('locations', LocationController::class)->except('show');
    Route::resource('users', UserController::class)->except(['show']);
    Route::resource('divisions', DivisionController::class)->except('show');
    Route::resource('employees', EmployeeController::class)->except('show');
    Route::get('visits', [VisitController::class, 'index'])->name('visits.index');
    Route::get('visits/export/excel', [VisitController::class, 'export'])->name('visits.export');
    Route::get('visits/{visit}', [VisitController::class, 'show'])->name('visits.show');
});
