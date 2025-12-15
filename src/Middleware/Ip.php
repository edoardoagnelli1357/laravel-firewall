<?php

namespace Edoardoagnelli1357\Firewall\Middleware;

use Edoardoagnelli1357\Firewall\Abstracts\Middleware;
use Edoardoagnelli1357\Firewall\Models\Ip as DefaultIpModel;
use Illuminate\Database\QueryException;

class Ip extends Middleware
{
    /**
     * Check if IP is blocked.
     *
     * @param mixed $patterns
     * @return bool
     */
    public function check($patterns): bool
    {
        try {
            /** @var class-string $ipModel */
            $ipModel = config('firewall.models.ip', DefaultIpModel::class);

            // expected: scopeBlocked($query, $ip)
            return $ipModel::blocked($this->ip())->exists();

        } catch (QueryException $e) {
            // Table not published / migrated yet
            if ($e->getCode() === '42S02') {
                return false;
            }

            // any other DB error = block request for safety
            return true;
        }
    }
}
