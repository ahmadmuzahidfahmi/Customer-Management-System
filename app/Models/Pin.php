<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pin extends Model
{
    protected $primaryKey = 'Pin_ID';

    protected $fillable = [
        'User_ID',
        'Entity_Type',
        'Entity_ID',
    ];
}