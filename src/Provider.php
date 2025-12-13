<?php

namespace Edoardoagnelli1357\Firewall;

use Edoardoagnelli1357\Firewall\Commands\UnblockIp;
use Edoardoagnelli1357\Firewall\Events\AttackDetected;
use Edoardoagnelli1357\Firewall\Listeners\BlockIp;
use Edoardoagnelli1357\Firewall\Listeners\CheckLogin;
use Edoardoagnelli1357\Firewall\Listeners\NotifyUsers;
use Illuminate\Auth\Events\Failed as LoginFailed;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Router $router
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $langPath = 'vendor/firewall';

        $langPath = (function_exists('lang_path'))
            ? lang_path($langPath)
            : resource_path('lang/' . $langPath);

        $this->publishes([
            __DIR__ . '/Config/firewall.php'                                            => config_path('firewall.php'),
            __DIR__ . '/Migrations/2019_07_15_000000_create_firewall_ips_table.php'     => database_path('migrations/2019_07_15_000000_create_firewall_ips_table.php'),
            __DIR__ . '/Migrations/2019_07_15_000000_create_firewall_logs_table.php'    => database_path('migrations/2019_07_15_000000_create_firewall_logs_table.php'),
            __DIR__ . '/Resources/lang'                                                 => $langPath,
        ], 'firewall');

        $this->registerMiddleware($router);
        $this->registerListeners();
        $this->registerTranslations($langPath);
        $this->registerCommands();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/firewall.php', 'firewall');
    }

    /**
     * Register middleware.
     *
     * @param Router $router
     *
     * @return void
     */
    public function registerMiddleware($router)
    {
        $router->middlewareGroup('firewall.all', config('firewall.all_middleware'));
        $router->aliasMiddleware('firewall.agent', 'Edoardoagnelli1357\Firewall\Middleware\Agent');
        $router->aliasMiddleware('firewall.bot', 'Edoardoagnelli1357\Firewall\Middleware\Bot');
        $router->aliasMiddleware('firewall.ip', 'Edoardoagnelli1357\Firewall\Middleware\Ip');
        $router->aliasMiddleware('firewall.geo', 'Edoardoagnelli1357\Firewall\Middleware\Geo');
        $router->aliasMiddleware('firewall.lfi', 'Edoardoagnelli1357\Firewall\Middleware\Lfi');
        $router->aliasMiddleware('firewall.php', 'Edoardoagnelli1357\Firewall\Middleware\Php');
        $router->aliasMiddleware('firewall.referrer', 'Edoardoagnelli1357\Firewall\Middleware\Referrer');
        $router->aliasMiddleware('firewall.rfi', 'Edoardoagnelli1357\Firewall\Middleware\Rfi');
        $router->aliasMiddleware('firewall.session', 'Edoardoagnelli1357\Firewall\Middleware\Session');
        $router->aliasMiddleware('firewall.sqli', 'Edoardoagnelli1357\Firewall\Middleware\Sqli');
        $router->aliasMiddleware('firewall.swear', 'Edoardoagnelli1357\Firewall\Middleware\Swear');
        $router->aliasMiddleware('firewall.url', 'Edoardoagnelli1357\Firewall\Middleware\Url');
        $router->aliasMiddleware('firewall.whitelist', 'Edoardoagnelli1357\Firewall\Middleware\Whitelist');
        $router->aliasMiddleware('firewall.xss', 'Edoardoagnelli1357\Firewall\Middleware\Xss');
    }

    /**
     * Register listeners.
     *
     * @return void
     */
    public function registerListeners()
    {
        $this->app['events']->listen(AttackDetected::class, BlockIp::class);
        $this->app['events']->listen(AttackDetected::class, NotifyUsers::class);
        $this->app['events']->listen(LoginFailed::class, CheckLogin::class);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations($langPath)
    {
        $this->loadTranslationsFrom(__DIR__ . '/Resources/lang', 'firewall');

        $this->loadTranslationsFrom($langPath, 'firewall');
    }

    public function registerCommands()
    {
        $this->commands(UnblockIp::class);

        if (config('firewall.cron.enabled')) {
            $this->app->booted(function () {
                app(Schedule::class)->command('firewall:unblockip')->cron(config('firewall.cron.expression'));
            });
        }
    }
}
