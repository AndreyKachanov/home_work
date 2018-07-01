<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'files';

    protected  $fillable = ['file', 'created_at', 'updated_at', 'deleted_at'];
}
