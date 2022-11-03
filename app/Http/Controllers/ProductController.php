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

class ProductController extends Controller
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

    // Products controller methods
    public function PdView()
    {
        $breadcrumbs = array(
            array('name' => 'Home',
            'url' => route('home')),
            array('name' => 'Products',
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
            'page_title' 	 => 'Products List',
            'active_sidebar' => '',
            'action_col_chk' => $action_col_chk,
            'breadcrumbs' => $breadcrumbs,
            "heading" => 'Products',

        ];
        //dd($data);
        
        return view('products_details_listing', $data);
        
    }

    public function PdAjaxList (Request $request){

        $columns = array(

            array( 
                "db"=> "checkbox" ,
                "dt"=> "checkbox" ,
            ),

            array( 
                "db"=> "products.product_name" ,     // database table's column name
                "dt"=> "product_name" ,     // name we get from as
            ),

            array(
                "db"=> "products.product_desc",
                "dt"=> "product_desc",
            ),
            array( 
                "db"=> "products.product_price" ,
                "dt"=> "product_price" ,
            ),
            
            array( 
                "db"=> "products.category_id" ,
                "dt"=> "product_cat_name" ,
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

        // $get_products = Category::with('products')->find(1);
        // dd($get_products->toarray());

        $filter_arr_clone = $filter_arr;
        $filter_arr_clone['recordsFiltered'] = TRUE;
        $o_list = $this->product_model->get_products(NULL, $filter_arr);
        $totalFiltered = ($this->product_model->get_products(NULL, $filter_arr_clone));
        if(!empty($totalFiltered)){
            $totalFiltered = count($totalFiltered);
        }
        else{
            $totalFiltered = 0;
        }

        $totalRecords = $this->product_model->get_products(NULL);
        if(!empty($totalRecords)){
            $totalRecords = count($totalRecords);
        } 
        else{
            $totalRecords = 0;
        }

        $data = array();

        if(!empty($o_list)){
            foreach ($o_list as $row) {

                $action_str = ' <a class="edit_pd_details" href="'.route('edit_pd_master_view', $row->id).'" title="Edit">'.'<i class="fa fa-pencil-square-o fa-sm action-icons"></i>'.'Edit</a> ';

                $action_str .= ' <a class="delete_pd text text-danger" data-uid="'.$row->id.'" href="javascript:void(0)" title="Delete">'.
                                    '<i class="fa fa-trash fa-sm action-icons"></i>'.
                                '</a>';

                // Sales team can view/access the products 
                // 1=SuperAdmin, 2= UserAdmin, 3=SalesTeam
                if($login_users_role == 1 || $login_users_role == 3){
                    $action_col_chk = $action_str;
                }
                else{
                    $action_col_chk = 'No Access';
                }
                $product_img = '';
                if(!empty($row->product_img)){
                    $img = asset('uploads/' .$row->product_img);
                    $product_img = '<img src="'.$img.'" id="profile_img_display" width="50" height="50">';
                }

                $cat_id = $row->category_id;
                $cat_details = $this->category_model->get_cat($cat_id);

                $product_cat_name = '<button type="button" cat-name="'.$cat_details->category_name.'" cat-des="'.$cat_details->category_desc.'" class="btn btn-outline-primary cat_data_load" data-toggle="modal" data-target="#exampleModalCenter">
                '.$row->product_cat_name.'</button>';
                

                // these pass to views
                $checkbox = '<input type="checkbox" class="checked_id" name="ids[]" value="'.$row->id.'">';
                $data[] = (object) array(
                    'checkbox' => $checkbox,
                    'product_name'  => e(!empty($row->product_name)? $row->product_name:''),
                    'product_desc'  => e(!empty($row->product_desc)? $row->product_desc:''),
                    'product_price'  => e(!empty($row->product_price)? $row->product_price:''),
                    'product_img'  => $product_img,
                    'product_cat_name'  => $product_cat_name,
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

    public function DeletePd(Request $request){
            
            $return_status = array(
                'status'  => FALSE,
                'message' => 'Failed to delete product',
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
                        $is_del = Product::where('id', $u_id)->delete();
                        if( !empty($is_del ) ){
                            $return_status['status'] = TRUE;
                            $return_status['message'] = 'Product successfully deleted';
                            $return_status['data'] = array();
                        } 
                }

            return response()->json(//Ajax response in json format
                $return_status
            );
        }
    }

    public function EditPdMasterView($id = NULL){
        $data = array();		
        $heading = 'Add Product';
        $pd_details = '';
        $pending_data = '';
        $permission_array = array();
        $breadcrumbs = array(
            array('name' => 'Home',
            'url' => route('home')),
            array('name' => 'Products',
            'url' => route('pd_view')),
        );

        $get_cat_list = $this->product_model->get_cat_list();

        if(!empty($id)){
            $heading = 'Edit Product';
            $breadcrumbs[] = array('name' => 'Edit Product',
            'url' => '');  
            $pd_details = $this->product_model->get_products($id);
        }
        else{
            $breadcrumbs[] = array('name' => 'Add Product',
            'url' => '');    
        }

        $data = [
            'heading'    => $heading,
            'go_back_url'    => route('pd_view'),
            'breadcrumbs' => $breadcrumbs,
            'row_id'        => $id,
            'pd_details'  => $pd_details,
            'get_cat_list'  => $get_cat_list,

        ];
        return view('pd_add_edit', $data);
    }

    public function SavePdDetails(Request $request){
        $return_status = array(
            'status' => FALSE,
            'message' => 'Product details failed to save',
            'data' => ''
        );


        $category_id = $request->category_id;
        $product_name = $request->product_name;
        $product_desc = $request->product_desc;
        $product_price = $request->product_price;
        $row_id = $request->row_id;

        $rules = array(
            'category_id' => 'required',
            'product_desc' => 'required',
            'product_price' => 'required',
            'product_name' => 'required',         
            'product_img' => 'nullable|mimes:jpeg,jpg,png,gif|max:5250',
        );
        
        $messages = [
            'category_id.required' 		=> 'User Role Required',
            'product_desc.required' 		=> 'First Name Role Required',
            'product_price.required' 		=> 'Last Name Required',
            'product_name.required' 			=>  'product_name Required',
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

            if(!empty($product_name)){
                $data_arr += array('product_name' => $product_name);
            }

            if(!empty($product_desc)){
                        $data_arr += array('product_desc' => $product_desc);
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
                    $creating_product = Product::create($data_arr);
                    $last_id = $creating_product->id;
                    //$last_id = $this->product_model->save_pd_details($data_arr);
                }
                else{
                    $data_arr += array('updated_at' => date('Y-m-d H:i:s'));
                    $last_id = Product::where('id', $row_id)
                                       ->update($data_arr);
                    //$last_id = $this->product_model->save_pd_details($data_arr, $row_id);
                }

                if(!empty($last_id)){
                    $return_status['status'] = TRUE;
                    $return_status['message'] = 'Product details successfully saved';
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