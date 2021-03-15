<?php

namespace App\Providers;


use DB;
use App\Models\Menu;
use App\Models\Role;
use App\Models\Admin;
use App\Models\Content\Post;
use App\Models\Content\Topic;
use App\Models\Content\PostComment;
use App\Models\Content\Video;
use App\Models\Content\Category;
use App\Models\Content\Tag;
use App\Models\Permission;
use App\Models\Passport\User;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Contracts\MenuRepository;
use App\Repositories\Contracts\RoleRepository;
use App\Repositories\Contracts\AdminRepository;
use App\Repositories\Contracts\PermissionRepository;
use App\Repositories\Contracts\Content\PostRepository;
use App\Repositories\Contracts\Content\PostCommentRepository;
use App\Repositories\Contracts\Content\VideoRepository;
use App\Repositories\Contracts\Content\CategoryRepository;
use App\Repositories\Contracts\Content\TagRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use App\Repositories\Eloquent\EloquentRoleRepository;
use App\Repositories\Eloquent\EloquentMenuRepository;
use App\Repositories\Eloquent\EloquentAdminRepository;
use App\Repositories\Eloquent\EloquentPermissionRepository;
use App\Repositories\Eloquent\Content\EloquentVideoRepository;
use App\Repositories\Eloquent\Content\EloquentPostRepository;
use App\Repositories\Eloquent\Content\EloquentPostCommentRepository;
use App\Repositories\Eloquent\Content\EloquentCategoryRepository;
use App\Repositories\Eloquent\Content\EloquentTagRepository;
use App\Repositories\Contracts\Content\TopicRepository;
use App\Repositories\Eloquent\Content\EloquentTopicRepository;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);
        $this->menuComposer();
        $this->setLocalesConfigurations();
       /* \DB::listen(function ($query) {
            $tmp = str_replace('?', '"'.'%s'.'"', $query->sql);
            $qBindings = [];
            foreach ($query->bindings as $key => $value) {
                if (is_numeric($key)) {
                    $qBindings[] = $value;
                } else {
                    $tmp = str_replace(':'.$key, '"'.$value.'"', $tmp);
                }
            }
            $tmp   = vsprintf($tmp, $qBindings);
            $tmp   = str_replace("\\", "", $tmp);
            $admin = ['bs_roles', 'bs_translations', 'bs_menus_translations', 'bs_permissions', 'bs_menus', 'information_schema', 'bs_audits', 'bs_admins'];
            $flag  = true;
            foreach ($admin as $item) {
                if (stripos($query->sql, $item)) {
                    $flag = false;
                }
            }
            if ($flag) {
                \Log::info(' execution time: '.$query->time.'ms; '.$tmp."\n\n\t");
            }
        })*/;

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

        $this->app->bind(PostRepository::class, function () {
            $repository = new EloquentPostRepository(new Post());
            return $repository;
        });
        $this->app->bind(TopicRepository::class, function () {
            $repository = new EloquentTopicRepository(new Topic());
            return $repository;
        });
        $this->app->bind(PostCommentRepository::class, function () {
            $repository = new EloquentPostCommentRepository(new PostComment());
            return $repository;
        });

        $this->app->bind(VideoRepository::class, function ($app) {
            $repository = new EloquentVideoRepository(new Video() , $app['request']);
            return $repository;
        });
        $this->app->bind(CategoryRepository::class, function () {
            $repository = new EloquentCategoryRepository(new Category());
            return $repository;
        });
        $this->app->bind(TagRepository::class, function () {
            $repository = new EloquentTagRepository(new Tag());
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
