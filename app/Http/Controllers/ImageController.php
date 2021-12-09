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
use App\Http\Requests\ImageValidation;
use App\Http\Requests\EmailValidation;

class ImageController extends Controller
{
    public function checkLogged($email,$token)
    {// Check whether user is logged in or not.
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
    function addAccess(EmailValidation $request)
    {//Add access to users for an image.
        try
        {
            $table = "images";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $email = $request->email;
            $token = $request->token;
            $friend = $request->friend;
            $picId = new \MongoDB\BSon\ObjectId($request->pic_id);
            $check = self::checkLogged($email,$token);
            if($check == true)
            {
                $collection->updateOne(["_id"=>$picId], ['$push'=>["shared"=>["friend"=>$friend]]]);
                return response()->json(['message'=> 'Added']);
            }
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
    function removeAccess(EmailValidation $request)
    {//Remove access for users for an image.
        try
        {
            $table = "images";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $email = $request->email;
            $token = $request->token;
            $friend = $request->friend;
            $picId = new \MongoDB\BSon\ObjectId($request->pic_id);
            $check = self::checkLogged($email,$token);
            if($check == true)
            {
                $collection->updateOne(["_id"=>$picId], ['$pull'=>["shared"=>["friend"=>$friend]]]);
                return response()->json(['message'=> 'Removed']);
            }
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
    function removeAllAccess(EmailValidation $request)
    {//Remove access for all users in an image.
        try
        {
            $table = "images";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $email = $request->email;
            $token = $request->token;
            $picId = new \MongoDB\BSon\ObjectId($request->pic_id);
            $check = self::checkLogged($email,$token);
            if($check == true)
            {
                $collection->updateOne(["_id"=>$picId], ['$unset'=>["shared"=>""]]);
                return response()->json(['message'=> 'Removed']);
            }
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
    function addImage(ImageValidation $request)
    {//Used to insert image with the link.
        try
        {
            $table = "images";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $email = $request->email;
            $token = $request->token;
            $picture = $request->file('picture')->store('images');
            $path = $_SERVER['HTTP_HOST']."/image/storage/".$picture;
            $extension = $request->extension;
            $imageId = new \MongoDB\BSon\ObjectId($request->imageId);
            $table = "users";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $data = $collection->findOne(['remember_token'=>$token]);
            $userId = $data["_id"];
            $table = "images";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $document = array( 
                "picture" => $path, 
                "data" => date("Y-m-d"),
                "time" => date("h:i:sa"),
                "extension" => $extension,
                "access" => "Hidden",
                "user_id" => $userId
            );
            $collection->insertOne($document);
            return response()->json(['message'=> 'Image Inserted']);
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
        //date, time, name, extensions, private, public, hidden
    }
    function removeImage(Request $request)
    {//Remove an image.
        try
        {
            $table = "users";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $token = $request->token;
            $imageId = new \MongoDB\BSon\ObjectId($request->image_id);
            $data = $collection->findOne(['remember_token'=>$token]);
            $userId = $data["_id"];
            $table = "images";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $data1 = $collection->findOne(['_id'=>$imageId]);
            $userId1 = $data1["user_id"];
            if($userId1==$userId)
            {
                $collection->deleteOne(['user_id'=>$userId, '_id' => $imageId]);
                return response->json(["Deleted"]);
            }
            else
            {
                return response->json(["This image can't be deleted by this user..."]);
            }
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
}
