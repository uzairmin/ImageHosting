<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Mail\TestMail;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use MongoDB\Client as Mongo;
use App\Services\ConnectionDb;
use App\Http\Requests\EmailValidation;
use App\Http\Requests\UpdatePasswordValidation;
use App\Http\Requests\ForgetPasswordValidation;

class ForgetPasswordController extends Controller
{
    function checkOtp(ForgetPasswordValidation $request)
    {
        $token = $request->token;
        $otp = $request->otp;
        $password = $request->new_password;
        $table = "users";
        $user = new ConnectionDb();
        $collection = $user->setConnection($table);
        $data = $collection->findOne(["otp"=>(int)$otp,"remember_token"=>$token]);
        if($data["email"])
        {
            $password = Hash::make($password);
            $collection->updateOne(array("remember_token"=>$token), array('$set'=>array("password"=>$password)));   
            return response()->json(['message'=> 'New Password Updated']);
        }
        else
        {
            return response()->json(['message'=> 'OTP not matched...']);
        }
    }
    function forgetPassword(EmailValidation $request)
    {// Creating an otp and sending it through email. If otp matches then password can be updated.
        try
        {
            $otp = rand(1000,9999);
            $table = "users";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $email = $request->email;
            $token = $request->token;
            $collection->updateOne(array("remember_token"=>$token), array('$set'=>array("otp"=>$otp)));
            $details = ['title'=>'This is your OTP number','body'=>'OTP is '.$otp];
            Mail::to($email)->send(new TestMail($details));
            return response()->json(['message'=> 'Enter your otp given in your email']);
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
}
