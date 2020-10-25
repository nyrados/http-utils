<?php

namespace Nyrados\Http\Utils\Message\Adapter;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class MessageAdapter implements MessageInterface
{
    /** @var MessageInterface */
    protected $target;

    public function __construct(MessageInterface $message)
    {
        $this->target = $message;
    }

    public function hasHeader($name)
    {
        return $this->target->hasHeader($name);
    }

    public function getHeader($name)
    {
        return $this->target->getHeader($name);
    }

    public function getHeaderLine($name)
    {
        return $this->target->getHeaderLine($name);
    }

    public function getBody()
    {
        return $this->response->getBody();
    }

    public function getProtocolVersion()
    {
        return $this->target->getProtocolVersion();
    }

    public function getHeaders()
    {
        return $this->target->getHeaders();
    }

    public function withBody(StreamInterface $body)
    {
        return $this->withTarget($this->target->withBody($body));
    }

    public function withHeader($name, $value)
    {
        return $this->withTarget($this->target->withHeader($name, $value));
    }

    public function withAddedHeader($name, $value)
    {
        return $this->withTarget($this->target->withAddedHeader($name, $value));
    }

    public function withProtocolVersion($version)
    {
        return $this->withTarget($this->target->withProtocolVersion($version));
    }

    public function withoutHeader($name)
    {
        return $this->withTarget($this->target->withoutHeader($name));
    }

    protected function withTarget(MessageInterface $message): self
    {
        $new = clone $this;
        $new->target = $message;

        return $new;  
    }
}