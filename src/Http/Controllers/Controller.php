<?php

namespace Sourcya\BoilerplateBox\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $message,$statusCode;

    public function errorResponseHandler($message,$statusCode)
    {
        $this->message = $message;
        $this->statusCode = $statusCode;

        return response()->json([
            'status' => 'error',
            'message' => $this->message
        ], $this->statusCode);
    }
}
