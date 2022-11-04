<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Menu;
use Validator;
use Illuminate\Support\Facades\DB;
use Auth;
Use Redirect;
use Session;
use File;
use Illuminate\Validation\Rule;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailSend;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends Controller
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

    // Menu controller methods 
    public function MenuView()
    {
        $breadcrumbs = array(
            array('name' => 'Home',
            'url' => route('home')),
            array('name' => 'Menu',
            'url' =>  ''),
            
        );
        $login_users_role = Auth::user()->role;
        if($login_users_role == 1 || $login_users_role == 2 || $login_users_role == 3){
            $action_col_chk = 'have_access';
        }
        else{
            $action_col_chk = '';
        }
        $data = [
            'page_title' 	 => 'Menu List',
            'active_sidebar' => '',
            'action_col_chk' => $action_col_chk,
            'breadcrumbs' => $breadcrumbs,
            "heading" => 'Menu',

        ];
        //dd($data);
        
        return view('menu_details_listing', $data);
        
    }

    public function MenuAjaxList (Request $request){

        $columns = array(

            array( 
                "db"=> "checkbox" ,
                "dt"=> "checkbox" ,
            ),

            array( 
                "db"=> "menus.menu_name" ,    
                "dt"=> "menu_name" ,    
            ),

            array(
                "db"=> "menus.menu_URL",
                "dt"=> "menu_URL",
            ),
            array( 
                "db"=> "action" ,
                "dt"=> "action" ,
            ),

        );
        
        $filter_arr = array(
            'offset' => $this->offset,
            'limit'  => $this->limit,
            'search_val' => '',
            'sort'   => array(),
            'recordsFiltered' => FALSE,
        );

        if(!empty($request->all())){
            $filter_arr['offset'] = $request->start;
            $filter_arr['limit'] = $request->length;
            $filter_arr['search_val'] = addslashes($request->search['value']);
            $column = $request->order[0]['column'];
            $dir    = $request->order[0]['dir'];
            if(!empty($columns[$column]['db'])){
                $filter_arr['sort'] = array(
                    'sort_column' => $columns[$column]['db'],
                    'sort_by' => $dir
                );
            }
        }
        $login_users_role = Auth::user()->role;

        $filter_arr_clone = $filter_arr;
        $filter_arr_clone['recordsFiltered'] = TRUE;

        $o_list = $this->menu_model->get_menu(NULL, $filter_arr);
        $totalFiltered = ($this->menu_model->get_menu(NULL, $filter_arr_clone));
        if(!empty($totalFiltered)){
            $totalFiltered = count($totalFiltered);
        }
        else{
            $totalFiltered = 0;
        }

        $totalRecords = $this->menu_model->get_menu(NULL);
        if(!empty($totalRecords)){
            $totalRecords = count($totalRecords);
        } 
        else{
            $totalRecords = 0;
        }

        $data = array();

        if(!empty($o_list)){
            foreach ($o_list as $row) {

                $action_str = ' <a class="edit_menu_details" href="'.route('edit_menu_master_view', $row->id).'" title="Edit">'.'<i class="fa fa-pencil-square-o fa-sm action-icons"></i>'.'Edit</a>&nbsp ';

                $action_str .= ' <a class="delete_menu text text-danger" data-uid="'.$row->id.'" href="javascript:void(0)" title="Delete">'.
                                    '<i class="fa fa-trash fa-sm action-icons"></i>'.
                                '</a>';

                // 1=SuperAdmin, 2= Admin
                if($login_users_role == 1 || $login_users_role == 2 || $login_users_role == 3){
                    $action_col_chk = $action_str;
                }
                else{
                    $action_col_chk = 'No Access';
                }
                
                // these pass to views
                $checkbox = '<input type="checkbox" class="checked_id" name="ids[]" value="'.$row->id.'">';
                $data[] = (object) array(
                    'checkbox' => $checkbox,
                    'menu_name'  => e(!empty($row->menu_name)? $row->menu_name:''),
                    'menu_URL'  => e(!empty($row->menu_URL)? $row->menu_URL:''),
                    'action'    =>	$action_col_chk
                );
            }
        }

        $return_status = array(
            "draw"            => intval( $request->draw ),   
            "recordsTotal"    => intval( $totalRecords ),  
            "recordsFiltered" => intval( $totalFiltered ),
            "data"            => $data,   // total data array 
            "filter_arr"      => $filter_arr,
        );

        return response()->json(//Ajax response in json format
            $return_status
        );  
    }

    public function DeleteMenu(Request $request){
            
        $return_status = array(
            'status'  => FALSE,
            'message' => 'Failed to delete Menu',
            'data'    => $request->all()
        );

        // Creating Rules for request
        $rules = array(
            'u_id' => 'required|min:1'
        );
        $messages = [
            
            'u_id.required' => 'Parameter missing',
            'u_id.min'      => 'Parameter missing'
        ];

        
        // Validate the request
        $validator = Validator::make($request->all() , $rules, $messages);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {

            $err_data = array();
            $errors = $validator->errors()->getMessages();
            foreach ($errors as $key => $value) {
                $err_data[] = implode('<br/>', $value);
            }
            $err_msg = implode('<br/>', $err_data);
            if(!empty($err_msg))
                $return_status['message'] = $err_msg;
            
            $return_status['data'] = $errors;
        } 
        else {
                $u_id = $request->u_id;
                if( empty($u_id) ){
                    $return_status['status'] = FALSE;
                    $return_status['message'] = 'Parameter missing';
                    $return_status['data'] = array();
                } else {
                        $delete_flag = FALSE;
                        $menu_row = DB::table('menus')->where('id', '=', $u_id)->first();
                        // deleting selected Menu
                        $menu_deleted = Menu::where('id', $u_id)->delete();

                        if( !empty($menu_deleted ) ){
                            $return_status['status'] = TRUE;
                            $return_status['message'] = 'Menu successfully deleted';
                            $return_status['data'] = array();
                        } 
                }

            return response()->json(//Ajax response in json format
                $return_status
            );
        }
    }

    public function EditMenuMasterView($id = NULL){
        $data = array();		
        $heading = 'Add Menu';
        $menu_details = '';
        $pending_data = '';
        $permission_array = array();
        $breadcrumbs = array(
            array('name' => 'Home',
            'url' => route('home')),
            array('name' => 'Menu',
            'url' => route('menu_view')),
        );

        if(!empty($id)){
            $heading = 'Edit Menu';
            $breadcrumbs[] = array('name' => 'Edit Menu',
            'url' => '');  
            $menu_details = $this->menu_model->get_menu($id);
        }
        else{
            $breadcrumbs[] = array('name' => 'Add Menu',
            'url' => '');    
        }

        $data = [
            'heading'    => $heading,
            'go_back_url'    => route('menu_view'),
            'breadcrumbs' => $breadcrumbs,
            'row_id'        => $id,
            'menu_details'  => $menu_details,

        ];
        return view('menu_add_edit', $data);
    }

    public function SaveMenuDetails(Request $request){
        $return_status = array(
            'status' => FALSE,
            'message' => 'Menu details failed to save',
            'data' => ''
        );

        $menu_name = $request->menu_name;
        $menu_URL = $request->menu_URL;
        $row_id = $request->row_id;

        $login_user_fname = Auth::user()->first_name;
        $login_user_role_id = Auth::user()->role;
        $login_user_role = getUserRoleNameOnIds($login_user_role_id);
        $login_user_lname = Auth::user()->last_name;
        $login_user_fullname = $login_user_fname.' '.$login_user_lname; 


        $rules = array(
            'menu_name'     => [
                'required',
                    Rule::unique('menus')
                    ->where(function ($query) {
                        $query->where('deleted_at', '=', NULL);
                    })
                    ->ignore($row_id),
            ], 

            'menu_URL'     => [
                'required',
                'url',
                    Rule::unique('menus')
                    ->where(function ($query) {
                        $query->where('deleted_at', '=', NULL);
                    })
                    ->ignore($row_id),
            ],

        );
        
        $messages = [
            'menu_name.required' 			=>  'Menu name Required',
            'menu_name.unique' 				=> 'Entered name already in system',
            'menu_URL.required' 			=>  'Menu URL Required',
            'menu_URL.unique' 				=> 'Entered URL already in system',
            'menu_URL.url' 				=> 'Invalid URL format',
        ];

        // Validate the request
        $validator = Validator::make($request->all() , $rules, $messages);
        if ($validator->fails()) {
            $err_data = array();
            $errors = $validator->errors()->getMessages();
            foreach ($errors as $key => $value) {
                $err_data[] = implode(' ', $value);
                // $return_status['message'] = $value;
            }
            $err_msg = implode(' ', $err_data);
            if(!empty($err_msg))
                $return_status['message'] = $err_msg;
            
            $return_status['data'] = $errors;
            
        }
        else{
            $data_arr = array();

            if(!empty($menu_name)){
                        $data_arr += array('menu_name' => $menu_name);
            }
            if(!empty($menu_URL)){
                $data_arr += array('menu_URL' => $menu_URL);
            }
            
            if( empty($data_arr) ){
                $return_status['status'] = FALSE;
                $return_status['message'] = 'data missing';
                $return_status['data'] = array();
            }
            else{
                $is_updated = '';
                $last_id;
                
                if(empty($row_id)){ //create new item
                    $data_arr += array('created_on' => date('Y-m-d H:i:s'));
                    $data_arr += array('created_by' => $login_user_fullname.':'. $login_user_role);
                    //dd($data_arr);
                    // $creating_menu = Menu::create($data_arr);
                    // $last_id = $creating_menu->id;
                    $last_id = $this->menu_model->save_menu_details($data_arr); 
                }
                else{
                    $data_arr += array('modified_on' => date('Y-m-d H:i:s'));
                    $data_arr += array('modified_by' => $login_user_fullname.':'. $login_user_role);
                    $last_id = Menu::where('id', $row_id)
                                       ->update($data_arr);
                    //$last_id = $this->menu_model->save_menu_details($data_arr, $row_id);
                }
                if(!empty($last_id)){
                    $return_status['status'] = TRUE;
                    $return_status['message'] = 'Menu details successfully saved';
                    $return_status['data'] = array();
                }
                
            }            
        }
        return response()->json($return_status);
    }
}    