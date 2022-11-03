<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
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

// common routes, access by all
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('users/listing', [UsersController::class, 'UsersView'])->name('users_view');
Route::get('users/ajax/list', [UsersController::class, 'UsersAjaxList'])->name('users_ajax_list');
Route::post('delete/user', [UsersController::class, 'DeleteUser'])->name('delete_user');
Route::get('users/edit/{id?}', [UsersController::class, 'EditUserMasterView'])->name('edit_user_master_view');
Route::post('save/users/details', [UsersController::class, 'SaveUsersDetails'])->name('save_users_details');

Route::get('users-export', function () {
    return Excel::download(new UsersExport, 'users.xlsx');
})->name('users.export');
Route::get('/send-email', [MailController::class, 'sendEmail']);

Route::middleware(['auth', 'role_super:superadmin'])->group(function () {
    // User is authentication and has super admin role
    Route::get('/super', [HomeController::class, 'index'])->name('home_super');
   
});


Route::middleware(['auth', 'role_admin:admin'])->group(function () {
    Route::get('/admin', [HomeController::class, 'index'])->name('home_admin');

});


Route::middleware(['auth', 'role_sales:sales'])->group(function () {
    // access by sales team and super admin

    // Category Routes
    Route::get('/sales', [HomeController::class, 'index'])->name('home_sales');
    Route::get('cat/listing', [CategoryController::class, 'CatView'])->name('cat_view');
    Route::get('cat/ajax/list', [CategoryController::class, 'CatAjaxList'])->name('cat_ajax_list');
    Route::post('delete/cat', [CategoryController::class, 'DeleteCat'])->name('delete_cat');
    Route::get('cat/edit/{id?}', [CategoryController::class, 'EditCatMasterView'])->name('edit_cat_master_view');
    Route::post('save/cat/details', [CategoryController::class, 'SaveCatDetails'])->name('save_cat_details');

    // Products routes
    Route::get('pd/listing', [ProductController::class, 'PdView'])->name('pd_view');
    Route::get('pd/ajax/list', [ProductController::class, 'PdAjaxList'])->name('pd_ajax_list');
    Route::post('delete/pd', [ProductController::class, 'DeletePd'])->name('delete_pd');
    Route::get('pd/edit/{id?}', [ProductController::class, 'EditPdMasterView'])->name('edit_pd_master_view');
    Route::post('save/pd/details', [ProductController::class, 'SavePdDetails'])->name('save_pd_details');

});