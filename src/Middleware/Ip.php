<?php

namespace Edoardoagnelli1357\FirewallMiddleware;

use Edoardoagnelli1357\FirewallAbstracts\Middleware;
use Edoardoagnelli1357\FirewallModels\Ip as Model;
use Illuminate\Database\QueryException;

class Ip extends Middleware
{
    public function check($patterns)
    {
        $status = false;

        try {
            $ip = config('firewall.models.ip', Model::class);
            $status = $ip::blocked($this->ip())->pluck('id')->first();
        } catch (QueryException $e) {
            // Base table or view not found
            //$status = ($e->getCode() == '42S02') ? false : true;
        }

        return $status;
    }
}
