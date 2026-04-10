<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => ['nullable', 'string', 'max:255'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city'          => ['required', 'string', 'max:100'],
            'state'         => ['required', 'string', 'max:50'],
            'postal_code'   => ['required', 'string', 'max:20'],
            'country'       => ['nullable', 'string', 'size:2'],
            'notes'         => ['nullable', 'string'],
        ];
    }
}
