<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Menu;
use Validator;
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

class RoleController extends Controller
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

    // Role controller methods
    public function RoleView()
    {
        // $survey_code = Crypt::decryptString($survey_code);
        // $survey_code = Crypt::encryptString(config('constants.SURVEY_CODES.1'));
        $breadcrumbs = array(
            array('name' => 'Home',
            'url' => route('home')),
            array('name' => 'Role',
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
            'page_title' 	 => 'Roles List',
            'active_sidebar' => '',
            'action_col_chk' => $action_col_chk,
            'breadcrumbs' => $breadcrumbs,
            "heading" => 'Roles',

        ];
        //dd($data);
        
        return view('role_details_listing', $data);
        
    }

    public function RoleAjaxList (Request $request){

        $columns = array(

            array( 
                "db"=> "checkbox" ,
                "dt"=> "checkbox" ,
            ),

            array( 
                "db"=> "roles.name" ,     // database table's column name
                "dt"=> "name" ,     // name we get from as
            ),

            array(
                "db"=> "roles.role_values",
                "dt"=> "role_values",
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
        $o_list = $this->role_model->get_roles(NULL, $filter_arr);
        $totalFiltered = ($this->role_model->get_roles(NULL, $filter_arr_clone));
        if(!empty($totalFiltered)){
            $totalFiltered = count($totalFiltered);
        }
        else{
            $totalFiltered = 0;
        }

        $totalRecords = $this->role_model->get_roles(NULL);
        if(!empty($totalRecords)){
            $totalRecords = count($totalRecords);
        } 
        else{
            $totalRecords = 0;
        }

        $data = array();

        if(!empty($o_list)){
            foreach ($o_list as $row) {

                $action_str = ' <a class="edit_role_details" href="'.route('edit_role_master_view', $row->id).'" title="Edit">'.'<i class="fa fa-pencil-square-o fa-sm action-icons"></i>'.'Edit</a>&nbsp ';

                $action_str .= ' <a class="delete_role text text-danger" data-uid="'.$row->id.'" href="javascript:void(0)" title="Delete">'.
                                    '<i class="fa fa-trash fa-sm action-icons"></i>'.
                                '</a>';
                 $menu_list_nm_arr =  $menu_list_url_arr = array();
                 $menu_list_nm_str = $menu_list_url_str = '';
                 $assign_ids_arr = explode(",",$row->role_values);
                 $menu_details = getUrlsWithMenuIds($assign_ids_arr);
                 foreach($menu_details as $detail){
                    $menu_list_nm_arr[] = $detail->menu_name;
                 }
                 $menu_list_nm_str = implode(", ",$menu_list_nm_arr);   
    
                // $view_assigned_menus = ' <a role-name="'.$row->name.'" data-toggle="modal" data-target="#exampleModalCenter" class="view_assign_menus text text-info" menu-name="'.$menu_list_nm_str.'" href="javascript:void(0)" title="View">'.' <i class="fa fa-eye fa-sm action-icons"></i> '.'  View</a>';

                $view_assigned_menus_ajax = ' <a role-name="'.$row->name.'" data-uid="'.$row->id.'" class="view_assign_menus_ajax text text-info" href="javascript:void(0)" title="View">'.' <i class="fa fa-eye fa-sm action-icons"></i> '.'  View</a>';

                $log_details = ' <a data-uid="'.$row->id.'" class="log_details text text-info" list-tab="role" href="javascript:void(0)" title="View">'.' <i class="fa fa-eye fa-sm action-icons"></i> '.'  View</a>';

                // 1=SuperAdmin, 2= Admin, 3=subadmin
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
                    'name'  => e(!empty($row->name)? $row->name:''),
                    'role_values'  => $view_assigned_menus_ajax,
                    'log_details'    =>	$log_details,
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
        //dd($return_status);

        return response()->json(//Ajax response in json format
            $return_status
        );  
    }

    public function DeleteRole(Request $request){
            
            $return_status = array(
                'status'  => FALSE,
                'message' => 'Failed to delete role',
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
            } else {
                $u_id = $request->u_id;
                $login_users_id = Auth::user()->id;
                if( empty($u_id) ){
                    $return_status['status'] = FALSE;
                    $return_status['message'] = 'Parameter missing';
                    $return_status['data'] = array();
                } else {

                        $delete_flag = TRUE;
                        $category_row = DB::table('categories')->where('id', '=', $u_id)->first();

                        // get users assigned to selected role
                        $user_assign_to_role = DB::table('users')->where('role', '=', $u_id)->get();

                        if(count($user_assign_to_role) > 0 && !empty(count($user_assign_to_role))){
                            foreach($user_assign_to_role as $user){
                                $all_assigned_ids[] = $user->id;
                            }
                            if(in_array($login_users_id,$all_assigned_ids)){
                                 // current loggedin-user assigned to selected role, don't delete
                                 $delete_flag = FALSE;
                            }
                            else{
                                $delete_flag = TRUE;
                                // deleting all users assigned to selected role
                                $users_deleted = User::where('role', $u_id)->delete();

                                // deleting selected role
                                $role_deleted = Role::where('id', $u_id)->delete();
                            }   
                        }
                        else{
                            $delete_flag = TRUE;
                            // no user assigned, deleting selected role
                            $role_deleted = Role::where('id', $u_id)->delete();
                        }

                        if($delete_flag){
                            $return_status['status'] = TRUE;
                            $return_status['message'] = 'Role successfully deleted';
                            $return_status['data'] = array();
                        } 
                        else{
                            $return_status['status'] = FALSE;
                            $return_status['message'] = 'Unable to delete. Your account assigned to this role.';
                            $return_status['data'] = array();
                        }
                        
                 }

            return response()->json(//Ajax response in json format
                $return_status
            );
        }
    }

    public function EditRoleMasterView($id = NULL){
        $data = array();		
        $heading = 'Add Role';
        $role_details = '';
        $pending_data = '';
        $permission_array = array();
        $breadcrumbs = array(
            array('name' => 'Home',
            'url' => route('home')),
            array('name' => 'Role',
            'url' => route('role_view')),
        );

        $get_menu_list = $this->role_model->get_menu_list();

        if(!empty($id)){
            $heading = 'Edit Role';
            $breadcrumbs[] = array('name' => 'Edit Role',
            'url' => '');  
            $role_details = $this->role_model->get_roles($id);
        }
        else{
            $breadcrumbs[] = array('name' => 'Add Role',
            'url' => '');    
        }

        $data = [
            'heading'    => $heading,
            'go_back_url'    => route('role_view'),
            'breadcrumbs' => $breadcrumbs,
            'row_id'        => $id,
            'role_details'  => $role_details,
            'get_menu_list'  => $get_menu_list,

        ];
        return view('role_add_edit', $data);
    }

    public function SaveRoleDetails(Request $request){
        $return_status = array(
            'status' => FALSE,
            'message' => 'Role details failed to save',
            'data' => ''
        );

        $name = $request->name;
        $menu_ids_arr = $request->menu_ids_arr;
        $menu_ids_str = implode(",", $menu_ids_arr);
        $row_id = $request->row_id;

        $login_user_fname = Auth::user()->first_name;
        $login_user_role_id = Auth::user()->role;
        $login_user_role = getUserRoleNameOnIds($login_user_role_id);
        $login_user_lname = Auth::user()->last_name;
        $login_user_fullname = $login_user_fname.' '.$login_user_lname; 

        $rules = array(
            'name' => 'required',
            'menu_ids_arr' => 'required',
        );
        
        $messages = [
            'name.required' 		=> 'Role Name Required',
            'menu_ids_arr.required' 		=> 'Select at least 1 item',
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
            if(!empty($name)){
                $data_arr += array('name' => $name);
            }

            if(!empty($menu_ids_str)){
                        $data_arr += array('role_values' => $menu_ids_str);
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
                    // $creating_product = Role::create($data_arr);
                    // $last_id = $creating_product->id;
                    $last_id = $this->role_model->save_menu_details($data_arr);
                }
                else{
                    $data_arr += array('modified_on' => date('Y-m-d H:i:s'));
                    $data_arr += array('modified_by' => $login_user_fullname.':'. $login_user_role);
                    $last_id = Role::where('id', $row_id)
                                       ->update($data_arr);
                    //$last_id = $this->role_model->save_pd_details($data_arr, $row_id);
                }

                if(!empty($last_id)){
                    $return_status['status'] = TRUE;
                    $return_status['message'] = 'Role details successfully saved';
                    $return_status['data'] = array();
                }
            }            
        }
        return response()->json($return_status);
    }

    public function ViewAssignMenuuAjax(Request $request)
    {
        $return_status = array(
            'status' => FALSE,
            'message' => "Something went wrong",
            'data' => ''
        );
        $res_data = "";

        $role_id = $request->u_id;
        $role_nm = $request->role_nm;
        $menu_list_nm_arr =  $menu_list_url_arr = array();
        $menu_list_nm_str = $menu_list_url_str = '';

        $data_row = DB::table('roles')->where('id', '=', $request->u_id)->first();
        $assign_ids_arr = explode(",",$data_row->role_values);
       // dd($data_row->role_values);

        if(empty($data_row->role_values) || $data_row->role_values == NULL || !isset($data_row->role_values)){
            $not_assign = "No menu assigned yet !!!";
            $res_data ="<div style='overflow-x:auto;'><h5><b>Menu Name(s) -   </b><span class='right badge badge-danger'>".$role_nm."</span></h5><br><h7>".$not_assign."</h7></div>";

            $return_status = array('status' => true,'mesage' => "Sucess",'data' => $res_data);
            return response()->json($return_status);
        }
        $menu_details = getUrlsWithMenuIds($assign_ids_arr);
        foreach($menu_details as $detail){
          $menu_list_arr[] = '<a href="'.$detail->menu_URL.'">'.$detail->menu_name.'</a>';
        }
         $menu_list_str = implode(", ",$menu_list_arr);
         //dd($menu_list_str);
         $res_data ='<div style="overflow-x:auto;"><h5><b>Menu Name(s) -   </b><span class="right badge badge-danger">'.$role_nm.'</span></h5><br><h7>'.$menu_list_str.'</h7></div>';   
        $return_status = array('status' => true,'mesage' => "Sucess",'data' => $res_data);
        return response()->json($return_status);
    
    }

    // How we catch exception example
    public function exception_example(Request $request)
	{
		$return_status = array(
			'status'  => FALSE,
			'message' => "",
			'data'    => $request->all(),
			'code'	  => ""
		);
		try {

        } catch (\Exception $e) {
			$return_status['message'] = $e->getMessage();
		} catch (\Throwable $e) {
			$return_status['message'] = $e->getMessage();
		}

		// dd($request->all());
		return response()->json( //Ajax response in json format
			$return_status
		);
	}
}