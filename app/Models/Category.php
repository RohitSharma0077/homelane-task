<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Category extends Model
{
    protected $table = 'categories';
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'category_name',
        'category_desc',
    ];


    public function products()
    {
    	return $this->hasMany(Product::class);    // category has many products
    }

    public function get_cat($id = NULL , $filter_arr = array()){ 
        
        if(empty($id)){

            $query = DB::table('categories')
            ->select(DB::raw('categories.id as id,categories.category_name as category_name, categories.category_desc as category_desc'))
            ->where('deleted_at', NULL)
            ->groupBy('categories.id');

                if(!empty($filter_arr)){
                $search_val = $filter_arr['search_val'];
                
                if(!empty($search_val)){ 
                    $havingStr = '( 
                                    category_name like "%' . $search_val . '%" OR
                                    category_desc like "%' . $search_val . '%"        
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
                            $query->orderBy('created_at','desc');
                        }
                    }
                    else{
                        $query->orderBy('created_at','desc');
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
            $query = DB::table("categories")
                        ->select(DB::raw('categories.*'));
                    
            $result = $query->where('categories.id', '=', $id)
                        ->first();
        }           
        return $result;
    }

    public function save_cat_details($data, $id=NULL){

        if(empty($data)){
            return FALSE;
        }
        if(empty($id)){ //Creating row
            $id = DB::table('categories')->insertGetId(
                $data
            );
            return $id;
        }
        else{ //Editing row
            
            $result = DB::table('categories')
                        ->where('categories.id', $id)
                        ->update($data);         
            if(!empty($result)){
                return $id;
            }
            return FALSE; 
        }
        return $result;

    }
}

