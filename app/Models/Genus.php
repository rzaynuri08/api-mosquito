<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genus extends Model
{
    protected $table = 'genus';
    protected $primaryKey = 'id_genus';

    protected $fillable = [
        'name',        // kolom nama genus
        'id_prevention',
        'id_disease'
    ];

    public function prevention()
    {
        return $this->belongsTo(Prevention::class, 'id_prevention');
    }

    public function disease()
    {
        return $this->belongsTo(DiseaseRisk::class, 'id_disease');
    }
    
    public function attachments()
    {
        return $this->hasMany(\App\Models\ScanAttachment::class, 'id_genus', 'id_genus');
    }
}
