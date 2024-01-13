<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            // exists: reject a dangling team_id at the edge instead of letting
            // it surface as a foreign key violation deep in the driver.
            'team_id' => ['required', 'integer', 'exists:teams,id'],
        ];
    }
}
