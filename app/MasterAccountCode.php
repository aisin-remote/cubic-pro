<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterAccountCode extends Model
{
    
	protected $table = 'master_account_code';
	protected $fillable = ['acc_code', 'department_code'];
    protected $hidden = ['created_at', 'updated_at'];

}
