<?php
namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait PostTrait
{


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

    public function destroy($data)
    {
       return $this->httpRequest('api/backstage/post', $data);
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

}