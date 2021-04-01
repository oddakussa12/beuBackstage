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

    private $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * @return string[]
     * 设置header头
     */
    public function headings(): array
    {
        return ['user_id', 'user_name', 'user_nick_name', 'user_created_at' , 'activeTime'];
    }

    public function collection()
    {
        $id = $this->id;
        $friendIds = array();
        DB::connection('lovbee')->table('users_friends')->where('user_id' , $id)->orderByDesc('friend_id')->chunk(100  , function ($friends) use (&$friendIds){
            $friendId = $friends->pluck('friend_id')->toArray();
            $friendIds = array_merge($friendIds , $friendId);
        });
        $result = $this->httpRequest('api/backstage/last/online' , array('user_id'=>$friendIds) , "GET");
        if(is_array($result))
        {
            $activeUsers = $result['users'];
            $activeUsers = collect($activeUsers)->reject(function ($value, $key) {
                return $value == 946656000;
            })->toArray();
            $userIds = array_keys($activeUsers);
            $users = app(UserRepository::class)->findByMany($userIds);
            $users->each(function($user) use ($activeUsers){
                $user->activeTime = Carbon::createFromTimestamp($activeUsers[$user->user_id] , "Asia/Shanghai")->toDateTimeString();
            });
            return collect($users)->values();
        }
        return collect()->values();
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