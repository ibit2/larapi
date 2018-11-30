<?php

namespace App\Services;

use App\Exceptions\MessageException;
use App\Foundations\SnowFlake;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;


class SmsService
{
    protected $easySms;
    protected $snowFlake;

    public function __construct()
    {
        $this->easySms = new EasySms(config('easysms'));
        $this->snowFlake = new SnowFlake();

    }

    public function send($phone,$type)
    {
        try {
            $code=123456;
            $time = 30;
            if(\App::environment(['production'])){
                $code = mt_rand(100000, 999999);
                $this->easySms->send($phone, [
                    'content' => "【bbs社区】您的验证码是{$code}。本验证码{$time}分钟内有效",
                ]);

            }
            $snowId = $this->snowFlake->snowId();
            $key = "code_".$snowId;
            \Cache::add($key, compact('code', 'phone','type'), $time);
            return [
                'key' => $key,
            ];

        } catch (NoGatewayAvailableException $exception) {
            throw new MessageException('短信异常');
        }

    }

    function verify($key, $code,$type)
    {
        $sms = \Cache::get($key);
        $smsCode = $sms['code'];
        $smsPhone = $sms['phone'];
        $smsType = $sms['type'];
        if (empty($sms)||$smsType!=$type) {
            throw new MessageException('验证码失效');
        }
//        if ($smsPhone != $phone) {
//            throw new MessageException('手机号码不一致');
//        }
        if (!hash_equals((string)$smsCode, $code)) {
            throw new MessageException('验证码错误');
        }
        \Cache::forget($key);

        return $smsPhone;
    }


}