<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Exceptions\UserException;
use App\Http\Resources\PostLoginResource;

class LoginController extends Controller
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
     * 登入
     * @return json
     */
    public function login(Request $request)
    {
        $inputData    = $request->all();
        $validateRule = [
            'account'  => 'required|string',
            'password' => 'required|string',
        ];
        $this->validateByRule($inputData, $validateRule);

        $loginData = $this->userService->verifyUser($inputData);

        return new PostLoginResource($loginData);
    }

    /**
     * 登出
     * @return BaseJsonResource
     */
    public function logout()
    {
        $this->userService->logout();
        return new BaseJsonResource(null);
    }
}
