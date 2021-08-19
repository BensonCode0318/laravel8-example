<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Exceptions\UserException;

class UserService
{
    private $userRepo;
    private $exception;

    public function __construct(
        UserRepository $userRepo,
        UserException $exception
    ) {
        $this->userRepo = $userRepo;
        $this->exception = $exception;
    }

    /**
     * verify user data
     * @param array $data [
     *    @param string account
     *    @param string password
     * ]
     *
     * @return array
     */
    public function verifyUser(array $data)
    {
        if (!$token = auth('api')->attempt($data)) {
            $this->exception->error(20001);
        }

        return [
            'access_token' => $token,
            'user'         => auth('api')->user()
        ];
    }

    /**
     * 登出
     * @return null
     */
    public function logout()
    {
        try {
            auth('api')->logout();
        } catch (\Exception $e) {
            $this->exception->error(20002);
        }
    }
}
