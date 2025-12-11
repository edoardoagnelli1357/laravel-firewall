<?php

namespace Edoardoagnelli1357\FirewallMiddleware;

use Edoardoagnelli1357\FirewallAbstracts\Middleware;
use Edoardoagnelli1357\FirewallEvents\AttackDetected;
use Jenssegers\Agent\Agent;

class Bot extends Middleware
{
    public function check($patterns)
    {
        $agent = new Agent();

        if (! $agent->isRobot()) {
            return false;
        }

        if (! $crawlers = config('firewall.middleware.' . $this->middleware . '.crawlers')) {
            return false;
        }

        $status = false;

        if (! empty($crawlers['allow']) && ! in_array((string) $agent->robot(), (array) $crawlers['allow'])) {
            $status = true;
        }

        if (in_array((string) $agent->robot(), (array) $crawlers['block'])) {
            $status = true;
        }

        if ($status) {
            $log = $this->log();

            event(new AttackDetected($log));
        }

        return $status;
    }
}
