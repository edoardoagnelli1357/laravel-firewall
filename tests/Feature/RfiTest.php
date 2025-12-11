<?php

namespace Edoardoagnelli1357\FirewallTests\Feature;

use Edoardoagnelli1357\FirewallMiddleware\Rfi;
use Edoardoagnelli1357\FirewallTests\TestCase;

class RfiTest extends TestCase
{
    public function testShouldAllow()
    {
        $this->assertEquals('next', (new Rfi())->handle($this->app->request, $this->getNextClosure()));
    }

    public function testShouldBlock()
    {
        $this->app->request->query->set('foo', 'https://attacker.example.com/evil.php');

        $this->assertEquals('403', (new Rfi())->handle($this->app->request, $this->getNextClosure())->getStatusCode());
    }
}
