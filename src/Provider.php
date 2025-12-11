<?php

namespace Akaunting\Firewall;

use Edoardoagnelli1357\FirewallCommands\UnblockIp;
use Edoardoagnelli1357\FirewallEvents\AttackDetected;
use Edoardoagnelli1357\FirewallListeners\BlockIp;
use Edoardoagnelli1357\FirewallListeners\CheckLogin;
use Edoardoagnelli1357\FirewallListeners\NotifyUsers;
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

        $this->app->register(\Jenssegers\Agent\AgentServiceProvider::class);
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
        $router->aliasMiddleware('firewall.agent', 'Edoardoagnelli1357\FirewallMiddleware\Agent');
        $router->aliasMiddleware('firewall.bot', 'Edoardoagnelli1357\FirewallMiddleware\Bot');
        $router->aliasMiddleware('firewall.ip', 'Edoardoagnelli1357\FirewallMiddleware\Ip');
        $router->aliasMiddleware('firewall.geo', 'Edoardoagnelli1357\FirewallMiddleware\Geo');
        $router->aliasMiddleware('firewall.lfi', 'Edoardoagnelli1357\FirewallMiddleware\Lfi');
        $router->aliasMiddleware('firewall.php', 'Edoardoagnelli1357\FirewallMiddleware\Php');
        $router->aliasMiddleware('firewall.referrer', 'Edoardoagnelli1357\FirewallMiddleware\Referrer');
        $router->aliasMiddleware('firewall.rfi', 'Edoardoagnelli1357\FirewallMiddleware\Rfi');
        $router->aliasMiddleware('firewall.session', 'Edoardoagnelli1357\FirewallMiddleware\Session');
        $router->aliasMiddleware('firewall.sqli', 'Edoardoagnelli1357\FirewallMiddleware\Sqli');
        $router->aliasMiddleware('firewall.swear', 'Edoardoagnelli1357\FirewallMiddleware\Swear');
        $router->aliasMiddleware('firewall.url', 'Edoardoagnelli1357\FirewallMiddleware\Url');
        $router->aliasMiddleware('firewall.whitelist', 'Edoardoagnelli1357\FirewallMiddleware\Whitelist');
        $router->aliasMiddleware('firewall.xss', 'Edoardoagnelli1357\FirewallMiddleware\Xss');
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
