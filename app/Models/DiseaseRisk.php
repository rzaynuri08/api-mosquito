<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiseaseRisk extends Model
{
    protected $table = 'disease_risk';
    protected $primaryKey = 'id_disease';

    protected $fillable = [
        'disease_name',      // contoh nama kolom penyakit
        'description',       // deskripsi risiko penyakit
        'created_at',
        'updated_at',
    ];
}
