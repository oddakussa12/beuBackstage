<?php

namespace App\Services;

use App\Repositories\Contracts\VideoRepository;
use Illuminate\Support\Collection;
class VideosService
{
    /**
     * @var VideoRepository
     */
    private $database;

    public function __construct()
    {
        $this->database = app(VideoRepository::class);
    }

    public function all()
    {
        return $this->database->all();
    }

    public function findByUuid($uuid)
    {
        return $this->database->findByVideoId($uuid);
    }

    public function store()
    {
        return $this->database->store();
    }


}
