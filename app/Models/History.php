<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $table = 'history';
    protected $primaryKey = 'id_history';
    public $timestamps = true;
    public $incrementing = true; // <--- tambahin ini
    protected $keyType = 'int';  // <--- tambahin juga

    protected $fillable = [
        'id_user',
        'final_label',
        'final_confidence',
        'id_attachment',
    ];

    public function attachments()
    {
        return $this->hasMany(ScanAttachment::class, 'id_history', 'id_history');
    }
}
