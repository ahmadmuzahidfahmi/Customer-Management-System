<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->Lead_ID,
            'name'        => $this->Lead_Name,
            'customer_id' => $this->Company_ID,
            'value'       => $this->Estimated_Value,
            'source'      => $this->Source,
            'status'      => $this->Status,
            'created_at'  => $this->Created_At,
            'updated_at'  => $this->Updated_At,
        ];
    }
}