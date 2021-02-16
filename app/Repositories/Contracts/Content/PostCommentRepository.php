<?php

namespace App\Repositories\Contracts\Content;

use App\Repositories\Contracts\BaseRepository;

interface PostCommentRepository extends BaseRepository
{
    public function findByPostId($id);
}
