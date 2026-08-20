<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Authorization is already handled by route middleware (auth + role
     * checks), so this request just needs to validate input shape.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'Company_Name'     => 'required|string|max:255',
            'Company_Email'    => 'nullable|email|max:255',
            'Country_Code'     => 'required|string|max:10',
            'Company_No'       => 'required|string|max:20',
            'Status'           => 'required|string',
            'Notes'            => 'nullable|array',
            'Notes.*.Subject'  => 'nullable|string|max:255',
            'Notes.*.Content'  => 'nullable|string',
            'Attachments'      => 'nullable|array',
            'Attachments.*'    => 'file|max:10240',
        ];
    }
}