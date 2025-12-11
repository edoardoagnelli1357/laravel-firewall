<?php

namespace Edoardoagnelli1357\FirewallTests\Feature;

use Edoardoagnelli1357\FirewallMiddleware\Ip;
use Edoardoagnelli1357\FirewallModels\Ip as Model;
use Edoardoagnelli1357\FirewallTests\TestCase;

class IpTest extends TestCase
{
    public function testShouldAllow()
    {
        $this->assertEquals('next', (new Ip())->handle($this->app->request, $this->getNextClosure()));
    }

    public function testShouldBlock()
    {
        Model::create(['ip' => '127.0.0.1', 'log_id' => 1]);

        $this->assertEquals('403', (new Ip())->handle($this->app->request, $this->getNextClosure())->getStatusCode());
    }
}
