<?php

namespace Edoardoagnelli1357\Firewall\Tests\Feature;

use Edoardoagnelli1357\Firewall\Tests\TestCase as TestsTestCase;
use Edoardoagnelli1357\Firewall\Middleware\Ip;
use Edoardoagnelli1357\Firewall\Models\Ip as Model;

class IpTest extends TestsTestCase
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
