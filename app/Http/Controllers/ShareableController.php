<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\ConnectionDb;

class ShareableController extends Controller
{
    function accessable($userId, $picId)
    {
        try
        {
            $table = "users";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $data = $collection->findOne(['_id'=>$userId]);
            $email = $data["email"];
            $table = "images";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $data1 = $collection->findOne(['_id'=>$picId]);
            $user = $data1["user_id"];
            if($userId == $user)
            {
                return true;
            }
            $data2 = $collection->findOne(['shared.friend'=>$email]);
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
    function selfCheck($picId,$userId)
    {
        try
        {
            $table = "images";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $data1 = $collection->findOne(['_id'=>$picId]);
            $user = $data1["user_id"];
            if($userId == $user)
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
    function shareLink(Request $request)
    {
        try
        {
            $token = $request->token;
            $picId = new \MongoDB\BSon\ObjectId($request->pic_id);
            $table = "users";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $data = $collection->findOne(['remember_token'=>$token]);
            $userId = $data["_id"];
            $table = "images";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $data1 = $collection->findOne(['_id'=>$picId]);
            $image = $data1["picture"];
            $user = $data1["user_id"];
            if($user == $userId)
            {
                return response()->json(['Image'=> $image]);
            }
            return response()->json(['message'=> "You can't access this picture..."]);
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }        
    }
    function showLink(Request $request)
    {
        try
        {
            $token = $request->token;
            $link = $request->link;
            $table = "users";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $data = $collection->findOne(['remember_token'=>$token]);
            $userId = $data["_id"];
            $table = "images";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $data1 = $collection->findOne(['picture'=>$link]);
            $picId = $data1["_id"];
            $link = explode('/',$link);
            if($data1["access"] == "public")
            {
                $headers = ["Cache-Control" => "no-store, no-cache, must-revalidate, max-age=0"];
                $path = storage_path("app/images".'/'.$link[4]);
                if (file_exists($path)) 
                {
                    return response()->download($path, null, $headers, null);
                }
                return response()->json(["Error"=>"Error downloading file"],400);
            }
            elseif($data1["access"] == "private")
            {
                $check = self::accessable($userId, $picId);
                if($check == true)
                {
                    $headers = ["Cache-Control" => "no-store, no-cache, must-revalidate, max-age=0"];
                    $path = storage_path("app/images".'/'.$link[4]);
                    if (file_exists($path)) 
                    {
                        return response()->download($path, null, $headers, null);
                    }
                    return response()->json(["Error"=>"Error downloading file"],400);
                }
                return response()->json(['message'=> "You can't access this picture..."]);
            }
            else
            {
                $check = self::selfCheck($picId,$userId);
                if($check == true)
                {
                    $headers = ["Cache-Control" => "no-store, no-cache, must-revalidate, max-age=0"];
                    $path = storage_path("app/images".'/'.$link[4]);
                    if (file_exists($path)) 
                    {
                        return response()->download($path, null, $headers, null);
                    }
                    return response()->json(["Error"=>"Error downloading file"],400);
                }
                return response()->json(['message'=> "You can't access this picture..."]);
            }
        }    
        catch(\Exception $show_error)    
        {        
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
}
