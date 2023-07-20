<?php

namespace App\Exceptions;

use Throwable;
use InvalidArgumentException;
use Encore\Admin\Facades\Admin;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

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
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // dd($request->isJson());
        if ($exception instanceof InvalidArgumentException) {
            logger($exception);
            if ($request->isJson()) {
                return response()->json(['status'=> -5 ,'message' => 'check Request.'], 200);
            } else {
                return abort(404);
            }
        }
        if ($exception instanceof MethodNotAllowedHttpException || $exception instanceof UnauthorizedHttpException) {
            if ($request->isJson()) {
                return response()->json(['status'=> -5 ,'message' => 'Unauthenticated.'], 200);
            } else {
                return abort(404);
            }
        }
        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return redirect()->guest('/admin/auth/login');
    }
}
