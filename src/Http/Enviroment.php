<?php

namespace Deck\Gttp;

use Deck\Types\Collection;

class Enviroment extends Collection
{
    protected $path;

    protected $mode;

    public function __construct(array $server, $path = '/', $mode = 'development')
    {
        $this->path = $path;

        $this->mode = $mode;

        $env = array(

        'document.root' => isset($server['DOCUMENT_ROOT']) ? $server['DOCUMENT_ROOT'] : null,

        /**/
        'php.input' => @file_get_contents('php://input') ? @file_get_contents('php://input') : null,
        'php.error' => ($this->mode === 'development') ? @fopen('php://stderr', 'w') : null,
        'php.debug' => ($this->mode === 'development') ? true : null,
        'php.self' => isset($server['PHP_SELF']) ? $server['PHP_SELF'] : null,

        /**/
        'server.cgi' => isset($server['GATEWAY_INTERFACE']) ? $server['GATEWAY_INTERFACE'] : null,
        'server.address' => isset($server['SERVER_ADDR']) ? $server['SERVER_ADDR'] : null,
        'server.name' => isset($server['SERVER_NAME']) ? $server['SERVER_NAME'] : null,
        'server.software' => isset($server['SERVER_SOFTWARE']) ? $server['SERVER_SOFTWARE'] : null,
        'server.protocol' => isset($server['SERVER_PROTOCOL']) ? $server['SERVER_PROTOCOL'] : null,
        'server.admin' => isset($server['SERVER_ADMIN']) ? $server['SERVER_ADMIN'] : null,
        'server.port' => isset($server['SERVER_PORT']) ? $server['SERVER_PORT'] : null,
        'server.signature' => isset($server['SERVER_SIGNATURE']) ? trim(strip_tags($server['SERVER_SIGNATURE'])) : null,

        /**/
        'request.method' => isset($server['REQUEST_METHOD']) ? $server['REQUEST_METHOD'] : null,
        'request.time' => isset($server['REQUEST_TIME']) ? $server['REQUEST_TIME'] : null,
        'request.time.float' => isset($server['REQUEST_TIME_FLOAT']) ? $server['REQUEST_TIME_FLOAT'] : null,
        'request.uri' => isset($server['REQUEST_URI']) ? $server['REQUEST_URI'] : null,
        'request.uri.relative' => isset($server['REQUEST_URI']) ? $this->parseRelativeUri($server['REQUEST_URI']) : null,
        'request.query.string' => isset($server['QUERY_STRING']) ? $server['QUERY_STRING'] : null,


        /**/
        'http.accept' => isset($server['HTTP_ACCEPT']) ? $server['HTTP_ACCEPT'] : null,
        'http.accept.charset' => isset($server['HTTP_ACCEPT_CHARSET']) ? $server['HTTP_ACCEPT_CHARSET'] : null,
        'http.accept.encoding' => isset($server['HTTP_ACCEPT_ENCODING']) ? $server['HTTP_ACCEPT_ENCODING'] : null,
        'http.accept.language' => isset($server['HTTP_ACCEPT_LANGUAGE']) ? $server['HTTP_ACCEPT_LANGUAGE'] : null,
        'http.connection' => isset($server['HTTP_CONNECTION']) ? $server['HTTP_CONNECTION'] : null,
        'http.host' => isset($server['HTTP_HOST']) ? $server['HTTP_HOST'] : null,
        'http.referer' => isset($server['HTTP_REFERER']) ? $server['HTTP_REFERER'] : null,
        'http.user.agent' => isset($server['HTTP_USER_AGENT']) ? $server['HTTP_USER_AGENT'] : null,
        'http.secure' => isset($server['HTTPS']) ? $server['HTTPS'] : null,
        'http.scheme' => empty($server['HTTPS']) || $server['HTTPS'] === 'off' ? 'http' : 'https',
        'http.xhr' => isset($server['HTTP_X_REQUESTED_WITH']) ? $server['HTTP_X_REQUESTED_WITH'] : null,
        'http.client.ip' => isset($server['HTTP_CLIENT_IP']) ? $server['HTTP_CLIENT_IP'] : null,
        'http.fowarded.for' => isset($server['HTTP_X_FORWARDED_FOR']) ? $server['HTTP_X_FORWARDED_FOR'] : null,

        /**/
        'remote.address' => $this->resolveIp(),
        'remote.host' => isset($server['REMOTE_HOST']) ? $server['REMOTE_HOST'] : null,
        'remote.port' => isset($server['REMOTE_PORT']) ? $server['REMOTE_PORT'] : null,
        'remote.user' => isset($server['REMOTE_USER']) ? $server['REMOTE_USER'] : null,
        'remote.user.redirect' => isset($server['REDIRECT_REMOTE_USER']) ? $server['REDIRECT_REMOTE_USER'] : null,

        /**/
        'script.name' => isset($server['SCRIPT_NAME']) ? $server['SCRIPT_NAME'] : null,
        'script.filename' => isset($server['SCRIPT_FILENAME']) ? $server['SCRIPT_FILENAME'] : null,

        /**/
        'path.translated' => isset($server['PATH_TRANSLATED']) ? $server['PATH_TRANSLATED'] : null,
        'path.info' => isset($server['PATH_INFO']) ? $server['PATH_INFO'] : null,
        'path.info.original' => isset($server['ORIG_PATH_INFO']) ? $server['ORIG_PATH_INFO'] : null,


        /**/
        'auth.digest' => isset($server['PHP_AUTH_DIGEST']) ? $server['PHP_AUTH_DIGEST'] : null,
        'auth.user' => isset($server['PHP_AUTH_USER']) ? $server['PHP_AUTH_USER'] : null,
        'auth.password' => isset($server['PHP_AUTH_PW']) ? $server['PHP_AUTH_PW'] : null,
        'auth.type' => isset($server['AUTH_TYPE']) ? $server['AUTH_TYPE'] : null,
        
        );

        $this->map(array_filter($env));
    }

