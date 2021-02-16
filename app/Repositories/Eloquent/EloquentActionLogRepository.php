<?php


namespace App\Repositories\Eloquent;

use Agent;
use App\Repositories\EloquentBaseRepository;
use App\Repositories\Contracts\ActionLogRepository;

class EloquentActionLogRepository  extends EloquentBaseRepository implements ActionLogRepository
{
    /**
     * 记录用户操作日志
     * @param $type
     * @param $content
     * @return bool
     */
    public function createActionLog($type,$content)
    {
        $actionLog = $this->model;
        if(auth()->check()){
            $actionLog->operate_uid = auth()->user()->admin_id;
            $actionLog->operate_username = auth()->user()->admin_username;
        }else{
            $actionLog->operate_uid=0;
            $actionLog->operate_username ="guest";
        }
        $actionLog->operate_browser = Agent::browser();
        $actionLog->operate_system = Agent::platform();
        $actionLog->operate_url = request()->getRequestUri();
        $actionLog->operate_ip = request()->getClientIp();
        $actionLog->operate_type = $type;
        $actionLog->operate_method = request()->getMethod();
        $actionLog->operate_content = $content;
        $res = $actionLog->save();
        return $res;
    }
}