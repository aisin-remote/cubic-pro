<?php

namespace App\SapModel;

use Illuminate\Database\Eloquent\Model;

class SapAsset extends Model
{
    protected $fillable = [
        'asset_type',
        'asset_code',
        'asset_name'
       
    ];
}
