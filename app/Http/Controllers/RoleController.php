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

                $action_str = ' <a class="edit_role_details" href="'.route('edit_role_master_view', $row->id).'" title="Edit">'.'<i class="fa fa-pencil-square-o fa-sm action-icons"></i>'.'Edit</a> ';

                $action_str .= ' <a class="delete_role text text-danger" data-uid="'.$row->id.'" href="javascript:void(0)" title="Delete">'.
                                    '<i class="fa fa-trash fa-sm action-icons"></i>'.
                                '</a>';

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
                    'role_values'  => e(!empty($row->role_values)? $row->role_values:''),
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


        $category_id = $request->category_id;
        $name = $request->name;
        $role_values = $request->role_values;
        $product_price = $request->product_price;
        $row_id = $request->row_id;

        $rules = array(
            'category_id' => 'required',
            'role_values' => 'required',
            'product_price' => 'required',
            'name' => 'required',         
            'product_img' => 'nullable|mimes:jpeg,jpg,png,gif|max:5250',
        );
        
        $messages = [
            'category_id.required' 		=> 'User Role Required',
            'role_values.required' 		=> 'First Name Role Required',
            'product_price.required' 		=> 'Last Name Required',
            'name.required' 			=>  'name Required',
            'product_img.max' 	   		=> "Profile image size cant be greater than 5MB",
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
            $data_arr += array('category_id' => $category_id);
            if(!empty($request->product_img)){
                $result_file = $this->saveFileToFolder($request->file('product_img'));  
                if($result_file['status'] === TRUE){
                    
                    $data_arr += array('product_img' => $result_file['data']->getFileName());
                }
                else{
    
                    $return_status['message'] = 'Pic failed to save';
                    $return_status['data'] = $errors;
                }
            }

            if(!empty($name)){
                $data_arr += array('name' => $name);
            }

            if(!empty($role_values)){
                        $data_arr += array('role_values' => $role_values);
            }
            if(!empty($product_price)){
                $data_arr += array('product_price' => $product_price);
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
                    $data_arr += array('created_at' => date('Y-m-d H:i:s'));
                    $data_arr += array('updated_at' => date('Y-m-d H:i:s'));
                    $creating_product = Role::create($data_arr);
                    $last_id = $creating_product->id;
                    //$last_id = $this->role_model->save_pd_details($data_arr);
                }
                else{
                    $data_arr += array('updated_at' => date('Y-m-d H:i:s'));
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

    //To store image 
    function saveFileToFolder($file = NULL, $destination_path = ''){

        if(empty($file)){

            return array(
                'status'	=> FALSE,
                'message'	=> trans('missing_arg'),
                'data'		=> array(),
            );
        }
            

        // $file = $request->file('image_name');
        // $file = $request->file($filename);
        
        //Display File Name
        $filename = $file->getClientOriginalName();
        // echo 'File Name: '.$filename;
        // echo '<br>';
        
        //Display File Extension
        $file_extension = $file->getClientOriginalExtension();
        // echo 'File Extension: '.$file_extension;
        // echo '<br>';
        
        //Display File Real Path
        $real_path = $file->getRealPath();
        // echo 'File Real Path: '.$real_path;
        // echo '<br>';
        
        //Display File Size
        $file_size = $file->getSize();
        // echo 'File Size: '.$file_size;
        // echo '<br>';
        
        //Display File Mime Type
        $file_mime_type = $file->getMimeType();
        // echo 'File Mime Type: '.$file_mime_type;
        // echo '<br>';
        
        //Display Destination Path
        if(empty($destination_path)){
            $destination_path = public_path('uploads/');
        } else {
            $destination_path = public_path('uploads/').$destination_path;
        }
        // echo 'File Destination Path: '.$destination_path;
        if(!File::isDirectory($destination_path)) {
            File::makeDirectory($destination_path, 0777, true, true);
        }

        

        $image_name = time().'_'.$filename;
        $image_name = preg_replace('/[^a-zA-Z0-9_.]/', '_', $image_name);

        $uploaded_data = $file->move( $destination_path , $image_name );
            
        
        if( !empty( $uploaded_data )){
            return array(
                'status'	=> TRUE,
                'message'	=> 'Uploaded successfully.',
                'data'		=> $uploaded_data,
            );
        }
        else{
            return array(
                'status'	=> FALSE,
                'message'	=> 'Not uploaded successfully. Please try again!!',
                'data'		=> $uploaded_data,
            );
        }
    }
}