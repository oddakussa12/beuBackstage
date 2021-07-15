<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Models\Passport\User;
use App\Models\Business\Goods;
use App\Models\Business\Comment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoodsCommentController extends Controller
{

    public function index(Request $request)
    {
        $params = $request->all();
        $comments = Comment::where('step', 1);
        if (isset($params['level'])&&$params['level']!=='') {
            $comments = $comments->where('level', intval($params['level']));
        }
        if (isset($params['verified'])&&$params['verified']!=='') {
            $comments = $comments->where('verified', intval($params['verified']));
        }
        if (isset($params['dateTime'])) {
            $dateTime = $this->parseTime($params['dateTime']);
            $comments = $comments->whereBetween('created_at' , array($dateTime['start'] , $dateTime['end']));
        }
        if (!empty($params['goods_id'])) {
            $comments = $comments->where('goods_id', strval($params['goods_id']));
        }
        if (!empty($params['keyword'])) {
            $comments  = $comments->whereIn('content', strval($params['keyword']));
        }
        if(!empty($params['order_by']))
        {
            $orderBy = $params['order_by'];
        }else{
            $orderBy = $params['order_by'] = 'desc';
        }
        $sort   = $orderBy == 'asc' ? 'orderBy' : 'orderByDesc';
        $comments = $comments->$sort('created_at')->paginate(10)->appends($params);
        $goodsIds = $comments->pluck('goods_id')->unique()->toArray();
        $shopIds = $comments->pluck('owner')->toArray();
        $userIds = $comments->pluck('user_id')->toArray();
        $toIds = $comments->pluck('to_id')->toArray();
        $userIds = array_unique(array_merge($shopIds , $userIds , $toIds));
        $users = User::whereIn('user_id' , $userIds)->get();
        $goods = Goods::whereIn('id' , $goodsIds)->get();
        $comments->each(function($comment) use ($users , $goods){
            $comment->user = $users->where('user_id' , $comment->user_id)->first();
            $comment->shop = $users->where('user_id' , $comment->owner)->first();
            $comment->to = $users->where('user_id' , $comment->to_id)->first();
            $comment->goods = $goods->where('id' , $comment->goods_id)->first();
        });
        $params['comments'] = $comments;
        return view('backstage.business.goods_comment.index' , $params);
    }

    public function update(Request $request,$id)
    {
        $params = $request->all();
        $comment = Comment::where('comment_id' , $id)->firstOrFail();
        if (!empty($params['level'])) {
            $comment->level = strval($params['level']);
            $comment->save();
        }
        if(!empty($params['verified'])&&$comment->verified==-1&&$comment->type=='comment')
        {
            $admin = auth()->user();
            $data = [
                'audit_id'  => $id,
                'type'      => 'comment',
                'date'      => date('Y-m-d'),
                'status'    =>$params['verified']=='yes'?'pass':'refuse',
                'admin_id'  => $admin->admin_id,
                'created_at'=> date('Y-m-d H:i:s'),
                'admin_username' => $admin->admin_username,
            ];
            try{
                DB::beginTransaction();
                $auditResult = DB::table('business_audits')->insert($data);
                if(!$auditResult)
                {
                    abort(500 , 'Audit insert failed!');
                }
                $claim_comments = DB::table('claim_comments')->where('comment_id' , $id)->delete();
                if($claim_comments<=0)
                {
                    abort(500 , 'Claim comment delete failed!');
                }
                if($params['verified']=='yes')
                {
                    $this->httpRequest('/api/backstage/review/comment', ['id'=>$id, 'level'=>$comment->level, 'reviewer'=>$admin->admin_id]);
                }else{
                    $commentResult = DB::connection('lovbee')->table('comments')->where('comment_id' , $id)->update(array(
                        'verified'=>0,
                        'reviewer'=>$admin->admin_id,
                        'verified_at'=>date('Y-m-d H:i:s'),
                    ));
                    if($commentResult<=0)
                    {
                        abort(500 , 'Comment update failed!');
                    }
                }
                DB::commit();
            }catch (\Exception $e)
            {
                Log::info('Comment review failed' , array(
                    'message'=>$e->getMessage(),
                    'data'=>$request->all(),
                ));
                DB::rollBack();
                abort(500 , 'Comment review failed!');
            }
        }
        if($request->ajax())
        {
            return response()->json(array(
                'result'=>'success'
            ));
        }else{
            return redirect(route('business::goods_comment.statistics'));
        }
    }


    public function statistics()
    {
        $user   = auth()->user();
        $adminId = auth()->user()->admin_id;
        $today   = date('Y-m-d 00:00:00');
        $claim   = DB::table('claim_comments')->where('admin_id', $adminId)->first();
        if ($claim) {
            $cCount  = DB::table('claim_comments')->where('admin_id', $adminId)->count();
            $comment  = Comment::where('comment_id', $claim->comment_id)->first();
            if(empty($comment)||$comment->verified!=-1&&$comment->step!=1)
            {
                DB::table('claim_comments')->where('comment_id' , $claim->comment_id)->delete();
                return $this->statistics();
            }
            $comment->goods = Goods::where('id' , $comment->goods_id)->first();
            $comment->shop = User::where('user_id' , $comment->owner)->first();
            $comment->comment_user = User::where('user_id' , $comment->user_id)->first();
            $comment->to_user = User::where('user_id' , $comment->to_id)->first();
            $result['comment'] = $comment;
        }else{
            $cCount = 0;
        }
        $count     = DB::connection('lovbee')->table('comments')
            ->join('users_countries', 'users_countries.user_id', '=', 'comments.owner')
            ->where('verified', -1)->where('country', $user->admin_country)->count();
        $todayCount= DB::table('business_audits')->where('admin_id', $adminId)->where('created_at', '>=', $today)->count();
        $totalCount= DB::table('business_audits')->where('admin_id', $adminId)->count();
        $result['todayCount'] = $todayCount ?? 0;
        $result['totalCount'] = $totalCount ?? 0;
        $result['claim']      = $cCount;
        $result['pendingReview']  = $count;
        return view('backstage.business.goods_comment.statistics', $result);
    }

    public function acquisition()
    {
        $user    = auth()->user();
        $adminId = $user->admin_id;
        $comment  = DB::table('claim_comments')->where('admin_id', $adminId)->first();
        if (empty($comment)) {
            $claim = DB::table('claim_comments')->get();
            $ids   = $claim->pluck('comment_id')->toArray();
            if($user->hasRole('administrator'))
            {
                $comments  = DB::connection('lovbee')->table('comments')->whereNotIn('comment_id', $ids)->where('verified', -1)->orderByDesc('comments.created_at')->limit(10)->get();
            }else{
                $comments  = DB::connection('lovbee')->table('comments')->join('users_countries', 'users_countries.user_id', '=', 'comments.owner')
                    ->where('verified', -1)->where('users_countries.country', $user->admin_country)->whereNotIn('comment_id', $ids)->orderByDesc('comments.created_at')->limit(10)->get();
            }
            $data  = array();
            foreach ($comments as $comment) {
                array_push($data , array('admin_id' => $adminId, 'comment_id' => $comment->comment_id, 'created_at'=>date('Y-m-d H:i:s')));
            }
            DB::table('claim_comments')->insert($data);
        }
        return redirect(route('business::goods_comment.statistics'));
    }

}
