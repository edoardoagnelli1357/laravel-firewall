<?php

namespace Edoardoagnelli1357\FirewallMiddleware;

use Edoardoagnelli1357\FirewallAbstracts\Middleware;
use Edoardoagnelli1357\FirewallEvents\AttackDetected;

class Referrer extends Middleware
{
    public function check($patterns)
    {
        $status = false;

        if (! $blocked = config('firewall.middleware.' . $this->middleware . '.blocked')) {
            return $status;
        }

        if (in_array((string) $this->request->server('HTTP_REFERER'), (array) $blocked)) {
            $status = true;
        }

        if ($status) {
            $log = $this->log();

            event(new AttackDetected($log));
        }

        return $status;
    }
}
