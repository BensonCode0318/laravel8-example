<?php

namespace App\Services;

use DB;
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

    /**
     * get user list
     * @param array $data
     * @return object
     */
    public function getUserList(array $data)
    {
        $filter = [
            'limit' => $data['limit'] ?? env('PAGE_LIMIT')
        ];
        return $this->userRepo->listByFilter($filter);
    }

    /**
     * get user
     * @param int $userId
     * @return object
     */
    public function getUser(int $userId)
    {
        $filter = [
            'id'    => $userId
        ];
        return $this->userRepo->listByFilter($filter);
    }

    /**
     * create user
     * @param array $data
     * @return object
     */
    public function createUser(array $data)
    {
        $userData = [
            'account'  => $data['account'],
            'password' => $data['password'],
            'name'     => $data['name'],
            'phone'    => $data['phone'] ?? null,
            'email'    => $data['email'] ?? null,
        ];

        DB::beginTransaction();
        try {
            $this->userRepo->create($userData);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->exception->error(20003, $e->getMessage());
        }
    }

    /**
     * update user by id
     * @param int $userId
     */
    public function updateUserById(array $data)
    {
        $userData = [
            'name'     => $data['name'],
            'phone'    => $data['phone'],
            'email'    => $data['email'],
        ];

        DB::beginTransaction();
        try {
            $this->userRepo->updateById($data['id'], $userData);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->exception->error(20004, $e->getMessage());
        }
    }

    /**
     * delete user by id
     * @param int $id
     */
    public function deleteUserById(int $id)
    {
        DB::beginTransaction();
        try {
            $this->userRepo->deleteById($id);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->exception->error(20004, $e->getMessage());
        }
    }
}
