<?php

namespace Edoardoagnelli1357\FirewallMiddleware;

use Edoardoagnelli1357\FirewallAbstracts\Middleware;
use Edoardoagnelli1357\FirewallEvents\AttackDetected;

class Url extends Middleware
{
    public function check($patterns)
    {
        $protected = false;

        if (! $inspections = config('firewall.middleware.' . $this->middleware . '.inspections')) {
            return $protected;
        }

        foreach ($inspections as $inspection) {
            if (! $this->request->is($inspection)) {
                continue;
            }

            $protected = true;

            break;
        }

        if ($protected) {
            $log = $this->log();

            event(new AttackDetected($log));
        }

        return $protected;
    }
}
