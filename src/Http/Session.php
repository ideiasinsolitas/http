<?php

namespace Deck\Http;

use Deck\Types\Collection;

class Session extends Collection
{

    protected $id;

    private $handler;

    public function __construct()
    {
        $this->start();
    }

    public function start()
    {
        if (!$this->started()) {
            session_start();
            $_SESSION['requests.count'] = 0;
        } else {
            $_SESSION['requests.count']++;
        }

        $this->map($_SESSION);
    }

    public function started()
    {
        if (session_status() === PHP_SESSION_NONE) {
            return false;
        }

        return true;
    }

    public function end()
    {
        unset($_SESSION);
        session_destroy();
    }

    public function renew($delete = false)
    {
        session_regenerate_id($delete);
    }

    public function set($id, $item)
    {
        $_SESSION[$id] = $item;
        parent::set($id, $item);
    }

    public function remove($id)
    {
        unset($_SESSION[$id]);
        parent::remove($id);
    }
}
