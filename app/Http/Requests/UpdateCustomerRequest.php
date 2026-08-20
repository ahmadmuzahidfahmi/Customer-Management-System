<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'Company_Name'  => 'required|string|max:255',
            'Company_Email' => 'nullable|email|max:255',
            'Country_Code'  => 'required|string|max:10',
            'Company_No'    => 'required|string|max:20',
            'Status'        => 'required|string',
        ];
    }
}