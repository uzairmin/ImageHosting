<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use MongoDB\Client as Mongo;
use App\Services\ConnectionDb;
use App\Http\Requests\LoginValidation;
use App\Http\Requests\UpdateEmailValidation;
use App\Http\Requests\UpdateNameValidation;
use App\Http\Requests\UpdatePasswordValidation;
use App\Http\Requests\UpdateAgeValidation;

class LoginController extends Controller
{
    function jwtToken($email,$passwor)
    {// Logging in user and creating a token for it.
        try
        {
            $key = "uzair";
            $payload = array(
                "iss" => "localhost",
                "aud" => time(),
                "iat" => now(),
                "nbf" => 100000
            );
            $table = "users";
            $jwt = JWT::encode($payload, $key, 'HS256');
            $conn = new ConnectionDb();
            $collection = $conn->setConnection($table);
            $data = $collection->findOne(['email'=>$email]);
            $pass = $data["password"];
            if (Hash::check($passwor, $pass)) 
            {
                $collection->updateOne(array("email"=>$email), array('$set'=>array("remember_token"=>$jwt)));
                $collection->updateOne(array("email"=>$email), array('$set'=>array("status"=>'1')));
                return response()->json(['remember_token'=>$jwt , 'message'=> 'successfuly login']);
            }
            else
            {
                return response()->json(['message'=> 'not login']);
            }
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
    public function checkLogged($email,$token)
    {//Check whether user is logged in or not.
        try
        {
            $table = "users";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $data = $collection->findOne(['email'=>$email,'remember_token'=>$token]);
            if($data!=NULL)
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
    function updateName(UpdateNameValidation $request)
    {//Updating user name.
        try
        {
            $table = "users";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $email = $request->email;
            $token = $request->token;
            $newname = $request->newname;
            $collection->updateOne(array("remember_token"=>$token), array('$set'=>array("name"=>$newname)));
            return response()->json(['message'=> 'Name updated...']);
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
    function updateEmail(UpdateEmailValidation $request)
    {//Updating user email.
        try
        {
            $table = "users";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $email = $request->email;
            $token = $request->token;
            $newemail = $request->newemail;
            $collection->updateOne(array("remember_token"=>$token), array('$set'=>array("email"=>$newemail)));
            return response()->json(['message'=> 'Email updated...']);
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
    function updatePassword(UpdatePasswordValidation $request)
    {//Updating user password.
        try
        {
            $table = "users";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $email = $request->email;
            $token = $request->token;
            $newpassword = Hash::make($request->newpassword);
            $collection->updateOne(array("remember_token"=>$token), array('$set'=>array("password"=>$newpassword)));
            return response()->json(['message'=> 'Password is changed...']);
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
    function updateAge(UpdateAgeValidation $request)
    {//Updating user age.
        try
        {
            $table = "users";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $email = $request->email;
            $token = $request->token;
            $age = $request->age;
            $collection->updateOne(array("remember_token"=>$token), array('$set'=>array("age"=>$age)));
            return response()->json(['message'=> 'Age is changed...']);
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
    function loggingIn(LoginValidation $request)
    {//Logging in.
        try
        {
            $email = $request->email;
            $password = $request->password;
            return self::jwtToken($email,$password);
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
}