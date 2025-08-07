<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanAttachment extends Model
{
    protected $table = 'scan_attachment';

    protected $primaryKey = 'id_attachment';

    protected $fillable = [
        'name',
        'id_genus',
        'confidence',
        'user_id',
        'created_at',
        'updated_at',
    ];
}
