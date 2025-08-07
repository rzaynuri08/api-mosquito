<?php   

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prevention extends Model
{
    protected $table = 'prevention';
    protected $primaryKey = 'id_prevention';

    protected $fillable = [
        'prevention_name',   // contoh kolom nama pencegahan
        'description',       // deskripsi pencegahan
        'created_at',
        'updated_at',
    ];
}
