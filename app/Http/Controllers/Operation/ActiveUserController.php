<?php
namespace App\Http\Controllers\Operation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ActiveUserController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword' , '');
        $dateTime = $request->input('dateTime' , '');
        $country_code = $request->input('country_code' , '');
        $appends['keyword'] = $keyword;
        $appends['dateTime'] = $dateTime;
        $appends['country_code'] = $country_code;
        $countries = config('country');
        $users = DB::connection('lovbee')->table('data_active_users')->join('users_countries' ,function($join){
            $join->on('users_countries.user_id' , '=' , 'data_active_users.user_id');
        })->join('users_phones' ,function($join){
            $join->on('data_active_users.user_id' , '=' , 'users_phones.user_id');
        });
        if (!empty($country_code)) {
            $users = $users->where('users_countries.country', strtolower($country_code));
        }
        if (!empty($keyword)) {
            $users = $users->where('user_name', 'like', "{$keyword}%")->orWhere('user_nick_name', 'like', "{$keyword}%");
        }
        if (!empty($dateTime)) {
            $users = $users->where('date', $dateTime);
        }
        $users = $users->select([
            'data_active_users.date',
            'data_active_users.user_id',
            'data_active_users.user_name',
            'data_active_users.created_at',
            'data_active_users.user_nick_name',
            'data_active_users.friend',
            'data_active_users.new',
            'data_active_users.detail',
            'data_active_users.user_created_at',
            'users_countries.country',
            'users_phones.user_phone_country',
            'users_phones.user_phone',

        ])->paginate(10)->appends($appends);
        return view('backstage.operation.active_user.index', compact('users' , 'keyword' , 'dateTime' , 'countries' , 'country_code'));
    }

}