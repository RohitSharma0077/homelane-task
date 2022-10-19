<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Validator;
use Illuminate\Support\Facades\DB;
use Auth;
use Illuminate\Validation\Rule;

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
        $this->product_model = new \App\Models\Product;
		$this->users_model = new \App\Models\User;
		$this->category_model = new \App\Models\Category;
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
			array('name' => 'Dashboard',
			'url' =>  ''),
			
		);
        $user_count = User::where('deleted_at','=',NULL)->count();
        $product_count = Product::where('deleted_at','=',NULL)->count();
        $category_count = Category::where('deleted_at','=',NULL)->count();
        //dd($product_count);

        $u_count = $c_count = $p_count = 0;
        if(!empty($user_count)){
            $u_count = $user_count;
        }
        if(!empty($product_count)){
            $p_count = $product_count;
        }
        if(!empty($category_count)){
            $c_count = $category_count;
        }

        $data = array(
           
            "category_count" => $c_count,  
            "product_count" => $p_count,  
            "user_count"    => $u_count,  
            "breadcrumbs" => $breadcrumbs
          
        );
       // dd($data);
        return view('home', $data);
    }


    public function users_view()
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
		];
        //dd($data);
		
        return view('users_details_listing', $data);
		
    }

    public function users_ajax_list (Request $request){

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
				"dt"=> "email",
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

    public function delete_user(Request $request){
            
            $return_status = array(
                'status'  => FALSE,
                'message' => trans('custom.fail_delete', ['s' => 'User']),
                'data'    => $request->all()
            );

            // Creating Rules for request
            $rules = array(
                'u_id' => 'required|min:1'
            );
            $messages = [
                
                'u_id.required' => trans('custom.missing_arg'),
                'u_id.min'      => trans('custom.missing_arg')
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
                    $return_status['message'] = trans('custom.missing_arg');
                    $return_status['data'] = array();
                } else {
                        $delete_flag = FALSE;
                        $user_row = DB::table('users')->where('id', '=', $u_id)->first();
                        $is_del = User::where('id', $u_id)->delete();
                        if( !empty($is_del ) ){
                            $return_status['status'] = TRUE;
                            $return_status['message'] = 'User Successfully Deleted';
                            $return_status['data'] = array();
                        } 
                }

            return response()->json(//Ajax response in json format
                $return_status
            );
        }
    }
}
