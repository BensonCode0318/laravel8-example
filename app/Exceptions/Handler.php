<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Sentry\State\Scope;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

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
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            if (!method_exists($e, 'useSentry') || $e->useSentry()) {
                if ($this->shouldReport($e) && app()->bound('sentry')) {
                    // 在sentry增加補充資訊
                    $detail = method_exists($e, 'getReason') ? $e->getReason() : [];
                    app('sentry')->configureScope(function (Scope $scope) use ($detail): void {
                        $scope->setContext('detail', $detail);
                        $scope->setTag('reason', implode(',', $detail));
                    });
                    app('sentry')->captureException($e);
                }
            }
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable               $e
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $e)
    {
        // 如果要看錯誤畫面，把這個註解打開
        // return parent::render($request, $e);

        $exceptionData = [];
        if ($e instanceof BadRequestHttpException) {
            $exceptionData = [
                'http_code'  => 400,
                'error_code' => 400,
                'message'    => 'Bad Request'
            ];
        }

        if ($e instanceof UnauthorizedHttpException) {
            $exceptionData = [
                'http_code'  => 401,
                'error_code' => 401,
                'message'    => 'Unauthorized'
            ];
        }

        if ($e instanceof AccessDeniedHttpException) {
            $exceptionData = [
                'http_code'  => 403,
                'error_code' => 403,
                'message'    => 'Access Denied'
            ];
        }

        if ($e instanceof NotFoundHttpException) {
            $exceptionData = [
                'http_code'  => 404,
                'error_code' => 404,
                'message'    => 'Not Found'
            ];
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            $exceptionData = [
                'http_code'  => 405,
                'error_code' => 405,
                'message'    => 'Method Not Allowed'
            ];
        }

        // 模組exception
        if ($e instanceof BaseException) {
            $exceptionData = [
                'http_code'  => $e->getType(),
                'error_code' => $e->getCode(),
                'message'    => $e->getMessage(),
                'detail'     => $e->getReason(),
            ];
        }

        if (!empty($exceptionData)) {
            return $this->exceptionResponse($request, (int) $exceptionData['error_code'], $exceptionData['http_code'], $exceptionData['message'], sprintf("%s, %s, %s", $exceptionData['error_code'], $exceptionData['http_code'], $exceptionData['message']));
        }

        $message = sprintf("%s, %s, %s, %s", $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
        return $this->exceptionResponse($request, (int) 0, 500, '未知的錯誤', $message);
    }

    /**
     * Exception Response
     *
     * @param string  $requestId  RequestId
     * @param integer $httpCode   Http Code
     * @param string  $code       錯誤代碼
     * @param string  $message    錯誤訊息
     * @param string  $reason     錯誤細部原因
     * @param boolean $showReason 是否顯示Reason
     *
     * @return mixed
     */
    private function exceptionResponse($request, int $errorCode, int $httpCode, string $message, string $detail = '')
    {
        $response = [
            "request_id" => $request->requestId,
            "code"       => $errorCode,
            "status"     => false,
            "message"    => $message,
            "detail"     => $detail,
            "response"   => null
        ];

        return response()->json($response, $httpCode);
    }
}
