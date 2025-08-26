<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $primaryKey = 'id_history';
    protected $fillable = ['id_user', 'id_attachment', 'final_label', 'final_confidence'];

    public function attachments()
    {
        return $this->hasMany(ScanAttachment::class, 'id_history', 'id_history');
    }

    public function attachment()
    {
        return $this->belongsTo(ScanAttachment::class, 'id_attachment', 'id_attachment');
    }
}
