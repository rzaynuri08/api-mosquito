<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $table = 'history';
    protected $primaryKey = 'id_history';

    protected $fillable = [
        'id_attachment',
        'id_user',         // kalau ada relasi ke user
        'scan_date',       // tanggal scan
        'result',          // hasil scan
        'created_at',
        'updated_at',
    ];

    public function attachment()
    {
        return $this->belongsTo(ScanAttachment::class, 'id_attachment');
    }
}
