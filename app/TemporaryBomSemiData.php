<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TemporaryBomSemiData extends Model
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
