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
            'Country_Code'  => ['required', 'regex:/^\+[0-9]{1,4}$/'],
            'Company_No'    => 'required|digits_between:5,15',
            'Status'        => 'required|string',
        ];
    }
}