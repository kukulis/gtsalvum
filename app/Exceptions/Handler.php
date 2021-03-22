<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

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
     * @throws \Throwable
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
        if (  is_a($exception, GtSalvumValidateException::class ) ) {
            /** @var GtSalvumValidateException $validateException */
            $validateException = $exception;
            $response = new JsonResponse([
                'Error'=>$validateException->getMessage(),
                'Messages' => $validateException->getErrorMessages(),
            ]);

            Log::notice('Validation error: '.$exception->getMessage().'   Causes: '.join(",\n", $validateException->getErrorMessages()));

            $response->setStatusCode(Response::HTTP_BAD_REQUEST );
            return $response;
        }
        elseif ( is_a($exception, GtSalvumException::class ) ) {
            Log::error($exception->getMessage());
            $response = new JsonResponse([
                'Error'=>'Server error',
            ]);
            $response->setStatusCode(Response::HTTP_SERVICE_UNAVAILABLE);
            return $response;
        }

        return parent::render($request, $exception);
    }
}
