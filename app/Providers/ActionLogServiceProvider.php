<?php


namespace App\Providers;

use App\Models\ActionLog;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\ActionLogRepository;
use App\Repositories\Eloquent\EloquentActionLogRepository;

class ActionLogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
//        $model = config("actionlog");
//        if ($model) {
//            foreach ($model as $k => $v) {
//                $v::updated(function ($data) {
//                    ActionLog::createActionLog('update', "更新的id:" . $data->id);
//                });
//                $v::created(function ($data) {
//                    ActionLog::createActionLog('add', "添加的id:" . $data->id);
//                });
//                $v::deleted(function ($data) {
//                    ActionLog::createActionLog('delete', "删除的id:" . $data->id);
//                });
//            }
//        }
    }
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('ActionLog', function () {
            return new EloquentActionLogRepository(new ActionLog());
        });
    }
}