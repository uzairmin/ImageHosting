<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\ConnectionDb;
use App\Http\Requests\EmailValidation;

class LogoutController extends Controller
{
    function checkLogged($email,$token)
    {//Check if user is logged in or not.
        try
        {
            $table = "users";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $data = $collection->findOne(['email'=>$email,'remember_token'=>$token]);
            if($data["email"])
            {
                return true;
            }
            return false;
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
    function loggingOut(EmailValidation $request)
    {//Check user is logged in or not and then will logged him out.
        try
        {
            $table = "users";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $email = $request->email;
            $token = $request->token;
            $check = self::checkLogged($email,$token);
            if($check == true)
            {   
                $check1 = $collection->updateOne(array("remember_token"=>$token), array('$set'=>array("status"=>'0')));
                $check2 = $collection->updateOne(array("remember_token"=>$token), array('$set'=>array("remember_token"=>null)));
                return response()->json(['message'=> 'Logged Out']);
            }
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        } 
    }
}