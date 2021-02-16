<?php

namespace App\Models\Invitation;

use App\Models\Base;

class Score extends Base
{

    protected $table = "scores";

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

}
