<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Menu;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailSend;
use Auth;
Use Redirect;
use Session;
use File;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->role_model = new \App\Models\Role;
		$this->users_model = new \App\Models\User;
		$this->menu_model = new \App\Models\Menu;
        $this->offset = config('constants.DEFAULT_OFFSET');
        $this->limit = config('constants.DEFAULT_LIMIT');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {   
        $breadcrumbs = array(
			array('name' => 'Home',
			'url' => route('home')),
			array('name' => 'Landing Page',
			'url' =>  ''),
			
		);
        $user_count = User::where('deleted_at','=',NULL)->count();
        $role_count = Role::where('deleted_at','=',NULL)->count();

        $u_count = $r_count = $m_count = $menu_count = 0;
        $assigned_menu_ids = getAssignedMenuIdsToRole();
        if(!empty($assigned_menu_ids)){
            $menu_ids_arr = explode(",", $assigned_menu_ids);
            $menu_count = count($menu_ids_arr);
        }
        if(!empty($user_count)){
            $u_count = $user_count;
        }
        if(!empty($menu_count)){
            $m_count = $menu_count;
        }
        if(!empty($role_count)){
            $r_count = $role_count;
        }

        $data = array(
           
            "role_count" => $r_count,  
            "menu_count" => $m_count,  
            "user_count"    => $u_count,  
            "breadcrumbs" => $breadcrumbs,
            "heading" => 'Landing Page',

          
        );
      // dd($data);
        return view('home', $data);
    }

    public function CommingSoonPage(Request $request)
    { 
        $breadcrumbs = array(
            array('name' => 'Home',
            'url' => route('home')),
            array('name' => 'Coming Soon',
            'url' =>  ''),
            
        );

        $data = [
			'page_title' 	 => 'Team working on it',
			'breadcrumbs' => $breadcrumbs,
            "heading" => 'We are working on it',

		];

        return view('CommingSoonPage', $data);

    }

    public function checkURLRoutes(Request $request)
    {  
            //dd($request->all());
            $return_status = array(
                'status'  => FALSE,
                'message' => 'Something went wrong',
                'data'    => $request->all(),
                'url' => '',
                'third_party' => ''
            );

            $coming_soon_url = route('coming_soon');
            $get_saved_url = $request->s_url;
            $get_name = $request->s_name;
            $current_url = URL::to('');
            $getRouteSlugs = getAllRouteSlugs();

            // if there is third part url like youtube, then redirect just there
            $get_saved_url_base = parse_url($get_saved_url);   // "scheme" => "http",  "host" => "localhost", "path" => "/homelane-task/public/clear-cache"
            $get_saved_url_host = $get_saved_url_base['host'];
            $get_current_url_base = parse_url($current_url); 
            $get_current_url_host = $get_current_url_base['host'];

           // check the url is not a third party url
            if($get_current_url_host == $get_saved_url_host){
                // host name are same
                 $get_saved_url_slug = str_replace($current_url.'/', "", $get_saved_url);
                 $get_only_slug = trim($get_saved_url_slug);
                 //dd($get_only_slug);

                 // check whether saved url define in routes or not
                if (!in_array($get_only_slug, $getRouteSlugs)) {
                    // not exist, so move to common custom page
                    $return_status['status'] = TRUE;
                    $return_status['message'] = 'Success';
                    $return_status['data'] = array();
                    $return_status['third_party'] = '';
                    $return_status['url'] = $coming_soon_url;
                }
                else{
                    $return_status['status'] = TRUE;
                    $return_status['message'] = 'Success';
                    $return_status['data'] = array();
                    $return_status['third_party'] = '';
                    $return_status['url'] = $get_saved_url;
                }
            }
            else{ // url is third party, open in new tab

                    $return_status['status'] = TRUE;
                    $return_status['message'] = 'Success';
                    $return_status['data'] = array();
                    $return_status['third_party'] = '1';
                    $return_status['url'] = $get_saved_url;

            }
            

            return response()->json(//Ajax response in json format
                $return_status
            );

    }

    
}
