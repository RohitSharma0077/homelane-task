<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Product extends Model
{
    protected $table = 'products';
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'product_name',
        'product_desc',
        'product_price',
        'product_img',
        'category_id',
    ];

    public function category()
    {
    	return $this->belongsTo(Category::class);
    }

    public function get_cat_list(){

        $query = DB::table('categories')
        ->select(DB::raw('categories.id, categories.category_name'));
        $query->groupBy('categories.category_name');
    
            $result = $query->get();
            if($result->count() == 0){
                $result = FALSE;
            }
         //  dd($query->toSql());
        return $result;
    }

    public function get_products($id = NULL , $filter_arr = array()){ 
        
        if(empty($id)){

            // $query = DB::table('products')
            //          ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            // ->select(DB::raw('products.id as id, products.product_name as product_name, products.product_desc as product_desc, products.product_price as product_price, products.product_img as product_img, products.category_id as category_id, categories.category_name as product_cat_name'))
            // ->where('products.deleted_at', NULL)
            // ->groupBy('products.id');

            $query = Product::leftJoin('categories', 'categories.id', '=', 'products.category_id')
                    ->select(DB::raw('products.id as id, products.product_name as product_name, products.product_desc as product_desc, products.product_price as product_price, products.product_img as product_img, products.category_id as category_id, categories.category_name as product_cat_name'))
                    ->where('products.deleted_at', NULL)
                    ->groupBy('products.id');

                if(!empty($filter_arr)){
                $search_val = $filter_arr['search_val'];
                
                if(!empty($search_val)){ 
                    $havingStr = '( 
                                    product_name like "%' . $search_val . '%" OR
                                    product_desc like "%' . $search_val . '%" OR
                                    product_price like "%' . $search_val . '%" OR
                                    product_cat_name like "%' . $search_val . '%"         
                                )';
                    $query->havingRaw($havingStr);
                } 
                //search by keyword and status close
                if($filter_arr['recordsFiltered'] === FALSE){
                    //offset
                    $offset = $filter_arr['offset'];
                    if(!empty($offset)){
                        $query->offset($offset);
                    }
                    //offset close

                    //limit
                    $limit = $filter_arr['limit'];
                    if(!empty($limit)){
                        $query->limit($limit);
                    }
                    //limit close

                    //sort
                    
                    $sort = $filter_arr['sort'];
                    if(!empty($sort)){
                        $sort_column = !empty($sort['sort_column'])?$sort['sort_column']:'';
                        $sort_by     = !empty($sort['sort_by'])?$sort['sort_by']:'';

                        if( !empty($sort_column) && !empty($sort_by) ){
                            $query->orderBy($sort_column, $sort_by);
                        }
                        else{
                            $query->orderBy('products.created_at','desc');
                        }
                    }
                    else{
                        $query->orderBy('products.created_at','desc');
                    }

                    //sort close
                }
                
            }
            $result = $query->get();
            
            if($result->count() == 0){
                $result = FALSE;
              
            }
        }
        else{
            $query = DB::table('products')
                        ->select(DB::raw('products.*'));
                    
            $result = $query->where('products.id', '=', $id)
                        ->first();
        }           
        return $result;
    }

    public function save_pd_details($data, $id=NULL){

        if(empty($data)){
            return FALSE;
        }
        if(empty($id)){ //Creating row
            $id = DB::table('products')->insertGetId(
                $data
            );
            return $id;
        }
        else{ //Editing row
            
            $result = DB::table('products')
                        ->where('products.id', $id)
                        ->update($data);
            if(!empty($result)){
                return $id;
            }
            return FALSE; 
        }

        return $result;

    }
}
