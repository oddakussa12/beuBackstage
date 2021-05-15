<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param $url
     * @param $data
     * @param string $method
     * @param bool $json
     * @return bool
     * HTTP Request
     */
    protected function httpRequest($url, $data=array(), $method='POST', $json=false)
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


}
