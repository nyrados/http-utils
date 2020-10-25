<?php

namespace Nyrados\Http\Utils\Middleware;

use Nyrados\Http\Utils\Handler\ResponseHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

trait InvokeableMiddlewareTrait
{
    abstract public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface;

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->process($request, new ResponseHandler($response));
    }
}