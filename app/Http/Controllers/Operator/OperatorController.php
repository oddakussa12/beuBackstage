<?php
namespace App\Http\Controllers\Operator;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;


class OperatorController extends Controller
{
    private $db;

    public function __construct() {
        $this->db = DB::connection('lovbee');
    }

    public function network(Request $request)
    {
        $table  = 'network_logs';
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

        $base = $this->db->table($table);
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
        $base        = $this->db->table('network_logs');
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
        $params['dates']  = [];
        $params['line']   = [];


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

        $list = $list->orderByDesc('created_at')->paginate(10);
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
        $user = auth()->user();

        $params = $request->all();
        $data   = collect($params)->only('id', 'user_id', 'type')->toArray();
        $data['admin_id'] = $user->admin_id;
        $data['admin_username'] = $user->admin_username;
        $data['time'] = date('Y-m-d H:i:s');
        $result = $this->httpRequest('api/backstage/destroyMedia' , $data);
        return isset($result['code']) ? $result : ['code'=>1];
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

    public function lastThree(Request $request)
    {
        $keyword = $request->input('keyword' , '');
        $dateTime = $request->input('dateTime' , '');
        $country = $request->input('country' , '');
        $appends['keyword'] = $keyword;
        $appends['dateTime'] = $dateTime;
        $appends['country'] = $country;
        $users = $this->db->table('data_active_users');
        if (!empty($country)) {
            $users = $users->where('phone_country', $country);
        }
        if (!empty($keyword)) {
            $users = $users->where('user_name', 'like', "%{$keyword}%")->orWhere('user_nick_name', 'like', "%{$keyword}%");
        }
        if (!empty($dateTime)) {
            $users = $users->where('date', $dateTime);
        }
        $users = $users->paginate(10)->appends($appends);
        return view('backstage.operator.lastThree', compact('users' , 'keyword' , 'dateTime'));
    }

    /**
     * @param Request $request
     * 封号管理
     */
    public function blackList(Request $request)
    {
        $uri    = parse_url($request->server('REQUEST_URI'));
        $params = $request->all();
        $params['appends']  = $params;
        $params['query']    = empty($uri['query']) ? "" : $uri['query'];

        $blackUser = $this->db->table('black_users');

        if (!empty($params['operator'])) {
            $blackUser = $blackUser->where('operator', $params['operator']);
        }
        if (!empty($params['keyword'])) {
            $users     = $this->db->table('users')->select('user_id')->where('user_name', 'like', "%{$params['keyword']}%")->orWhere('user_nick_name', 'like', "%{$params['keyword']}%")->get();
            $userIds   = $users->pluck('user_id')->toArray();
            $blackUser = $blackUser->whereIn('user_id', $userIds);
        }

        $blackUsers = $blackUser->orderByDesc('id')->groupBy('user_id')->paginate(10);
        if (empty($params['keyword'])) {
            $blackIds = $blackUsers->pluck('user_id')->toArray();
            $users    = $this->db->table('users')->select('user_id', 'user_name', 'user_nick_name', 'user_avatar')->whereIn('user_id', $blackIds)->get();
        }
        foreach ($blackUsers as $key=>$black) {
            foreach ($users as $user) {
                if ($black->user_id==$user->user_id) {
                    $blackUsers[$key] = (object) array_merge((array) $black, (array) $user);
                }
            }
        }
        $params['users'] = $blackUsers;
        return view('backstage.operator.suspend', $params);
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


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     * 四月目标旧版
     */
    public function goal()
    {

//        $dau = DB::connection('lovbee')->table('dau_counts')->whereIn('date' , $dates)->select('date' , 'dau' , '0 as zero' , '1 as one' , '2 as two' , 'gt3')->get();

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

        $hrCurrent = 18;
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

        $prodMaskCurrent = 37;
        $prodMaskMiddle = 50;
        $prodMaskGoal = 80;
        $prodMaskData = array('percentage'=>strval(round($prodMaskCurrent/$prodMaskGoal , 4)*100)."%" , 'current'=>$prodMaskCurrent , 'goal'=>$prodMaskGoal ,'marginTop'=>empty($prodMaskGoal)?0:strval((($prodMaskGoal-$prodMaskMiddle)/$prodMaskGoal)*500).'px' , 'middle'=>$prodMaskMiddle);

        return view('backstage.operator.operator.goal' , compact('dauData' , 'operData' , 'hrData' , 'prodRetentionData' , 'prodMaskData'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     * 四月目标
     */
    public function goal202104()
    {
        $monthData = array();
        $dateData = array();
        $dauList = array();
        $dauTable = array();
        if (Cache::has('helloo_dau_list')) {
            $cache = Cache::get('helloo_dau_list');
            $cacheData = \json_decode($cache , true);
            $dateData = $cacheData['dateData'];
            $dauList = $cacheData['dauList'];
            $zoomStart = $cacheData['zoomStart'];
        }else{
            $yearStart = Carbon::now('Asia/Shanghai')->startOfYear()->format('Ym');
            $yearEnd = Carbon::now('Asia/Shanghai')->format('Ym');
            while($yearStart<=$yearEnd)
            {
                array_push($monthData  , $yearStart);
                $yearStart = Carbon::createFromFormat('Ym' , $yearStart , 'Asia/Shanghai')->addMonths(1)->format('Ym');
            }
            foreach ($monthData as $month)
            {
                $dauTable = array_merge($dauTable , $this->db->select('SELECT count(DISTINCT user_id) as `num`,created_at from t_visit_logs_'.$month.' GROUP BY created_at ORDER BY created_at'));
            }
            foreach ($dauTable as $dau)
            {
                array_push($dateData , $dau->created_at);
                array_push($dauList , array($dau->created_at , $dau->num));
            }
            $zoomCount = count($dauList);
            $zoomStart = ceil(($zoomCount-30)/$zoomCount*100);
            Cache::put('helloo_dau_list', \json_encode(array(
                'dateData'=>$dateData,
                'dauList'=>$dauList,
                'zoomStart'=>$zoomStart
            )), 360);
        }
        $dates = array();
        $start = Carbon::now('Asia/Shanghai')->startOfMonth()->toDateString();
        $end = Carbon::now('Asia/Shanghai')->toDateString();
        while ($start<=$end) {
            array_push($dates , $start);
            $start = Carbon::createFromFormat('Y-m-d' , $start)->addDays(1)->toDateString();
        }
        $operMiddle = 40000;
        $operCurrent = $this->db->table('data_retentions')->whereIn('date' , $dates)->sum('new');
        $operGoal = 60000;
        $operData = array(
            'percentage'=>strval(round($operCurrent/$operGoal , 4)*100)."%" ,
            'current'=>strval($operCurrent/1000)."K" ,
            'goal'=>strval($operGoal/1000)."K" ,
            'marginTop'=>strval(round((round(($operGoal-$operCurrent)/$operGoal , 4)*480))).'px',
            'middle'=>strval(ceil($operMiddle/1000))."K"
        );

        $hrCurrent = 26;
        $hrMiddle = 36;
        $hrGoal = 50;
        $hrData = array(
            'percentage'=>strval(round($hrCurrent/$hrGoal , 4)*100)."%" ,
            'current'=>$hrCurrent ,
            'goal'=>$hrGoal ,
            'marginTop'=>strval(round((round(($hrGoal-$hrCurrent)/$hrGoal , 4)*480))).'px',
            'middle'=>$hrMiddle
        );

        $yesterday = Carbon::yesterday('Asia/Shanghai')->toDateString();
        $dauMiddle = 16000;
        $dauCurrent = $this->db->table('visit_logs_'.Carbon::yesterday('Asia/Shanghai')->format('Ym'))->where('created_at' , $yesterday)->count(DB::raw('DISTINCT(user_id)'));
        $dauGoal = 17000;
        $dauData = array('percentage'=>strval(round($dauCurrent/$dauGoal , 4)*100)."%" , 'current'=>$dauCurrent , 'goal'=>strval(ceil($dauGoal/1000))."K" , 'middle'=>strval(ceil($dauMiddle/1000))."K" );

        $nineDayAgo = Carbon::now('Asia/Shanghai')->subDays(9)->toDateString();
        $prodRetentionCurrent = 8;
        $prodRetentionMiddle = 19;
        $prodRetentionData = $this->db->table('data_retentions')->where('date' , $nineDayAgo)->select(array(
            DB::raw('SUM(new) as reg'),
            DB::raw('SUM(`7`) as seven')
        ))->get()->toArray();
        $prodRetentionCurrent = empty($prodRetentionData[0]->reg)?0:round($prodRetentionData[0]->seven/$prodRetentionData[0]->reg , 4)*100;
        $prodRetentionGoal = 30;
        $prodRetentionData = array(
            'percentage'=>strval(round($prodRetentionCurrent/$prodRetentionGoal , 4)*100)."%" ,
            'current'=>strval($prodRetentionCurrent)."%" ,
            'goal'=>strval($prodRetentionGoal)."%" ,
            'marginTop'=>strval(round((round(($prodRetentionGoal-$prodRetentionCurrent)/$prodRetentionGoal , 4)*235))).'px',
            'middle'=>strval($prodRetentionMiddle)."%"
        );

        $prodMaskCurrent = 58;
        $prodMaskMiddle = 50;
        $prodMaskGoal = 80;
        $prodMaskData = array(
            'percentage'=>strval(round($prodMaskCurrent/$prodMaskGoal , 4)*100)."%" ,
            'current'=>$prodMaskCurrent ,
            'goal'=>$prodMaskGoal ,
            'marginTop'=>strval(round((round(($prodMaskGoal-$prodMaskCurrent)/$prodMaskGoal , 4)*235))).'px',
            'middle'=>$prodMaskMiddle
        );


        $devCurrent = 1;
        $devMiddle = 2;
        $devGoal = 4;
        $devData = array(
            'percentage'=>strval(round($devCurrent/$devGoal , 4)*100)."%" ,
            'current'=>$devCurrent ,
            'goal'=>$devGoal ,
            'marginTop'=>strval(round((round(($devGoal-$devCurrent)/$devGoal , 4)*480))).'px',
            'middle'=>$devMiddle
        );

        $dataCurrent = 0;
        $dataMiddle = 2;
        $dataGoal = 4;
        $dataData = array(
            'percentage'=>strval(round($dataCurrent/$dataGoal , 4)*100)."%" ,
            'current'=>$dataCurrent ,
            'goal'=>$dataGoal ,
            'marginTop'=>strval(round((round(($dataGoal-$dataCurrent)/$dataGoal , 4)*480))).'px',
            'middle'=>$dataMiddle
        );
        return view('backstage.operator.operator.goal-optimization' , compact(
            'dauData' ,
            'operData' ,
            'hrData' ,
            'prodRetentionData' ,
            'prodMaskData' ,
            'devData' ,
            'dataData',
            'dateData',
            'dauList',
            'zoomStart'
        ));
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     * 五月目标
     */
    public function goal202105()
    {
        $monthData = array();
        $dateData = array();
        $dauList = array();
        $dauTable = array();
        if (Cache::has('helloo_dau_list')) {
            $cache = Cache::get('helloo_dau_list');
            $cacheData = \json_decode($cache , true);
            $dateData = $cacheData['dateData'];
            $dauList = $cacheData['dauList'];
            $zoomStart = $cacheData['zoomStart'];
        }else{
            $yearStart = Carbon::now('Asia/Shanghai')->startOfYear()->format('Ym');
            $yearEnd = Carbon::now('Asia/Shanghai')->format('Ym');
            while($yearStart<=$yearEnd)
            {
                array_push($monthData  , $yearStart);
                $yearStart = Carbon::createFromFormat('Ym' , $yearStart , 'Asia/Shanghai')->addMonths(1)->format('Ym');
            }
            foreach ($monthData as $month)
            {
                $dauTable = array_merge($dauTable , $this->db->select('SELECT count(DISTINCT user_id) as `num`,created_at from t_visit_logs_'.$month.' GROUP BY created_at ORDER BY created_at'));
            }
            foreach ($dauTable as $dau)
            {
                array_push($dateData , $dau->created_at);
                array_push($dauList , array($dau->created_at , $dau->num));
            }
            $zoomCount = count($dauList);
            $zoomStart = ceil(($zoomCount-30)/$zoomCount*100);
            Cache::put('helloo_dau_list', \json_encode(array(
                'dateData'=>$dateData,
                'dauList'=>$dauList,
                'zoomStart'=>$zoomStart
            )), 86400*2);
        }
        $dates = array();
        $start = Carbon::now('Asia/Shanghai')->startOfMonth()->toDateString();
        $end = Carbon::now('Asia/Shanghai')->toDateString();
        while ($start<=$end) {
            array_push($dates , $start);
            $start = Carbon::createFromFormat('Y-m-d' , $start)->addDays(1)->toDateString();
        }

        $hrCurrent = 29;
        $hrMiddle = 36;
        $hrGoal = 50;
        $hrData = [
            'percentage'=>strval(round($hrCurrent/$hrGoal , 4)*100)."%" ,
            'current'=>$hrCurrent ,
            'goal'=>$hrGoal ,
            'marginTop'=>strval(round((round(($hrGoal-$hrCurrent)/$hrGoal , 4)*480))).'px',
            'middle'=>$hrMiddle
        ];

        $productSelected = json_decode(Cache::get('goal_product'), true);
        $pText= [
            '💭 Share',
            '💭 Discover',
            '💭 Map',
            '💭 Membership',
            '💭 Community',
            '💭 Colors(look)',
            '💭 Fonts, text',
            '💭 Ready for delivery',
        ];
        foreach ($pText as $key=>$text) {
            if (!empty($productSelected)) {
                foreach ($productSelected as $item) {
                    $pText[$key] = '<li><label><input name="product[]" value="'.($key+1).'" type="checkbox" />'.$text.'</label></li>';
                    if (($item-1)==$key) {
                        $pText[$key] = '<li><label><input name="product[]" value="'.($key+1).'" type="checkbox" checked />'.$text.'</label></li>';
                        break;
                    }
                }
            } else {
                $pText[$key] = '<li><label><input name="product[]" value="'.($key+1).'" type="checkbox" />'.$text.'</label></li>';
            }
        }

        $productCurrent = !empty($productSelected) ? count($productSelected) : 0;
        $productGoal    = count($pText);
        $productData    = [
            'percentage' => strval(round($productCurrent/$productGoal , 4)*100)."%" ,
            'current'    => $productCurrent ,
            'goal'       => $productGoal ,
            'marginTop'  => strval(round((round(($productGoal-$productCurrent)/$productGoal , 4)*480))).'px',
            'text'       => $pText,
        ];

        $shopMiddle  = 100;
        $shopCurrent = $this->db->table('users')->where('user_shop', 1)->where('user_verified', 1)->count();
        $shopGoal    = 170;
        $shopData    = array('percentage'=>strval(round($shopCurrent/$shopGoal , 4)*100)."%" , 'current'=>$shopCurrent , 'goal'=>$shopGoal, 'marginTop'=>strval(round((round(($shopGoal-$shopCurrent)/$shopGoal , 4)*235))).'px',
            'middle'=>$shopMiddle);

        $newUserMiddle  = 20000;
        $newUserCurrent = $this->db->table('users')->whereBetween('user_created_at', [date('2021-05-01 00:00:00'), date('2021-05-31 23:59:59')])->count();
        $newUserGoal    = 25000;
        $newUserData = array(
            'percentage'=>strval(round($newUserCurrent/$newUserGoal , 4)*100)."%" ,
            'current'=>strval($newUserCurrent/1000)."K" ,
            'goal'=>strval($newUserGoal/1000)."K" ,
            'marginTop'=>strval(round((round(($newUserGoal-$newUserCurrent)/$newUserGoal , 4)*235))).'px',
            'middle'=>strval(ceil($newUserMiddle/1000))."K"
        );

        if (Cache::has('helloo_month_online')) {
            $onlineCurrent = Cache::get('helloo_month_online');
        }else{
            $online = DB::connection('lovbee')->select('select count(1) num from (select user_id from t_visit_logs_202105 where 1 group by user_id) b');
            $onlineCurrent = !empty($online[0]->num) ? $online[0]->num : 1;
            Cache::put('helloo_month_online', $onlineCurrent, $onlineCurrent, 7200);
        }

        $monthDau = [
            'percentage'=> (round($onlineCurrent/($onlineCurrent*2), 4)*100)."%",
            'current'   => $onlineCurrent,
            'goal'      => $onlineCurrent*2,
        ];

        $devSelected = json_decode(Cache::get('goal_developer'), true);
        $dText= [
            'Analytics',
            'Firebase Remote Config',
            'In-App Messaging',
            '对于不同前、后端接口的功能上增加“时效性缓存系统”(针对不同的接口,缓存时长各有不同、可以统一封装一套缓存系统)能有效提升用户体验，减少服务器并发访问压力等',
            '对于一些"一定会成功、网络缺不太好、或后端响应时间较长"的接口，做一个入库操作，找一个准确的时机、做合适的轮训操作能很大幅度提升用户体验',
            '整理规范GitHub',
        ];
        foreach ($dText as $key=>$text) {
            if (!empty($devSelected)) {
                foreach ($devSelected as $item) {
                    $dText[$key] = '<li><label><input name="developer[]" value="'.($key+1).'" type="checkbox" />'.$text.'</label></li>';
                    if (($item-1)==$key) {
                        $dText[$key] = '<li><label><input name="developer[]" value="'.($key+1).'" type="checkbox" checked />'.$text.'</label></li>';
                        break;
                    }
                }
            } else {
                $dText[$key] = '<li><label><input name="developer[]" value="'.($key+1).'" type="checkbox" />'.$text.'</label></li>';
            }
        }

        $devCurrent = !empty($devSelected) ? count($devSelected) : 0;
        $devMiddle = 0;
        $devGoal = count($dText);
        $devData = [
            'percentage'=> strval(round($devCurrent/$devGoal, 4)*100)."%",
            'current'   => $devCurrent,
            'goal'      => $devGoal,
            'marginTop' => strval(round((round(($devGoal-$devCurrent)/$devGoal, 4)*480))).'px',
            'middle'    => $devMiddle,
            'text'      => $dText,
        ];

        return view('backstage.operator.operator.goal05v2', compact(
            'shopData',
            'productData',
            'newUserData',
            'hrData',
            'monthDau',
            'devData',
            'dateData',
            'dauList',
            'zoomStart'
        ));
    }

    public function goalData(Request $request)
    {
        $params = $request->all();
        $name   = $params['name']=='product[]' ? 'product' : 'developer';
        $select = !empty($params['value']) ? $params['value'] : [];
        $params += ['divName'=>$name, 'current'=>count($select)];
        Cache::put('goal_'.$name, json_encode($select), 86400*30);
        return $params;
    }
}