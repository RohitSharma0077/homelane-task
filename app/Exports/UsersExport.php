<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class UsersExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //return User::all();
        //return User::select("id", "first_name", "last_name","user_role", "email", "created_at")->get();

        return DB::table('users')->select(DB::raw('id, first_name, last_name,(CASE WHEN user_role = 1 THEN "Super Admin" WHEN user_role = 2 THEN "User Admin" WHEN user_role = 3 THEN "Sales Team" ELSE "" END) as user_role, email, created_at'))->get();
    }

     /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
       return ["ID", "First Name", "Last Name","User Role", "Email", "Created at"];
   }
}
