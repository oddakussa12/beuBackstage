<?php
namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait PostTrait
{


    public function setCarousel($id , $locale='' , $image='')
    {
        $post     = $this->post->find($id);
        $postUuid = $post->post_uuid;
        if ($post->post_topping==1) {
            $params = ['post_uuid'=>$postUuid, 'post_id'=>$id];
            if (!empty($locale)&&!empty($image)) {
                $params['locale'] = $locale;
                $params['image'] = $image;
            }
            return $this->httpRequest('api/bk/carousel/post/'.$postUuid, $params, 'PATCH');
        }
    }

    public function setNonFine($id , $flag=false)
    {
        $params = ['post_id'=>$id, 'flag'=>intval($flag)];
        return $this->httpRequest("api/bk/non_fine/$id/post", $params, 'PATCH');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $posts
     * @param string $type
     * @param array $param
     * @return JsonResponse
     */
    public function batchPost($posts, $type='delete', $param=[])
    {
        $time    = date('Y-m-d H:i:s');
        $postIds = $posts->pluck('post_id')->toArray();
        if ($type=='delete') {
            $sql = ['post_audit'=>4, 'post_audited_at'=>$time, 'is_delete'=>2, 'post_deleted_at'=>$time];
        } else {
            $sql = ['post_audit'=>4, 'post_audited_at'=>$time, 'is_delete'=>0, 'post_deleted_at'=>null];
        }
        if (!empty($postIds)) {
            $params = [
                'post_id' => $postIds,
                'type'    => $type,
                'time'    => time(),
                'sql'     => $sql
            ];

            $params = array_merge($params, $param);
            return $this->httpRequest('api/bk/batch/post', $params);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return JsonResponse
     * 删除帖子
     */
    public function destroy($id)
    {
        $post = $this->post->find($id);
        if (!empty($post)) {
            $result = $this->httpRequest('api/bk/post/'.$post->post_uuid, [], 'DELETE');
            if (empty($result)) {
                abort(500);
            }
        }
        return response()->json([
            'result' => 'success',
        ]);
    }


    /**
     * @param $post
     * @return JsonResponse
     * 帖子恢复，需将ES也一同恢复
     */
    public function postRestore($post)
    {
        $params = [
            'post_id'   => $post->post_id,
            'post_uuid' => $post->post_uuid,
            'time_stamp'=> time()
        ];

        $this->httpRequest('api/bk/post/restore/'.$post->post_uuid, $params, 'PATCH');
        return response()->json([
            'result' => 'success',
        ]);
    }

    private function setPreheat($id , $post_preheat)
    {
        $params = [
            'post_id'      => $id,
            'post_preheat' => $post_preheat=='on' ? 1 : 0,
            'time'         => time()
        ];
        return $this->httpRequest('api/bk/set/post/preheat', $params);
    }

    /**
     * @param $url
     * @param $data
     * @param string $method
     * @param bool $json
     * @return bool
     * HTTP Request
     */
    public function httpRequest($url, $data, $method='POST', $json=false)
    {
        try {
            $client = new Client();
            foreach ($data as &$datum) {
                $datum = is_array($datum) ? json_encode($datum, JSON_UNESCAPED_UNICODE) : $datum;
            }
            $signature = common_signature($data);
            $data['signature'] = $signature;

            $data     = $json ? json_encode($data, JSON_UNESCAPED_UNICODE) : $data;
            $response = $client->request($method, front_url($url), ['form_params'=>$data]);
            $code     = $response->getStatusCode();
            if (!in_array($code, [200, 204])) {
                Log::error(__FUNCTION__.' message: HTTP code 返回不等于204  当前值为'. $code ?? '空');
                return false;
            }
            return true;
        } catch (GuzzleException $e) {
            Log::error(__FUNCTION__.' message: code:'.$e->getCode(). ' message:'. $e->getMessage());
            return false;
        }
    }
}