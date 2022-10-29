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

class CategoryController extends Controller
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

    // Category controller methods 
    public function CatView()
    {
        $breadcrumbs = array(
            array('name' => 'Home',
            'url' => route('home')),
            array('name' => 'Category',
            'url' =>  ''),
            
        );
        $login_users_role = Auth::user()->user_role;
        if($login_users_role == 1 || $login_users_role == 3){
            $action_col_chk = 'have_access';
        }
        else{
            $action_col_chk = '';
        }
        $data = [
            'page_title' 	 => 'Category List',
            'active_sidebar' => '',
            'action_col_chk' => $action_col_chk,
            'breadcrumbs' => $breadcrumbs,
            "heading" => 'Category',

        ];
        //dd($data);
        
        return view('cat_details_listing', $data);
        
    }

    public function CatAjaxList (Request $request){

        $columns = array(

            array( 
                "db"=> "checkbox" ,
                "dt"=> "checkbox" ,
            ),

            array( 
                "db"=> "categories.category_name" ,    
                "dt"=> "category_name" ,    
            ),

            array(
                "db"=> "categories.category_desc",
                "dt"=> "category_desc",
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

        $o_list = $this->category_model->get_cat(NULL, $filter_arr);
        $totalFiltered = ($this->category_model->get_cat(NULL, $filter_arr_clone));
        if(!empty($totalFiltered)){
            $totalFiltered = count($totalFiltered);
        }
        else{
            $totalFiltered = 0;
        }

        $totalRecords = $this->category_model->get_cat(NULL);
        if(!empty($totalRecords)){
            $totalRecords = count($totalRecords);
        } 
        else{
            $totalRecords = 0;
        }

        $data = array();

        if(!empty($o_list)){
            foreach ($o_list as $row) {

                $action_str = ' <a class="edit_cat_details" href="'.route('edit_cat_master_view', $row->id).'" title="Edit">'.'<i class="fa fa-pencil-square-o fa-sm action-icons"></i>'.'Edit</a> ';

                $action_str .= ' <a class="delete_cat text text-danger" data-uid="'.$row->id.'" href="javascript:void(0)" title="Delete">'.
                                    '<i class="fa fa-trash fa-sm action-icons"></i>'.
                                '</a>';

                // Sales team access/view the categories 
                // 1=SuperAdmin, 2= UserAdmin, 3=SalesTeam
                if($login_users_role == 1 || $login_users_role == 3){
                    $action_col_chk = $action_str;
                }
                else{
                    $action_col_chk = 'No Access';
                }
                
                // these pass to views
                $checkbox = '<input type="checkbox" class="checked_id" name="ids[]" value="'.$row->id.'">';
                $data[] = (object) array(
                    'checkbox' => $checkbox,
                    'category_name'  => e(!empty($row->category_name)? $row->category_name:''),
                    'category_desc'  => e(!empty($row->category_desc)? $row->category_desc:''),
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

    public function DeleteCat(Request $request){
            
        $return_status = array(
            'status'  => FALSE,
            'message' => 'Failed to delete Category',
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
                        $category_row = DB::table('categories')->where('id', '=', $u_id)->first();

                        // get products assigned to category
                        $products_in_cat = DB::table('products')->where('category_id', '=', $u_id)->get();
                        if(count($products_in_cat) > 0 && !empty(count($products_in_cat))){
                            // deleting all products assigned to selected category
                            $pds_deleted = Product::where('category_id', $u_id)->delete();

                        }
                            // deleting selected category
                        $category_deleted = Category::where('id', $u_id)->delete();

                        if( !empty($category_deleted ) ){
                            $return_status['status'] = TRUE;
                            $return_status['message'] = 'Category successfully deleted';
                            $return_status['data'] = array();
                        } 
                }

            return response()->json(//Ajax response in json format
                $return_status
            );
        }
    }

    public function EditCatMasterView($id = NULL){
        $data = array();		
        $heading = 'Add Category';
        $cat_details = '';
        $pending_data = '';
        $permission_array = array();
        $breadcrumbs = array(
            array('name' => 'Home',
            'url' => route('home')),
            array('name' => 'Category',
            'url' => route('cat_view')),
        );

        if(!empty($id)){
            $heading = 'Edit Category';
            $breadcrumbs[] = array('name' => 'Edit Category',
            'url' => '');  
            $cat_details = $this->category_model->get_cat($id);
        }
        else{
            $breadcrumbs[] = array('name' => 'Add Category',
            'url' => '');    
        }

        $data = [
            'heading'    => $heading,
            'go_back_url'    => route('cat_view'),
            'breadcrumbs' => $breadcrumbs,
            'row_id'        => $id,
            'cat_details'  => $cat_details,

        ];
        return view('cat_add_edit', $data);
    }

    public function SaveCatDetails(Request $request){
        $return_status = array(
            'status' => FALSE,
            'message' => 'Category details failed to save',
            'data' => ''
        );

        $category_name = $request->category_name;
        $category_desc = $request->category_desc;
        $row_id = $request->row_id;

        $rules = array(
            'category_name' => 'required',
            'category_desc' => 'required',
        );
        
        $messages = [
            'category_name.required' 		=> 'Category Name Role Required',
            'category_desc.required' 		=> 'Category Description Required',
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

            if(!empty($category_name)){
                        $data_arr += array('category_name' => $category_name);
            }
            if(!empty($category_desc)){
                $data_arr += array('category_desc' => $category_desc);
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
                    $creating_category = Category::create($data_arr);
                    $last_id = $creating_category->id;
                    //$last_id = $this->category_model->save_cat_details($data_arr); 
                }
                else{
                    $data_arr += array('updated_at' => date('Y-m-d H:i:s'));
                    $last_id = Category::where('id', $row_id)
                                       ->update($data_arr);
                    //$last_id = $this->category_model->save_cat_details($data_arr, $row_id);
                }
                if(!empty($last_id)){
                    $return_status['status'] = TRUE;
                    $return_status['message'] = 'Category details successfully saved';
                    $return_status['data'] = array();
                }
                
            }            
        }
        return response()->json($return_status);
    }
}    