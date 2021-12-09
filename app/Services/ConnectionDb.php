<?php

namespace App\Services;
use MongoDB\Client as mongo;
use Illuminate\Http\Request;

class ConnectionDb
{    
    public function setConnection($table)    
    {        
        $collection=(new mongo)->ImageHosting->$table;        
        return $collection;    
    }
}