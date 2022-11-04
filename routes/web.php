<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RoleController;
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
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/url/check/routes', [HomeController::class, 'checkURLRoutes'])->name('check_url_exist_in_routes');
Route::get('/coming/soon', [HomeController::class, 'CommingSoonPage'])->name('coming_soon');

Route::middleware(['auth', 'verify_url:verifyUrl'])->group(function () {
    // User Roles
    Route::get('users/listing', [UsersController::class, 'UsersView'])->name('users_view');
    Route::get('users/ajax/list', [UsersController::class, 'UsersAjaxList'])->name('users_ajax_list');
    Route::post('delete/user', [UsersController::class, 'DeleteUser'])->name('delete_user');
    Route::get('users/edit/{id?}', [UsersController::class, 'EditUserMasterView'])->name('edit_user_master_view');
    Route::post('save/users/details', [UsersController::class, 'SaveUsersDetails'])->name('save_users_details');

    // Menu Routes
    Route::get('menu/listing', [MenuController::class, 'MenuView'])->name('menu_view');
    Route::get('menu/ajax/list', [MenuController::class, 'MenuAjaxList'])->name('menu_ajax_list');
    Route::post('delete/menu', [MenuController::class, 'DeleteMenu'])->name('delete_menu');
    Route::get('menu/edit/{id?}', [MenuController::class, 'EditMenuMasterView'])->name('edit_menu_master_view');
    Route::post('save/menu/details', [MenuController::class, 'SaveMenuDetails'])->name('save_menu_details');

    // Roles Routes
    Route::get('role/listing', [RoleController::class, 'RoleView'])->name('role_view');
    Route::get('role/ajax/list', [RoleController::class, 'RoleAjaxList'])->name('role_ajax_list');
    Route::post('delete/role', [RoleController::class, 'DeleteRole'])->name('delete_role');
    Route::get('role/edit/{id?}', [RoleController::class, 'EditRoleMasterView'])->name('edit_role_master_view');
    Route::post('save/role/details', [RoleController::class, 'SaveRoleDetails'])->name('save_role_details');

    // Dummy menus pages
    Route::get('super/menu/1', [MenuController::class, 'SuperMenu1'])->name('supermenu1');
    
   
});


