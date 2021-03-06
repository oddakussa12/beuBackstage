<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 2019/5/19
 * Time: 18:35
 */
namespace App\Repositories\Eloquent;

use Carbon\Carbon;
use App\Repositories\EloquentBaseRepository;
use App\Repositories\Contracts\UserRepository;


class EloquentUserRepository  extends EloquentBaseRepository implements UserRepository
{
    public function videoViews($id)
    {
        return $this->model->find($id)->videoViews;
    }

    public function findByWhere($params)
    {
        $now  = Carbon::now();

        $user = $this->model->withTrashed()->join('users_countries', 'users.user_id', '=', 'users_countries.user_id');

        $sql = "select u.*,c.* from t_users where 1 ";

        if (!empty($params['field'])&&!empty($params['value'])) {
            $keyword = $params['value'];
            $user    = $params['field']=='user_key'
                ? $user->where(function ($query) use ($keyword) {$query->where('user_name', 'like', "%{$keyword}%")->orWhere('user_nick_name', 'like', "%{$keyword}%");})
                : $user->where('users.'.$params['field'] , $params['value']);
        }
        if (!empty($params['country_code'])) {
            //$counties = config('country');
            //$codes    = collect($counties)->pluck('code')->all();
            //$k        = array_search($params['country_code'] , $codes);
            //$country  = $k===false?0:intval($k+1);
            $user     = $user->where('users_countries.country', strtolower($params['country_code']));
        }
        if (!empty($params['dateTime'])) {
            $endDate   = $now->endOfDay()->toDateTimeString();
            $allDate   = explode(' - ' , $params['dateTime']);
            $start     = Carbon::createFromFormat('Y-m-d H:i:s' , array_shift($allDate))->subHours(8)->toDateTimeString();
            $end       = Carbon::createFromFormat('Y-m-d H:i:s' , array_pop($allDate))->subHours(8)->toDateTimeString();
            $end       = $end > $endDate ? $endDate : $end;
            $start     = $start > $end   ? $end     : $start;
            if ($end>$endDate) {
                $end   = $endDate;
            }
            if ($start>$end) {
                $start = $end;
            }

            $user = $user->where('users.user_created_at' , '>=' , $start)->where('users.user_created_at' , '<=' , $end);
        }
        return $user->orderBy("users.".$this->model->getCreatedAtColumn(), 'DESC')->paginate(10);
    }

    public function export($params)
    {

    }

    public function paginate($perPage = 10, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $now = Carbon::now();
        $request = request();
        $pageName = isset($this->model->paginateParamName)?$this->model->paginateParamName:$pageName;
        $name = $request->input('name' , '');
        $id = $request->input('id' , '');
        $key = $request->input('key' , '');
        $startDate = $now->startOfDay()->toDateTimeString();
        $endDate = $now->endOfDay()->toDateTimeString();
        $dateTime = $request->input('dateTime' , $startDate.' - '.$endDate);
        $date = $request->input('dateTime' , '');
        $allDate = explode(' - ' , $dateTime);
        $start = Carbon::createFromFormat('Y-m-d H:i:s' , array_shift($allDate))->subHours(8)->toDateTimeString();
        $end = Carbon::createFromFormat('Y-m-d H:i:s' , array_pop($allDate))->subHours(8)->toDateTimeString();
        if($end>$endDate)
        {
            $end =  $endDate;
        }
        if($start>$end)
        {
            $start = $end;
        }
        $user = $this->model->withTrashed();
        if(!empty($id))
        {
            $user = $user->where('user_id' , $id);
        }
        if(!empty($name))
        {
            $user = $user->where('user_name' , $name)->orWhere('user_email' , $name);
        }
        if(!empty($key))
        {
            $user = $user->where(function ($query) use ($key) {
                $query->where('user_name', 'like', "%{$key}%")->orWhere('user_nick_name', 'like', "%{$key}%");
            });
        }
        $counties = config('country');
        $country_code = $request->input('country_code' , '');
        $codes = collect($counties)->pluck('code')->all();
        $k = array_search($country_code , $codes);
        $country = $k===false?0:intval($k+1);
        if(!empty($country_code))
        {
            $user = $user->where('user_country_id' , $country);
        }
        if(!empty($date))
        {
            $user = $user->where('user_created_at' , '>=' , $start)->where('user_created_at' , '<=' , $end);
        }
        return $user->orderBy($this->model->getCreatedAtColumn(), 'DESC')->paginate($perPage , $columns , $pageName , $page);
    }

    public function update($model, $data)
    {
        $model->timestamps = false;

        $model->update($data);

        return $model;
    }
}
