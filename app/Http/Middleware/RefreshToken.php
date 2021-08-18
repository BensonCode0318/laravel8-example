<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\JWTAuth;

use App\Exceptions\AuthException;

class RefreshToken extends BaseMiddleware
{
    protected $exception;
    protected $auth;
    public function __construct(
        AuthException $exception,
        JWTAuth $auth
    ) {
        parent::__construct($auth);
        $this->exception = $exception;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // 檢查此次請求中是否帶有 token，如果沒有則丟擲異常。
        try {
            $this->checkForToken($request);
        } catch (\Throwable $e) {
            $this->exception->error(903);
        }
        // 判斷token是否在有效期內
        try {
            if ($this->auth->parseToken()->authenticate()) {
                return $next($request);
            }
        } catch (TokenExpiredException $e) {
            try {
                // 重新整理使用者的 token
                $token = $this->auth->refresh();
                // 使用一次性登入以保證此次請求的成功
                Auth::guard($guard)->onceUsingId($this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub']);
                return $this->setAuthenticationHeader($next($request), $token);
            } catch (JWTException $e) {
                // 如果捕獲到此異常，即代表 refresh 也過期了，使用者無法重新整理令牌，需要重新登入。
                // 驗證過期，請重新登入。
                $this->exception->error(902);
            }
        } catch (JWTException $e) {
            // 驗證異常，請重新登入
            $this->exception->error(901);
        }
    }

    protected function setAuthenticationHeader($response, $token = null)
    {
        $token = $token ?: $this->auth->refresh();
        $response->headers->set('Authorization', 'Bearer '.$token);
        $response->headers->set('Access-Control-Expose-Headers', 'Authorization');

        return $response;
    }
}
