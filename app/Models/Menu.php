<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menus';
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    public $timestamps = false;

    protected $fillable = [
        'menu_name',
        'menu_URL',
    ];

    public function get_menu($id = NULL , $filter_arr = array()){ 
        
        if(empty($id)){

            $query = DB::table('menus')
            ->select(DB::raw('menus.id as id,menus.menu_name as menu_name, menus.menu_URL as menu_URL'))
            ->where('deleted_at', NULL)
            ->groupBy('menus.id');

                if(!empty($filter_arr)){
                $search_val = $filter_arr['search_val'];
                
                if(!empty($search_val)){ 
                    $havingStr = '( 
                                    menu_name like "%' . $search_val . '%" OR
                                    menu_URL like "%' . $search_val . '%"        
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
                            $query->orderBy('created_on','desc');
                        }
                    }
                    else{
                        $query->orderBy('created_on','desc');
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
            $query = DB::table("menus")
                        ->select(DB::raw('menus.*'));
                    
            $result = $query->where('menus.id', '=', $id)
                        ->first();
        }           
        return $result;
    }

    public function save_menu_details($data, $id=NULL){

        if(empty($data)){
            return FALSE;
        }
        if(empty($id)){ //Creating row
            $id = DB::table('menus')->insertGetId(
                $data
            );
            return $id;
        }
        else{ //Editing row
            
            $result = DB::table('menus')
                        ->where('menus.id', $id)
                        ->update($data);         
            if(!empty($result)){
                return $id;
            }
            return FALSE; 
        }
        return $result;

    }
}
