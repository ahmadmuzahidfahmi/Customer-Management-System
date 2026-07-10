<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
     use SoftDeletes;
public function company()
{
    return $this->belongsTo(
        Customer::class,
        'Company_ID',
        'Company_ID'
    );
}
    protected $table = 'contacts';

    protected $primaryKey = 'Contact_ID';

    protected $fillable = [
    'Contact_Name',
    'Contact_Email',
    'Contact_No',
    'Contact_Role',
    'Contact_Note',
    'Company_ID',
    'Country_Code',
];

   
}