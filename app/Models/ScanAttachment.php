<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanAttachment extends Model
{
    protected $table = 'scan_attachment';
    protected $primaryKey = 'id_attachment';
    public $timestamps = true;

    protected $fillable = [
        'id_history',
        'id_genus',
        'name',
        'confidence',
    ];

    public function genus()
    {
        return $this->belongsTo(Genus::class, 'id_genus', 'id_genus');
    }

    public function history()
    {
        return $this->belongsTo(History::class, 'id_history', 'id_history');
    }
}
