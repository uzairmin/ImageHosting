<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ConnectionDb;

class AccessAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {//Checking whether user is trying to access his picture or not.
        try
        {
            $token = $request->token;
            $picId = new \MongoDB\BSon\ObjectId($request->pic_id);
            $data = NULL;
            $table = "users";
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $data = $collection->findOne(['remember_token'=>$token]);
            if($data!=NULL)
            {
                $userId = $data["_id"];
                $table = "images";
                $user = new ConnectionDb();
                $collection = $user->setConnection($table);
                $data1 = $collection->findOne(['user_id'=>$userId,'_id'=>$picId]);
                if($data1!=NULL)
                {
                    return $next($request);
                }
                else
                {
                    return response()->json(['message'=> 'Wrong Credential...']);
                }
            }
            else
            {
                return response()->json(['message'=> 'Wrong Credential...']);
            }
        }    
        catch(\Exception $show_error)    
        {   
            return response()->json(['Error' => $show_error->getMessage()], 500);    
        }
    }
}
