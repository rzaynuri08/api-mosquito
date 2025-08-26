<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $table = 'history'; // nama tabel asli
    protected $primaryKey = 'id_history'; // primary key
    public $timestamps = true; // kalau tabelmu ada kolom created_at & updated_at

    protected $fillable = [
        'id_user',
        'final_label',
        'final_confidence'
    ];

    public function attachments()
    {
        return $this->hasMany(ScanAttachment::class, 'id_history', 'id_history');
    }
}
