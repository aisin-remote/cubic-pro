<?php

namespace App\Temporary;

use Illuminate\Database\Eloquent\Model;

class TemporaryBomData extends Model
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
}
