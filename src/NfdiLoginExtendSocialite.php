<?php

namespace Biigle\Modules\AuthNfdi;

use SocialiteProviders\Manager\SocialiteWasCalled;

class LifeScienceLoginExtendSocialite
{
    public function handle(SocialiteWasCalled $socialiteWasCalled): void
    {
        $socialiteWasCalled->extendSocialite('nfdilogin', Provider::class);
    }
}
