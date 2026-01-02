<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SimpleLoggerService;

class SimpleLoggerController extends Controller
{
    public function __construct(public SimpleLoggerService $simpleLogger)
    {
        //throw new \Exception('Not implemented');
    }
    
    public function logMessage(Request $request)
    {
        $message = $request->input('message', 'Default log message');
        $result = $this->simpleLogger->log($message);
        dump($message);

        //return response()->json(['result' => $result]);
    }
}
