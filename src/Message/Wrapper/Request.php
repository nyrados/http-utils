<?php

namespace Nyrados\Http\Utils\Message\Wrapper;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;

class Request extends Message implements RequestInterface
{
    /** @var RequestInterface */
    protected $target;

    public function __construct(RequestInterface $request)
    {
        $this->target = $request;
    }

    public function getMethod()
    {
        return $this->target->getMethod();
    }

    public function withMethod($method)
    {
        return $this->withTarget($this->target->withMethod($method));
    }

    public function getRequestTarget()
    {
        return $this->target->getRequestTarget();
    }

    public function withRequestTarget($requestTarget)
    {
        return $this->withTarget($this->target->withRequestTarget($requestTarget));
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        return $this->withTarget($this->target->withUri($uri, $preserveHost));
    }

    public function getUri()
    {
        return $this->target->getUri();
    }

    /**
     * Returns the request with form params in the body
     * 
     * It requires a writeable body.
     * You can pass null to $contentType if you dont want to set a content-type 
     *
     * @param array $params
     * @param string|null $contentType
     * @return self
     */
    public function withFormParams(array $params, ?string $contentType = 'application/x-www-form'): self
    {
        return $this->getWithNewBody(http_build_query($params), $contentType);
    }

    /**
     * Returns the request with json in the body
     * 
     * It requires a writeable body.
     * You can pass null to $contentType if you dont want to set a content-type 
     *
     * @param array $params
     * @param int $flags
     * @param string|null $contentType
     * @return self
     */
    public function withJsonBody(array $params, int $flags = JSON_UNESCAPED_SLASHES, ?string $contentType = 'application/json'): self
    {
        return $this->getWithNewBody(json_encode($params, $flags), $contentType);
    }

    private function getWithNewBody(string $content, string $contentType = null): self
    {
        $body = $this->getBody();

        if (!$body->isWritable()) {
            throw new RuntimeException('To set the body on this request, the body must be writeable!');
        }

        $self = $contentType === null ? clone $this : $this->withHeader('Content-Type', $contentType);

        if (strtolower($this->getMethod()) === 'get') {
            $self = $self->withMethod('POST');
        }

        $body->rewind();
        $body->write($content);

        return $self->withBody($body); 
    }
}
