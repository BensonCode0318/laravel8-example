<?php

namespace App\Exceptions;

use App\Constants\ExceptionConstant;
use Exception;
use ReflectionClass;
use ReflectionException;
use Throwable;

/**
 * Class BaseException
 *
 * @package App\Exceptions
 */
class BaseException extends Exception
{
    private $type;
    private $reason;
    private $errorConfig;
    private $sentry;

    /**
     * 異常處理
     *
     * @param mixed     $code     異常代碼
     * @param mixed     $reason   拋出的異常訊息內容 (string or key-value Array)
     * @param Throwable $previous 前一個異常
     * @param integer   $type     錯誤類別
     *
     * @return void 異常資料
     *
     * @throws BaseException
     * @throws ReflectionException
     */
    public function error($code, $reason = "", Throwable $previous = null)
    {
        !is_array($reason) && $reason = ['reason' => $reason];

        // TODO: 從config去讀取error code設定
        $this->code   = $code;
        $this->reason = $reason;
        $this->setPrevious($previous);
        $data          = $this->errorConfig[$code] ?? null;
        $this->message = $data['message'] ?? "未知的錯誤";
        $this->type    = $data['type'] ?? ExceptionConstant::FAILURE;
        $this->sentry  = $data['sentry'] ?? true;

        throw $this;
    }

    /**
     * @return mixed
     */
    public function useSentry()
    {
        return $this->sentry;
    }

    /**
     * @param $config
     */
    public function setErrorConfig($config)
    {
        $this->errorConfig = $config;
    }

    /**
     * @param $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }

    /**
     * 取得ExceptionType
     *
     * @return string exceptionType
     */
    public function getReason()
    {
        if (empty($this->reason)) {
            return "";
        }

        return $this->reason;
    }

    /**
     * 設定ExceptionType
     *
     * @param integer $type ExceptionType
     *
     * @return void
     */
    public function setType(int $type)
    {
        $this->type = $type;
    }

    /**
     * 取得ExceptionType
     *
     * @return string exceptionType
     */
    public function getType()
    {
        if (empty($this->type)) {
            return ExceptionConstant::FAILURE;
        }

        return $this->type;
    }

    /**
     * 設定前一個Exception
     *
     * @param mixed $previous 異常鏈中的前一個異常
     *
     * @return void
     * @throws ReflectionException
     */
    public function setPrevious($previous)
    {
        if (empty($previous)) {
            return;
        }

        $reflection = new ReflectionClass($this);

        while (!$reflection->hasProperty('previous')) {
            $reflection = $reflection->getParentClass();
        }

        $prop = $reflection->getProperty('previous');
        $prop->setAccessible('true');
        $prop->setValue($this, $previous);
        $prop->setAccessible('false');
    }
}
