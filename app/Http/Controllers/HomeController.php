<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;

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
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {   
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
          
        );
        return view('home', $data);
    }
}
