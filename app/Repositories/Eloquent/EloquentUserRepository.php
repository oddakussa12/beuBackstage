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
use DateTimeZone;
use Illuminate\Support\Facades\DB;


class EloquentUserRepository  extends EloquentBaseRepository implements UserRepository
{
    public function videoViews($id)
    {
        return $this->model->find($id)->videoViews;
    }

    public function findByWhere($params, $export=false)
    {
        $now  = Carbon::now();
        $user = $this->model->withTrashed();
        if($export){
            $user = $user->select('users.user_id', 'users.user_avatar', 'users.user_name', 'users.user_nick_name', 'users_phones.user_phone_country', 'users_phones.user_phone','users.user_gender', 'users_countries.country','users.user_created_at');
        }
        $user = $user->join('users_countries', 'users.user_id', '=', 'users_countries.user_id')
                     ->join('users_phones', 'users.user_id', '=', 'users_phones.user_id');

        if (!empty($params['user_id'])) {
            $user    = $user->where('users.user_id', $params['user_id']);
        }
        if (!empty($params['phone'])) {
            $user    = $user->where('users_phones.user_phone', 'like', "%{$params['phone']}%");
        }
        if (!empty($params['keyword'])) {
            $keyword = $params['keyword'];
            $user    = $user->where(function($query)use($keyword){$query->where('user_name', 'like', "%{$keyword}%")->orWhere('user_nick_name', 'like', "%{$keyword}%");});
        }
        if (!empty($params['country_code'])) {
            $user    = $user->where('users_countries.country', strtolower($params['country_code']));
        }
        if (!empty($params['dateTime'])) {
            $endDate = $now->endOfDay()->toDateTimeString();
            $allDate = explode(' - ' , $params['dateTime']);
            $start   = Carbon::createFromFormat('Y-m-d H:i:s' , array_shift($allDate))->subHours(8)->toDateTimeString();
            $end     = Carbon::createFromFormat('Y-m-d H:i:s' , array_pop($allDate))->subHours(8)->toDateTimeString();
            $end     = $end > $endDate ? $endDate : $end;
            $start   = $start > $end   ? $end     : $start;
            $user    = $user->whereBetween('users.user_created_at', [$start, $end]);
        }
        $user = $user->orderBy("users.".$this->model->getCreatedAtColumn(), 'DESC');
        if ($export===false) {
            $result  = $user->paginate(10);
        } else {
            $result  = $user->get();
        }
        $result = $this->friend($result);
        return $result;
    }

    public function friend($result) {
        $userIds = $result->pluck('user_id')->toArray();
        $logs    = DB::connection('lovbee')->table('status_logs')->whereIn('user_id', $userIds)->groupBy('user_id')->orderByDesc('created_at')->get();
        $friends = DB::connection('lovbee')->table('users_friends')->select(DB::raw('count(1) num'), 'user_id')->whereIn('user_id', $userIds)->groupBy('user_id')->get();

        foreach ($result as $item) {
            $item->ip = $item->time = '';
            $item->friends = 0;

            foreach ($logs as $log) {
                if ($item->user_id==$log->user_id) {
                    $item->ip   = $log->ip;
                    $item->time = date('Y-m-d H:i:s', $log->time);
                }
            }
            foreach ($friends as $friend) {
                if ($item->user_id==$friend->user_id) {
                    $item->friends = $friend->num;
                }
            }
        }

        return $result;
    }

