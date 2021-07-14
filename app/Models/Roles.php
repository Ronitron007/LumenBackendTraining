<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    public $timestamps = false;
    protected $table = 'roles';
    protected $fillable = [
      'role',
      'permissions'
    ];
    protected $casts = [
        'permissions' => 'array',
    ];
}
