<?php

namespace App\Services;

use App\Exceptions\MessageException;
use App\Foundations\SnowFlake;
use App\Repositories\UserRepository;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUser($phone)
    {
        $user = $this->userRepository->findBy('phone', $phone);
        if (empty($user)) {
            $user = $this->userRepository->create([
                'phone' => $phone,
                'password' => bcrypt(str_random()),
            ]);
        }
        return $user;
    }

    public function cacheData($data, $minutes = 30)
    {
        $key = "key_" . (new SnowFlake())->snowId();
        \Cache::add($key, $data, $minutes);
        return compact('key');
    }

    public function resetPassword($key, $password)
    {

        $phone = \Cache::get($key);
        if (empty($phone)) {
            throw new MessageException('key过期');
        }
        return $this->userRepository->updateBy('phone', $phone, [
            'password'=>bcrypt($password)
        ]);
    }


}