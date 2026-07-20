<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'users';

    protected $primaryKey = 'User_ID';

    const CREATED_AT = 'Created_At';
    const UPDATED_AT = 'Updated_At';

    protected $fillable = [
        'User_Name',
        'User_Email',
        'User_Password',
        'User_Role',
        'Status',
        'Last_Login',
    ];

    protected $hidden = [
        'User_Password',
    ];

    protected $casts = [
        'Created_At'    => 'datetime',
        'Updated_At'    => 'datetime',
        'Last_Login'    => 'datetime',
        'User_Password' => 'hashed',
    ];

    public function getAuthPassword()
    {
        return $this->User_Password;
    }
}