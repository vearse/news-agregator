<?php

namespace App\Http\Requests;

use App\Enums\NewsSource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArticleIndexRequest extends FormRequest
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
            'search' => ['nullable', 'string', 'max:255'],
            'sources' => ['nullable', 'array'],
            'sources.*' => ['string', Rule::in(NewsSource::values())],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['string', 'max:100'],
            'authors' => ['nullable', 'array'],
            'authors.*' => ['string', 'max:255'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function filters(): array
    {
        return [
            'search' => $this->input('search'),
            'sources' => $this->input('sources', []),
            'categories' => $this->input('categories', []),
            'authors' => $this->input('authors', []),
            'from' => $this->input('from'),
            'to' => $this->input('to'),
        ];
    }
}