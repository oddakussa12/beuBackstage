<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 2019/5/19
 * Time: 18:35
 */
namespace App\Repositories\Eloquent;

use App\Repositories\EloquentBaseRepository;
use App\Repositories\Contracts\ConfigRepository;


class EloquentConfigRepository  extends EloquentBaseRepository implements ConfigRepository
{
    /**
     * @inheritdoc
     */
    public function all()
    {
        if (method_exists($this->model, 'translations')) {
            return $this->model->with('translations')->orderBy($this->model->getCreatedAtColumn(), 'DESC')->get();
        }
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
}
