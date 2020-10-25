<?php
namespace Nyrados\Http\Utils\Message\Adapter;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;

class RequestAdapter extends MessageAdapter implements RequestInterface
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

    public function withFormParams(array $params, ?string $contentType = 'application/x-www-form')
    {
        return $this->getWithNewBody($contentType, http_build_query($params));
    }

    public function withJsonBody(array $params, int $flags = JSON_UNESCAPED_SLASHES, ?string $contentType = 'application/json')
    {
        return $this->getWithNewBody($contentType, json_encode($params, $flags));
    }

    private function getWithNewBody(?string $contentType, string $content): self
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
