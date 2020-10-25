<?php

namespace Nyrados\Http\Utils\Cookie;

use DateInterval;
use DateTime;
use LogicException;

class ServerCookie extends Cookie
{

    /** @var DateTime|null */
    private $expires = null;

    /** @var DateInterval|null */
    private $maxAge = null;

    /** @var array<string, string> */
    private $attributes = [];


    public function __construct(string $name, string $value)
    {
        parent::__construct($name, $value);
    }

    /**
     * Checks if cookie is a session cookie
     *
     * @return boolean
     */
    public function isSessionCookie(): bool
    {
        return $this->expires === null && $this->maxAge === null;
    }

    /**
     * Checks if attribute is set
     * 
     * The attribute name is case-insetive
     *
     * @param string $name
     * @return boolean
     */
    public function hasAttribute(string $name): bool
    {
        return isset($this->attributes[self::normalizeAttributeName($name)]);
    }

    /**
     * Returns attribute value
     * 
     * The attribute name is case-insetive
     *
     * @param string $name
     * @return string
     */
    public function getAttribute(string $name): string
    {
        if (!$this->hasAttribute($name)) {
            throw new LogicException(sprintf("Attribute '%s' is not aviable", $name));
        }

        return $this->attributes[self::normalizeAttributeName($name)];
    }

    /**
     * Returns new instance with new attribute
     *
     * @param string $name
     * @param string $value
     * @return static
     */
    public function withAttribute(string $name, string $value = ''): self
    {
        $new = clone $this;
        $new->attributes[self::normalizeAttributeName($name)] = $value;

        return $new;
    }

    /**
     * Returns new instance with "Max-Age" attribute and removes "Expire" attribute
     *
     * @param string $name
     * @param string $value
     * @return static
     */
    public function withMaxAge(DateInterval $interval): self
    {
        $new = clone $this;
        $new->maxAge = $interval;
        $new->expires = null;

        return $new;
    }

    /**
     * Returns new instance with "Expire" attribute and removes "Max-Age" attribute
     *
     * @param DateTime $expires
     * @return static
     */
    public function withExpireDate(DateTime $expires): self
    {
        $new = clone $this;
        $new->expires = $expires;
        $new->maxAge = null;

        return $new;
    }

    /**
     * Returns new instance for an expired cookie 
     *
     * @return static
     */
    public function asExpired(): self
    {
        return $this->withExpireDate((new DateTime())->setTimestamp(1));
    }

    /**
     * Returns Cookie as string representaion
     * 
     * It can be used as Set-Cookie header value
     *
     * @return string
     */
    public function __toString()
    {
        $string = sprintf("%s=%s", $this->name, $this->value);

        if ($this->expires !== null) {
            $string .= '; Expires=' . $this->expires->format(Cookie::DATE_FORMAT);
        } else if ($this->maxAge !== null) {
            $string .= '; Max-Age=' . $this->maxAge->format('s');
        }

        foreach ($this->attributes as $attribute => $value) {
            $string .= '; ' . $attribute .  ($value === '' ? '' : '=' . $value);
        }

        return $string;
    }

    /**
     * Parses a Set-Cookie header.
     *
     * @param string $headerLine
     * @return self
     */
    public static function fromHeaderLine(string $headerLine): self
    {
        $elements = explode('; ', $headerLine);
        $general = explode('=', $elements[0], 2);

        unset($elements[0]);

        $self = new self($general[0], $general[1]);

        foreach ($elements as $attribute) {
            
            $data = explode('=', $attribute, 2);
            $name = self::normalizeAttributeName($data[0]);

            if ($name === 'Max-Age') {
                $self = $self->withMaxAge(new DateInterval('PT' . $data[1] . 'S'));
                continue;
            }

            if ($name === 'Expires') {
                $self = $self->withExpireDate(DateTime::createFromFormat(Cookie::DATE_FORMAT, $data[1]));
                continue;
            }

            $self = $self->withAttribute($name,  $data[1] ?? '');
        }

        return $self;
    }

    /**
     * Normalize casing of attribute names. 
     * 
     * e.g.:
     * max-age => Max-Age
     * domain => Domain
     *
     * @param string $name
     * @return string
     */
    public static function normalizeAttributeName(string $name): string
    {
        return implode('-', array_map(fn(string $value) => ucfirst($value),
            explode('-', $name)
        ));
    }
}