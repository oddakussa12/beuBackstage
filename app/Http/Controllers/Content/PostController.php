<?php

namespace App\Http\Controllers\Content;

use App\Models\Passport\User;
use App\Models\Post;
use App\Models\PostComment;
use App\Traits\PostTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    use PostTrait;

    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $uri    = parse_url($request->server('REQUEST_URI'));
        $query  = empty($uri['query']) ? "" : $uri['query'];
        $params = $request->all();
        $posts  = $this->findByWhere($params);

        $params['query']   = $query;
        $params['appends'] = $params;
        $params['posts']   = $posts;

        return view('backstage.content.post.index' , $params);
    }

    public function findByWhere($params)
    {
        $now  = Carbon::now();
        $post = Post::with('owner');

        if (!empty($params['user_name'])) {
            $user = User::where('user_name', 'like', "%{$params['keyword']}%")->where('user_name', 'like', "%{$params['keyword']}%")->get();
            $uIds = $user->pluck('user_id')->toArray();
            $post = $post->whereIn('user_id' , $uIds);
        }
        if (!empty($params['dateTime'])) {
            $endDate = $now->endOfDay()->toDateTimeString();
            $allDate = explode(' - ' , $params['dateTime']);
            $start   = Carbon::createFromFormat('Y-m-d H:i:s' , array_shift($allDate))->subHours(8)->toDateTimeString();
            $end     = Carbon::createFromFormat('Y-m-d H:i:s' , array_pop($allDate))->subHours(8)->toDateTimeString();
            $start   = $start>$end ? $end : $start;
            $end     = $end>$endDate ? $endDate : $end;
            $post = $post->where('created_at' , '>=' , $start)->where('created_at' , '<=' , $end);
        }
        return $post->orderBy('created_at', 'DESC')->paginate(10);
    }

    public function comment(Request $request, $id)
    {
        $params   = $request->all();
        $comments = PostComment::with('owner')->where('post_id', $id);
        if (!empty($params['keyword'])) {
            $user = User::where('user_name', 'like', "%{$params['keyword']}%")->orWhere('user_nick_name', 'like', "%{$params['keyword']}%")->get();
            $uIds = $user->pluck('user_id')->toArray();
            $comments = $comments->whereIn('user_id', $uIds);
        }
        $comments = $comments->orderByDesc('created_at')->paginate(10);
        $params['comments'] = $comments;
        return view('backstage.content.post.comment', $params);
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
        $post = Post::find($id);
        if (!empty($post)) {
            $result = $this->httpRequest('api/backstage/post', ['post_id'=>$post->post_id, 'operator'=>auth()->user()->admin_username]);
            if (empty($result)) {
                abort(500);
            }
        } 
        return response()->json(['result'=>'success']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * 批量删除、恢复帖子
     */
    public function batch(Request $request)
    {
        $params = $request->all();

        if (empty($params['ids']) || !is_array($params['ids']) || !in_array($params['type'], ['delete', 'recover'])) {
            return '参数异常';
        }
        if ($params['type']=='recover') {
            $posts = Post::whereIn('post_id', $params['ids'])->get();
        } else {
            $posts = Post::whereIn('post_id', $params['ids'])->get();
        }
        $this->batchPost($posts, $params['type']);

        return response()->json([
            'result' => 'success',
        ]);
    }

}
