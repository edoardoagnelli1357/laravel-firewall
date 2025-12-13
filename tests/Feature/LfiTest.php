<?php

namespace Edoardoagnelli1357\Firewall\Tests\Feature;

use Edoardoagnelli1357\Firewall\Tests\TestCase as TestsTestCase;
use Edoardoagnelli1357\Firewall\Middleware\Lfi;
use Edoardoagnelli1357\FirewallTests\TestCase;

class LfiTest extends TestsTestCase
{
    public function testShouldAllow()
    {
        $this->assertEquals('next', (new Lfi())->handle($this->app->request, $this->getNextClosure()));
    }

    public function testShouldBlock()
    {
        $this->app->request->query->set('foo', '../../../../etc/passwd');

        $this->assertEquals('403', (new Lfi())->handle($this->app->request, $this->getNextClosure())->getStatusCode());
    }
}
