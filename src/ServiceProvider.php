<?php

namespace Biigle\Modules\AuthNfdi;

use Biigle\Services\Modules;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class ServiceProvider extends BaseServiceProvider
{

   /**
   * Bootstrap the application events.
   *
   * @param Modules $modules
   * @param  Router  $router
   * @return  void
   */
    public function boot(Modules $modules, Router $router)
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'auth-nfdi');
        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');

        $router->group([
            'namespace' => 'Biigle\Modules\AuthNfdi\Http\Controllers',
            'middleware' => 'web',
        ], function ($router) {
            require __DIR__.'/Http/routes.php';
        });

        $modules->register('auth-nfdi', [
            'viewMixins' => [
                'loginButton',
                'registerButton',
                'settingsThirdPartyAuthentication',
            ],
        ]);

        $this->publishes([
            __DIR__.'/public/assets' => public_path('vendor/auth-nfdi'),
        ], 'public');

        Event::listen(
            SocialiteWasCalled::class,
            [NfdiLoginExtendSocialite::class, 'handle']
        );
    }

    /**
    * Register the service provider.
    *
    * @return  void
    */
    public function register()
    {
        //
    }
}
