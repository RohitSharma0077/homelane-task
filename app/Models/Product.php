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
}
