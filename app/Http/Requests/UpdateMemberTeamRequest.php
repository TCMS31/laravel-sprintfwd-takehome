<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberTeamRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'team_id' => ['required', 'integer', 'exists:teams,id'],
        ];
    }
}
