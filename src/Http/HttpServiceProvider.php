<?php

namespace Deck\Http;

class HttpServiceProvider implements ServiceProviderInterface
{
    /**
     *
     *
     *
     * @access       public
     * @param        type [ $varname] description
     * @return       type description
     */
    public function register(Container $container)
    {
        /* http */
        $container['http.enviroment'] = function ($container) {
            return new Enviroment($container['app.path']);
        };

        $container['http.session'] = function () {
            return new Session();
        };

        $container['http.cookies'] = function () {
            return new CookieJar();
        };

        $container['http.files'] = function ($container) {
            return new Files($container['http.upload.dir']);
        };

        $container['http.request'] = function ($container) {
            return new Request($container['enviroment']);
        };

        $container['http.response'] = function () {
            return new Response();
        };

        return $container;
    }
}
