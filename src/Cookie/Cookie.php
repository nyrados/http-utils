<?php

namespace Nyrados\Http\Utils\Cookie;

use DateTime;

class Cookie
{
    public const 
        DATE_FORMAT = 'D, d M Y H:i:s T', 
        ATTRIBUTE_HTTP_ONLY = 'HttpOnly',
        ATTRIBUTE_SAME_SITE = 'SameSite',
        ATTRIBUTE_EXPIRES = 'Expires',
        ATTRIBUTE_MAX_AGE = 'Max-Age',
        ATTRIBUTE_SECURE = 'Secure',
        ATTRIBUTE_DOMAIN = 'Domain',
        ATTRIBUTE_PATH = 'Path'
    ;

    protected $name;
    protected $value;

    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Returns name of the cookie
     *
     * @return string
     */
    final public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns value of the cookie
     *
     * @return string
     */
    final public function getValue(): string
    {
        return $this->value;
    }
    
    /**
     * Returns string representation
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name . '=' . $this->value;
    }

    /**
     * Parses a Cookie (name=value) to a cookie object
     *
     * @param string $value
     * @return self
     */
    public static function parseSingleCookie(string $value): self
    {
        $explode = explode('=', $value, 2);

        return new self($explode[0], $explode[1]);
    }

    /**
     * Parses cookie line (name=value; name2=value2) to an array of cookies
     * 
     * Can be used for the cookie header in a request 
     *
     * @param string $value
     * @return array<self>
     */
    public static function parseCookieLine(string $value): array
    {
        return array_map(
            fn(string $cookie) => self::parseCookieLine($cookie),
            explode('; ', $value)
        );
    }
}