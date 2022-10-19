<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

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


Route::get('/clear-cache', function() {

    $exitCode = \Artisan::call('cache:clear');

    $exitCode1 = \Artisan::call('config:cache');

    $exitCode2 = \Artisan::call('view:clear');

	echo $exitCode;

	echo '<br>';

	echo $exitCode1;

	echo '<br>';

	echo $exitCode2;

	die;

    // return what you want

});

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//Route::any('dashboard/landing/page', [HomeController::class,'dashboard_landing_page'])->name('dashboard_landing_page');