<?php

namespace Nyrados\Http\Utils\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

class RangeMiddleware implements MiddlewareInterface
{

    use InvokeableMiddlewareTrait;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $body = $response->getBody();
        $size = $body->getSize();

        if (!$body->isReadable() || !$body->isSeekable()) {
            throw new RuntimeException('RangeMiddleware expects a writeable & seekable response body');
        }

        $response = $response->withHeader('Accept-Ranges', '0-' . $size);

        if (!$request->hasHeader('Range')) {
            return $response
                ->withHeader('Content-Range', 'bytes ' . sprintf('0-%d/%d', $size - 1, $size))
                ->withHeader('Content-Length', $size);
        }

        if (
            null === ($chunk = $this->parseRange($request, $size)) ||
            $chunk['start'] > $chunk['end'] || $chunk['start'] > $size - 1 || $chunk['end'] >= $size
        ) {
            return $this->rangeNotSatisfiable($response);
        }

        $body->seek($chunk['start']);

        return $response
            ->withStatus(206)
            ->withHeader('Content-Range', 'bytes ' . sprintf('%d-%d/%d', $chunk['start'], $chunk['end'], $size))
            ->withHeader('Content-Length', $chunk['end'] - $chunk['start'] + 1);
    }

    /**
     * Parses range header
     *
     * @param ServerRequestInterface $request
     * @param int $size
     * @return array<int>|null
     */
    private function parseRange(ServerRequestInterface $request, int $size): ?array
    {
        if (0 === preg_match('/^(?<unit>[a-z]+)=(?<start>\d*)-(?<end>\d*)$/', $request->getHeaderLine('Range'), $range)) {
            return null;
        }

        return [
            'start' => isset($range['start'])
                ? $range['start']
                : $size - $range['start'],

            'end' => is_numeric($range['end'])
                ? $range['end']
                : $size - 1
        ];
    }

    /**
     * Sets HTTP 416 Response code with header
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    private function rangeNotSatisfiable(ResponseInterface $response): ResponseInterface
    {
        $size = $response->getBody()->getSize();

        return $response
            ->withStatus(416)
            ->withHeader('Content-Range', 'bytes 0-' . ($size - 1) . '/' . $size);
    }
}
