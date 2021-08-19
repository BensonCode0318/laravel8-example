<?php

namespace App\Exceptions;

use App\Constants\ExceptionConstant;
use Throwable;

/**
 * Class AddressException
 *
 * @package App\Exceptions
 */
class ValidatorException extends BaseException
{
    private $errorConfig = [
        '10001' => [
            'type'    => ExceptionConstant::FAILURE,
            'message' => 'Validator error',
            'sentry'  => false,
        ]
    ];

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->setErrorConfig($this->errorConfig);
    }
}
