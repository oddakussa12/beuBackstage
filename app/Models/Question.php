<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    //
    public $timestamps = false;
    protected $connection = 'lovbee';

    protected $primaryKey = 'id';

    protected $fillable = ['title', 'content', 'sort', 'status', 'url', 'created_at'];

}
