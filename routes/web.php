<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

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

Auth::routes();

// common routes
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('users/listing', [App\Http\Controllers\HomeController::class, 'users_view'])->name('users_view');
Route::get('users/ajax/list', [App\Http\Controllers\HomeController::class, 'users_ajax_list'])->name('users_ajax_list');
Route::get('users/edit/{id?}', [App\Http\Controllers\HomeController::class, 'edit_user_master_view'])->name('edit_user_master_view');
Route::post('/admin/delete/user', [App\Http\Controllers\HomeController::class, 'delete_user'])->name('delete_user');
Route::post('save/users/details', [App\Http\Controllers\HomeController::class, 'save_users_details'])->name('save_users_details');

Route::get('cat/listing', [App\Http\Controllers\HomeController::class, 'cat_view'])->name('cat_view');
Route::get('cat/ajax/list', [App\Http\Controllers\HomeController::class, 'cat_ajax_list'])->name('cat_ajax_list');

Route::get('users-export', function () {
    return Excel::download(new UsersExport, 'users.xlsx');
})->name('users.export');
Route::get('/send-email', [MailController::class, 'sendEmail']);

Route::middleware(['auth', 'role_super:superadmin'])->group(function () {
    // User is authentication and has super admin role
    Route::get('/super', [App\Http\Controllers\HomeController::class, 'index'])->name('home_super');
   
});


Route::middleware(['auth', 'role_admin:admin'])->group(function () {
    Route::get('/admin', [App\Http\Controllers\HomeController::class, 'index'])->name('home_admin');

});


Route::middleware(['auth', 'role_sales:sales'])->group(function () {
    Route::get('/sales', [App\Http\Controllers\HomeController::class, 'index'])->name('home_sales');

});