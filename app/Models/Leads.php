<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leads extends Model
{
       protected $fillable = [
        'Lead_ID',
        'Lead_Name',
        'Source',
        'User_ID',
        'Lead_Note',
    ];
    public function company()
{
    return $this->belongsTo(Customer::class, 'Company_ID', 'Company_ID');
}
}
