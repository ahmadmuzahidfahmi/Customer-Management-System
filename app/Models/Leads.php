<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leads extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'Lead_ID';

    protected $fillable = [
        'Lead_Name',
        'Source',
        'User_ID',
        'Lead_Note',
        'Status',
        'Estimated_Value',
        'Company_ID',
        'Assigned_To',
        'Contact_ID',
    ];
    public function company()
{
    return $this->belongsTo(Customer::class, 'Company_ID', 'Company_ID');
}

public function user()
{
    return $this->belongsTo(
        User::class,
        'User_ID',
        'User_ID'
    );
}

}
