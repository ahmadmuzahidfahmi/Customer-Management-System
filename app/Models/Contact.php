<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Note;
use App\Traits\Auditable;


class Contact extends Model
{
     use SoftDeletes, Auditable, HasFactory;
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
    'Is_Pinned',
];

public function notes()
{
    return $this->hasMany(Note::class, 'Contact_ID', 'Contact_ID')->latest('Created_At');
}

public function activities()
{
    return $this->hasMany(Activity::class, 'Contact_ID', 'Contact_ID')->latest('Created_At');
}

public function getAuditLabel(): string
{
    return $this->Contact_Name;
}
public function attachments()
{
    return $this->morphMany(Attachment::class, 'entity', 'Entity_Type', 'Entity_ID', 'Contact_ID');
}
public function leads()
{
    return $this->hasMany(Leads::class, 'Contact_ID', 'Contact_ID')->latest('Created_At');
}

public function getWhatsappNumberAttribute()
{
    return preg_replace(
        '/[^0-9]/',
        '',
        $this->Country_Code . $this->Contact_No
    );
}
}