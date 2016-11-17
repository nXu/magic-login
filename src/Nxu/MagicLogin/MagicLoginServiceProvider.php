<?php
/**
 * (c) 2016. 10. 10..
 * Authors: nxu
 */

namespace Nxu\MagicLogin;

use Illuminate\Support\ServiceProvider;

class MagicLoginServiceProvider extends ServiceProvider
{
    public function __construct()
    {
    }

    /**
     * Bootstraps the application.
     */
    public function boot()
    {
        $configFile = __DIR__ . '/../../../resources/config/magiclogin.php';
        $this->mergeConfigFrom($configFile, 'magiclogin');

        $this->publishes([
            $configFile => config_path('magiclogin.php')
        ]);
    }

    /**
     * Registers application services.
     */
    public function register()
    {

    }
}
