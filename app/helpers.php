<?php 
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Role;
use App\Models\Menu;


//To get role of logged in user
function getLoggedInUserRole(){
		
    if(Auth::guest()){
        return FALSE;
    } else {
        return Auth::user()->role;
    }
}

// to get all saved menu url list 
function getAllMenuUrls()
{
    $menu_list = Menu::all();
    $all_saved_menu_urls = array();
    foreach($menu_list as $detail){
        $all_saved_menu_urls[] = $detail->menu_URL;
    }
    return $all_saved_menu_urls;   // non deleted rows
}

//To get menu ids based on role of logged in user : Role specific
function getAssignedMenuIdsToRole(){
		
    $loggedin_users_role_id = Auth::user()->role;
    $role_details = Role::where('id','=',$loggedin_users_role_id)->where('deleted_at','=',NULL)->first();
    $assigned_menu_ids = $role_details->role_values;
    return $assigned_menu_ids;
}

//To get saved URL based on menu ids: Role specific
function getUrlsWithMenuIds($menu_ids_arr = NULL){
    if(empty($menu_ids_arr)){
        return FALSE;
    }
    //get only non-deleted menus
    $menu_details = Menu::whereIn('id', $menu_ids_arr)
                         ->where('deleted_at','=',NULL)
                         ->get();
    return $menu_details;
}

// to get existing routes in web.php
function getAllRouteSlugs()
{
    $slugs  = [];
    $routes = Route::getRoutes();

    foreach ($routes as $route)
    {
        $slugs[] = $route->uri();
    }

     return array_unique($slugs);
}

// to get role name based on role id's 
function getUserRoleNameOnIds($role_id = NULL)
{
    $role_details = Role::where('id','=',$role_id)->first();
    $r_name = $role_details->name;
    return $r_name;
}

// to get all roles list 
function getUserRoleList($role_id = NULL)
{
    $roles_list = Role::all();
    return $roles_list;
}

function isRoutePresent($urlToCheck)
{
    $available_url = [];
    $routes = Route::getRoutes();
    foreach ($routes as $route) {
        array_push($available_url, url($route->uri));
    }
    return in_array($urlToCheck, $available_url);
}



?>