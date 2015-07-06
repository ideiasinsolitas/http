<?php

namespace Deck\Http;

class Response
{

    protected $contentType;
    protected $isRedirect;
    protected $isAttachment;
    protected $isStream;
    protected $code;
    protected $body;

    public function __construct($body = null, $code = 200)
    {
        if ($body && !is_string($body)) {
            throw new \InvalidArgumentException("Error Processing Request");
        }

        if (!is_int($code)) {
            throw new \InvalidArgumentException("Error Processing Request");
        }

        $this->session = new Session();
        $this->cookies = new CookieJar();
        $this->headers = new HeaderCollection();
        $this->code = $code;
    }


    public function setStatusCode($code)
    {
        if (!is_int($code)) {
            throw new \InvalidArgumentException("Error Processing Request");
        }

        $this->code = $code;
    }

    public function setHeader($name, $value, $replace = null)
    {
        $header = new Header($name, $value, $replace);
        $this->headers->set($name, $header);
    }

    public function setRedirect($value)
    {
        $this->setHeader('location', $value);
    }
    
    public function setAge($value)
    {
        $this->setHeader('age', $value);
    }
    
    public function setTransferEncoding($value)
    {
        $this->setHeader('content.transfer.encoding', $value);
    }
    
    public function setCacheControl($value)
    {
        $this->setHeader('cache.control', $value);
    }
    
    public function setEtag($value)
    {
        $this->setHeader('etag', $value);
    }
    
    public function setExpires($value)
    {
        $this->setHeader('date.expires', $value);
    }
    
    public function setLastModified($value)
    {
        $this->setHeader('date.last.modified', $value);
    }
    
    public function setContentEncoding($value)
    {
        $this->setHeader('content.encoding', $value);
    }
    
    public function setContentLanguage($value)
    {
        $this->setHeader('content.language', $value);
    }
    
    public function setContentLength($value)
    {
        $this->setHeader('content.length', $value);
    }
    
    public function setContentLocation($value)
    {
        $this->setHeader('content.location', $value);
    }
    
    public function setContentMd5($value)
    {
        $this->setHeader('content.md5', $value);
    }
    
    public function setContentDisposition($value)
    {
        $this->setHeader('content.disposition', $value);
    }

    public function setContentType($value)
    {
        $this->setHeader('content.type', $value);
    }
    
    public function setAuthentication($value)
    {
        $this->setHeader('authentication', $value);
    }
    
    public function setXUACompatible($value)
    {
        //$this->setHeader('', $value);
    }
    
    public function setXPoweredBy($value)
    {
        $this->setHeader('powered', $value);
    }
    
    public function setBody($body)
    {
        $this->body = $body;
    }
    
    public function hasBody()
    {
        return !empty($this->body);
    }
    
    /**
     * Helpers: Empty?
     * @return bool
     */
    public function isEmpty()
    {
        return in_array($this->code, array(201, 204, 304));
    }
    
    /**
     * Helpers: Informational?
     * @return bool
     */
    public function isInformational()
    {
        return $this->code >= 100 && $this->code < 200;
    }
    
    /**
     * Helpers: OK?
     * @return bool
     */
    public function isOk()
    {
        return $this->code === 200;
    }
    
    /**
     * Helpers: Successful?
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->code >= 200 && $this->code < 300;
    }
    
    /**
     * Helpers: Redirect?
     * @return bool
     */
    public function isRedirect()
    {
        return in_array($this->code, array(301, 302, 303, 307));
    }
    
    /**
     * Helpers: Redirection?
     * @return bool
     */
    public function isRedirection()
    {
        return $this->code >= 300 && $this->code < 400;
    }
    
    /**
     * Helpers: Forbidden?
     * @return bool
     */
    public function isForbidden()
    {
        return $this->code === 403;
    }
    
    /**
     * Helpers: Not Found?
     * @return bool
     */
    public function isNotFound()
    {
        return $this->code === 404;
    }
    
    /**
     * Helpers: Client error?
     * @return bool
     */
    public function isClientError()
    {
        return $this->code >= 400 && $this->code < 500;
    }

    /**
     * Helpers: Server Error?
     * @return bool
     */
    public function isServerError()
    {
        return $this->code >= 500 && $this->code < 600;
    }

    /**
     * Serve html
     * @return bool
     */
    protected function serveHtml()
    {
        $this->setHeader('content.type', 'text/html');
    }

    /**
     * Serve xml
     * @return bool
     */
    protected function serveXml()
    {
        $this->setHeader('content.type', 'text/html');
    }

    /**
     * Serve css
     * @return bool
     */
    protected function serveCss()
    {
        $this->setHeader('content.type', 'text/html');
    }

    /**
     * Serve javascript
     * @return bool
     */
    protected function serveJs()
    {
        $this->setHeader('content.type', '  /html');
    }

    /**
     * Serve json
     * @return bool
     */
    protected function serveJson()
    {
        $this->setHeader('content.type', 'application/json');
    }

    /**
     * Serve custom type
     * @return bool
     */
    protected function serveFile($type)
    {
        $this->setHeader('content.type', $type);
    }

    /**
     * Stream
     * @return bool
     */
    protected function streamFile($file)
    {
        $this->sream($file);
    }

    /**
     * download
     * @return bool
     */
    protected function downloadFile($filename)
    {
        $this->setHeader('content.disposition', 'attachment; filename=' . $filename . ';');
    }

    protected function append($chunk)
    {
        $this->body = $this->body . $chunk;
    }

    protected function prepend($chunk)
    {
        $this->body = $chunk . $this->body;
    }

    public function send()
    {
        $body = $this->body;
        $length = strlen($body);
        $statusCodeHeader = new StatusCodeHeader($this->code);
        $this->headers->set('content.length', $length);
        $this->headers->set('code', $statusCodeHeader);
        $this->headers->send();
        ob_start();
        echo $body;
        ob_flush();
    }

    public function stream()
    {
        $this->headers->set('content.encoding', 'chunked');
        $this->headers->set('content.type', 'text/html');
        $this->headers->set('connection', 'keep-alive');
        flush();
        ob_flush();

        for ($i = 0; $i < 1000; $i++) {
            $this->dump($chunk);
            flush();
            ob_flush();
        }
    }

    protected function dump($chunk)
    {
        echo $chunk;
    }
}
