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

    public function user()
    {
        return $this->belongsTo(User::class, 'User_ID', 'User_ID');
    }
}