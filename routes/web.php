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


/*
|--------------------------------------------------------------------------
| KIOSK / HALAMAN TAMU
|--------------------------------------------------------------------------
*/

Route::get(
    '/',
    [KioskController::class, 'home']
)->name('kiosk.home');


Route::get(
    '/pilih-lokasi',
    [KioskController::class, 'locationPicker']
)->name('kiosk.location.picker');


Route::get(
    '/lokasi/{location:slug}',
    [KioskController::class, 'selectLocation']
)->name('kiosk.location.select');


Route::get(
    '/kunjungan/baru',
    [KioskController::class, 'create']
)->name('kiosk.visit.create');


Route::post(
    '/kunjungan',
    [KioskController::class, 'store']
)->name('kiosk.visit.store');


Route::get(
    '/status-kunjungan',
    [KioskController::class, 'status']
)->name('kiosk.status');


/*
|--------------------------------------------------------------------------
| SURVEY
|--------------------------------------------------------------------------
*/

Route::get(
    '/survey-kepuasan',
    [KioskController::class, 'surveyIndex']
)->name('kiosk.survey.index');


Route::get(
    '/survey-kepuasan/{visit:visit_number}',
    [KioskController::class, 'survey']
)->name('kiosk.survey.show');


Route::post(
    '/survey-kepuasan/{visit:visit_number}',
    [KioskController::class, 'submitSurvey']
)->name('kiosk.survey.store');


Route::get(
    '/kunjungan/berhasil/{visit:visit_number}',
    [KioskController::class, 'success']
)->name('kiosk.visit.success');


/*
|--------------------------------------------------------------------------
| LOGIN PETUGAS
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get(
        '/petugas/login',
        [AuthController::class, 'showLogin']
    )->name('login');


    Route::post(
        '/petugas/login',
        [AuthController::class, 'login']
    )->name('login.process');

});


Route::post(
    '/petugas/logout',
    [AuthController::class, 'logout']
)
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| ADMIN / PETUGAS
|--------------------------------------------------------------------------
|
| Semua route di dalam group ini otomatis mendapatkan:
|
| URL:
| /admin/...
|
| Nama:
| admin....
|
| Jadi kalau di dalam group kita menulis:
|
| ->name('visits.identity')
|
| Laravel otomatis menjadikannya:
|
| admin.visits.identity
|
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth')
    ->group(function () {


        /*
        |--------------------------------------------------------------------------
        | DASHBOARD
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/',
            [DashboardController::class, 'index']
        )->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | MASTER DATA
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'locations',
            LocationController::class
        )->except('show');


        Route::resource(
            'users',
            UserController::class
        )->except(['show']);


        Route::resource(
            'divisions',
            DivisionController::class
        )->except('show');


        Route::resource(
            'employees',
            EmployeeController::class
        )->except('show');


        /*
        |--------------------------------------------------------------------------
        | DATA KUNJUNGAN
        |--------------------------------------------------------------------------
        */

        Route::get(
            'visits',
            [VisitController::class, 'index']
        )->name('visits.index');


        /*
         * Export Excel.
         *
         * Diletakkan sebelum route detail kunjungan.
         */

        Route::get(
            'visits/export/excel',
            [VisitController::class, 'export']
        )->name('visits.export');


        /*
        |--------------------------------------------------------------------------
        | FOTO IDENTITAS KTP / SIM
        |--------------------------------------------------------------------------
        |
        | Route ini berada di middleware auth,
        | sehingga hanya user yang sudah login
        | yang dapat mengakses foto.
        |
        | NAMA AKHIR ROUTE:
        |
        | admin.visits.identity
        |
        */

        Route::get(
            'visits/{visit:visit_number}/identity',
            [VisitController::class, 'identity']
        )->name('visits.identity');


        /*
        |--------------------------------------------------------------------------
        | DETAIL KUNJUNGAN
        |--------------------------------------------------------------------------
        */

        Route::get(
            'visits/{visit:visit_number}',
            [VisitController::class, 'show']
        )->name('visits.show');

    });