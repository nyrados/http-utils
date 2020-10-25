<?php

namespace Nyrados\Http\Utils\Factory\Guzzle;

use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class StreamFactory  extends GuzzleFactory implements StreamFactoryInterface
{
    public function createStream(string $content = ''): StreamInterface
    {
        $stream = fopen('php://temp', 'r+');
        
        if ($content !== '') {
            fwrite($stream, $content);
            fseek($stream, 0);
        }

        return $this->createStreamFromResource($stream);
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        return $this->createStreamFromResource(fopen($filename, $mode));
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        return new Stream($resource);
    }
}