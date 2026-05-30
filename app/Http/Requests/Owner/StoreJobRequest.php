<?php

namespace App\Http\Requests\Owner;

use App\Models\Job;
use App\Models\Property;
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
        $orgId = $this->user()->organization_id;

        return [
            'customer_id'  => ['required', 'integer', Rule::exists('customers', 'id')->where('organization_id', $orgId)],
            'property_id'  => ['nullable', 'integer', function ($attribute, $value, $fail) {
                if ($value === null) return;
                if (! Property::where('id', $value)
                        ->where('customer_id', $this->customer_id)
                        ->where('organization_id', $this->user()->organization_id)
                        ->exists()) {
                    $fail('The selected property does not belong to this customer.');
                }
            }],
            'job_type_id'  => ['nullable', 'integer', Rule::exists('job_types', 'id')->where('organization_id', $orgId)],
            'assigned_to'  => ['nullable', 'integer', Rule::exists('users', 'id')->where('organization_id', $orgId)],
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'scheduled_at' => ['nullable', 'date'],
            'office_notes' => ['nullable', 'string'],
        ];
    }
}
