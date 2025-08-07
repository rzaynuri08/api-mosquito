<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $table = 'history';
    protected $primaryKey = 'id_history';

    public function attachment()
    {
        return $this->belongsTo(ScanAttachment::class, 'id_attachment');
    }
}
