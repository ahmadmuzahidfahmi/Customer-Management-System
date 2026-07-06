<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'company'; // 👈 this tells Laravel to use "company" table

    protected $primaryKey = 'Company_ID'; // 👈 this tells Laravel to use "Company_ID" as the primary key

    protected $fillable = [
        'Company_id',
        'company_name',
        'company_email',
        'company_phone',
        'status',
        'company',
        'last_contact',
    ];

    protected $casts = [
    'Closed_Date' => 'datetime',
];
public function leads()
{
    return $this->hasMany(Leads::class, 'Company_ID', 'Company_ID');
}
}