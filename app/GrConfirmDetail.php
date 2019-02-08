<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GrConfirmDetail extends Model
{
    protected $fillable = ['*'];
    
    public function approval_master()
    {
        return $this->belongsTo('App\ApprovalMaster','approval_master_id', 'id');
    }

    public function approval_detail()
    {
        return $this->belongsTo('App\ApprovalDetail','approval_detail_id', 'id');
    }

    public function gr_confirm()
    {
        return $this->belongsTo('App\GrConfirm', 'gr_confirm_id', 'id');
    }

    
}
