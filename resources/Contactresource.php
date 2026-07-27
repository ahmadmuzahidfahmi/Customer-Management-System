<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->Contact_ID,
            'name'         => $this->Contact_Name,
            'email'        => $this->Contact_Email,
            'phone'        => $this->Contact_No,
            'country_code' => $this->Country_Code,
            'role'         => $this->Contact_Role,
            'note'         => $this->Contact_Note,
            'company_id'   => $this->Company_ID,
            'created_at'   => $this->Created_At?->toIso8601String(),
            'updated_at'   => $this->Updated_At?->toIso8601String(),
        ];
    }
}