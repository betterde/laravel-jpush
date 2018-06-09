<?php

namespace Betterde\Laravel\Jpush\Providers;

use JPush\Client;
use Illuminate\Config\Repository;
use Illuminate\Support\ServiceProvider;

/**
 * Date: 2018/6/9
 * @author George
 * @package Betterde\Laravel\Jpush\Providers
 */
class PushServiceProvider extends ServiceProvider
{
    /**
     * Date: 2018/6/9
     * @author George
     */
    public function boot()
    {
        /**
         * 发布配置文件
         */
        $this->publishes([
            __DIR__.'/../../config/jpush.php' => config_path('jpush.php'),
        ], 'jpush');
    }

    /**
     * Date: 2018/6/9
     * @author George
     */
    public function register()
    {
        $this->app->singleton('push', function ($app) {
            /**
             * @var Repository $config
             */
            $config = $app->config;
            return new Client($config->get('jpush.key'), $config->get('jpush.secret'), $config->get('jpush.path'), $config->get('jpush.retry_time'));
        });
    }
}
