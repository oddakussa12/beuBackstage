<?php
namespace App\Exports;

use Carbon\Carbon;
use App\Models\Passport\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromQuery,WithMapping
{
    use Exportable;

    private $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function map($user): array
    {
        return [
            $user->user_id,
            $user->user_name,
            $user->user_email,
            $user->user_src,
            $user->country_name,
            $user->user_ip_address,
            Carbon::parse($user->user_created_at)->addHours(8)->toDateTimeString()
        ];
    }

    public function query()
    {
        $now = Carbon::now();
        $params = $this->params;
        $user = User::query()
            ->leftJoin('countries', 'users.user_country_id', '=', 'countries.country_id')
            ->select(['users.user_id' , 'users.user_name' , 'users.user_email' , 'users.user_src'  , 'countries.country_name' , 'users.user_ip_address' , 'users.user_created_at']);
        if(!empty($params['field'])&&!empty($params['value']))
        {
            $user = $params['field']=='user_key'?$user->where('user_name' , 'like' , "%{$params['value']}%"):$user->where($params['field'] , $params['value']);
        }

        if(!empty($params['country_code']))
        {
            $counties = config('country');
            $codes = collect($counties)->pluck('code')->all();
            $k = array_search($params['country_code'] , $codes);
            $country = $k===false?0:intval($k+1);
            $user = $user->where('user_country_id' , $country);
        }
        if(!empty($params['dateTime']))
        {
            $startDate = $now->startOfDay()->toDateTimeString();
            $endDate = $now->endOfDay()->toDateTimeString();
            $dateTime =  $params['dateTime'];
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
            if(Carbon::createFromFormat('Y-m-d H:i:s' , $start)->DiffInDays($end)>15)
            {
                $start = $now->startOfDay()->toDateTimeString();
                $end = $now->endOfDay()->toDateTimeString();
            }
        }else{
            $start = $now->startOfDay()->toDateTimeString();
            $end = $now->endOfDay()->toDateTimeString();
        }
        return $user->where('user_created_at' , '>=' , $start)->where('user_created_at' , '<=' , $end);
    }
}