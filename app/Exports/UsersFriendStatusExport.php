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

class UsersFriendStatusExport extends StringValueBinder implements FromCollection,WithHeadings,ShouldAutoSize,WithCustomValueBinder
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
        return ['user_id', 'user_phone_country' , 'user_phone' , 'user_name', 'user_nick_name', 'user_created_at' , 'activeTime'];
    }

    public function collection()
    {
        $id = $this->id;
        $data = array();
        DB::connection('lovbee')->table('users_friends')->where('user_id' , $id)->orderByDesc('friend_id')->chunk(100  , function ($friends) use (&$data){
            $friendIds = $friends->pluck('friend_id')->toArray();
            $result = $this->httpRequest('api/backstage/last/online' , array('user_id'=>join(',' , $friendIds)) , "GET");
            if(is_array($result))
            {
                $activeUsers = $result['users'];
                $activeUsers = collect($activeUsers)->reject(function ($value, $key) {
                    return $value == 946656000;
                })->toArray();
                $userIds = array_keys($activeUsers);
                $users = app(UserRepository::class)->findByMany($userIds);
                $userPhones = DB::connection('lovbee')->table('users_friends')->whereIn('user_id' , $userIds)->get()->map(function ($value) {return (array)$value;})->values();
                Log::info('$userPhones' , $userPhones);
                Log::info('$suserPhones' , collect($userPhones->where('user_id' , 273)->first())->toArray());
                $users = $users->map(function($user) use ($activeUsers , $userPhones){
//                    $phone = collect($userPhones)->where('user_id' , $user->user_id)->all();
//                    Log::info('test' , collect($phone)->toArray());
                    return array(
                        'user_id'=>$user->user_id,
//                        'user_phone'=>$phone->pluck('user_phone')->first(),
//                        'user_phone_country'=>$phone->pluck('user_phone_country')->first(),
                        'user_name'=>$user->user_name,
                        'user_nick_name'=>$user->user_nick_name,
                        'user_created_at'=>$user->user_created_at,
                        'activeTime'=>Carbon::createFromTimestamp($activeUsers[$user->user_id] , "Asia/Shanghai")->toDateTimeString(),
                    );
                });
                $data = collect($data)->merge($users);
            }
        });
        return collect($data)->values();
    }


    /**
     * @param $url
     * @param $data
     * @param string $method
     * @param bool $json
     * @return bool
     * HTTP Request
     */
    private function httpRequest($url, $data=array(), $method='POST', $json=false)
    {
        try {
            $client = new Client();
            foreach ($data as &$datum) {
                $datum = is_array($datum) ? json_encode($datum, JSON_UNESCAPED_UNICODE) : $datum;
            }
            $signature = common_signature($data);
            $data['signature'] = $signature;
            $data     = $json ? json_encode($data, JSON_UNESCAPED_UNICODE) : $data;
            if(strtolower($method)=='get')
            {
                $response = $client->request($method, front_url($url), array('query'=>$data));
            }else{
                $response = $client->request($method, front_url($url), ['form_params'=>$data]);
            }
            $code     = intval($response->getStatusCode());
            if ($code>=300) {
                Log::info('http_request_fail' , array('code'=>$code));
                return false;
            }
            return \json_decode($response->getBody()->getContents() , true);
        } catch (GuzzleException $e) {
            Log::info('http_request_fail' , array('code'=>$e->getCode() , 'message'=>$e->getMessage()));
            return false;
        }
    }

}