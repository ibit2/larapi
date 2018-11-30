<?php

namespace App\Exceptions;

use Exception;
use http\Exception\RuntimeException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Debug\Exception\FatalErrorException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {

        if(\App::environment(['production'])&&$request->expectsJson()){
            $code=-1;
            $status = 200;
            $msg='error';
            if($exception instanceof MessageException){
                $msg=$exception->getMessage();
            }else if($exception instanceof ValidationException){
                $msg=current(current($exception->errors()));
            }else if($exception instanceof AuthenticationException){
                $code=401;
                $msg='未登录';
            }else if($exception instanceof FatalErrorException){
                $status=500;
            }else if($exception instanceof RuntimeException){
                $status=500;
            }
            $data=[
                'code'=>$code,
                'msg'=>$msg,
            ];

            return response()->json($data,$status);
        }
        return parent::render($request, $exception);
    }
}
