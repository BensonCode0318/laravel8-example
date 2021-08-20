<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Exceptions\UserException;
use App\Http\Resources\BaseJsonResource;
use App\Http\Resources\GetUserResource;
use App\Http\Resources\GetUserListCollection;

class UserController extends Controller
{
    private $userService;
    private $exception;

    public function __construct(
        UserService $userService,
        UserException $exception
    ) {
        $this->userService = $userService;
        $this->exception   = $exception;
    }

    /**
     * get user list
     * @return json
     */
    public function index(Request $request)
    {
        $inputData    = $request->all();
        $validateRule = [
            'limit' => 'sometimes|int',
        ];
        $this->validateByRule($inputData, $validateRule);

        $users = $this->userService->getUsers($inputData);
        return new GetUserListCollection($users);
    }

    /**
     * get user
     * @param int $userId 員工ID
     *
     * @return json
     */
    public function show(int $userId)
    {
        $validateRule = [
            'user_id'    => 'required|int',
        ];
        $this->validateByRule([
            'user_id' => $userId
        ], $validateRule);

        $user = $this->userService->getUser($userId);
        return new GetUserResource($user);
    }


    /**
     * create user
     * @return BaseJsonResource
     */
    public function create(Request $request)
    {
        $inputData    = $request->all();
        $validateRule = [
            'account'  => 'required|string',
            'password' => 'required|string',
            'name'     => 'required|string',
            'phone'    => 'string|nullable|regex:/^09[0-9]{8}$/',
            'email'    => 'string|nullable|regex:/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9_-]+)/gi',
        ];
        $this->validateByRule($inputData, $validateRule);
        
        $this->userService->createUser($inputData);
        return new BaseJsonResource(null);
    }

    /**
     * update user
     * @param int $userId
     * @return BaseJsonResource
     */
    public function update(Request $request, int $userId)
    {
        $inputData       = $request->all();
        $inputData['id'] = $userId;

        $validateRule = [
            'id'    => 'required|integer',
            'name'  => 'required|string',
            'phone' => 'string|nullable|regex:/^09[0-9]{8}$/',
            'email' => 'string|nullable|regex:/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9_-]+)/gi',
        ];
        $this->validateByRule($inputData, $validateRule);

        $this->userService->updateUserById($inputData);
        return new BaseJsonResource(null);
    }

    /**
     * delete user by id
     * @param int $userId 員工ID
     *
     * @return BaseJsonResource
     */
    public function delete(int $userId)
    {
        $inputData['id'] = $userId;
        $validateRule = [
            'id' => 'required|integer'
        ];
        $this->validateByRule($inputData, $validateRule);
        $this->userService->deleteUserById($inputData['id']);
        return new BaseJsonResource(null);
    }

    /**
     * 建立員工資料
     * @param int $userId 員工ＩＤ
     * @param array $request[
     *    @param string $user_no 員工編號
     *    @param string $password 員工密碼
     *    @param string $name 員工姓名
     *    @param string $phone 員工手機
     *    @param string $notes 員工備註
     *    @param string $auth 員工權限
     *]
     * @return json
     */
    public function updatePassword(Request $request, int $userId)
    {
        $validateRule = [
            'password'          => 'required | string',
            'userId'            => 'required | int'
        ];
        $data = $request->all();
        $data['userId'] = $userId;
        $this->validateByRule($data, $validateRule);
        $this->userService->updateUserPasswordById($userId, $data);
        return new BaseJsonResource(null);
    }
}
