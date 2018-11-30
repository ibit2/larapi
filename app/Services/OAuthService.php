<?php

namespace App\Services;


use App\Exceptions\MessageException;
use Laravel\Passport\Http\Controllers\ConvertsPsrResponses;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;

class OAuthService
{
    use ConvertsPsrResponses;
    protected $server;

    public function __construct(AuthorizationServer $server)
    {
        $this->server = $server;
    }

    public function passwordToken($username, $password)
    {
        $data=[
            "grant_type" => "password",
            "username" => $username,
            "password" => $password
        ];
        return $this->password($data);


    }

    public function passwordRefreshToken($refresh_token){
        $data=[
            "grant_type" => "refresh_token",
            "refresh_token" => $refresh_token,
        ];
        return $this->password($data);
    }

    protected function password($data){

        try {
            $request = \App::make(ServerRequestInterface::class);
            $request = $request->withParsedBody(array_merge($data,[
                "client_id" => 1,
                "client_secret" => "iA7zg1awbhloxHRGJWnr1Omb3sJNx7UOblfaNvxJ",
            ]));
            return json_decode($this->convertResponse($this->server->respondToAccessTokenRequest($request, new Response()))->getContent());

        } catch (OAuthServerException $exception) {
            throw new MessageException($exception->getMessage()."|".$exception->getHint());
        }
    }


}