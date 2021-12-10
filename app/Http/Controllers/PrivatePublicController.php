<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\ConnectionDb;
use App\Http\Requests\EmailValidation;
use App\Http\Requests\AccessValidation;

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
    function checkAccess($access)
    {//Check whether the image is private or not.
        try
        {
            if($access == "Private")
            {
                return "Private";
            }
            elseif($access == "Public")
            {
                return "Public";
            }
            return "Hidden";
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
    function checkPrivate($imageId)
    {//Check whether the image is public or not.
        try
        {
            $table = "images";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $data = $collection->findOne(['_id'=>$imageId]);
            if($data["access"] == "Private")
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
            if($data["access"] == "Public")
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
            if($data["access"] == "Hidden")
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
    function changeAccess(AccessValidation $request)
    {//Make the image private.
        try
        {
            $table = "images";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $token = $request->token;
            $picId = new \MongoDB\BSon\ObjectId($request->pic_id);
            $access = $request->access;
            $check = self::checkAccess($access);
            if($check == "Private")
            {
                if(self::checkPrivate($picId)==true)
                {
                    self::deleteAccess($picId);
                    $collection->updateOne(array("_id"=>$picId), array('$set'=>array("access"=>"Private")));
                    return response()->json(['message'=> 'Photo is now Private...']);
                }
                else
                {
                    return response()->json(['message'=> 'Already Private...']);
                }      
            }
            elseif($check == "Public")
            {
                if(self::checkPublic($picId)==true)
                {
                    self::deleteAccess($picId);
                    $collection->updateOne(array("_id"=>$picId), array('$set'=>array("access"=>"Public")));
                    return response()->json(['message'=> 'Photo is now Public...']);
                }
                else
                {
                    return response()->json(['message'=> 'Already Public...']);
                }
            }
            else
            {
                if(self::checkHidden($picId)==true)
                {
                    self::deleteAccess($picId);
                    $collection->updateOne(array("_id"=>$picId), array('$set'=>array("access"=>"Hidden")));
                    return response()->json(['message'=> 'Photo is now Hidden...']);
                }
                else
                {
                    return response()->json(['message'=> 'Already Hidden...']);
                }
            }
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
}
