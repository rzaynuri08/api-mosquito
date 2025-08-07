<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanAttachment extends Model
{
    // Sesuaikan dengan nama tabel kalau tidak pakai konvensi Laravel (jamak dari nama model)
    protected $table = 'scan_attachments';

    // Sesuaikan kolom yang boleh diisi
    protected $fillable = ['name', 'id_genus', 'confidence', 'user_id'];
}
