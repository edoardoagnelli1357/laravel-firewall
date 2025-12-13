<?php

namespace Edoardoagnelli1357\Firewall\Listeners;

use Edoardoagnelli1357\FirewallEvents\AttackDetected as Event;
use Edoardoagnelli1357\FirewallNotifications\AttackDetected;
use Edoardoagnelli1357\FirewallNotifications\Notifiable;
use Throwable;

class NotifyUsers
{
    /**
     * Handle the event.
     *
     * @param Event $event
     *
     * @return void
     */
    public function handle(Event $event)
    {
        try {
            (new Notifiable)->notify(new AttackDetected($event->log));
        } catch (Throwable $e) {
            report($e);
        }
    }
}
