<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    //
    public $timestamps = false;
    protected $connection = 'lovbee';

    protected $primaryKey = 'id';
    const CREATED_AT = 'created_at';

    protected $fillable = ['title', 'content', 'category', 'sort', 'status', 'created_at'];

}
