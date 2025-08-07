<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genus extends Model
{
    protected $table = 'genus';
    protected $primaryKey = 'id_genus';

    public function prevention()
    {
        return $this->belongsTo(Prevention::class, 'id_prevention');
    }

    public function disease()
    {
        return $this->belongsTo(DiseaseRisk::class, 'id_disease');
    }
}
