<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DonorRequest extends FormRequest
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
            'role' => 'required|in:donor',
            'name' => 'required|string',
            'surname' => 'required|string|max:255',
            'password' => 'required|min:6',
            'occupation' => 'required|string',
            'telephone' => ['required', 'regex:/^(?:\+237)?6[0-9]{8}$/'],
        ];
    }
}
