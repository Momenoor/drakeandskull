<?php

use App\Http\Controllers\ExcelImportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/pdf-merger',App\Http\Controllers\combinePDFController::class);
Route::post('/import', [ExcelImportController::class, 'import'])->name('import');
Route::get('/import-form', [ExcelImportController::class, 'showForm'])->name('import.form');
Route::get('/matching',App\Http\Controllers\MatchingController::class)->name('matching');
Route::get('/case-request',App\Http\Controllers\CaseRequestController::class)->name('case.request');


