<?php

namespace Nyrados\Http\Utils\Factory\Guzzle;

use GuzzleHttp\Psr7\ServerRequest;
use Nyrados\Http\Utils\IncomingRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

class IncomingRequestFactory extends GuzzleFactory implements IncomingRequestFactoryInterface
{
    public function createIncomingRequest(): ServerRequestInterface
    {
        return ServerRequest::fromGlobals();
    }
}