<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
        $role = $this->role;

        $rules = [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'telephone' => ['required','unique:users,telephone', 'regex:/^(?:\+237)?6[0-9]{8}$/'],
            'role' => 'required|string|in:student,donor,admin,restaurant',
        ];

        if ($role == 'student') {
            $rules = array_merge($rules, [
                'department' => 'required|string',
                'matricule' => 'required|string|unique:users,matricule',
                'school' => 'required|string',
                'level' => 'required|string',
            ]);
        }
        if ($role == 'donor') {
            $rules = array_merge($rules, [
                'occupation' => 'required|string',
            ]);
        }

        return $rules;
    }

}
