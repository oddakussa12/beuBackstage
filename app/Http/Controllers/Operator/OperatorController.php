<?php
namespace App\Http\Controllers\Operator;

use App\Exports\MessageExport;
use App\Models\Passport\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use function GuzzleHttp\Psr7\str;

class OperatorController extends Controller
{
    private $table;
    private $db;

    public function __construct() {
        $this->table = 'network_logs';
        $this->db = DB::connection('lovbee');
    }
    public function network(Request $request)
    {
        $table  = $this->table;
        $params = $request->all();
        $params['dateTime'] = $params['dateTime'] ?? date('Y-m-d', strtotime('-7day')).' - '.date('Y-m-d');
        $time   = explode(' - ', $params['dateTime']);
        $start  = current($time);
        $end    = last($time);
        $list   = $this->db->table($table)->select(DB::raw("t_$table.*"), 'users.user_name','users.user_nick_name');
        $list   = $list->leftJoin('users', 'users.user_id', '=', "$table.user_id")->whereBetween('time', [$start, $end]);
        $fields = ['app_version', 'system_version', 'networking', 'network_type'];

        foreach ($fields as $field) {
            if (!empty($params[$field])) {
                $list = $list->where("$table.$field", $params[$field]);
            }
        }

        if (!empty($params['keyword'])) {
            $keyword = trim($params['keyword']);
            $list    = $list->where(function($query)use($keyword){$query->where('user_name', 'like', "%$keyword%")->orWhere('user_nick_name', 'like', "%{$keyword}%");});
        }
        $list = $list->orderByDesc('time')->paginate(10);
        $params['appends']  = $params;
        $params['list']     = $list;

        $base = $this->db->table($this->table);
        $params['appVersions']    = $base->pluck('app_version')->unique()->toArray();
        $params['systemVersions'] = $base->pluck('system_version')->unique()->toArray();
        $params['networks']       = $base->pluck('networking')->unique()->toArray();
        $params['networkTypes']   = $base->pluck('network_type')->unique()->toArray();

        return view('backstage.operator.network', $params);
    }

    public function chart($params)
    {
        $params['dateTime'] = $params['dateTime'] ?? date('Y-m-d', strtotime('-7day')).' - '.date('Y-m-d');
        $time        = explode(' - ', $params['dateTime']);
        $start       = current($time);
        $end         = last($time);
        $base        = $this->db->table($this->table);
        $chart       = $base->whereBetween('time', [$start, $end])->get()->toArray();
        $appVersion  = collect($chart)->pluck('app_version')->toArray();
        $networkType = collect($chart)->pluck('network_type')->toArray();

        $dates = printDates($start, $end);
        $count = array();
        foreach ($dates as $date) {
            foreach ($appVersion as $item) {
                $num = collect($chart)->where('time',$date)->where('app_version', $item)->count();
            }
            $count['appVersion'][]   = !empty($appVersion)  ? current($appVersion)  : 0;
            $count['networkType'][]  = !empty($networkType) ? current($networkType) : 0;
        }

        $params['chart']    = $chart;
        $params['header']   = array_keys($count);
        $params['dates']    = $dates;

        foreach ($count as $key=>$value) {
            $params['line'][] = [
                "name" => $key,
                "type" => "line",
                "data" => $value,
                'areaStyle' => [],
                'markPoint' => ['data' =>[['type'=>'max', 'name'=>'MAX'], ['type'=>'min', 'name'=>'MIN']]],
                'markLine'  => ['data' =>[['type'=>'average']]],
                'itemStyle' => ['normal'=>['label'=>['show'=>true]]]
            ];
        }
        $params['header'] = [];
        $params['dates'] = [];
        $params['line'] = [];


        $params['appVersions']    = $base->pluck('app_version')->unique()->toArray();
        $params['systemVersions'] = $base->pluck('system_version')->unique()->toArray();
        $params['networks']       = $base->pluck('networking')->unique()->toArray();
        $params['networkTypes']   = $base->pluck('network_type')->unique()->toArray();

        return $params;

    }

