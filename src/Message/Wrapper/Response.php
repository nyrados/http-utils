<?php

namespace Nyrados\Http\Utils\Message\Wrapper;

use Nyrados\Http\Utils\Cookie\ServerCookie;
use Psr\Http\Message\ResponseInterface;

class Response extends Message implements ResponseInterface
{

    /** @var ResponseInterface */
    protected $target;

    /** @var array<string, ServerCookie>  */
    private $cookies = [];

    public function __construct(ResponseInterface $response)
    {
        parent::__construct($response);
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        return $this->withTarget($this->target->withStatus($code, $reasonPhrase));
    }

    public function getStatusCode()
    {
        return $this->target->getStatusCode();
    }

    public function getReasonPhrase()
    {
        return $this->target->getStatusCode($this->target);
    }

    /**
     * Returns response with new a cookie. 
     * 
     * It is added to the header list of the response
     *
     * @param ServerCookie $cookie
     * @return static
     */
    public function withCookie(ServerCookie $cookie): self
    {
        $new = $this->withAddedHeader('Set-Cookie', (string) $cookie);
        $new->cookies[$cookie->getName()] = $cookie;
        
        return $new;
    }

    /**
     * Returns response with
     *
     * @param string $cookie
     * @return static
     */
    public function withRemovedCookie(string $cookie): self	
    {
        return $this->withCookie((new ServerCookie($cookie, ''))->asExpired());
    }

    /**
     * Returns response without a specific cookie.
     * 
     * To remove a cookie for the client use withRemovedCookie()
     *
     * @param string $name
     * @return static
     */
    public function withoutCookie(string $name): self
    {
        $self = $this->withoutHeader('Set-Cookie');

        if(isset($self->cookies[$name])) {
            unset($self->cookies[$name]);
        }

        foreach ($this->getHeader('Set-Cookie') as $header) {
            
            if( (bool) preg_match('/^(?<name>[\w]+)=/', $header, $match)) {
                if ($match['name'] === $name) {
                    continue;
                }
            }

            $self = $self->withAddedHeader('Set-Cookie', $header);
        }

        return $self;
    }

    /**
     * Returns all Cookies that were added via withCookie()
     *
     * @return array<string, ServerCookie>
     */
    public function getCookies(): array
    {
        return $this->cookies;
    }

    /**
     * Returns true if status code is 4xx or 5xx
     *
     * @return boolean
     */
    public function isError(): bool
    {
        return $this->isClientError() || $this->isServerError();
    }

    /**
     * Returns true if status code is 5xx
     *
     * @return boolean
     */
    public function isServerError(): bool
    {
        return ((string) $this->getStatusCode())[0] === '5';
    }

    /**
     * Returns true if status code is 4xx
     *
     * @return boolean
     */
    public function isClientError(): bool
    {
        return ((string) $this->getStatusCode())[0] === '4';
    }

    /**
     * Returns true if status code is 3xx
     *
     * @return boolean
     */
    public function isRedirect(): bool
    {
        return ((string) $this->getStatusCode())[0] === '3';
    }

    /**
     * Returns true if status code is 2xx
     *
     * @return boolean
     */
    public function isSucess(): bool
    {
        return ((string) $this->getStatusCode())[0] === '2';
    }
}