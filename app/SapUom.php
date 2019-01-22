<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SapUom extends Model
{
    protected $fillable = ['*'];
    protected $hidden = ['created_at', 'updated_at'];
}