    public function findMessage($params, $detail=false, $export=false)
    {
        $sort = !empty($params['sort']) ? $params['sort'] : 'chat_from_id';
        $time = !empty($params['dateTime']) ? $params['dateTime'] : date('Y-m-d', time());
        $date = !empty($params['dateTime']) ? date('Ym', strtotime($params['dateTime'])) : date('Ym');
        $num  = $sort=='chat_from_id' ? 'send' : 'receive';

        $table= 'ry_chats_'.$date;
        $user = DB::connection('lovbee')->table($table);
        $user = $user->select(DB::raw("`t_$table`.$sort,
                `t_users`.user_id,`t_users`.user_name, `t_users`.user_nick_name, `t_users`.user_avatar, `t_users`.user_gender,`t_users`.user_created_at,
                count(`t_$table`.`chat_id`) as $num,
                `t_users_countries`.country,
                `t_users_phones`.user_phone_country, `t_users_phones`.user_phone"));
        $user = $user
            ->join('users', 'users.user_id', '=', $table.'.'.$sort)
            ->join('users_countries', 'users.user_id', '=', 'users_countries.user_id')
            ->join('users_phones', 'users.user_id', '=', 'users_phones.user_id');

        if (!empty($params['user_id'])) {
            $user    = $user->where('users.user_id', $params['user_id']);
        }
        if (!empty($params['phone'])) {
            $user    = $user->where('users_phones.user_phone', 'like', "%{$params['phone']}%");
        }
        if (!empty($params['keyword'])) {
            $keyword = $params['keyword'];
            $user    = $user->where(function($query)use($keyword){$query->where('user_name', 'like', "%{$keyword}%")->orWhere('user_nick_name', 'like', "%{$keyword}%");});
        }
        if (!empty($params['country_code'])) {
            $user    = $user->where('users_countries.country', strtolower($params['country_code']));
        }

        $start = Carbon::createFromFormat('Y-m-d H:i:s', $time.' 00:00:00')->subHours(8)->toDateTimeString();
        $end   = Carbon::createFromFormat('Y-m-d H:i:s', $time.' 23:59:59')->subHours(8)->toDateTimeString();
        $user  = $user->whereBetween($table.'.chat_created_at', [$start, $end]);
        if (empty($detail)) {
            $user = $user->groupBy($table.'.'.$sort);
        } else {
            $chat = $sort=='chat_from_id' ? 'chat_to_id' : 'chat_from_id';
            $user = $user->groupBy($table.'.'.$chat);
        }
        $user  = $user->orderByDesc(DB::raw("count(`t_$table`.`chat_id`)"));

        if ($export===false) {
            $result = $user->paginate(10);
        } else {
            $result  = $user->get();
        }
        $chat_id = $sort=='chat_from_id' ? 'chat_to_id' : 'chat_from_id';

        $userIds = $result->pluck($sort)->toArray();
        $chatFrom= $sort=='chat_from_id' ? 'receive'    : 'send';
        $messages= DB::connection('lovbee')->table($table)->select($chat_id, DB::raw("count(chat_id) as num"))
            ->whereIn($chat_id, $userIds)->whereBetween($table.'.chat_created_at', [$start, $end])->groupBy($chat_id)->get();

        $result = $this->friend($result);
        foreach ($result as $item) {
            $item->$chatFrom = 0;
            foreach ($messages as $message) {
                if ($message->$chat_id==$item->user_id) {
                    $item->$chatFrom = $message->num;
                }
            }
        }
        return $result;


    }

    public function friendManage($params)
    {
        $user = DB::connection('lovbee')->table('users_friends');
        $user = $user->select(DB::raw("
                `t_users`.user_id,`t_users`.user_name, `t_users`.user_nick_name, `t_users`.user_avatar, `t_users`.user_gender,`t_users`.user_created_at,
                count(`t_users_friends`.`id`) as friends,
                `t_users_countries`.country,
                `t_users_phones`.user_phone_country, `t_users_phones`.user_phone"));
        $user = $user
            ->join('users', 'users.user_id', '=', 'users_friends.user_id')
            ->join('users_countries', 'users.user_id', '=', 'users_countries.user_id')
            ->join('users_phones', 'users.user_id', '=', 'users_phones.user_id');
        if (!empty($params['country_code'])) {
            $user = $user->where('users_countries.country', strtolower($params['country_code']));
        }

        if (!empty($params['num'])) {
            $user = $user->having(DB::raw("count(t_users_friends.id)"), '>=', $params['num']);
        }
        $user   = $user->groupBy('users_friends.user_id');
        if (isset($params['sort']) && $params['sort']=='friend') {
            $user = $user->orderByDesc(DB::raw("count(`t_users_friends`.`id`)"));
        }
        return $user->paginate(10);
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
        $countries = config('country');
        $country_code = $request->input('country_code' , '');
        $codes = collect($countries)->pluck('code')->all();
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
