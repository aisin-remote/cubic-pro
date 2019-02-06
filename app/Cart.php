<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    public function scopeCountTotal($query)
    {
        if (auth()->check()) {
            return $query->where('user_id', auth()->user()->id)
                        ->get();
        }

        return collect([]);
    }
}
