<?php

use DateTime;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Nyrados\Http\Utils\Cookie\ServerCookie;
use Nyrados\Http\Utils\Message\Adapter\ResponseAdapter;

require './vendor/autoload.php';

$s = new ServerCookie('test', '123');


$s = $s
    ->withExpireDate(new DateTime('2021-01-01'))
    ->withAttribute('domain-test', '123');

var_dump ( ServerCookie::fromHeaderLine( (string) $s) );

var_dump ( (string) $s);


$response = new ResponseAdapter(new Response());

$response = $response->withCookie(new ServerCookie('test', '123'));
$response = $response->withCookie(new ServerCookie('abc', 'tests'));

var_dump ($response->withoutCookie('abc'));
