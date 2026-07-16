<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leads extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'Lead_ID';

    const CREATED_AT = 'Created_At';
    const UPDATED_AT = 'Updated_At';

    protected $fillable = [
        'Lead_Name',
        'Source',
        'User_ID',
        'Note_ID',
        'Status',
        'Estimated_Value',
        'Company_ID',
        'Assigned_To',
        'Contact_ID',
        'Position',
        'Status_Changed_At',
    ];

    protected $casts = [
        'Created_At' => 'datetime',
        'Updated_At' => 'datetime',
        'Status_Changed_At' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($lead) {
            // New leads go to the bottom of their column
            $maxPosition = static::where('Status', $lead->Status)->max('Position');
            $lead->Position = ($maxPosition ?? 0) + 1;
            $lead->Status_Changed_At = now();
        });

        static::updating(function ($lead) {
            if ($lead->isDirty('Status')) {
                $lead->Status_Changed_At = now();
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Customer::class, 'Company_ID', 'Company_ID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'User_ID', 'User_ID');
    }

public function notes()
{
    return $this->hasMany(Note::class, 'Lead_ID', 'Lead_ID')->latest('Created_At');
}

public function activities()
{
    return $this->hasMany(Activity::class, 'Lead_ID', 'Lead_ID')->latest('Created_At');
}
}