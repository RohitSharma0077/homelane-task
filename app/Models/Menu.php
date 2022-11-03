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

    protected $fillable = [
        'menu_name',
        'menu_URL',
    ];
}
