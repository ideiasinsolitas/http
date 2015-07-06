<?php

namespace Deck\Http;

use Deck\Types\Collection;

class HeaderCollection extends Collection implements HeaderInterface
{

    public function set($name, Header $header)
    {
        parent::set($name, $header);
    }

    public function send()
    {

        if (isset($this->items['code'])) {
            $this->items['code']->send();

            unset($this->items['code']);
        }

        foreach ($this->items as $header) {
            $header->send();
        }
    }
}
