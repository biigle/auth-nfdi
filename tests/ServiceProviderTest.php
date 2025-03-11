<?php

namespace Biigle\Tests\Modules\AuthNfdi;

use Biigle\Modules\AuthNfdi\ServiceProvider;
use TestCase;

class ServiceProviderTest extends TestCase
{
    public function testServiceProvider()
    {
        $this->assertTrue(class_exists(ServiceProvider::class));
    }
}