    public function set($prefix, $name, $value)
    {
        $key = $prefix . '.' . $name;
        $this->items[$key] = $value;
    }

    public function get($prefix, $key)
    {
        $arrayKey = $prefix . '.' . $key;
        return isset($this->items[$arrayKey]) ? $this->items[$arrayKey] : null;
    }

    public function getDocumentRoot()
    {
        return isset($this->items['document.root']) ? $this->items['document.root'] : null;
    }

    public function getCgi()
    {
        return isset($this->items['server.cgi']) ? $this->items['server.cgi'] : null;
    }

    public function getPhpInfo($key)
    {
        return $this->get('php', $key);
    }

    public function getServer($key)
    {
        return $this->get('server', $key);
    }

    public function getRequestInfo($key)
    {
        return $this->get('request', $key);
    }

    public function getHttp($key)
    {
        return $this->get('http', $key);
    }

    public function getRemote($key)
    {
        return $this->get('remote', $key);
    }

    public function getScript($key)
    {
        return $this->get('script', $key);
    }

    public function getPath($key)
    {
        return $this->get('path', $key);
    }

    public function getAuth($key)
    {
        return $this->get('auth', $key);
    }

    private function replaceFirstMatch($search, $replace, $subject)
    {
        $pos = strpos($subject, $search);

        if ($pos !== false) {
            $string = substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $string;
    }

    public function resolveIp()
    {
        $ip = null;

        if (isset($this->items['http.client.ip'])) {
            $ip = $this->items['http.client.ip'];

        } elseif (isset($this->items['http.fowarded.for'])) {
            $ip = $this->items['http.fowarded.for'];

        } else {
            $ip = $this->items['remote.address'];
        }

        if (!$ip) {
            //throw new \Exception("Error Processing Request");
            return false;
        }

        return $ip;
    }

    public function parseRelativeUri($uri)
    {
        if ($this->path === '/') {
            return $uri;
        }

        return $this->replaceFirstMatch($this->path, '', $uri);
    }

    public function getFileName()
    {
        $name = $this->replaceFirstMatch($this->path, '', $this->getScript('name'));
        return trim($name, '/');
    }

    public function getBasePath()
    {
        return $this->path;
    }

    public function getRootUrl()
    {
        return $this->getHttp('scheme') . '://' . $this->getServer('name');
    }

    public function getBaseUrl()
    {
        if ($this->path === '/') {
            return $this->getRootUrl();
        }

        return $this->getRootUrl() . $this->path;
    }

    public function getScriptUrl()
    {
        return $this->getBaseUrl() . '/' . $this->getFileName();
    }

    public function getCurrentUrl()
    {
        return $this->getBaseUrl() . $this->getRequest('uri');
    }
}
