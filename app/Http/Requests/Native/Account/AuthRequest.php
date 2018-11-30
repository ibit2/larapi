<?php

namespace App\Http\Requests\Native\Account;


use App\Http\Requests\Request;

class AuthRequest extends Request
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $actionMethod = $this->route()->getActionMethod();
        switch ($actionMethod) {
            case "login":
                return [
                    'phone' => 'required',
                    'code' => 'required',
                    'key' => 'required',
                ];
                break;
            case "refreshToken":
                return [
                    'refresh_token' => 'required',
                ];
                break;
            case "sendSmsCode":
                return [
                    'phone' => 'required',
                ];
                break;
            case "resetPassword":
                $rules = [
                    'step' => 'required|integer|in:1,2,3',

                ];
                switch ($this->input('step')) {
                    case 1:
                        $rules['phone'] = 'required|string';
                        break;
                    case 2:
                        $rules['code'] = 'required|string';
                        $rules['key'] = 'required|string';
                        break;
                    case 3:
                        $rules['key'] = 'required|string';
                        $rules['password'] = 'required|string';
                        break;
                }
                return $rules;
                break;

        }
    }

    public function attributes()
    {
        $actionMethod = $this->route()->getActionMethod();
        switch ($actionMethod) {
            case "login":
                return [
                    'phone' => '手机号',
                ];
                break;
            case "refreshToken":
               return [];
                break;
            case "sendSmsCode":
                return [
                    'phone' => '手机号',
                ];
                break;
            case "resetPassword":
                return [];

                break;
        }
    }
}
