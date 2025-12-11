<?php

namespace Edoardoagnelli1357\FirewallEvents;

class AttackDetected
{
    public $log;

    /**
     * Create a new event instance.
     */
    public function __construct($log)
    {
        $this->log = $log;
    }
}
