<?php

namespace App\Providers;

use App\Models\Translation;
use App\Repositories\Contracts\FileTranslationRepository;
use App\Repositories\Contracts\TranslationRepository;
use App\Repositories\Eloquent\EloquentFileTranslationRepository;
use App\Repositories\Eloquent\EloquentTranslationRepository;
use App\Services\TranslationLoader;
use Illuminate\Translation\TranslationServiceProvider as IlluminateTranslationServiceProvider;

class TranslationServiceProvider extends IlluminateTranslationServiceProvider
{
    public function register()
    {
        parent::register();
    }

    protected function registerLoader()
    {
        $this->app->singleton('translation.loader', function ($app) {
            return new TranslationLoader($app['files'], $app['path.lang']);
        });
    }

    public function boot()
    {
        $this->app->bind(TranslationRepository::class, function () {
            $repository = new EloquentTranslationRepository(new Translation());
            return $repository;
        });

        $this->app->bind(FileTranslationRepository::class, function ($app) {
            return new EloquentFileTranslationRepository($app['files'], $app['translation.loader']);
        });
    }
}
