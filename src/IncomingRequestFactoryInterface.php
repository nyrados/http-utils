<?php

namespace Nyrados\Http\Utils;

use Psr\Http\Message\ServerRequestInterface;

interface IncomingRequestFactoryInterface
{
    /**
     * Retrieve the current request from globals 
     *
     * @return ServerRequestInterface
     */
    public function createIncomingRequest(): ServerRequestInterface;
}