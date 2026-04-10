<?php

namespace App\Http\Requests\Owner;

use App\Models\Job;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id'  => ['required', 'integer', 'exists:customers,id'],
            'property_id'  => ['nullable', 'integer', 'exists:properties,id'],
            'job_type_id'  => ['nullable', 'integer', 'exists:job_types,id'],
            'assigned_to'  => ['nullable', 'integer', 'exists:users,id'],
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'scheduled_at' => ['nullable', 'date'],
            'office_notes' => ['nullable', 'string'],
        ];
    }
}
