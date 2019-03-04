<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CapexArchive extends Model
{
    protected $fillable = ['*'];
    protected $hidden = ['created_at', 'updated_at'];
}
