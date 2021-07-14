<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class mail_verif extends Model
{
    protected $table = 'mail_verif';
    // public $timestamps = false;
    protected $fillable = ['user_id','OTP'];

}
