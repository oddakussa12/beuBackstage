<?php

namespace App\Repositories\Contracts;


interface UserRepository extends BaseRepository
{
    public function findByWhere($params);
    public function findMessage($params, $detail=false, $export=false);
    public function friendManage($params);
    public function export($params);
}
