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
        'Is_On_Local',
        'Is_On_Drive', 
    ];

    protected $casts = [
        'Created_At' => 'datetime',
        'Updated_At' => 'datetime',
        'File_Size'  => 'integer',
        'Is_On_Local' => 'boolean',
        'Is_On_Drive' => 'boolean',
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

    public function isFullySynced(): bool
    {
        return $this->Is_On_Local && $this->Is_On_Drive;
    }

    public function syncStatusLabel(): string
    {
        if ($this->Is_On_Local && $this->Is_On_Drive) return 'Synced';
        if ($this->Is_On_Local && ! $this->Is_On_Drive) return 'Backup missing';
        if (! $this->Is_On_Local && $this->Is_On_Drive) return 'Local missing';
        return 'Missing everywhere';
    }

    public function entityLabel(): string
    {
        $entity = $this->entity;

        if (! $entity) {
            return $this->Entity_Type . ' #' . $this->Entity_ID . ' (record no longer exists)';
        }

        return match ($this->Entity_Type) {
            'Company'  => $entity->Company_Name,
            'Contacts' => $entity->Contact_Name,
            'Leads'    => $entity->Lead_Name,
            'Activity' => $entity->Subject ?: ('Activity #' . $entity->Activity_ID),
            'Notes'    => $entity->Subject ?: ('Note #' . $entity->Note_ID),
            default    => $this->Entity_Type . ' #' . $this->Entity_ID,
        };
    }

    public function entityUrl(): ?string
    {
        if (! $this->entity) return null;

        return match ($this->Entity_Type) {
            'Company'  => route('customers.show', $this->Entity_ID),
            'Contacts' => route('contacts.show', $this->Entity_ID),
            'Leads'    => route('leads.show', $this->Entity_ID),
            default    => null, // Activity/Notes don't have a standalone page
        };
    }

    protected function getAuditLabel(): string
    {
        return 'attachment "' . $this->Original_Name . '"';
    }
}