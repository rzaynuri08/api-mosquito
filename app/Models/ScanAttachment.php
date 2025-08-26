<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanAttachment extends Model
{
    protected $primaryKey = 'id_attachment';
    protected $fillable = ['name', 'id_genus', 'confidence', 'id_history'];

    public function history()
    {
        return $this->belongsTo(History::class, 'id_history', 'id_history');
    }

    public function genus()
    {
        return $this->belongsTo(Genus::class, 'id_genus', 'id_genus');
    }
}
