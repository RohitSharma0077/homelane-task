<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;
    protected $table = 'users';
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'user_role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function get_users($id = NULL , $filter_arr = array()){ 
        
        if(empty($id)){

            $query = DB::table('users')
            ->select(DB::raw('users.id as id,users.first_name as first_name, users.last_name as last_name, users.email as email, users.user_role as user_role,CONCAT(COALESCE(users.first_name," ")," ", COALESCE(users.last_name," ")) AS users_full_name'))
            ->where('deleted_at', NULL)
            ->groupBy('users.id');

                if(!empty($filter_arr)){
                $search_val = $filter_arr['search_val'];
                
                if(!empty($search_val)){ 
                    $havingStr = '( 
                                    first_name like "%' . $search_val . '%" OR
                                    last_name like "%' . $search_val . '%" OR
                                    user_role like "%' . $search_val . '%" OR
                                    email like "%' . $search_val . '%"         
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
            $query = DB::table($this->table)
                        ->select(DB::raw($this->table.'.*'));
                    
            $result = $query->where($this->table.'.id', '=', $id)
                        ->first();
        }           
        return $result;
    }

    public function save_users_details($data, $id=NULL){

        if(empty($data)){
            return FALSE;
        }
        if(empty($id)){ //Creating row
            $id = DB::table($this->table)->insertGetId(
                $data
            );
            return $id;
        }
        else{ //Editing row
            
            $result = DB::table($this->table)
                        ->where($this->table.'.id', $id)
                        ->update($data);
            if(!empty($result)){
                return $id;
            }
            return FALSE; 
        }

        return $result;

    }
}
