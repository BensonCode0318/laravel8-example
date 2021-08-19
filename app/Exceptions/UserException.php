<?php

namespace App\Exceptions;

use App\Constants\ExceptionConstant;
use Throwable;

/**
 * Class AddressException
 *
 * @package App\Exceptions
 */
class AuthException extends BaseException
{
    private $errorConfig = [
        '20001' => [
            'type'    => ExceptionConstant::FAILURE,
            'message' => '登入失敗，請檢查帳號密碼是否正確',
            'sentry'  => false,
        ],
        '20002' => [
            'type'    => ExceptionConstant::FAILURE,
            'message' => '登出失敗',
            'sentry'  => false,
        ],
    ];

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->setErrorConfig($this->errorConfig);
    }
}
