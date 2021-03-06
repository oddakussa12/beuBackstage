<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param $url
     * @param array $data
     * @param string $method
     * @param bool $json
     * @return bool
     * HTTP Request
     */
    protected function httpRequest($url, array $data=array(), string $method='POST', bool $json=false)
    {

        try {
            $client = new Client();
            foreach ($data as &$datum) {
                $datum = is_array($datum) ? json_encode($datum, JSON_UNESCAPED_UNICODE) : $datum;
            }
            $signature = common_signature($data);
            $data['signature'] = $signature;
            if(strtolower($method)=='get')
            {
                $response = $client->request($method, front_url($url), array('query'=>$data));
            }else{
                if($json)
                {
                    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
                    $response = $client->request($method, front_url($url), ['json'=>$data]);
                }else{
                    $response = $client->request($method, front_url($url), ['form_params'=>$data]);
                }
            }
            $code     = intval($response->getStatusCode());
            if ($code>=300||$code<200) {
                Log::info('http_request_fail' , array('code'=>$code));
                abort($code);
            }
            return \json_decode($response->getBody()->getContents() , true);
        } catch (GuzzleException $e) {
            Log::info('http_request_fail' , array('code'=>$e->getCode() , 'message'=>$e->getMessage()));
            return false;
        }
    }

    public function dateTime($result, $params, $hour='addHours', $tablePre='')
    {
        if (!empty($params['dateTime'])) {
            $allDate = explode(' - ' , $params['dateTime']);
            $start   = Carbon::createFromFormat('Y-m-d H:i:s' , array_shift($allDate))->$hour(8)->toDateTimeString();
            $end     = Carbon::createFromFormat('Y-m-d H:i:s' , array_pop($allDate))->$hour(8)->toDateTimeString();
            $createAt= !empty($tablePre) ? "$tablePre.created_at" : 'created_at';
            $result  = $result->whereBetween($createAt, [$start, $end]);
        }

        return $result;
    }

    public function parseTime($dateTime, $function='subHours' , $hour = 8 , $format = 'Y-m-d H:i:s')
    {
        $allDate = explode(' - ' , $dateTime);
        $startTime = array_shift($allDate);
        $endTime = array_pop($allDate);
        if(date($format , strtotime($startTime))!=$startTime||date($format , strtotime($endTime))!=$endTime)
        {
            return false;
        }
        if($format=='Y-m-d')
        {
            return array(
                'start'=>$startTime,
                'end'=>$endTime,
            );
        }
        $start   = Carbon::createFromFormat($format , $startTime)->$function($hour)->toDateTimeString();
        $end     = Carbon::createFromFormat($format , $endTime)->$function($hour)->toDateTimeString();
        return array(
            'start'=>$start,
            'end'=>$end,
        );
    }


}
