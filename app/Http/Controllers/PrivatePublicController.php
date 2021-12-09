<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\ConnectionDb;
use App\Http\Requests\EmailValidation;

class PrivatePublicController extends Controller
{ 
    function deleteAccess($picId)
    {//Delete the access of the user of a specific image.
        try
        {
            $table = "images";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $collection->updateOne(["_id"=>$picId], ['$unset'=>["shared"=>""]]);
            return response()->json(['message'=> 'Removed']);
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
    function checkPrivate($imageId)
    {//Check whether the image is private or not.
        try
        {
            $table = "images";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $data = $collection->findOne(['_id'=>$imageId]);
            if($data["access"] == "private")
            {
                return false;
            }
            return true;
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
    function checkPublic($imageId)
    {//Check whether the image is public or not.
        try
        {
            $table = "images";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $data = $collection->findOne(['_id'=>$imageId]);
            if($data["access"] == "public")
            {
                return false;
            }
            return true;
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
    function checkHidden($imageId)
    {//Check whether the image is hidden or not.
        try
        {
            $table = "images";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $data = $collection->findOne(['_id'=>$imageId]);
            if($data["access"] == "hidden")
            {
                return false;
            }
            return true;
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
    function makePrivate(Request $request)
    {//Make the image private.
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
            $imageId = new \MongoDB\BSon\ObjectId($request->pic_id);
            $check = self::checkPrivate($imageId);
            if($check == true)
            {   self::deleteAccess($picId);
                $collection->updateOne(array("_id"=>$imageId, "user_id"=>$userId), array('$set'=>array("access"=>"private")));
                return response()->json(['message'=> 'Photo is now private...']);
            }
            else
            {
                return response()->json(['message'=> 'Already private...']);
            }
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
    function makePublic(Request $request)
    {//Make the image public.
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
            $imageId = new \MongoDB\BSon\ObjectId($request->pic_id);
            $check = self::checkPublic($imageId);
            if($check == true)
            {      
                self::deleteAccess($picId);
                $collection->updateOne(array("_id"=>$imageId, "user_id"=>$userId), array('$set'=>array("access"=>"public")));
                return response()->json(['message'=> 'Photo is now public...']);
            }
            else
            {
                return response()->json(['message'=> 'Already public...']);
            }
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
    function makeHidden(Request $request)
    {//Make the image hidden.
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
            $imageId = new \MongoDB\BSon\ObjectId($request->pic_id);
            $check = self::checkHidden($imageId);
            if($check == true)
            {
                self::deleteAccess($picId);
                $collection->updateOne(array("_id"=>$imageId, "user_id"=>$userId), array('$set'=>array("access"=>"hidden")));
                return response()->json(['message'=> 'Photo is now hidden...']);
            }
            else
            {
                return response()->json(['message'=> 'Already hidden...']);
            }
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
}
