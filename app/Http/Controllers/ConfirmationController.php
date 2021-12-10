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

class ConfirmationController extends Controller
{
    function confirming(Request $request)
    {
        $email = $request->email;
        $table = "users";
        $user = new ConnectionDb();
        $collection = $user->setConnection($table);
        $collection->updateOne(array("email"=>$email), array('$set'=>array("active"=>1)));
        return response()->json(['message'=> 'Email Verified...']);
    }
}
