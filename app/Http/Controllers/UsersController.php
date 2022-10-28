<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
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

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->product_model = new \App\Models\Product;
		$this->users_model = new \App\Models\User;
		$this->category_model = new \App\Models\Category;
        $this->offset = config('constants.DEFAULT_OFFSET');
        $this->limit = config('constants.DEFAULT_LIMIT');
    }

    public function UsersView()
    {
        $breadcrumbs = array(
			array('name' => 'Home',
			'url' => route('home')),
			array('name' => 'Users',
			'url' =>  ''),
			
		);
        $login_users_role = Auth::user()->user_role;
        if($login_users_role == 1 || $login_users_role == 2){
            $action_col_chk = 'have_access';
        }
        else{
            $action_col_chk = '';
        }
		$data = [
			'page_title' 	 => 'Users List',
			'active_sidebar' => '',
            'action_col_chk' => $action_col_chk,
			'breadcrumbs' => $breadcrumbs,
            "heading" => 'Users',

		];
        //dd($data);
		
        return view('users_details_listing', $data);
		
    }

    public function UsersAjaxList (Request $request){

        $columns = array(

            array( 
                "db"=> "checkbox" ,
                "dt"=> "checkbox" ,
            ),

            array( 
                "db"=> "users.first_name" ,     // database table's column name
                "dt"=> "first_name" ,     // name we get from as
            ),

            array(
				"db"=> "users.last_name",
				"dt"=> "last_name",
			),
            array( 
                "db"=> "users.email" ,
                "dt"=> "email" ,
            ),
           
            array( 
                "db"=> "users.user_role" ,
                "dt"=> "user_role" ,
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
        $login_users_role = Auth::user()->user_role;

        $filter_arr_clone = $filter_arr;
        $filter_arr_clone['recordsFiltered'] = TRUE;

        $o_list = $this->users_model->get_users(NULL, $filter_arr);
        $totalFiltered = ($this->users_model->get_users(NULL, $filter_arr_clone));
        if(!empty($totalFiltered)){
            $totalFiltered = count($totalFiltered);
        }
        else{
            $totalFiltered = 0;
        }

        $totalRecords = $this->users_model->get_users(NULL);
        if(!empty($totalRecords)){
            $totalRecords = count($totalRecords);
        } 
        else{
            $totalRecords = 0;
        }

        $data = array();

        if(!empty($o_list)){
        	foreach ($o_list as $row) {

                $action_str = ' <a class="edit_user_details" href="'.route('edit_user_master_view', $row->id).'" title="Edit">'.'<i class="fa fa-pencil-square-o fa-sm action-icons"></i>'.'Edit</a> ';

                $action_str .= ' <a class="delete_user text text-danger" u-role="'.$row->user_role.'" data-uid="'.$row->id.'" href="javascript:void(0)" title="Delete">'.
                                    '<i class="fa fa-trash fa-sm action-icons"></i>'.
                                '</a>';

                // Sales team only view the users 
                // 1=SuperAdmin, 2= UserAdmin, 3=SalesTeam
                if($login_users_role == 1 || $login_users_role == 2){
                    $action_col_chk = $action_str;
                }
                else{
                    $action_col_chk = 'No Access';
                }
                switch($row->user_role){
                    case '1':
                        $u_role = 'Super Admin';
                    break;
                    case '2':
                        $u_role = 'User Admin';
                    break;
                    case '3':
                        $u_role = 'Sales Team';
                    break;
                }
                

				// these pass to views
                $checkbox = '<input type="checkbox" class="checked_id" name="ids[]" value="'.$row->id.'">';
        		$data[] = (object) array(
                    'checkbox' => $checkbox,
                    'email'  => e(!empty($row->email)? $row->email:''),
                    'first_name'  => e(!empty($row->first_name)? $row->first_name:''),
                    'last_name'  => e(!empty($row->last_name)? $row->last_name:''),
                    'user_role'  => $u_role,
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

    public function DeleteUser(Request $request){
            
            $return_status = array(
                'status'  => FALSE,
                'message' => 'Failed to delete User',
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
                if( empty($u_id) ){
                    $return_status['status'] = FALSE;
                    $return_status['message'] = 'Parameter missing';
                    $return_status['data'] = array();
                } else {
                        $delete_flag = FALSE;
                        $user_row = DB::table('users')->where('id', '=', $u_id)->first();
                        $is_del = User::where('id', $u_id)->delete();
                        if( !empty($is_del ) ){
                            $return_status['status'] = TRUE;
                            $return_status['message'] = 'User successfully deleted';
                            $return_status['data'] = array();
                        } 
                }

            return response()->json(//Ajax response in json format
                $return_status
            );
        }
    }

    public function EditUserMasterView($id = NULL){
        $data = array();		
		$heading = 'Add User';
        $user_details = '';
        $pending_data = '';
        $permission_array = array();
        $breadcrumbs = array(
			array('name' => 'Home',
			'url' => route('home')),
			array('name' => 'Users',
			'url' => route('users_view')),
		);

        if(!empty($id)){
            $heading = 'Edit User';
            $breadcrumbs[] = array('name' => 'Edit User',
            'url' => '');  
            $user_details = $this->users_model->get_users($id);
            $user_role = $user_details->user_role; 
        }
        else{
            $breadcrumbs[] = array('name' => 'Add User',
            'url' => '');    
        }

        $data = [
        	'heading'    => $heading,
            'go_back_url'    => route('users_view'),
			'breadcrumbs' => $breadcrumbs,
            'row_id'        => $id,
            'user_details'  => $user_details,

        ];
        return view('users_add_edit', $data);
    }

    public function SaveUsersDetails(Request $request){
        $return_status = array(
            'status' => FALSE,
            'message' => 'Users details failed to save',
            'data' => ''
        );


        $user_role = $request->user_role;
        $email = $request->email;
        $password = $request->password;
        $first_name = $request->first_name;
        $last_name = $request->last_name;
        $row_id = $request->row_id;

        $rules = array(
            'user_role' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email'     => [
                            'required',
                            'email',
                                Rule::unique('users')
                                ->where(function ($query) {
                                    $query->where('deleted_at', '=', NULL);
                                })
                                ->ignore($row_id),
                        ],            
        );
        
        $messages = [
            'user_role.required' 		=> 'User Role Required',
            'first_name.required' 		=> 'First Name Role Required',
            'last_name.required' 		=> 'Last Name Required',
            'email.required' 			=>  'Email Required',
            'email.unique' 				=> 'Email already taken',
            'email.email' 				=> 'Invalid email format',
        ];

		if(!empty($request->password)||!empty($request->password_confirmation)){
            $rules += array(
                'password' => 'required|min:6',
                'password_confirmation' => 'required|same:password',
            );
                
            $messages += array(
                'password_confirmation.same' => trans('custom.must_match_password'),
            ); 
        }
        
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
            $data_arr = array(
                'user_role' => $user_role, 
                'email' => $email, 
                'password' => bcrypt($password),
                'first_name' => $first_name, 
                'last_name' => $last_name,                    
            );
            
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
                    //dd($request->all());
                     $creating_user = User::create($data_arr);
                     //dd($creating_user);
                     $last_id = $creating_user->id;
                    //$last_id = $this->users_model->save_users_details($data_arr);
                     //dd($last_id);
                    $user_details = $this->users_model->get_users($last_id);
                    $user_details->txt_password = $password;
                    $genrated_mail = $this->send_mail_user($user_details, "User Added", "Welcome, Your account has been created.Here are your login credentials", );
                   
                }
                else{
                    $data_arr += array('updated_at' => date('Y-m-d H:i:s'));
                    $last_id = User::where('id', $row_id)
                                    ->update($data_arr);                
                    //$last_id = $this->users_model->save_users_details($data_arr, $row_id);
                    $user_details = $this->users_model->get_users($row_id);
                    $user_details->txt_password = $password;
                    $is_updated = 1;
                    $genrated_mail = $this->send_mail_user($user_details, "User Updated", "Here are updated details.", $is_updated);
                }

                if(!empty($last_id)){
                    $return_status['status'] = TRUE;
                    $return_status['message'] = 'Users details successfully saved';
                    $return_status['data'] = array();
                }
            }            
        }
        return response()->json($return_status);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    // public function export() 
    // {
    //     return Excel::download(new UsersExport, 'users.xlsx');
    // }
        
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function import() 
    // {
    //     Excel::import(new UsersImport,request()->file('file'));
                
    //     return back();
    // }

    public function send_mail_user($user_details = NULL, $sub = NULL, $msg= NULL, $is_updated = NULL)
    {
        //dd($user_details);
        $u_details = $user_details;
        $to_email = 'r_test007@yopmail.com';       
        $m_sub = $sub;
        $m_msg = '';

        switch($u_details->user_role){
            case '1':
                $u_role = 'Super Admin';
            break;
            case '2':
                $u_role = 'User Admin';
            break;
            case '3':
                $u_role = 'Sales Team';
            break;
        }

        if(!empty($is_updated)){
            $m_msg = '<div>
            <h3>Here are updated User details</h3>
            <ul>
            <li>ID : '.$u_details->id.' </li>
            <li>First Name : '.$u_details->first_name.' </li>
            <li>Last Name : '.$u_details->last_name.' </li>
            <li>User Role : '.$u_role.' </li>
            <li>Email : '.$u_details->email.' </li>
            <li>Password : '.$u_details->txt_password.' </li>
            <br><br>
            </ul> 
            </<div>';
        }
        else{

            $m_msg = '<div>
            <h3>Welcome, Your account has been created.<br>
            Here are your login credentials</h3>
            <ul>
            <li>Email : '.$u_details->email.' </li>
            <li>Password : '.$u_details->txt_password.' </li>
            <br><br>
            </ul> 
            </<div>';

        }
       

         $mail_data = [
             'm_sub' => $m_sub,
             'm_msg' => $m_msg,
         ];
        $sendInvoiceMail = Mail::to($to_email);
        $sendInvoiceMail->send(new EmailSend($mail_data));
  
   
        return response()->json([
            'message' => 'Email has been sent.'
        ], Response::HTTP_OK);
    }
}