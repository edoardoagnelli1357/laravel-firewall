<?php

namespace Edoardoagnelli1357\Firewall\Middleware;

use Edoardoagnelli1357\Firewall\Abstracts\Middleware;
use Edoardoagnelli1357\Firewall\Models\Ip as IpModel;
use Illuminate\Database\QueryException;

class Ip extends Middleware
{
    public function check($patterns)
    {
        $status = false;

        try {
            $ip = config('firewall.models.ip', IpModel::class);
            $status = $ip::blocked($this->ip())->pluck('id')->first();
        } catch (QueryException $e) {
            // Base table or view not found
            //$status = ($e->getCode() == '42S02') ? false : true;
        }

        return $status;
    }
}
