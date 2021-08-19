<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Exceptions\UserException;
use App\Http\Resources\BaseJsonResource;

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
     * 取得 員工清單
     * @param array $request[
     *      @param string $keyword  搜尋關鍵字
     *]
     * @return json
     */
    public function index(Request $request)
    {
        $validateRule = [
            'keyword' => 'nullable | string',
        ];
        $data = $request->all();
        $this->validateByRule($data, $validateRule);
        $users = $this->userService->getUsers($data);
        return new GetUserIndexCollection($users);
    }

    /**
     * 取得 單一員工資料
     * @param int $userId 員工ID
     *
     * @return json
     */
    public function show(int $userId)
    {
        $validateRule = [
            'userId'    => 'required | int',
        ];
        $data['userId'] = $userId;
        $this->validateByRule($data, $validateRule);
        $user = $this->userService->getOneUser($userId);
        return new GetUserIndexResource($user);
    }

    /**
     * 員工登入，以及取得token
     * @param array $request[
     *    @param string $user_no 員工編號
     *    @param string $password 員工密碼
     *]
     * @return json
     */
    public function login(Request $request)
    {
        $validateRule = [
            'user_no'    => 'required | string',
            'password'   => 'required | string',
        ];
        $data  = $request->all();
        $this->validateByRule($data, $validateRule);
        $token = $this->userService->login($data);
        return new PostUserLoginResource($token);
    }

    /**
     * 員工登出
     * @return json
     */
    public function logout()
    {
        $this->userService->logout();
        return new BaseJsonResource(null);
    }

    /**
     * 建立員工資料
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
    public function store(Request $request)
    {
        $validateRule = [
            'user_no'             => 'required | string',
            'password'            => 'required | string',
            'name'                => 'required | string',
            'phone'               => 'string | nullable',
            'notes'               => 'string | nullable',
            'permission_ids'      => 'string | nullable',
        ];
        $data = $request->all();
        $this->validateByRule($data, $validateRule);
        $this->userService->createUser($data);
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
    public function update(Request $request, int $userId)
    {
        $validateRule = [
            'user_no'           => 'required | string',
            'name'              => 'required | string',
            'phone'             => 'string | nullable',
            'notes'             => 'string | nullable',
            'permission_ids'    => 'string | nullable',
            'userId'            => 'required | int'
        ];
        $data = $request->all();
        $data['userId'] = $userId;
        $this->validateByRule($data, $validateRule);
        $this->userService->updateUser($userId, $data);
        return new BaseJsonResource(null);
    }

    /**
     * 刪除 單一員工
     * @param int $userId 員工ID
     *
     * @return json
     */
    public function delete(int $userId)
    {
        $validateRule = [
            'userId'     => 'required | int'
        ];
        $data['userId'] = $userId;
        $this->validateByRule($data, $validateRule);
        $this->userService->deleteUser($userId);
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
