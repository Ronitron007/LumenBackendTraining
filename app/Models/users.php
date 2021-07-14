<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

use Tymon\JWTAuth\Contracts\JWTSubject;

class users extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    //
    public $timestamps = false;

    protected $fillable = [
        'name', 'email',
    ];

    protected $hidden = [
        'password'
    ];



    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    /**
     * The attributes excluded from the model's JSON form.
     *
     *
     */



    // private $password;

    // public function setpassword($hash){
    //   $this->password = $hash;
    // }
    // public function verifypassword($hash){
    //   if($hash == $this->$password){
    //     return true;
    //   }
    //   else{
    //     return false;
    //   }
    // }

}

?>
