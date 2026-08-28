<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->Company_ID,
            'name'       => $this->Company_Name,
            'email'      => $this->Company_Email,
            'phone'      => $this->Company_No,
            'status'     => $this->Status,
            'closed_at'  => $this->Closed_Date?->toIso8601String(),
            // The company table's timestamps are Created_At / Updated_At
            // (see the rename_timestamps_on_company_table migration and
            // Customer::CREATED_AT) — the lowercase attributes are null.
            'created_at' => $this->Created_At?->toIso8601String(),
            'updated_at' => $this->Updated_At?->toIso8601String(),
        ];
    }
}