    public function feedback(Request $request)
    {
        $params = $request->all();
        $params['dateTime'] = $params['dateTime'] ?? date('Y-m-d', strtotime('-7day')).' - '.date('Y-m-d');

        $table  = 'feedback';
        $result = $this->db->table($table)->select(DB::raw("t_$table.*"), 'users.user_name','users.user_nick_name','users.user_avatar');
        $result = $result->leftJoin('users', 'users.user_id', '=', "$table.user_id");

        if (isset($params['status'])) {
            if (in_array($params['status'], [0, 1, '0', '1'])) {
                $result = $result->where("$table.status", $params['status']);
            }
        } else {
            $result = $result->where("$table.status", 0);
        }
        if (!empty($params['keyword'])) {
            if (!empty($params['keyword'])) {
                $keyword = trim($params['keyword']);
                $result  = $result->where(function($query)use($keyword){$query->where('user_name', 'like', "%{$keyword}%")->orWhere('user_nick_name', 'like', "%{$keyword}%");});
            }
        }
        $result = $result->orderByDesc('id')->paginate(10);
        foreach ($result as $item) {
            $item->image = !empty($item->image) ? explode(';', $item->image) : [];
        }

        $params['list'] = $result;
        return view('backstage.operator.feedback', $params);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     * 照片墙
     */
    public function media(Request $request)
    {
        $params = $request->all();
        $params['media'] = $params['media'] ?? 'video';
        $table  = $params['media'] == 'video' ? 'users_videos' : 'users_photos';

        $list   = $this->db->table($table)->select(DB::raw("t_$table.*"), 'users.user_name','users.user_nick_name', 'user_avatar');
        $list   = $list->leftJoin('users', 'users.user_id', '=', "$table.user_id");
        if (!empty($params['dateTime'])) {
            $time   = explode(' - ', $params['dateTime']);
            $start  = current($time);
            $end    = last($time);
            $list = $list->whereBetween('created_at', [$start, $end]);
        }

        if (!empty($params['keyword'])) {
            $keyword = trim($params['keyword']);
            $list    = $list->where(function($query)use($keyword){$query->where('user_name', 'like', "%{$keyword}%")->orWhere('user_nick_name', 'like', "%{$keyword}%");});
        }

        $list = $list->orderByDesc('user_id')->orderByDesc('created_at')->paginate(10);
        foreach ($list as $item) {
            $item->image = !empty($item->video_url) ? $item->image : $item->photo;
        }
        $params['appends'] = $params;
        $params['list']    = $list;
        $params['type']    = ['video', 'photo'];
        return view('backstage.operator.media', $params);
    }

    /**
     * @param Request $request
     * 删除
     */
    public function destroyMedia(Request $request)
    {
        $params = $request->all();
//        dump($params);
        return [];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     * 积分
     */
    public function score3(Request $request)
    {
        $params = $request->all();
        $params['sort'] = !empty($params['sort']) ? $params['sort'] : 'score';
        $table  = 'users_scores';

        $list   = $this->db->table($table)->select(DB::raw("t_$table.init,t_$table.score,t_users_kpi_counts.*"), 'users.user_name','users.user_nick_name', 'user_avatar');
        $list   = $list->leftJoin('users', 'users.user_id', '=', "$table.user_id");
        $list   = $list->leftJoin('users_kpi_counts', 'users_kpi_counts.user_id', '=', "$table.user_id");
        if (!empty($params['dateTime'])) {
            $time   = explode(' - ', $params['dateTime']);
            $start  = current($time);
            $end    = last($time);
            $list = $list->whereBetween("$table.created_at", [$start, $end]);
        }

        if (!empty($params['keyword'])) {
            $keyword = trim($params['keyword']);
            $list    = $list->where(function($query)use($keyword){$query->where('user_name', 'like', "%{$keyword}%")->orWhere('user_nick_name', 'like', "%{$keyword}%");});
        }

        $sTable = in_array($params['sort'], ['init', 'score']) ? $table : 'users_kpi_counts';
        $list = $list->groupBy("$table.user_id")->orderByDesc("$sTable.{$params['sort']}")->paginate(10);

        $params['appends'] = $params;
        $params['list']    = $list;
        $params['type']    = ['init','score','sent', 'friend', 'like','liked','video','txt','audio','image','props','like_video','liked_video','game_score','other_school_friend'];

        return view('backstage.operator.score', $params);

    }

    public function score(Request $request)
    {
        $params = $request->all();
        $params['sort'] = !empty($params['sort']) ? $params['sort'] : 'score';
        $ids   = [];
        if (!empty($params['keyword'])) {
            $keyword = trim($params['keyword']);
            $select  = $this->db->table('users')->where('user_name', 'like', "%{$keyword}%")->paginate(10);
            $ids     = $select->pluck('user_id')->toArray();
        }

        if (in_array($params['sort'], ['score', 'init'])) {
            $result  = $this->db->table('users_scores')->orderByDesc($params['sort'])->groupBy('user_id');
            $ids && $result = $result->whereIn('user_id', $ids);
            $result  = $result->paginate(10);
            $userIds = $result->pluck('user_id')->toArray();
            $list    = $this->db->table('users_kpi_counts')->whereIn('user_id', $userIds)->get();
        } else {
            $result  = $this->db->table('users_kpi_counts')->orderByDesc($params['sort'])->groupBy('user_id');
            $ids && $result = $result->whereIn('user_id', $ids);
            $result  = $result->paginate(10);
            $userIds = $result->pluck('user_id')->toArray();
            $list    = $this->db->table('users_scores')->whereIn('user_id', $userIds)->get();
        }
        $users  = $this->db->table('users')->select('user_id', 'user_name', 'user_nick_name', 'user_avatar')->whereIn('user_id', $userIds)->get();

        foreach ($result as $key=>$item) {
            $tmp = (array)$item;
            foreach ($list as $li) {
                $tmp1 = (array)$li;
                if ($tmp['user_id']==$tmp1['user_id']) {
                    $tmp = array_merge($tmp, $tmp1);
                }
            }
            foreach ($users as $user) {
                $tmp2 = (array)$user;
                if ($item->user_id==$tmp2['user_id']) {
                    $tmp = array_merge($tmp, $tmp2);
                }
            }
            $result[$key] = (object)($tmp);
        }

        $params['appends'] = $params;
        $params['list']    = $result;
        $params['type']    = ['init','score','sent', 'friend', 'like','liked','video','txt','audio','image','props','like_video','liked_video','game_score','other_school_friend'];

        return view('backstage.operator.score', $params);
    }

    /**
     * @param Request $request
     * @param $userId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     * 积分详情
     */
    public function scoreDetail(Request $request, $userId)
    {
        $params = $request->all();
        $hash   = hashDbIndex($userId);
        $result = $this->db->table('users_scores_logs_'.$hash)->where('user_id', $userId)->orderByDesc('created_at')->paginate(10);
        $params['list'] = $result;

        return view('backstage.operator.scoreDetail', $params);
    }

    public function goal()
    {
        $yesterday = Carbon::yesterday('Asia/Shanghai')->toDateString();
        $dauCurrent = 0;
        $dauMiddle = 16000;
        $dauCurrent = $this->db->table('visit_logs_'.Carbon::yesterday('Asia/Shanghai')->format('Ym'))->where('created_at' , $yesterday)->count(DB::raw('DISTINCT(user_id)'));
        $dauGoal = 17000;
        $dauData = array('percentage'=>strval(round($dauCurrent/$dauGoal , 4)*100)."%" , 'current'=>$dauCurrent , 'goal'=>strval(ceil($dauGoal/1000))."K" , 'middle'=>strval(ceil($dauMiddle/1000))."K" );

        $dates = array();
        $start = Carbon::now('Asia/Shanghai')->startOfMonth()->toDateString();
        $end = Carbon::now('Asia/Shanghai')->toDateString();
        while ($start<=$end)
        {
            array_push($dates , $start);
            $start = Carbon::createFromFormat('Y-m-d' , $start)->addDays(1)->toDateString();
        }
        $operCurrent = 0;
        $operMiddle = 40000;
        $operCurrent = $this->db->table('data_retentions')->whereIn('date' , $dates)->sum('new');
        $operGoal = 60000;
        $operData = array('percentage'=>strval(round($operCurrent/$operGoal , 4)*100)."%" , 'current'=>strval($operCurrent/1000)."K" , 'goal'=>strval($operGoal/1000)."K" , 'marginTop'=>empty($operGoal)?0:strval((($operGoal-$operMiddle)/$operGoal)*500).'px' , 'middle'=>strval(ceil($operMiddle/1000))."K");

        $hrCurrent = 0;
        $hrMiddle = 36;
        $hrGoal = 50;
        $hrData = array('percentage'=>strval(round($hrCurrent/$hrGoal , 4)*100)."%" , 'current'=>$hrCurrent , 'goal'=>$hrGoal , 'marginTop'=>empty($hrGoal)?0:strval((($hrGoal-$hrMiddle)/$hrGoal)*500).'px' , 'middle'=>$hrMiddle);

        $nineDayAgo = Carbon::now('Asia/Shanghai')->subDays(9)->toDateString();
        $prodRetentionCurrent = 8;
        $prodRetentionMiddle = 19;
        $prodRetentionData = $this->db->table('data_retentions')->where('date' , $nineDayAgo)->select(array(
            DB::raw('SUM(new) as reg'),
            DB::raw('SUM(`7`) as seven')
        ))->get()->toArray();
        $prodRetentionCurrent = empty($prodRetentionData[0]->reg)?0:round($prodRetentionData[0]->seven/$prodRetentionData[0]->reg , 4)*100;
        $prodRetentionGoal = 30;
        $prodRetentionData = array('percentage'=>strval(round($prodRetentionCurrent/$prodRetentionGoal , 4)*100)."%" , 'current'=>strval($prodRetentionCurrent)."%" , 'goal'=>strval($prodRetentionGoal)."%" ,'marginTop'=>empty($prodRetentionGoal)?0:strval((($prodRetentionGoal-$prodRetentionMiddle)/$prodRetentionGoal)*500).'px' , 'middle'=>strval($prodRetentionMiddle)."%" );

        $prodMaskCurrent = 0;
        $prodMaskMiddle = 50;
        $prodMaskGoal = 80;
        $prodMaskData = array('percentage'=>strval(round($prodMaskCurrent/$prodMaskGoal , 4)*100)."%" , 'current'=>$prodMaskCurrent , 'goal'=>$prodMaskGoal ,'marginTop'=>empty($prodMaskGoal)?0:strval((($prodMaskGoal-$prodMaskMiddle)/$prodMaskGoal)*500).'px' , 'middle'=>$prodMaskMiddle);

        return view('backstage.operator.operator.goal' , compact('dauData' , 'operData' , 'hrData' , 'prodRetentionData' , 'prodMaskData'));
    }
}