<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Note extends Model
{
    protected $primaryKey = 'Note_ID';

    const CREATED_AT = 'Created_At';
    const UPDATED_AT = 'Updated_At';

    protected $fillable = [
        'Subject',
        'Content',
        'Company_ID',
        'Contact_ID',
        'Lead_ID',
    ];

    protected $casts = [
        'Created_At' => 'datetime',
        'Updated_At' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'Company_ID', 'Company_ID');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'Contact_ID', 'Contact_ID');
    }

    public function lead()
    {
        return $this->belongsTo(Leads::class, 'Lead_ID', 'Lead_ID');
    }

        public function getAuditLabel(): string
    {
        return $this->Subject ?: 'Note #' . $this->Note_ID;
    }
        public function attachments()
    {
        return $this->morphMany(Attachment::class, 'entity', 'Entity_Type', 'Entity_ID', 'Note_ID');
    }
}