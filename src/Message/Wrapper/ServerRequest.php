<?php

namespace Nyrados\Http\Utils\Message\Adapter;

use Nyrados\Http\Utils\Message\Wrapper\Request;
use Psr\Http\Message\ServerRequestInterface;

class ServerRequest extends Request implements ServerRequestInterface
{
    /** @var ServerRequestInterface */
    protected $target;

    public function __construct(ServerRequestInterface $request)
    {
        parent::__construct($request);
    }

    public function getAttributes()
    {
        return $this->target->getAttributes();
    }

    public function getAttribute($name, $default = null)
    {
        return $this->target->getAttribute($name, $default);
    }

    public function getParsedBody()
    {
        return $this->target->getParsedBody();
    }

    public function getServerParams()
    {
        return $this->target->getServerParams();
    }

    public function getCookieParams()
    {
        return $this->target->getCookieParams();
    }
    
    public function getUploadedFiles()
    {
        return $this->target->getUploadedFiles();
    }

    public function getQueryParams()
    {
        return $this->target->getQueryParams();
    }

    public function withAttribute($name, $value)
    {
        return $this->withTarget($this->target->withAttribute($name, $value));
    }

    public function withoutAttribute($name)
    {
        return $this->withTarget($this->target->withoutAttribute($name));
    }

    public function withCookieParams(array $cookies)
    {
        return $this->withTarget($this->target->withCookieParams($cookies));
    }

    public function withQueryParams(array $query)
    {
        return $this->withTarget($this->target->withQueryParams($query));
    }

    public function withUploadedFiles(array $uploadedFiles)
    {
        return $this->withTarget($this->target->withUploadedFiles($uploadedFiles));
    }
    
    public function withParsedBody($data)
    {
        return $this->withTarget($this->target->withParsedBody($data));
    }
}
