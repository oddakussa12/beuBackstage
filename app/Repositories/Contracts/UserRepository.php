<?php

namespace App\Repositories\Contracts;


interface UserRepository extends BaseRepository
{
    public function findByWhere($params);
    public function findMessage($params);
    public function export($params);
}
