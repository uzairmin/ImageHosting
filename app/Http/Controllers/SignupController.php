<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Mail\TestMail;
use App\Http\Requests\SignupValidation;
use App\Http\Requests\EmailValidation;
use App\Services\ConnectionDb;
use App\Jobs\MailJob;

class SignupController extends Controller
{
    function signingup(SignupValidation $request)
    {//Signing in the user and sending confirmation on his mail.
        try
        {
            $table = "users";
            $token = rand(1000,1000000);
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $name = $request->name;
            $email = $request->email;
            $password = Hash::make($request->password);
            $age = $request->age;
            $picture = $request->file('path')->store('profile');
            $path = $_SERVER['HTTP_HOST']."/profile/storage/".$picture;
            $token = $token;
            $document = array( 
                "name" => $name,
                "path" => $path,
                "email" => $email, 
                "password" => $password,
                "age" => $age,
                "token" => $token,
                "active" => 0,
                "otp" => 0
            );
            $collection->insertOne($document);
            $details = ['title'=>'Verify to continue',
                    'body'=>'http://127.0.0.1:8000/user/confirmation/'.$email.'/'.$token
                ];
            dispatch(new MailJob($email, $details));
            return response()->json(['message'=> 'To complete signup process please verify your account from the mail Sent...']);
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
            if($data->email)
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
    function deactivate(EmailValidation $request)
    {//Will deactivate or temporary close user account.
        try
        {
            $table = "users";
            $email = $request->email;
            $token = $request->token;
            $check = self::checkLogged($email,$token);
            if($check == true)
            {
                $user = new ConnectionDb();
                $collection = $user->setConnection($table);
                $collection->updateOne(array("remember_token"=>$token), array('$set'=>array("active"=>null,"status"=>null,"remember_token"=>null)));
                return response()->json(['message'=> 'User deactivated']);
            }
            else
            {
                return response()->json(['message'=> 'User is not authenticated']);
            }
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
}
