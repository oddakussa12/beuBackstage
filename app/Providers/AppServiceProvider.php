<?php

namespace App\Providers;


use App\Models\Menu;
use App\Models\Role;
use App\Models\Admin;
use App\Models\Permission;
use App\Models\Passport\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Contracts\MenuRepository;
use App\Repositories\Contracts\RoleRepository;
use App\Repositories\Contracts\AdminRepository;
use App\Repositories\Contracts\PermissionRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use App\Repositories\Eloquent\EloquentRoleRepository;
use App\Repositories\Eloquent\EloquentMenuRepository;
use App\Repositories\Eloquent\EloquentAdminRepository;
use App\Repositories\Eloquent\EloquentPermissionRepository;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        $this->menuComposer();
        $this->setLocalesConfigurations();
        DB::listen(function ($query) {
            $tmp = str_replace('?', '"'.'%s'.'"', $query->sql);
            $tmp = str_replace('%Y-%m-%d', 'YY-mm-dd', $tmp);

            $qBindings = [];
            foreach ($query->bindings as $key => $value) {
                if (is_numeric($key)) {
                    $qBindings[] = $value;
                } else {
                    $tmp = str_replace(':'.$key, '"'.$value.'"', $tmp);
                }
            }
            $flag  = true;
            $tmp   = vsprintf($tmp, $qBindings);
            $tmp   = str_replace('YY-mm-dd', '%Y-%m-%d', $tmp);
            $tmp   = str_replace("\\", "", $tmp);
            $admin = ['bs_roles', 'bs_translations', 'bs_menus_translations', 'bs_permissions', 'bs_menus', 'information_schema', 'bs_audits', 'bs_admins'];
            foreach ($admin as $item) {
                if (stripos($query->sql, $item)) {
                    $flag = false;
                }
            }
            if ($flag) {
                Log::info(' execution time: '.$query->time.'ms; '.$tmp."\n\n\t");
            }
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (class_exists(TranslationServiceProvider::class)) {
            $this->app->register(TranslationServiceProvider::class);
        }

        if (class_exists(ConfigServiceProvider::class)) {
            $this->app->register(ConfigServiceProvider::class);
        }
        $this->registerBindings();
    }

    private function registerBindings()
    {
        $this->app->bind(MenuRepository::class, function () {
            $repository = new EloquentMenuRepository(new Menu());
            return $repository;
        });
        $this->app->bind(AdminRepository::class, function () {
            $repository = new EloquentAdminRepository(new Admin());
            return $repository;
        });
        $this->app->bind(PermissionRepository::class, function () {
            $repository = new EloquentPermissionRepository(new Permission());
            return $repository;
        });
        $this->app->bind(RoleRepository::class, function () {
            $repository = new EloquentRoleRepository(new Role());
            return $repository;
        });
        $this->app->bind(UserRepository::class, function () {
            $repository = new EloquentUserRepository(new User());
            return $repository;
        });

    }

    private function menuComposer()
    {
        View::composer(
            ['layouts.side' , 'layouts.bread_crumb' , 'backstage.menu.index'], 'App\Composers\MenuComposer'
        );
    }

    /**
     * Set the locale configuration for
     * - laravel localization
     * - laravel translatable
     */
    private function setLocalesConfigurations()
    {
        $localeConfig = $this->app['cache']
//            ->tags('config', 'global')
            ->remember(
                'config.locales',
                120,
                function () {
                    return array();
                    //return DB::table('configs')->whereConfig_key('set.translatable.locales')->first();
                }
            );
        if($localeConfig){
            $locales = json_decode($localeConfig->configValue);
            $availableLocales = [];
            foreach ($locales as $locale) {
                $availableLocales = array_merge($availableLocales, [$locale => config("laravellocalization.supportedLocales.$locale")]);
            }

            $laravelDefaultLocale = $this->app->config->get('app.locale');

            if (! in_array($laravelDefaultLocale, array_keys($availableLocales))) {
                $this->app->config->set('app.locale', array_keys($availableLocales)[0]);
            }
            $this->app->config->set('laravellocalization.supportedLocales', $availableLocales);
            $this->app->config->set('translatable.locales', $locales);
        }
    }
}
