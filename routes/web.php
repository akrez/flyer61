<?php

use App\Http\Controllers\EntityController;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes([
    'register' => false,
    'reset' => false,
    'confirm' => false,
    'verify' => false,
]);

Route::get(RouteServiceProvider::HOME, [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/entity/upload', [EntityController::class, 'upload'])->name('entity-upload');
Route::post('/entity/import', [EntityController::class, 'import'])->name('entity-import');
Route::get('/entity/export', [EntityController::class, 'export'])->name('entity-export');
Route::get('/entity/index', [EntityController::class, 'index'])->name('entity-index');
