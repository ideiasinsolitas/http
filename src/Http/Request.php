<?php

namespace Deck\Http;

class Request
{

    // REQUEST METHODS

    protected $methods = array(
        'METHOD_HEAD' => 'HEAD',
        'METHOD_GET' => 'GET',
        'METHOD_POST' => 'POST',
        'METHOD_PUT' => 'PUT',
        'METHOD_PATCH' => 'PATCH',
        'METHOD_DELETE' => 'DELETE',
        'METHOD_OPTIONS' => 'OPTIONS',
        'METHOD_OVERRIDE' => '_METHOD',
    );

    protected $requestData;
    protected $requestDataIndex;
    protected $files;
    protected $enviroment;
    protected $getData;
    protected $postData;
    protected $getDataIndex;
    protected $postDataIndex;
    protected $session;
    protected $cookies;
    protected $detector;

    public function __construct(Enviroment $enviroment, array $request, array $get, array $post, array $files)
    {
        $this->enviroment = $enviroment;
        $this->requestData = $request;
        $this->getData = $get;
        $this->postData = $post;

        $this->requestDataIndex = array_keys($request);
        $this->getDataIndex = array_keys($get);
        $this->postDataIndex = array_keys($post);

        $this->files = new Files($files);
        $this->session = new Session();
        $this->cookies = new CookieJar();
        $this->detector = new DeviceDetector();
    }

    public function detectDevice()
    {
        if ($this->detector->isMobile()) {
            return array(
                'device' => array(
                    'cell' => $this->detector->isMobile() && !$this->detector->isTablet(),
                    'tablet' => $this->detector->isMobile() && $this->detector->isTablet(),
                ),
                'os' => array(
                    'android' => $this->detector->isAndroidOS(),
                    'iOs' => $this->detector->isiOS(),
                    'other' => !$this->detector->isAndroidOS() && !$this->detector->isiOS(),
                ),
                'browser' => array(
                    'android' => false,
                    'chrome' => false,
                    'ie' => false,
                    'safari' => false,
                    'opera' => false,
                    'other' => false,
                ),
            );
        }

        return array(

            'os' => array(
                'windows' => false,
                'macos' => false,
                'linux' => false
            ),

            'browser' => array(
                'firefox' => false,
                'chrome' => false,
                'ie' => false,
                'safari' => false,
                'opera' => false,
                'other' => false,
            ),
        );
    }

    /**/

    public function getMethod()
    {
        return $this->enviroment->getRequest('method');
    }
 
    public function isGet()
    {
        return $this->enviroment->getRequest('method') === 'GET';
    }

    public function isPost()
    {
        return $this->enviroment->getRequest('method') === 'POST';
    }

    public function isPut()
    {
        return $this->enviroment->getRequest('method') === 'PUT';
    }

    public function isDelete()
    {
        return $this->enviroment->getRequest('method') === 'DELETE';
    }

    public function isPatch()
    {
        return $this->enviroment->getRequest('method') === 'PATCH';
    }

    public function isHead()
    {
        return $this->enviroment->getRequest('method') === 'HEAD';
    }

    public function isOptions()
    {
        return $this->enviroment->getRequest('method') === 'OPTIONS';
    }

    /**/

    public function isAjax()
    {
        return $this->enviroment->getHttp('xhr') === 'XmlHttpRequest';
    }

    public function isMobile()
    {
        return $this->detector->isMobile();
    }

    public function isFormData()
    {
        return $this->isPost() && in_array('form', $this->postIndex);
    }

    public function isUpload()
    {
        return $this->isPost() && in_array('upload', $this->postIndex);
    }

    public function hasFiles()
    {
        return ($this->files->count() > 0 ? true : false);
    }

    /**/

    public function getBody()
    {
        if ($this->isPost() || $this->isPut()) {
            return $this->enviroment->getPhp('input');
        }

        return false;
    }

    public function getUri()
    {
        $relative = $this->enviroment->getRequest('uri.relative');
        
        if (is_string($relative)) {
            return $relative;
        }

        return $this->enviroment->getRequest('uri');
    }

    public function getRequestData()
    {
        return array_merge($this->getData, $this->postData);
    }

    public function getIp()
    {
        return $this->enviroment->resolveIp();
    }
}
