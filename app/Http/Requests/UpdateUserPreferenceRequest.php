<?php

namespace App\Http\Requests;

use App\Enums\NewsSource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserPreferenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sources' => ['nullable', 'array'],
            'sources.*' => ['string', Rule::in(NewsSource::values())],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['string', 'max:100'],
            'authors' => ['nullable', 'array'],
            'authors.*' => ['string', 'max:255'],
        ];
    }
}
