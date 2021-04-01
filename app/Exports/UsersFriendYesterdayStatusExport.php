<?php
namespace App\Exports;

use App\Repositories\Contracts\UserRepository;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class UsersFriendYesterdayStatusExport extends StringValueBinder implements FromCollection,WithHeadings,ShouldAutoSize,WithCustomValueBinder
{
    use Exportable;

    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return string[]
     * 设置header头
     */
    public function headings(): array
    {
        return ['user_id', 'user_phone_country' , 'user_phone' , 'user_name', 'user_nick_name', 'user_created_at'];
    }

    public function collection()
    {
        $id = $this->id;
        $data = array();
        $index = Carbon::yesterday('Asia/Shanghai')->format('Ym');
        $date = Carbon::yesterday('Asia/Shanghai')->toDateString();
        $table = 'visit_logs_'.$index;
        DB::connection('lovbee')->table('users_friends')->where('user_id' , $id)->orderByDesc('friend_id')->chunk(100  , function ($friends) use (&$data , $table , $date){
            $friendIds = $friends->pluck('friend_id')->toArray();
            $users = app(UserRepository::class)->findByMany($friendIds);
            $activeUsers = DB::connection('lovbee')->table($table)->where('created_at' , $date)->whereIn('user_id' , $friendIds)->get()->map(function ($value) {return (array)$value;})->pluck('user_id')->unique()->values()->toArray();
            $userPhones = DB::connection('lovbee')->table('users_phones')->whereIn('user_id' , $friendIds)->get()->map(function ($value) {return (array)$value;})->values();
            $users = $users->reject(function ($user) use ($activeUsers) {
                return !in_array($user->user_id , $activeUsers);
            })->map(function($user) use ($userPhones){
                $phone = collect($userPhones->where('user_id' , $user->user_id)->first())->toArray();
                return array(
                    'user_id'=>$user->user_id,
                    'user_phone_country'=>$phone['user_phone_country'],
                    'user_phone'=>$phone['user_phone'],
                    'user_name'=>$user->user_name,
                    'user_nick_name'=>$user->user_nick_name,
                    'user_created_at'=>$user->user_created_at,
                );
            });
            $data = collect($data)->merge($users);
        });
        return collect($data)->values();
    }




}