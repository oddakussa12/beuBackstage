<?php

namespace App\Repositories\Contracts\Content;

use App\Repositories\Contracts\BaseRepository;

interface PostRepository extends BaseRepository
{
    public function findByWhere($params);
}
