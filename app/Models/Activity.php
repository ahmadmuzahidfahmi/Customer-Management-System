<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activities';

    protected $primaryKey = 'Activity_ID';

    const CREATED_AT = 'Created_At';
    const UPDATED_AT = 'Updated_At';

    protected $fillable = [
        'Company_ID',
        'Contact_ID',
        'Lead_ID',
        'User_ID',
        'Assigned_To',
        'Activity_Type',
        'Subject',
        'Activity_Detail',
        'Status',
        'Dead_Line',
        'Completed_At',
    ];

    protected $casts = [
        'Dead_Line'    => 'date',
        'Completed_At' => 'datetime',
        'Created_At'   => 'datetime',
        'Updated_At'   => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Leads::class, 'Lead_ID', 'Lead_ID');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'Contact_ID', 'Contact_ID');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'Created_By', 'User_ID');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'Assigned_To', 'User_ID');
    }

    public function isOverdue(): bool
    {
        return $this->Status === 'Pending'
            && $this->Dead_Line
            && $this->Dead_Line->isPast();
    }
        public function attachments()
    {
        return $this->morphMany(Attachment::class, 'entity', 'Entity_Type', 'Entity_ID', 'Activity_ID');
    }
}