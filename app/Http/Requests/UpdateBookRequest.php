<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
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
            'title'        => 'sometimes|string|max:255',
            'author'       => 'sometimes|string|max:255',
            'isbn'         => 'sometimes|string|unique:books,isbn,' . $this->book->id,
            'stock'        => 'sometimes|integer|min:0',
            'published_at' => 'nullable|date',
        ];
    }
}
