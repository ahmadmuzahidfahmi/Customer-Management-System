<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Attachment extends Model
{
    use Auditable;

    protected $table = 'attachments';

    protected $primaryKey = 'Attachment_ID';

    const CREATED_AT = 'Created_At';
    const UPDATED_AT = 'Updated_At';

    // Maps the Entity_Type enum values to the actual subfolder name on the network share.
    const FOLDER_MAP = [
        'Contacts' => 'contacts',
        'Company'  => 'company',
        'Leads'    => 'leads',
        'Activity' => 'activity',
        'Notes'    => 'notes',
    ];

    protected $fillable = [
        'Entity_Type',
        'Entity_ID',
        'Original_Name',
        'Stored_Name',
        'File_Path',
        'File_Type',
        'File_Size',
        'Uploaded_By',
    ];

    protected $casts = [
        'Created_At' => 'datetime',
        'Updated_At' => 'datetime',
        'File_Size'  => 'integer',
    ];

    public function entity()
    {
        return $this->morphTo('entity', 'Entity_Type', 'Entity_ID');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'Uploaded_By', 'User_ID');
    }

    public function isImage(): bool
    {
        return str_starts_with($this->File_Type, 'image/');
    }

    public function humanSize(): string
    {
        $bytes = $this->File_Size;

        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }

        return $bytes . ' B';
    }

    protected function getAuditLabel(): string
    {
        return 'attachment "' . $this->Original_Name . '"';
    }
}