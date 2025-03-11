<?php

namespace Biigle\Tests\Modules\AuthNFDI;

use Biigle\Modules\AuthNFDI\ServiceProvider;
use TestCase;

class ServiceProviderTest extends TestCase
{
    public function testServiceProvider()
    {
        $this->assertTrue(class_exists(ServiceProvider::class));
    }
}
