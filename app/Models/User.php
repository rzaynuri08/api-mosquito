<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users'; // karena nama tabel bukan 'users'
    protected $primaryKey = 'id_user';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'username',
        'password',
        'number_phone',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'password',
    ];
}
