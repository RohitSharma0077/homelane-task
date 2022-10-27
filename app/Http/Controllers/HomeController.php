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
            "breadcrumbs" => $breadcrumbs,
            "heading" => 'Dashboard',

          
        );
      // dd($data);
        return view('home', $data);
    }

    
}
