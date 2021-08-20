<?php

namespace App\Exceptions;

use App\Constants\ExceptionConstant;
use Throwable;

/**
 * Class AddressException
 *
 * @package App\Exceptions
 */
class UserException extends BaseException
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
        '20003' => [
            'type'    => ExceptionConstant::FAILURE,
            'message' => '新增會員失敗',
            'sentry'  => false,
        ],
        '20004' => [
            'type'    => ExceptionConstant::FAILURE,
            'message' => '更新會員失敗',
            'sentry'  => false,
        ],
        '20005' => [
            'type'    => ExceptionConstant::FAILURE,
            'message' => '刪除會員失敗',
            'sentry'  => false,
        ],
    ];

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->setErrorConfig($this->errorConfig);
    }
}
