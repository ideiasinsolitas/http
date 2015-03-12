# http

HTTP abstraction

The purpose of this package is to abstract away PHP`s superglobals $_SERVER, $_POST and $_GET and sending of response headers. Use Enviroment and Request to parse an HTTP request and the use a Response object to create and send Header objects and terminate the response.

CookieJar and Session objects come with native encryption and will abstract the use of $_SESSION and $_COOKIE vars.

`php

<?php

$env = new Enviroment();
$req = new Request($env);

$res = new Response();
$res->write('test...');
$res->send();

