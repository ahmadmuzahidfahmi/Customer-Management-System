<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Contact;
use App\models\Note;

class Customer extends Model
{
    use SoftDeletes;
    protected $table = 'company'; // 👈 this tells Laravel to use "company" table
    protected $primaryKey = 'Company_ID'; // 👈 this tells Laravel to use "Company_ID" as the primary key

    protected $fillable = [
        'Company_ID',
        'Company_Name',
        'Company_Email',
        'Company_No',
        'Status',
        'Closed_Date',
        'Created_At',
        'Updated_At',
    ];

    protected $casts = [
    'Closed_Date' => 'datetime',
    'Created_At' => 'datetime',
    'Updated_At' => 'datetime',
];


public function leads()
{
    return $this->hasMany(Leads::class, 'Company_ID', 'Company_ID');
}

public function contacts()
{
    return $this->hasMany(
        Contact::class,
        'Company_ID',   // foreign key in contacts table
        'Company_ID'    // primary key in company table
    );
}

public function notes()
{
    return $this->hasMany(Note::class, 'Company_ID', 'Company_ID')->latest('Created_At');
}

}