<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    public $timestamps = false;

    protected $fillable = [
        'name',
        'role_values',
    ];

    public function get_menu_list(){

        $query = DB::table('menus')
        ->select(DB::raw('menus.id, menus.menu_name'));
        $query->groupBy('menus.menu_name');
    
            $result = $query->get();
            if($result->count() == 0){
                $result = FALSE;
            }
         //  dd($query->toSql());
        return $result;
    }

    public function get_roles($id = NULL , $filter_arr = array()){ 
        
        if(empty($id)){

            $query = DB::table('roles')
                    ->select(DB::raw('roles.id as id, roles.name as name, roles.role_values as role_values'))
                    ->where('roles.deleted_at', NULL)
                    ->groupBy('roles.id');

                if(!empty($filter_arr)){
                $search_val = $filter_arr['search_val'];
                
                if(!empty($search_val)){ 
                    $havingStr = '( 
                                    name like "%' . $search_val . '%" OR
                                    role_values like "%' . $search_val . '%" OR   
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
                            $query->orderBy('roles.created_on','desc');
                        }
                    }
                    else{
                        $query->orderBy('roles.created_on','desc');
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
            $query = DB::table('roles')
                        ->select(DB::raw('roles.*'));
                    
            $result = $query->where('roles.id', '=', $id)
                        ->first();
        }           
        return $result;
    }

    public function save_pd_details($data, $id=NULL){

        if(empty($data)){
            return FALSE;
        }
        if(empty($id)){ //Creating row
            $id = DB::table('roles')->insertGetId(
                $data
            );
            return $id;
        }
        else{ //Editing row
            
            $result = DB::table('roles')
                        ->where('roles.id', $id)
                        ->update($data);
            if(!empty($result)){
                return $id;
            }
            return FALSE; 
        }

        return $result;

    }
}
