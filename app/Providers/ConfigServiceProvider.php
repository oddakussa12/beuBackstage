<?php


namespace App\Providers;

use App\Models\Config;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Cache\CacheConfigDecorator;
use App\Repositories\Contracts\ConfigRepository;
use App\Repositories\Eloquent\EloquentConfigRepository;

class ConfigServiceProvider extends ServiceProvider
{

    /**
     * 注册服务.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ConfigRepository::class, function () {
            $repository = new EloquentConfigRepository(new Config());
            if (! config('app.cache')) {
                return $repository;
            }
            return new CacheConfigDecorator($repository);
        });
    }

    /**
     * 引导服务。
     *
     * @return void
     */
    public function boot()
    {

    }


}