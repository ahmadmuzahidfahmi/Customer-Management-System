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
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
