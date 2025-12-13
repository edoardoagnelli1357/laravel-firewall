<?php

namespace Edoardoagnelli1357\Firewall\Middleware;

use Edoardoagnelli1357\Firewall\Abstracts\Middleware;

class Whitelist extends Middleware
{
    public function check($patterns)
    {
        return ($this->isWhitelist() === false);
    }
}
