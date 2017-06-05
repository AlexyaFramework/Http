<?php
namespace Alexya\Http;

/**
 * Request class.
 *
 * This class is a wrapper for the request
 * super-globals that PHP offers.
 *
 * The main request is the request that was sent to the
 * server and you can retrieve it by using the method `main`.
 *
 * The constructor accepts the following parameters:
 *
 *  * A string being the requested URI.
 *  * An array being GET parameters.
 *  * An array being POST parameters.
 *  * An array being COOKIE parameters.
 *  * An array being SERVER parameters.
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class Request
{
    ///////////////////////////////////
    // Static methods and properties //
    ///////////////////////////////////

    /**
     * Main request.
     *
     * @var Request
     */
    protected static $_main;

    /**
     * Returns main request.
     *
     * @param bool $refreshCache `true` to refresh cached instance (default `false`).
     *
     * @return Request Main request.
     */
    public static function main(bool $refreshCache = false) : Request
    {
        if(
            static::$_main === null ||
            $refreshCache
        ) {
            static::$_main = new static(
                $_SERVER["REQUEST_URI"],
                ($_GET ?? []),
                ($_POST ?? []),
                ($_COOKIES ?? []),
                ($_FILES ?? []),
                ($_SERVER ?? [])
            );
        }

        return static::$_main;
    }

    ///////////////////////////////////////
    // Non static methods and properties //
    ///////////////////////////////////////

    /**
     * Request URI.
     *
     * @var string
     */
    public $uri = "/";

    /**
     * Request method.
     *
     * @var string
     */
    public $method = "GET";

    /**
     * Headers.
     *
     * @var array
     */
    public $headers = [];

    /**
     * GET parameters.
     *
     * @var array
     */
    public $get = [];

    /**
     * POST parameters.
     *
     * @var array
     */
    public $post = [];

    /**
     * PUT parameters.
     *
     * @var array
     */
    public $put = [];

    /**
     * COOKIES parameters.
     *
     * @var array
     */
    public $cookies = [];

    /**
     * FILES parameters.
     *
     * @var array
     */
    public $files = [];

    /**
     * SERVER parameters.
     *
     * @var array
     */
    public $server = [];

    /**
     * Constructor.
     *
     * @param string $uri     Requested URI.
     * @param array  $get     The GET parameters.
     * @param array  $post    The POST parameters.
     * @param array  $cookies The COOKIE parameters.
     * @param array  $files   The FILES parameters.
     * @param array  $server  The SERVER parameters.
     */
    public function __construct(string $uri = "/", array $get = [], array $post = [], array $cookies = [], array $files = [], array $server = [])
    {
        $this->uri     = $uri;
        $this->get     = $get;
        $this->post    = $post;
        $this->cookies = $cookies;
        $this->files   = $files;
        $this->server  = $server;
        $this->headers = $this->getHeaders();
        $this->method  = $this->server["REQUEST_METHOD"];

        if($this->method == "PUT") {
            parse_str("php://input", $this->put);
        }

        $this->onInstance();
    }

    /**
     * Returns request headers.
     *
     * @return array Request headers.
     */
    public function getHeaders() : array
    {
        $headers = [];
        foreach($this->server as $key => $value) {
            $key = ucwords(
                str_replace(
                    "_",
                    "-",
                    strtolower($key)
                ),
                "-"
            );

            if(strpos($key, "Http-") === 0) {
                $headers[substr($key, 5)] = $value;
            } else if(strpos($key, "Content-") !== false) {
                $headers[$key] = $value;
            }
        }

        return $headers;
    }

    /**
     * On instance method.
     *
     * Override it instead of the constructor.
     */
    public function onInstance()
    {

    }

    /**
     * Returns the URI as an array.
     *
     * @return array `$this->uri` as array.
     */
    public function uri() : array
    {
        return explode("/", $this->uri);
    }
}
