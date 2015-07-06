<?php

namespace Deck\Http;

class Header implements HeaderInterface
{

    protected $name;

    protected $value;

    protected $replace = true;

        protected $allowedHeaders = array(
        'location' => 'Location',
        'refresh' => 'Refresh',
        'powered' => 'X-Powered-By',
        'content.language' => 'Content-language',
        'content.length' => 'Content-Length',
        'content.type' => 'Content-Type',
        'content.disposition' => 'Content-Disposition',
        'content.transfer.encoding' => 'Content-Transfer-Encoding',
        'content.encoding' => 'Content-Encoding',
        'content.location' => 'Content-Location',
        'content.md5' => 'Content-MD5',
        'transfer.encoding' => 'Transfer-Encoding',
        'date' => 'Date',
        'date.last.modified' => 'Last-Modified',
        'date.expires' => 'Expires',
        'pragma' => 'Pragma',
        'etag' => 'Etag',
        'vary' => 'Vary',
        'cache.control' => 'Cache-Control',
        'authentication' => 'WWW-Authenticate',
        'access.control' => 'Access-Control-Allow-Origin',
        'allow' => 'Allow',
        'age' => 'Age',
        'link' => 'Link',
        'retry.after' => 'Retry-After',
        'server' => 'Server',
    );

    public function __construct($name, $value, $replace = null)
    {
        $this->$name = $$name;
        $this->value = $value;

        if ($replace) {
            $this->replace = $replace;
        }
    }

    public function send()
    {
        $name = $this->allowedHeaders[$this->name];
        header($name, $this->value, $this->replace);
    }
}
