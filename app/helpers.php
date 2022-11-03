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

//To get menu ids based on role of logged in user
function getAssignedMenuIdsToRole(){
		
    $loggedin_users_role_id = Auth::user()->role;
    $role_details = Role::where('id','=',$loggedin_users_role_id)->where('deleted_at','=',NULL)->first();
    $assigned_menu_ids = $role_details->role_values;
    return $assigned_menu_ids;
}

//To get saved URL based on menu ids
function getUrlsWithMenuIds($menu_ids_arr = NULL){
    
    if(empty($menu_ids_arr)){
        return FALSE;
    }
    $menu_details = Menu::whereIn('id', $menu_ids_arr)
                         ->where('deleted_at','=',NULL)
                         ->get();
    return $menu_details;
}

// to get existing routes 
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

// to get roles list 
function getUserRoleList($role_id = NULL)
{
    $roles_list = Role::all();
    return $roles_list;
}


?>