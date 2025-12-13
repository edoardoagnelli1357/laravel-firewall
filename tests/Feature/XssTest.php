<?php

namespace Edoardoagnelli1357\Firewall\Tests\Feature;

use Edoardoagnelli1357\Firewall\Tests\TestCase as TestsTestCase;
use Edoardoagnelli1357\Firewall\Middleware\Xss;
use Edoardoagnelli1357\FirewallTests\TestCase;

class XssTest extends TestsTestCase
{
    public function testShouldAllow()
    {
        $this->assertEquals('next', (new Xss())->handle($this->app->request, $this->getNextClosure()));
    }

    public function testShouldBlock()
    {
        $this->app->request->query->set('foo', '<script>alert(123)</script>');

        $this->assertEquals('403', (new Xss())->handle($this->app->request, $this->getNextClosure())->getStatusCode());
    }
}
