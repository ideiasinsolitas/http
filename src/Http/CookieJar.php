<?php

namespace Deck\Http;

use Deck\Types\Collection;

class CookieJar extends Collection
{
    
    public function __construct()
    {
        $this->map($_COOKIE);
    }

    public function set($id, Cookie $item)
    {

        if (!is_scalar($id)) {
            throw new \Exception("Error Processing Request");
        }

        $cookie = $item->get();

        setcookie($id, $cookie['value'], $cookie['expires'], $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httponly']);
        
        parent::set($id, $cookie);
    }

    public function remove($id)
    {

        if (!is_scalar($id)) {
            throw new \Exception("Error Processing Request");
        }

        setcookie($id, '', time()-3600);
        unset($_COOKIE[$id]);

        parent::remove($id);
    }

    public function get($id)
    {

        if (!is_scalar($id)) {
            throw new \Exception("Error Processing Request");
        }

        $value = parent::get($id);

        return new Cookie($value);
    }
}
