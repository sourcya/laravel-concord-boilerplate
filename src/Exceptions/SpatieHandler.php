<?php

namespace Sourcya\BoilerplateBox\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class SpatieHandler extends ExceptionHandler
{
    public function render($request, Exception $exception)
    {
        if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error','message' => $exception->getMessage()], 401);
            }
        }
        return parent::render($request, $exception);
    }
}
