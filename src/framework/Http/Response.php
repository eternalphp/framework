<?php

namespace framework\Http;

use framework\Exception\UnexpectedValueException;
use framework\Exception\InvalidArgumentException;
use framework\Cookie\Cookie;
use Exception;

final class Response{
	
	private $options = array();
	protected $headers = array();
	protected $contentType = 'text/html';
	protected $content;
	protected $version;
	protected $statusCode = 200;
	protected $statusText;
	protected $charset;
	private $level = 0;
	
	public static $statusTexts = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',            // RFC2518
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',          // RFC4918
        208 => 'Already Reported',      // RFC5842
        226 => 'IM Used',               // RFC3229
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',    // RFC7238
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',                                               // RFC2324
        421 => 'Misdirected Request',                                         // RFC7540
        422 => 'Unprocessable Entity',                                        // RFC4918
        423 => 'Locked',                                                      // RFC4918
        424 => 'Failed Dependency',                                           // RFC4918
        425 => 'Too Early',                                                   // RFC-ietf-httpbis-replay-04
        426 => 'Upgrade Required',                                            // RFC2817
        428 => 'Precondition Required',                                       // RFC6585
        429 => 'Too Many Requests',                                           // RFC6585
        431 => 'Request Header Fields Too Large',                             // RFC6585
        451 => 'Unavailable For Legal Reasons',                               // RFC7725
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',                                     // RFC2295
        507 => 'Insufficient Storage',                                        // RFC4918
        508 => 'Loop Detected',                                               // RFC5842
        510 => 'Not Extended',                                                // RFC2774
        511 => 'Network Authentication Required',                             // RFC6585
    );
	
	public function __construct($content = '', $status = 200, $headers = []){
        $this->setContent($content);
        $this->setStatusCode($status);
        $this->setProtocolVersion('1.0');
		$this->headers = $headers;
	}
	
	public function addHeader($key,$value){
		$this->headers[$key] = $value;
	}
	
	public function removeHeader($key){
		unset($this->headers[$key]);
	}
	
	public function sendHeaders(){
		if (!headers_sent()) {
			
			$length = strlen($this->content);
			header(sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText), true, $this->statusCode);
            header("Content-type: {$this->contentType}");
            header("Content-length:{$length}");
            header("Powered-by: songdian.net.cn");
			
			foreach ($this->headers as $key => $value) {
				header($key . ': ' . $value);
			}
			
			
		}
		return $this;
	}
	
    public function setContent(string $content)
    {
        $this->content = $content;
        return $this;
    }
	
    /**
     * Sends content for the current web response.
     *
     * @return $this
     */
	public function sendContent(){
        echo $this->content;

        return $this;
	}
	
    /**
     * Sends HTTP headers and content.
     *
     * @return $this
     */
	public function send(){
		
		if ($this->level) {
			$this->content = $this->compress($this->content, $this->level);
		}

        $this->sendHeaders();
        $this->sendContent();

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } elseif (!in_array(PHP_SAPI, ['cli', 'phpdbg'], true)) {
            static::closeOutputBuffers(0, true);
        }

        return $this;

	}
	
    /**
     * Sets the response charset.
     *
     * @param string $charset Character set
     *
     * @return $this
     *
     * @final since version 3.2
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * Retrieves the response charset.
     *
     * @return string Character set
     *
     * @final since version 3.2
     */
    public function getCharset()
    {
        return $this->charset;
    }
	
    /**
     * Sets the response status code.
     *
     * If the status text is null it will be automatically populated for the known
     * status codes and left empty otherwise.
     *
     * @param int   $code HTTP status code
     * @param mixed $text HTTP status text
     *
     * @return $this
     *
     * @throws \InvalidArgumentException When the HTTP status code is not valid
     *
     * @final since version 3.2
     */
    public function setStatusCode($code, $text = null)
    {
        $this->statusCode = $code = (int) $code;
        if ($this->isInvalid()) {
            throw new InvalidArgumentException(sprintf('The HTTP status code "%s" is not valid.', $code));
        }

        if (null === $text) {
            $this->statusText = isset(self::$statusTexts[$code]) ? self::$statusTexts[$code] : 'unknown status';

            return $this;
        }

        if (false === $text) {
            $this->statusText = '';

            return $this;
        }

        $this->statusText = $text;

        return $this;
    }

    /**
     * Retrieves the status code for the current web response.
     *
     * @return int Status code
     *
     * @final since version 3.2
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
	
    /**
     * Sets the HTTP protocol version (1.0 or 1.1).
     *
     * @param string $version The HTTP protocol version
     *
     * @return $this
     *
     * @final since version 3.2
     */
    public function setProtocolVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Gets the HTTP protocol version.
     *
     * @return string The HTTP protocol version
     *
     * @final since version 3.2
     */
    public function getProtocolVersion()
    {
        return $this->version;
    }
	
    /**
     * @param $contentType
     * @return $this
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }
	
    /**
     * @param array $data
     */
    public function json(array $data)
    {
        $this->setContentType("application/json");
		$this->setContent(json_encode($data));
        $this->send();
    }

    /**
     * @param array $data
     */
    public function jsonp(array $data, $callback = 'callback')
    {
        $this->setContentType("application/jsonp");
        $body = sprintf('%s(%s)', $callback, json_encode($data));
		$this->setContent($body);
        $this->send();
    }
	
	/**
     * @param $tpl
     * @param $data
     * @throws \Exception
     */
    public function render($tpl, $data)
    {
        if (!file_exists($tpl)) {
            throw new Exception("tpl not exists !");
        }
        extract($data);
        ob_start();
        require $tpl;
        $content = ob_get_contents();
        ob_end_clean();
		$this->setContent($content);
        $this->send();

    }
	
	public function redirect($url){
		header("location:$url");
        exit();
	}
	
    /**
     * @param DataFormat $dataFormat
     * @return mixed
     */
    public function dataformat(DataFormat $dataFormat)
    {
        $data = $dataFormat->format($this);
        $this->sendHeader();
        return $data;
    }
	
    /**
     * Get cookie
     *
     * return framework\Cookie\Cookie
     */
	public function getCookie(){
		return new Cookie();
	}
	
    /**
     * set cookie to response
     *
	 * @param string $name
	 * @param string $value
     * return $this
     */
	public function cookie($name,$value){
		$this->getCookie()->save($name,$value);
		return $this;
	}
	
    /**
     * Get session
     *
     * return framework\Session\Session
     */
	public function getSession(){
		return app('session');
	}
	
    /**
     * set session to response
     *
	 * @param string $name
	 * @param string $value
     * return $this
     */
	public function session($name,$value){
		$this->getSession()->put($name,$value);
		return $this;
	}
	
    /**
     * @param $filename
	 * @param $data
     */
	public function write($filename,$data){
		if(!file_exists(dirname($filename))){
			mkdir(dirname($filename),0777,true);
		}
		file_put_contents($filename,$data);
	}
	
    /**
     * @param $filename
	 * @param $data
     */
	public function append($filename,$data){
		if(!file_exists(dirname($filename))){
			mkdir(dirname($filename),0777,true);
		}
		file_put_contents($filename,$data,FILE_APPEND);
	}
	
	public function compress($data,$level = 0){
		if(isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE)){
			$encoding = 'gzip';
		}

		if(isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== FALSE)) { 
			$encoding = 'x-gzip';
		}
		
		if(!isset($encoding)) {
			return $data;
		}
		
		if(!extension_loaded('zlib') || ini_get('zlib.output_compression')) { 
			return $data;
		}
		
		if(headers_sent()) { 
			return $data;
		}
		
		if(connection_status()){
			return $data;
		}
		
		$this->addHeader('Content-Encoding', $encoding);
		
		return gzencode($data,(int)$level);
	}
	
    /**
     * Is response invalid?
     *
     * @return bool
     *
     * @see https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
     *
     * @final since version 3.2
     */
    public function isInvalid()
    {
        return $this->statusCode < 100 || $this->statusCode >= 600;
    }
	
   /**
     * Is response informative?
     *
     * @return bool
     *
     * @final since version 3.3
     */
    public function isInformational()
    {
        return $this->statusCode >= 100 && $this->statusCode < 200;
    }

    /**
     * Is response successful?
     *
     * @return bool
     *
     * @final since version 3.2
     */
    public function isSuccessful()
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * Is the response a redirect?
     *
     * @return bool
     *
     * @final since version 3.2
     */
    public function isRedirection()
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    /**
     * Is there a client error?
     *
     * @return bool
     *
     * @final since version 3.2
     */
    public function isClientError()
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    /**
     * Was there a server side error?
     *
     * @return bool
     *
     * @final since version 3.3
     */
    public function isServerError()
    {
        return $this->statusCode >= 500 && $this->statusCode < 600;
    }

    /**
     * Is the response OK?
     *
     * @return bool
     *
     * @final since version 3.2
     */
    public function isOk()
    {
        return 200 === $this->statusCode;
    }

    /**
     * Is the response forbidden?
     *
     * @return bool
     *
     * @final since version 3.2
     */
    public function isForbidden()
    {
        return 403 === $this->statusCode;
    }

    /**
     * Is the response a not found error?
     *
     * @return bool
     *
     * @final since version 3.2
     */
    public function isNotFound()
    {
        return 404 === $this->statusCode;
    }

    /**
     * Is the response a redirect of some form?
     *
     * @param string $location
     *
     * @return bool
     *
     * @final since version 3.2
     */
    public function isRedirect($location = null)
    {
        return in_array($this->statusCode, [201, 301, 302, 303, 307, 308]) && (null === $location ?: $location == $this->headers->get('Location'));
    }

    /**
     * Is the response empty?
     *
     * @return bool
     *
     * @final since version 3.2
     */
    public function isEmpty()
    {
        return in_array($this->statusCode, [204, 304]);
    }
	
    /**
     * Cleans or flushes output buffers up to target level.
     *
     * Resulting level can be greater than target level if a non-removable buffer has been encountered.
     *
     * @param int  $targetLevel The target output buffering level
     * @param bool $flush       Whether to flush or clean the buffers
     *
     * @final since version 3.3
     */
    public static function closeOutputBuffers($targetLevel, $flush)
    {
        $status = ob_get_status(true);
        $level = count($status);
        // PHP_OUTPUT_HANDLER_* are not defined on HHVM 3.3
        $flags = defined('PHP_OUTPUT_HANDLER_REMOVABLE') ? PHP_OUTPUT_HANDLER_REMOVABLE | ($flush ? PHP_OUTPUT_HANDLER_FLUSHABLE : PHP_OUTPUT_HANDLER_CLEANABLE) : -1;

        while ($level-- > $targetLevel && ($s = $status[$level]) && (!isset($s['del']) ? !isset($s['flags']) || ($s['flags'] & $flags) === $flags : $s['del'])) {
            if ($flush) {
                ob_end_flush();
            } else {
                ob_end_clean();
            }
        }
    }
}
