<?php 

//To get role of logged in user
function getLoggedInUserRole(){
		
    if(Auth::guest()){
        return FALSE;
    } else {
        return Auth::user()->user_role;
    }
}


?>