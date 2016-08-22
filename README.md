HTTP
====
Alexya's HTTP components

Contents
--------

 - [Request](#request)
 - [Response](#response)

<a name="request"></a>
Request
-------

The class `\Alexya\Http\Request` offers a wrapper for the request supergloblas (`$_GET`, `$_POST`...).

You can retrieve the request with the method `main`.

The constructor accepts the following parameters:

 * A string being the requested URI.
 * An array being GET parameters.
 * An array being POST parameters.
 * An array being COOKIE parameters.
 * An array being SERVER parameters.

Example:

```php
<?php

$request = new \Alexya\Http\Request(
    $_SERVER["REQUEST_URI"],
    $_GET,
    $_POST,
    $_COOKIES,
    $_FILES,
    $_SERVER
); // Which is the same as `$request = \Alexya\Http\Request::main();`

echo $request->get["param_name"];

foreach($request->headers as $key => $value) {
    echo "Requested header: {$key}: {$value}";
}
```

<a name="response"></a>
Response
--------

The class `\Alexya\Http\Response` offers a way for create and send HTTP responses.

The constructor accepts the following parameters (all of them optional):

 * An array being the headers.
 * A string being the response body.
 * An integer being the status code (or a string being the status name).

To send the request use the method `send`.

To add a new header use the method `header` which accepts as parameter a string being the header
name and a string or an array being the value of the header.

The method `status` sets the status code of the response, it can be either an integer being
the code or a string being the name (see `Response::responseCodes`).

The method `redirect` sends a redirect response thrugh headers.
It accepts as parameter a string being the URL to redirect, a string being the method of redirect
("Refresh" or "Location") and the status code of the redirect.

Example:

```php
<?php

$request = new Request();

$request->header("Content-Type", "text/html");
$request->status(200);
$request->body("<h1>Hello World</h1>");

Request::redirect("/Home");
```
