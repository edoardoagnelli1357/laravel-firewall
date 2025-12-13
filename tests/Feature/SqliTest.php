<?php

namespace Edoardoagnelli1357\Firewall\Tests\Feature;

use Edoardoagnelli1357\Firewall\Tests\TestCase as TestsTestCase;
use Edoardoagnelli1357\Firewall\Middleware\Sqli;
use Edoardoagnelli1357\FirewallTests\TestCase;

class SqliTest extends TestsTestCase
{
    public function testShouldAllow()
    {
        $this->assertEquals('next', (new Sqli())->handle($this->app->request, $this->getNextClosure()));
    }

    public function testShouldBlock()
    {
        $this->app->request->query->set('foo', '-1+union+select+1,2,3,4,5,6,7,8,9,(SELECT+password+FROM+users+WHERE+ID=1)');

        $this->assertEquals('403', (new Sqli())->handle($this->app->request, $this->getNextClosure())->getStatusCode());
    }
}
