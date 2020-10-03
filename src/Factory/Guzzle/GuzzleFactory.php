<?php

namespace Nyrados\Http\Utils\Factory\Guzzle;

use Exception;
use GuzzleHttp\Psr7\ServerRequest;

abstract class GuzzleFactory
{
    public function __construct()
    {
        if (!class_exists(ServerRequest::class)) {
            throw new Exception('To use a Guzzlefactory you need to add guzzlehttp/psr-7 to your project');
        }
    }
}