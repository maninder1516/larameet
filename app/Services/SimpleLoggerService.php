<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SimpleLoggerService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function log($message){
        Log::info("Simple LoggerService :".$message);

        return 'Logged: '.$message;
    }
}
