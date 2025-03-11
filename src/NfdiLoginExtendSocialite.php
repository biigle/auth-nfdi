<?php

namespace Biigle\Modules\AuthNfdi;

use SocialiteProviders\Manager\SocialiteWasCalled;

class NfdiLoginExtendSocialite
{
    public function handle(SocialiteWasCalled $socialiteWasCalled): void
    {
        $socialiteWasCalled->extendSocialite('nfdilogin', Provider::class);
    }
}
