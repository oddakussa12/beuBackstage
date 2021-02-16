<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 2019/5/19
 * Time: 18:35
 */
namespace App\Repositories\Eloquent\Content;

use App\Repositories\Contracts\Content\TopicRepository;
use App\Repositories\EloquentBaseRepository;
use Carbon\Carbon;

class EloquentTopicRepository  extends EloquentBaseRepository implements TopicRepository
{
    /**
     * @inheritdoc
     */
    public function all()
    {
        return $this->model->get();
    }

    /**
     * Create or update the settings
     * @param $settings
     * @return mixed
     */
    public function createOrUpdate($settings)
    {
        foreach ($settings as $settingName => $settingValues) {
            if($setting = $this->findByName($settingName))
            {
                $this->updateSetting($setting , $settingValues);
                continue;
            }
            $this->createForKey($settingName , $settingValues);
        }
    }

    private function updateSetting($setting, $settingValues)
    {
        $setting->config_value = $settingValues;
        $setting->save();
        return $setting;
    }

    private function createForKey($settingName, $settingValues)
    {
        $setting = new $this->model();
        $setting->config_key = $settingName;
        $setting->config_value = $settingValues;
        $setting->save();
        return $setting;
    }
    /**
     * Find a setting by its name
     * @param $settingName
     * @return mixed
     */
    public function findByName($settingName)
    {
        return $this->model->where('config_key', $settingName)->first();
    }

    public function findByWhere($params)
    {
        $model = $this->model;
        //$model = $this->model->withTrashed();
        $now = Carbon::now();
        if (!empty($params['v'])) {
            $model = $model->where('topic_content','LIKE', "%{$params['v']}%");
        }
        if(!empty($params['dateTime'])) {
            $endDate = $now->endOfDay()->toDateTimeString();
            $allDate = explode(' - ' , $params['dateTime']);
            $start   = Carbon::createFromFormat('Y-m-d H:i:s' , array_shift($allDate))->subHours(0)->toDateTimeString();
            $end     = Carbon::createFromFormat('Y-m-d H:i:s' , array_pop($allDate))->subHours(0)->toDateTimeString();
            $start   = strtotime($start);
            $end     = strtotime($end);
            $end     = $end   > $endDate ? $endDate : $end;
            $start   = $start > $end     ? $end     : $start;
            $model   = $model->where('start_time', '>=', $start)->where('end_time', '<=', $end);
        }

        return $model->orderBy($this->model->getCreatedAtColumn(), 'DESC')->paginate(10);
    }

    public function find($id)
    {
        return $this->model->find($id);
        return $this->model->withTrashed()->find($id);
    }

    public function update($model, $data)
    {
        $model->update($data);

        return $model;
    }

    public function getList($limit=20)
    {
        $time = Carbon::createFromFormat('Y-m-d H:i:s' , date('Y-m-d H:i:s'))->toDateTimeString();
        return $this->model->select('topic_content', 'flag', 'sort')
            ->where('is_delete', '<', 1)
            ->where('start_time', '<=', $time)
            ->where('end_time', '>=', $time)
            ->orderBy('flag', 'ASC')
            ->orderBy('sort', 'DESC')
            ->offset(0)
            ->limit(20)->get()->toArray();
        //->limit(20)->toSql();
    }
}
