<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->Activity_ID,
            'type'         => $this->Activity_Type,
            'subject'      => $this->Subject,
            'detail'       => $this->Activity_Detail,
            'status'       => $this->Status,
            'is_overdue'   => $this->isOverdue(),
            'dead_line'    => $this->Dead_Line?->toIso8601String(),
            'completed_at' => $this->Completed_At?->toIso8601String(),
            'company_id'   => $this->Company_ID,
            'contact_id'   => $this->Contact_ID,
            'lead_id'      => $this->Lead_ID,
            'assigned_to'  => $this->Assigned_To,
            'created_at'   => $this->Created_At?->toIso8601String(),
            'updated_at'   => $this->Updated_At?->toIso8601String(),
        ];
    }
}