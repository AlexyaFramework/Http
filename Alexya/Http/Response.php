<?php
namespace Alexya\Http;

/**
 * Response class.
 *
 * This class builds HTTP requests.
 *
 * The constructor accepts as parameter an array being the initial
 * headers, a string being the response body and an integer being the
 * response status, all of them optional.
 *
 * To send the request use the method `send`.
 *
 * To add a new header use the method `header` which accepts
 * as parameter a string being the header name and a string or an array
 * being the value of the header.
 *
 * The method `status` sets the status code of the response, it can be
 * either an integer being the code or a string being the name (see `Response::responseCodes`).
 *
 * The method `redirect` sends a redirect response through headers.
 * It accepts as parameter a string being the URL to redirect, a string being
 * the method of redirect ("Refresh" or "Location") and the status code of
 * the redirect.
 *
 * Example:
 *
 * ```php
 * $request = new Request();
 *
 * $request->header("Content-Type", "text/html");
 * $request->status(200);
 * $request->body("<h1>Hello World</h1>");
 *
 * Request::redirect("/Home");
 * ```
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class Response
{
    ///////////////////////////////////
    // Static methods and properties //
    ///////////////////////////////////

    /**
     * Array containing response codes.
     *
     * @var array
     */
    public static $responseCodes = [
        // INFORMATIONAL CODES
        "Continue"            => 100,
        "Switching Protocols" => 101,
        "Processing"          => 102,

        // SUCCESS CODES
        "OK"                            => 200,
        "Created"                       => 201,
        "Accepted"                      => 202,
        "Non-Authoritative Information" => 203,
        "No Content"                    => 204,
        "Reset Content"                 => 205,
        "Partial Content"               => 206,
        "Multi-status"                  => 207,
        "Already Reported"              => 208,

        // REDIRECTION CODES
        "Multiple Choices"   => 300,
        "Moved Permanently"  => 301,
        "Found"              => 302,
        "See Other"          => 303,
        "Not Modified"       => 304,
        "Use Proxy"          => 305,
        "Switch Proxy"       => 306, // Deprecated
        "Temporary Redirect" => 307,

        // CLIENT ERROR
        "Bad Request"                     => 400,
        "Unauthorized"                    => 401,
        "Payment Required"                => 402,
        "Forbidden"                       => 403,
        "Not Found"                       => 404,
        "Method Not Allowed"              => 405,
        "Not Acceptable"                  => 406,
        "Proxy Authentication Required"   => 407,
        "Request Time-out"                => 408,
        "Conflict"                        => 409,
        "Gone"                            => 410,
        "Length Required"                 => 411,
        "Precondition Failed"             => 412,
        "Request Entity Too Large"        => 413,
        "Request-URI Too Large"           => 414,
        "Unsupported Media Type"          => 415,
        "Requested range not satisfiable" => 416,
        "Expectation Failed"              => 417,
        "I\'m a teapot"                   => 418, //Kek
        "Unprocessable Entity"            => 422,
        "Locked"                          => 423,
        "Failed Dependency"               => 424,
        "Unordered Collection"            => 425,
        "Upgrade Required"                => 426,
        "Precondition Required"           => 428,
        "Too Many Requests"               => 429,
        "Request Header Fields Too Large" => 431,

        // SERVER ERROR
        "Internal Server Error"           => 500,
        "Not Implemented"                 => 501,
        "Bad Gateway"                     => 502,
        "Service Unavailable"             => 503,
        "Gateway Time-out"                => 504,
        "HTTP Version not supported"      => 505,
        "Variant Also Negotiates"         => 506,
        "Insufficient Storage"            => 507,
        "Loop Detected"                   => 508,
        "Network Authentication Required" => 511
    ];

    /**
     * Performs a redirect response.
     *
     * @param string     $path   Where to redirect.
     * @param string     $method Location or Refresh (default Location).
     * @param int|string $code   Redirect code (default 301).
     */
    public static function redirect(string $path, string $method = "Location", int $code = 301)
    {
        $response = new static();
        $method   = strtolower($method);

        if($method === "location") {
            $response->header("Location", $path);
        } else if($method === "refresh") {
            $response->header("Refresh", "0;url={$path}");
        }

        $response->status($code);

        $response->send();
        die();
    }

    ///////////////////////////////////////
    // Non static methods and properties //
    ///////////////////////////////////////

    /**
     * Response headers.
     *
     * @var array
     */
    private $_headers = [];

    /**
     * Response body.
     *
     * @var string
     */
    private $_body = "";

    /**
     * Response status.
     *
     * @var int
     */
    private $_status = 200;

    /**
     * Constructor.
     *
     * @param array      $headers Headers.
     * @param string     $body    Response body.
     * @param int|string $status  Status code.
     */
    public function __construct(array $headers = [], string $body = "", $status = 200)
    {
        $this->_headers = $headers;
        $this->_body    = $body;

        $this->status($status);
    }

    /**
     * Adds a header.
     *
     * @param string       $name  Header name.
     * @param string|array $value Header value.
     */
    public function header(string $name, $value)
    {
        if(is_array($value)) {
            $value = implode(", ", $value);
        } else if(!is_string($value)) {
            // Throw exception?
            return;
        }

        $this->_headers[$name] = $value;
    }

    /**
     * Sets the response body.
     *
     * @param string $body Response body.
     */
    public function body(string $body)
    {
        $this->_body = $body;
    }

    /**
     * Sets the response status.
     *
     * @param int|string $status Status code
     */
    public function status($status)
    {
        if(is_int($status)) {
            $this->_status = $status;
            return;
        } else if(!is_string($status)) {
            return;
        }

        foreach(static::$responseCodes as $key => $value) {
            if(
                $key   === $status ||
                $value === $status
            ) {
                $this->_status = $key;
                return;
            }
        }
    }

    /**
     * Sends the response.
     */
    public function send()
    {
        if(!headers_sent()) {
            $this->_sendHeaders();
        }

        echo $this->_body;
    }

    /**
     * Sends headers.
     */
    private function _sendHeaders()
    {
        http_response_code($this->_status);
        foreach($this->_headers as $key => $value) {
            header("{$key}: {$value}");
        }
    }
}
