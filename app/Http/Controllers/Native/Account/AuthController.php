<?php

namespace App\Http\Controllers\Native\Account;

use App\Http\Controllers\Native\BaseController;
use App\Http\Requests\Native\Account\AuthRequest;
use App\Services\AuthService;
use App\Services\OAuthService;
use App\Services\SmsService;


class AuthController extends BaseController
{
    protected $smsService;
    protected $authService;
    protected $oauthService;

    public function __construct(AuthService $authService, SmsService $smsService, OAuthService $OAuthService)
    {
        $this->smsService = $smsService;
        $this->authService = $authService;
        $this->oauthService = $OAuthService;
    }

    /**
     * 登录
     * @param AuthRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(AuthRequest $request)
    {

        $code = $request->input('code');
        $key = $request->input('key');
        //检查验证码
        $phone = $this->smsService->verify($key, $code,1);
        //未注册，自动注册
        $user = $this->authService->getUser($phone);
        //获取token
        $rs=$this->oauthService->passwordToken($user->phone, $user->password);
        //单用户
        \Cache::forever('sigle_user_login'.$user->id,$this->getSnowFlakeId());
        return $this->success($rs);

    }

    /**
     * 刷新token
     * @param AuthRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken(AuthRequest $request)
    {
        return $this->success($this->oauthService->passwordRefreshToken($request->input('refresh_token')));
    }

    /**
     * 退出
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        \Auth::guard('api')->user()->token()->revoke();
        return $this->success(null, '退出成功');
    }

    /**
     * 重置密码
     * @param AuthRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(AuthRequest $request)
    {
        $step = $request->input('step');
        switch ($step) {
            case 1:
                //发送找回密码验证码
                $phone = $request->input('phone');
                return $this->success($this->smsService->send($phone,2));
                break;
            case 2:
                //检查验证码
                $code = $request->input('code');
                $key = $request->input('key');
                $phone = $this->smsService->verify($key, $code,2);
                return $this->success($this->authService->cacheData($phone));
                break;
            case 3:
                //更新密码
                $password = $request->input('password');
                $key = $request->input('key');
                $this->authService->resetPassword($key, $password);
                return $this->success();
                break;
        }

    }

    /**
     * 发送登录验证码
     * @param AuthRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendSmsCode(AuthRequest $request)
    {
        $phone = $request->input('phone');
        return $this->success($this->smsService->send($phone,1));
    }


}
