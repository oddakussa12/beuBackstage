<?php

namespace App\Models\Invitation;


use App\Models\Base;

class Invitation extends Base
{

    protected $table = "invitations";

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

}
