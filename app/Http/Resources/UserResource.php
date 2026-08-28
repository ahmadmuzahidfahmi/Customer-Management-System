<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->User_ID,
            'name'       => $this->User_Name,
            'email'      => $this->User_Email,
            'role'       => $this->User_Role,
            'status'     => $this->Status ?? 'Active',
            'last_login' => $this->Last_Login?->toIso8601String(),
        ];
    }
}
