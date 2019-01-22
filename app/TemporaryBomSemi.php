<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TemporaryBomSemi extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
	protected $fillable = ['*'];

    public function parts()
    {
        return $this->belongsTo('App\Part', 'part_id', 'id');
    }
     public function suppliers()
    {
        return $this->belongsTo('App\Supplier','supplier_id', 'id');
    }
    public function details_temporary()
    {
    	return $this->hasMany('App\TemporaryBomSemiData', 'part_id_head', 'part_id');
    }

}
