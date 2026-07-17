<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';
    protected $primaryKey = 'Log_ID';
    public $timestamps = false;

    protected $fillable = [
        'User_ID', 'Action', 'Auditable_Type', 'Auditable_ID',
        'Description', 'Old_Values', 'New_Values',
        'IP_Address', 'User_Agent', 'Created_At',
    ];

    protected $casts = [
        'Old_Values' => 'array',
        'New_Values' => 'array',
        'Created_At' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'User_ID', 'User_ID');
    }
}