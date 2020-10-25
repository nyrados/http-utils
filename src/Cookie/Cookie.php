<?php

namespace Nyrados\Http\Utils\Cookie;

class Cookie
{
    public const 
        DATE_FORMAT = 'D, d M Y H:i:s T',
        ATTRIBUTE_SECURE = 'Secure',
        ATTRIBUTE_HTTP_ONLY = 'HttpOnly',
        ATTRIBUTE_DOMAIN = 'Domain',
        ATTRIBUTE_SAME_SITE = 'SameSite',
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


    
    public function __toString()
    {
        return sprintf('%s=%s', $this->name, $this->value);
    }

    public static function parseSingleCookie(string $value): self
    {
        $explode = explode('=', $value, 1);

        return new self($explode[0], $explode[1]);
    }

    public static function parseCookieLine(string $value): array
    {
        return array_map(
            fn(string $cookie) => self::parseCookieLine($cookie),
            explode('; ', $value)
        );
    }
}