<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BomData extends Model
{
	protected $fillable = ['*'];
     public function parts()
    {
        return $this->belongsTo('App\Part', 'part_id', 'id');
    }
     public function suppliers()
    {
        return $this->belongsTo('App\Supplier','supplier_id', 'id');
    }

    public function bom()
    {
        return $this->belongsTo('App\Bom', 'bom_id', 'id');
    }
}
