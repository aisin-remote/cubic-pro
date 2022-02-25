<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterCode extends Model
{
	protected $table = 'master_acc_codes';
	protected $fillable = ['acc_code', 'cell'];
    protected $hidden = ['created_at', 'updated_at'];

}