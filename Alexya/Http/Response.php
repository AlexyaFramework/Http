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
 * The method `redirect` sends a redirect response thrugh headers.
 * It accepts as parameter a string being the URL to redirect, a string being
 * the method of redirect ("Refresh" or "Location") and the status code of
 * the redirect.
 *
 * Example:
 *
 *     $request = new Request();
 *
 *     $request->header("Content-Type", "text/html");
 *     $request->status(200);
 *     $request->body("<h1>Hello World</h1>");
 *
 *     Request::redirect("/Home");
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class Response
{
    ///////////////////////////////////
    // Static methods and properties //
    ///////////////////////////////////

    /**
     * Array containing response codes
     *
     * @var array
     */
    public static $responseCodes = [
        // INFORMATIONAL CODES
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',

        // SUCCESS CODES
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',

        // REDIRECTION CODES
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy', // Deprecated
        307 => 'Temporary Redirect',

        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot', //Kek
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',

        // SERVER ERROR
        500 => 'Internal Server Error',
        01 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required'
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

        if($method == "Location") {
            $response->header("Location", $path);
        } else if($method == "Refresh") {
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
    public function __construct(array $headers = [], string $body = "", $status)
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
                $key   == $status ||
                $value == $status
            ) {
                $this->_status = $status;
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
        foreach($this->_headers as $key => $value) {
            header("{$key}: {$value}");
        }
    }
}
