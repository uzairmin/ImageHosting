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

class ListingController extends Controller
{
    function Listing(Request $request)
    {//Listing all images of that user.
        try
        {
            $table = "users";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $token = $request->token;
            $data = $collection->findOne(['remember_token'=>$token]);
            $userId = $data["_id"];
            $table = "images";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $pictures = $collection->find(['user_id'=>$userId]);
            $photosArr = json_decode(json_encode($pictures->toArray(),true));
            return response()->json(['message'=> 'Your photos :',$photosArr]);
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
    function searchImage(Request $request)
    {//Search a specific image.
        try
        {
            $table = "images";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $token = $request->token;
            $search = $request->search;
            $format = $request->format;
            $pictures = $collection->find([$format=>$search]);
            $photosArr = json_decode(json_encode($pictures->toArray(),true));
            return response()->json(['message'=> 'Photos are :',$photosArr]);
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
}
