<?php

namespace App\Http\Middleware;
use Auth;

use Closure;
use Illuminate\Http\Request;

class VerifyUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {   
         $get_all_stored_urls = array();
         $current_url = url()->full();
         $get_all_stored_urls = getAllMenuUrls();
         $unauthorized_access_flag = FALSE;

         if(empty($get_all_stored_urls)){
            $unauthorized_access_flag = FALSE;
         }
         //check whether current url exist in db, if not then proceed
         if(count($get_all_stored_urls) > 0 && !in_array($current_url, $get_all_stored_urls)){
            $unauthorized_access_flag = FALSE;
         } 
         else{ // url found in db, now check that url assigned to loggedin role
                $assigned_menus_ids = getAssignedMenuIdsToRole();
                $menu_ids_arr = explode(",", $assigned_menus_ids);
                $menu_details = getUrlsWithMenuIds($menu_ids_arr);    // Role specific
                $all_assigned_menu_urls = array();
                foreach($menu_details as $detail){
                    $all_assigned_menu_urls[] = $detail->menu_URL;
                }

                // Menu list is empty 
                if(count($all_assigned_menu_urls) <= 0 && !in_array($current_url, $all_assigned_menu_urls)){
                     $unauthorized_access_flag = TRUE; 
                 }
                // Menu list have at least one menu, And url is not found, it means current-url is not assigned to logged in user-role
                else if(count($all_assigned_menu_urls) > 0 && !in_array($current_url, $all_assigned_menu_urls)){
                      $unauthorized_access_flag = TRUE;      
                 } 
                 else{
                      // Menu assigned to role
                      $unauthorized_access_flag = FALSE;   
                 }   
           }

         if($unauthorized_access_flag){
            return \Redirect::to('/')->with(['type' => 'error','err_msg' => 'Unauthorized access']);
         }
         else{
             return $next($request);
         }
   }
}
