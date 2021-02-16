<?php

namespace App\Http\Controllers\Content;

use App\Models\Content\Audit;
use App\Models\Content\Post;
use App\Traits\PostTrait;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Content\PostTranslation;
use App\Repositories\Contracts\Content\PostRepository;
use App\Repositories\Contracts\Content\PostCommentRepository;

class PostController extends Controller
{
    use PostTrait;
    private $post;

    private $postComment;

    public function __construct(PostRepository $post , PostCommentRepository $postComment)
    {
        $this->post = $post;
        $this->postComment = $postComment;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $uri = parse_url($request->server('REQUEST_URI'));
        $query = empty($uri['query'])?"":$uri['query'];
        $params = $request->all();
        $params['query']=$query;
        $posts = $this->post->findByWhere($params);
        $params['appends'] = $params;

        $essence_posts = essence_post_list();
        $postIds = $posts->pluck('post_id')->toArray();

        $preheatPosts = collect(DB::connection('mt_front')->table('preheat_posts')->whereIn('post_id' , $postIds)->select('post_id')->get())->pluck('post_id')->toArray();
        $posts->each(function ($item, $key) use ($essence_posts , $preheatPosts){
            $item->is_preheat = in_array($item->post_id , $preheatPosts);
            $item->is_essence = in_array($item->post_id , array_keys($essence_posts));
            $item->post_format_created_at = Carbon::parse($item->post_created_at)->addHours(8)->toDateTimeString();
        });
        $params['posts']=$posts;

        return view('backstage.content.post.index' , $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backstage.content.video.create');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $videoinfo = $this->video->find($id);
        dd($videoinfo->toArray());
        $categoryinfo = $this->category->find('1');
        dd($videoinfo->toArray());
        return view('backstage.content.video.edit')->with(['videoinfo' => $videoinfo]);
    }

    public function comment(Request $request , $id)
    {
        $params   = $request->all();
        $comments = $this->postComment->findByPostId($id);
        $params['comments'] = $comments;

        return view('backstage.content.post.comment' , $params);
    }

    /**
     * Show post translation.
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     */

    public function translation(Request $request , $id)
    {
        $params['id'] = $id;
        $posts = PostTranslation::where('post_id' , $id)->get();
        $params['posts'] = $posts;
        return view('backstage.content.post.translation' , $params);
    }

    /**
     * Show post translation.
     *
     * @param Request $request
     * @param int $id
     * @param int $translation_id
     * @return mixed
     */

    public function translationUpdate(Request $request , $id , $translation_id)
    {
        $post_content = $request->input('post_content' , '');
        $post = $this->post->find($id);
        $postTranslation = PostTranslation::where('post_translation_id' , $translation_id)->first();
        if($post->post_content_default_locale!=$postTranslation->post_locale)
        {
            $postTranslation->post_content = $post_content;
            $postTranslation->save();
        }
        $referer = $request->server('HTTP_REFERER');
        return redirect($referer.'#translation='.$translation_id);
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
            $posts = Post::withTrashed()->whereIn('post_id', $params['ids'])->get();
        } else {
            $posts = Post::whereIn('post_id', $params['ids'])->get();
        }
        $this->batchPost($posts, $params['type']);

        return response()->json([
            'result' => 'success',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(Request $request, $id)
    {
        $post = $this->post->find($id);
        $data = $request->all();
        if ($request->has('is_essence')) {
            $essence = $request->input('is_essence');
            $this->setEssence($id , $essence);
        }
        if ($request->has('is_preheat')) {
            $preheat = $request->input('is_preheat');
            $this->setPreheat($id , $preheat);
        }
        if ($request->has('carousel')){
            $locale = $request->input('locale' , '');
            $image = $request->input('name' , '');
            $this->setCarousel($id , $locale , $image);
        }
        if ($request->has('post_topping')){
            $post_topping = $request->input('post_topping');
            $data['post_topped_at'] = $post_topping=='on' ? date('Y-m-d H:i:s' , time()) : null;
            if ($data['post_topped_at']==null) {
                $this->setCarousel($id);
                non_carousel_post($id);
            } else {
                $this->setNonFine($id);
            }

            $this->post->update($post , $data);
            $topCount = $this->post->getTopCount();
            if ($topCount>10) {
                $lastTopPost = $this->post->findLastTop();
                $this->post->update($lastTopPost , array('post_topped_at'=>null , 'post_topping'=>'off'));
            }
        }
        if ($request->has('post_hotting')) {
                $post_hotting = $request->input('post_hotting');
                $flag = $post_hotting==='on';
                $this->setNonFine($id , $flag);

            $this->post->update($post , $data);
        }
        if ($request->has('delete')) {
            $delete = $request->input('delete');
            $flag = $delete==='on';
            if ($flag) {
                $this->destroy($id);
                $post->is_delete=2;
                $post->post_deleted_at=date('Y-m-d H:i:s');
                $post->save();
                Audit::create([
                        'admin_id'       => auth()->user()->admin_id,
                        'admin_user_name'=> auth()->user()->admin_username,
                        'user_id'        => $post->user_id,
                        'source'         => '帖子列表',
                        'post_audit'     => '单个删除',
                        'is_delete'      => 1,
                        'post_id'        => $post->post_id,
                        'post_uuid'      => $post->post_uuid,
                    ]
                );
            } else{
                //$this->post->restore($post);
                $post->is_delete=0;
                $post->post_deleted_at=null;
                $post->save();
                $this->postRestore($post);
                $audit = Audit::where('post_id', $post->post_id)->first();
                if ($audit) {
                    $audit->is_delete=0;
                    $audit->unoperator=auth()->user()->admin_username;
                    $audit->save();
                }
            }
        }

        return response()->json([
            'result' => 'success',
        ]);
    }

    public function clearCache()
    {
        $client = new Client();
        $client->request('GET', 'https://api.yooul.net/api/clear/cache');
        return response()->json([
            'result' => 'success',
        ]);

    }

    public function image($id)
    {
        $result = Post::withTrashed()->where('post_id',$id)->with('translations')->first();
        if (!empty($result)) {
            $result = collect($result)->toArray();
            $result['media'] = !empty($result['post_media']) ? json_decode($result['post_media'], true) : [];

            if (!empty($result['post_media'])) {
                $result['media'] = json_decode($result['post_media'], true);
                $type            = !empty($result['media']['image']) ? 'image' : 'video';
                $result['media'] = postMedia($type, $result['media'], 3);
            }

            if (!empty($result['translations'])) {
                $result['translations'] = sortArrByManyField($result['translations'], 'post_locale', SORT_DESC);
            }
        }

        return view('backstage.content.post.image', compact('result'));

    }


    public function setEssence($id , $post_fine)
    {
        $params = ['post_id'=>$id];
        if($post_fine=='on') {
            $operation = 'essence_post';
            $params['score'] = mt_rand(11111 , 99999);
            $params['operation'] = 1;
        } else {
            $params['operation'] = 0;
            $operation = 'non_essence_post';
        }

        $result = $this->httpRequest('api/bk/essence/post/'.$id, $params, 'PATCH');
        if ($result) {
            $operation($id);
        } else {
            abort(400);
        }
    }

}
