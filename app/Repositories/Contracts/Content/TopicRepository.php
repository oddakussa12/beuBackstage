<?php

namespace App\Repositories\Contracts\Content;

use App\Repositories\Contracts\BaseRepository;

interface TopicRepository extends BaseRepository
{
    public function findByWhere($params);

    public function getList($limit);
}
