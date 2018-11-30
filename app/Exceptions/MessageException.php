<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;


class MessageException extends HttpException
{
    public function __construct($message)
    {
        parent::__construct(200, $message);
    }

}