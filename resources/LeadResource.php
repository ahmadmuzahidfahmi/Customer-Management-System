<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->Lead_ID,
            'name'              => $this->Lead_Name,
            'source'            => $this->Source,
            'status'            => $this->Status,
            'estimated_value'   => $this->Estimated_Value,
            'position'          => $this->Position,
            'company_id'        => $this->Company_ID,
            'contact_id'        => $this->Contact_ID,
            'assigned_to'       => $this->Assigned_To,
            'status_changed_at' => $this->Status_Changed_At?->toIso8601String(),
            'created_at'        => $this->Created_At?->toIso8601String(),
            'updated_at'        => $this->Updated_At?->toIso8601String(),
        ];
    }
}