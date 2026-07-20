<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
public static function bootAuditable()
{
    static::created(function ($model) {
        $model->writeAuditLog('created', null, $model->getAttributes());
    });

    static::updated(function ($model) {
        $changes = $model->getChanges();
        unset($changes['Updated_At'], $changes['Position']); // ignore noisy fields

        if (empty($changes)) {
            return;
        }

        $original = collect($model->getOriginal())->only(array_keys($changes))->toArray();
        $model->writeAuditLog('updated', $original, $changes);
    });

    static::deleted(function ($model) {
        $model->writeAuditLog('deleted', null, null);
    });

    // 'restored' and 'forceDeleted' events only exist on models using SoftDeletes.
    if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive(static::class))) {
        static::restored(function ($model) {
            $model->writeAuditLog('restored', null, null);
        });

        static::forceDeleted(function ($model) {
            $model->writeAuditLog('force_deleted', null, null);
        });
    }
}

    public function writeAuditLog(string $action, ?array $old, ?array $new): void
    {
        AuditLog::create([
            'User_ID' => Auth::id(),
            'Action' => $action,
            'Auditable_Type' => class_basename($this),
            'Auditable_ID' => $this->getKey(),
            'Description' => $this->auditDescription($action),
            'Old_Values' => $old,
            'New_Values' => $new,
            'IP_Address' => Request::ip(),
            'User_Agent' => Request::userAgent(),
            'Created_At' => now(),
        ]);
    }

    protected function auditDescription(string $action): string
    {
        $label = method_exists($this, 'getAuditLabel')
            ? $this->getAuditLabel()
            : class_basename($this) . ' #' . $this->getKey();

        return ucfirst(str_replace('_', ' ', $action)) . ' ' . $label;
    }
}