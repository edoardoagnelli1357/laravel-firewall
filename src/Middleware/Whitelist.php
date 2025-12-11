<?php

namespace Edoardoagnelli1357\FirewallMiddleware;

use Edoardoagnelli1357\FirewallAbstracts\Middleware;

class Whitelist extends Middleware
{
    public function check($patterns)
    {
        return ($this->isWhitelist() === false);
    }
}
