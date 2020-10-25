# Http Utils
Utils for working with HTTP in PHP.

## Response Dumper
Dump PSR-7 responses into the output stream.

```php
<?php

use Nyrados\Http\Utils\ResponseDumper;

$dump = new ResponseDumper($response);

// Usage:
$dump->dumpHeaders();
$dump->dumpBody();

// Or:
$dump->dump();
```

## Working with Middlewares

### InvokeableMiddlewareTrait

```php
<?php

use Nyrados\Http\Utils\Middleware\InvokeableMiddlewareTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MyMiddleware
{
    use InvokeableMiddlewareTrait;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        //...

        return $handler->handle($request);
    } 
}

$middleware = new MyMiddlware();

// Access your middlware without a request handler, via invoking your middleware

$response = $middleware($request, $response);
```

### RangeMiddleware

Use The Middleware `Nyrados\Http\Utils\Middleware\RangeMiddleware` in your dispatcher, to send an `Accept-Range` header and parse the `Range` Header from your client.



