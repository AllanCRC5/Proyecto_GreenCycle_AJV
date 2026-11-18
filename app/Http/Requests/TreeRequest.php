<?php

namespace App\Http\Requests;

use App\Enums\SeedType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TreeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'seed_type' => [
                'nullable',
                Rule::enum(SeedType::class),
                'in:BASIC',
            ],
        ];
    }
}
