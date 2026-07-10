<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';

    protected $primaryKey = 'User_ID';

    protected $fillable = [
        'User_Name',
        'User_Email',
        'User_Password',
        'User_Role',
        'Status'
    ];

    protected $hidden = [
        'User_Password',
    ];

    public function getAuthPassword()
    {
        return $this->User_Password;
    }
}