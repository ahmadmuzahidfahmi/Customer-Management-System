<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\Note;


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

public function notes()
{
    return $this->hasMany(Note::class, 'Contact_ID', 'Contact_ID')->latest('Created_At');
}

public function activities()
{
    return $this->hasMany(Activity::class, 'Contact_ID', 'Contact_ID')->latest('Created_At');
}
}